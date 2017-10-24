<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Table;

use BEdita\Core\Model\Entity\ObjectType;
use BEdita\Core\Model\Entity\Property;
use BEdita\Core\ORM\Inheritance\Table as InheritanceTable;
use BEdita\Core\Utility\Text;
use Cake\Cache\Cache;
use Cake\Database\Connection;
use Cake\Database\Driver\Postgres;
use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Query;
use Cake\Database\Schema\TableSchema;
use Cake\Log\Log;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Properties Model
 *
 * @property \Cake\ORM\Association\BelongsTo $PropertyTypes
 * @property \Cake\ORM\Association\BelongsTo $ObjectTypes
 *
 * @since 4.0.0
 */
class StaticPropertiesTable extends PropertiesTable
{

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setEntityClass(Property::class);

        // Use a unique table name for each instance. This avoids conflicts if we don't drop the temporary table.
        // Temporary tables can be safely left there, since they will be deleted as soon as the connection
        // to the database is closed. Doing so, we never explicitly drop a temporary table.
        $this->setTable(sprintf(
            'static_properties_%016x',
            function_exists('random_int') ? random_int(0, PHP_INT_MAX) : mt_rand(0, PHP_INT_MAX)
        ));

        // Create the temporary table.
        $this->createTable();

        if ($this->getConnection()->getDriver() instanceof Postgres) {
            // If we're using PostgreSQL we must tell CakePHP to use the correct namespace (that depends on
            // the current connection) to describe the temporary table, or it will believe that the table has
            // zero columns, and the ORM will fail to create new entities and persist them.
            // This query must be executed _after_ the temporary table has been created, because the namespace
            // is not present at all until at least one temporary table has been created.
            $schema = (new Query($this->getConnection()))
                ->select(['nspname'])
                ->from(['pg_namespace'])
                ->where([
                    'oid' => new FunctionExpression('pg_my_temp_schema'),
                ])
                ->execute()
                ->fetch();
            $this->setTable(sprintf('%s.%s', $schema[0], $this->getTable()));
        }

