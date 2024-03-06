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

use BEdita\Core\Exception\ImmutableResourceException;
use BEdita\Core\Model\Validation\Validation;
use BEdita\Core\Search\SimpleSearchTrait;
use Cake\Cache\Cache;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Property Types - available property types
 *
 * @method \BEdita\Core\Model\Entity\PropertyType newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\PropertyType[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\PropertyType|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\PropertyType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\PropertyType[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\PropertyType findOrCreate($search, callable $callback = null, $options = [])
 * @property \Cake\ORM\Association\HasMany $Properties
 * @since 4.0.0
 */
class PropertyTypesTable extends Table
{
    use SimpleSearchTrait;

    /**
     * Map between specific column types and property types names.
     *
     * @var array
     */
    protected const COLUMN_TYPE_MAP = [
        'float' => 'number',
        'timestamp' => 'datetime',
        'timestampfractional' => 'datetime',
    ];

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('property_types');
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');

        $this->addBehavior('Timestamp');
        $this->addBehavior('BEdita/Core.Searchable');
        $this->addBehavior('BEdita/Core.ResourceName');

        $this->hasMany('Properties');

        $this->setupSimpleSearch(['fields' => ['name', 'params']]);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->setProvider('bedita', Validation::class)

            ->requirePresence('name', 'create')
            ->notEmptyString('name')
            ->regex('name', Validation::RESOURCE_NAME_REGEX)

            ->allowEmptyArray('params')
            ->add('params', 'valid', [
                'rule' => ['jsonSchema', 'http://json-schema.org/draft-06/schema#'],
                'provider' => 'bedita',
            ]);

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _initializeSchema(TableSchemaInterface $schema): TableSchemaInterface
    {
        $schema->setColumnType('params', 'json');

        return $schema;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['name']));

        return $rules;
    }

    /**
     * Avoid modifications when `core_type` is true.
     *
     * @param \Cake\Event\EventInterface $event Event fired
     * @param \Cake\Datasource\EntityInterface $entity Entity to be saved
     * @return void
     * @throws \BEdita\Core\Exception\ImmutableResourceException
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity)
    {
        if (!$entity->isNew() && $entity->isDirty() && $entity->get('core_type')) {
            throw new ImmutableResourceException(__d('bedita', 'Could not modify core property'));
        }
    }

    /**
     * Invalidate object types cache after updating a property type.
     *
     * @return void
     */
    public function afterSave()
    {
        Cache::clear(ObjectTypesTable::CACHE_CONFIG);
    }

    /**
     * Check that no properties exist linked to the property type before deleting it.
     *
     * @param \Cake\Event\EventInterface $event Dispatched event.
     * @param \Cake\Datasource\EntityInterface $entity Entity being deleted.
     * @return void
     * @throws \Cake\Http\Exception\ForbiddenException Throws an exception if one or more properties exist
     *      with the property type being deleted.
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity)
    {
        if ($this->Properties->exists([$this->Properties->getForeignKey() => $entity->get($this->Properties->getBindingKey())])) {
            throw new ForbiddenException(__d('bedita', 'Property type with existing properties'));
        }
    }

    /**
     * Invalidate object types cache after deleting a property type.
     *
     * @return void
     */
    public function afterDelete()
    {
        Cache::clear(ObjectTypesTable::CACHE_CONFIG);
    }

    /**
     * Detect most appropriate property type for a column.
     *
     * @param string $name Column name.
     * @param \Cake\ORM\Table $table Table object.
     * @return \BEdita\Core\Model\Entity\PropertyType
     */
    public function detect($name, Table $table)
    {
        /** @var \BEdita\Core\Model\Entity\PropertyType[] $propertyTypes */
        $propertyTypes = Cache::remember(
            'property_types',
            function () {
                return $this->find()
                    ->all()
                    ->indexBy('name')
                    ->toArray();
            },
            ObjectTypesTable::CACHE_CONFIG
        );

        // Check if there is a property type whose name matches column name.
        if (isset($propertyTypes[$name])) {
            return $propertyTypes[$name];
        }

        // Check if there is a property type whose name matches an applicable validation rule's name.
        $rules = array_keys($table->getValidator()->field($name)->rules());
        foreach ($rules as $ruleName) {
            if (isset($propertyTypes[$ruleName])) {
                return $propertyTypes[$ruleName];
            }
        }

        // Try to infer a generic type from column definition.
        $type = $table->getSchema()->getColumnType($name);
        if (isset($propertyTypes[$type])) {
            return $propertyTypes[$type];
        }
        // Try to convert specific types to more generic ones.
        if (isset(static::COLUMN_TYPE_MAP[$type])) {
            $type = static::COLUMN_TYPE_MAP[$type];
        }
        if (isset($propertyTypes[$type])) {
            return $propertyTypes[$type];
        }

        // Fallback on string type.
        return $propertyTypes['string'];
    }
}
