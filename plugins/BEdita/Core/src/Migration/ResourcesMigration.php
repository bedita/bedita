<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Migration;

use BEdita\Core\Utility\Resources;
use Cake\Database\Connection;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Migrations\AbstractMigration;
use Migrations\Table;
use ReflectionClass;
use RuntimeException;
use Symfony\Component\Yaml\Yaml;

abstract class ResourcesMigration extends AbstractMigration
{
    /**
     * Read YAML migration data
     *
     * @param bool $up Up direction
     * @return array
     */
    protected function readData(bool $up = true): array
    {
        $path = (new ReflectionClass($this))->getFileName();
        $file = str_replace('.php', '.yml', $path);
        if (!file_exists($file)) {
            throw new RuntimeException(__d('bedita', 'YAML file not found'));
        }

        $data = (array)Yaml::parse(file_get_contents($file));
        if ($up) {
            return array_intersect_key($data, array_flip(['create', 'update', 'remove']));
        }

        return array_filter([
            'create' => array_reverse((array)Hash::get($data, 'remove')),
            'remove' => array_reverse((array)Hash::get($data, 'create')),
            'update' => (array)Hash::get($data, 'restore'),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function up(): void
    {
        $data = $this->readData();
        $this->executeMigration($data);
    }

    /**
     * {@inheritDoc}
     */
    public function down(): void
    {
        $data = $this->readData(false);
        $this->executeMigration($data);
    }

    /**
     * Extract column related data from array, then:
     *  - perform internal resources migration
     *  - update table columns
     *
     * @param array $data Migration data
     * @return void
     */
    protected function executeMigration(array $data): void
    {
        $columnActions = $this->tableColumnsActions($data);
        // first perform column removal
        $removeColumns = array_filter([
            'remove' => Hash::get($columnActions, 'remove')
        ]);
        $this->updateColumns($removeColumns);
        unset($columnActions['remove']);

        // then perform resources operations
        Resources::save(
            $data,
            ['connection' => $this->getConnection()]
        );
        // finally columns creation + change
        $this->updateColumns($columnActions);
    }

    /**
     * Retrieve Db Connection
     *
     * @return Connection
     *
     * @codeCoverageIgnore
     */
    protected function getConnection(): Connection
    {
        return $this->getAdapter()->getCakeConnection();
    }

    /**
     * Extracts column related actions from migration data.
     * Removes column actions from migration data.
     *
     * @param array $data Migration data.
     * @return array
     */
    protected function tableColumnsActions(array &$data): array
    {
        $res = [];
        foreach ($data as $action => &$value) {
            $path = 'properties.{n}[is_column=true]';
            $res[$action] = Hash::extract($value, $path);
            $value = Hash::remove($value, $path);
        }

        return $res;
    }

    /**
     * Perform schema change actions on columns.
     *
     * @param array $data Column actions data
     * @return void
     */
    protected function updateColumns(array $data): void
    {
        foreach ($data as $action => $items) {
            array_walk(
                $items,
                function ($item) use ($action) {
                    $this->columnAction($action, $item);
                }
            );
        }
    }

    /**
     * Perform schema operation on a single table column.
     *
     * @param string $action Column action ('create', 'update' or 'remove' actions)
     * @param array $data Action data.
     * @return void
     */
    protected function columnAction(string $action, array $data): void
    {
        $table = $this->migrationTable($data['object']);
        $column = $data['name'];

        if ($action === 'remove') {
            $table->removeColumn($column)->update();

            return;
        }

        $type = $this->getColumnType($data['property']);
        $options = $this->getColumnOptions($data);

        if ($action === 'create') {
            $table->addColumn($column, $type, $options)
                ->update();

                return;
        }

        $table->changeColumn($column, $type, $options)
            ->update();
    }

    /**
     * Get Migrations table object from object type name.
     *
     * @param string $object Object type name.
     * @return Table
     */
    protected function migrationTable(string $object): Table
    {
        $name = TableRegistry::getTableLocator()->get($object)->getTable();

        return $this->table($name);
    }

    /**
     * Retrieve column type options from property name.
     *
     * @param string $property Property name.
     * @return string
     */
    protected function getColumnType(string $property): string
    {
        return $property;
    }

    /**
     * Retrieve table column options from property data.
     *
     * @param array $propData Property data.
     * @return array
     */
    protected function getColumnOptions(array $propData): array
    {
        $attributes = (array)Hash::get($propData, 'column_attributes');
        $comment = Hash::get($propData, 'description');

        return $attributes + array_filter(compact('comment'));
    }
}