        // Insert data into table.
        $this->addSchemaDetails();
    }

    /**
     * Create temporary table with same columns, options and indexes as `properties`.
     *
     * @return void
     */
    protected function createTable()
    {
        Log::debug('Using temporary table for static properties'); // Log for statistics purposes... :/

        $tableName = $this->getTable();
        $parentTable = TableRegistry::get('Properties');
        $safeRename = function ($indexOrConstraint) use ($tableName) {
            return preg_replace(
                '/^properties_/',
                sprintf('%s_', str_replace('_', '', $tableName)),
                $indexOrConstraint
            );
        };

        // Create new temporary table.
        $table = new TableSchema($tableName);
        $table->setTemporary(true);

        // Copy options, columns, indexes and constraints (except foreign keys) from `properties`.
        $schema = $parentTable->getSchema();
        $table->setOptions($schema->getOptions());
        foreach ($schema->columns() as $column) {
            $attributes = $schema->getColumn($column);
            if ($column === $parentTable->getPrimaryKey()) {
                // Use custom IDs.
                $attributes['type'] = 'uuid';
                $attributes['length'] = null;
            }
            $table->addColumn($column, $attributes);
        }
        foreach ($schema->indexes() as $index) {
            $table->addIndex($safeRename($index), $schema->getIndex($index));
        }
        foreach ($schema->constraints() as $constraint) {
            $attributes = $schema->getConstraint($constraint);
            if (empty($attributes['type']) || $attributes['type'] === $schema::CONSTRAINT_FOREIGN) {
                // Temporary tables can't have foreign key constraints in MySQL.
                // https://dev.mysql.com/doc/refman/5.7/en/create-table-foreign-keys.html
                continue;
            }
            $table->addConstraint($safeRename($constraint), $attributes);
        }

        // Execute SQL to create table. In MySQL the transaction is completely useless,
        // because `CREATE TABLE` implicitly implies a commit.
        $this->getConnection()->transactional(function (Connection $connection) use ($table) {
            foreach ($table->createSql($connection) as $statement) {
                $connection->execute($statement);
            }
        });
    }

    /**
     * Store schema details into the temporary table.
     *
     * @return void
     */
    protected function addSchemaDetails()
    {
        $properties = Cache::remember(
            'static_properties',
            function () {
                return $this->ObjectTypes->find()
                    ->contain(['Parent'])
                    ->order([
                        $this->ObjectTypes->aliasField('tree_left') => 'ASC', // Ensure parent tables are processed first!
                    ])
                    ->reduce(
                        function (array $accumulator, ObjectType $objectType) {
                            $tables = $this->listOwnTables($objectType);
                            foreach ($tables as $table) {
                                $accumulator = array_merge(
                                    $accumulator,
                                    $this->prepareTableFields($objectType, $table)
                                );
                            }

                            return $accumulator;
                        },
                        []
                    );
            },
            ObjectTypesTable::CACHE_CONFIG
        );
        $this->saveMany($properties);
    }

    /**
     * List models that are specific for the object type.
     *
     * @param \BEdita\Core\Model\Entity\ObjectType $objectType Object type to be described.
     * @return \Cake\ORM\Table[]
     */
    protected function listOwnTables(ObjectType $objectType)
    {
        $table = TableRegistry::get($objectType->alias);
        $tables = [$table];
        if ($table instanceof InheritanceTable) {
            $tables = array_merge($tables, $table->inheritedTables());
        }

        if (!$objectType->has('parent')) {
            // Object type does not have a parent.
            return $tables;
        }

        $parentTable = TableRegistry::get($objectType->parent->alias);
        if ($parentTable->getTable() === $table->getTable()) {
            // Same physical table as parent object: nothing to do. This happens for
            // "null extensions", like documents that extend objects, but don't have
            // their own table.
            return [];
        }

        if ($table instanceof InheritanceTable) {
            // Object type own tables are tables in inheritance chain that are not in
            // parent object type's inheritance chain.
            $commonTables = $table->commonInheritance($parentTable);
            $tables = array_filter(
                $tables,
                function (Table $table) use ($commonTables) {
                    return !in_array($table, $commonTables, true);
                }
            ); // `array_diff($tables, $table->commonInheritance($parentTable))` does not work. :(
        }

        return $tables;
    }

    /**
     * Return an array of Property entities that represent object type concrete fields.
     *
     * Static properties are assigned a UUID version 5 based on their `table_name.column_name`, so that
     * the ID is consistent across subsequent requests and even installations. For instance,
     * `objects.status` will always have ID `bd4dae3e-6b54-5d46-b4e2-a8d553676a82`.
     *
     * @param \BEdita\Core\Model\Entity\ObjectType $objectType Object type to be described.
     * @param \Cake\ORM\Table $table Table object.
     * @return \BEdita\Core\Model\Entity\Property[]
     */
    protected function prepareTableFields(ObjectType $objectType, Table $table)
    {
        $schema = $table->getConnection()
            ->getSchemaCollection()
            ->describe($table->getTable());

        $sampleEntity = $table->newEntity();
        $hiddenProperties = $sampleEntity->getHidden();

        $properties = [];
        foreach ($schema->columns() as $name) {
            if (in_array($name, (array)$table->getPrimaryKey()) || in_array($name, $hiddenProperties)) {
                continue;
            }

            $column = $schema->getColumn($name);

            $property = $this->newEntity(compact('name'));
            $property->id = Text::uuid5(sprintf('%s.%s', $objectType->name, $name));
            $property->set('object_type_id', $objectType->id);
            $property->set('property_type_id', $this->PropertyTypes->find()->firstOrFail()->id); // TODO
            $property->set('description', Hash::get($column, 'comment'));

            $properties[] = $property;
        }

        return $properties;
    }
}
