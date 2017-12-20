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

namespace BEdita\Core\Model\Entity;

use Cake\Database\Type;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Static property entity.
 *
 * @property mixed $default
 * @property \Cake\ORM\Table $table
 *
 * @since 4.0.0
 */
class StaticProperty extends Property
{

    /**
     * Convert a property into a static property.
     *
     * This method is used to hydrate entities correctly.
     *
     * @param \BEdita\Core\Model\Entity\Property $property Property to convert.
     * @return static
     * @internal
     */
    public static function fromProperty(Property $property)
    {
        return new static(
            $property->_properties,
            [
                'markNew' => $property->isNew(),
                'markClean' => true,
                'guard' => false,
                'source' => $property->getSource(),
            ]
        );
    }

    /**
     * Setter for `name` property.
     *
     * @param string $name Property name.
     * @return string
     */
    protected function _setName($name)
    {
        $this->inferFromSchema($name, $this->table);

        return $name;
    }

    /**
     * Setter for `table` virtual property.
     *
     * @param string|\Cake\ORM\Table $table Table.
     * @return string
     */
    protected function _setTable($table)
    {
        if (!($table instanceof Table)) {
            $table = TableRegistry::get($table);
        }

        $this->inferFromSchema($this->name, $table);

        return $table->getRegistryAlias();
    }

    /**
     * Getter for `table` virtual property.
     *
     * @return \Cake\ORM\Table|null
     */
    protected function _getTable()
    {
        if (isset($this->_properties['table'])) {
            // Explicitly set.
            return TableRegistry::get($this->_properties['table']);
        }

        if ($this->object_type_id) {
            return TableRegistry::get($this->object_type_name);
        }

        return null;
    }

    /**
     * Get schema column definition for property.
     *
     * @param string|null $name Column name.
     * @param \Cake\ORM\Table|null $table Table object instance.
     * @return array|mixed|null
     */
    protected static function getSchemaColumnDefinition($name, Table $table = null)
    {
        if ($name === null || $table === null) {
            return null;
        }

        return $table->getSchema()->getColumn($name);
    }

    /**
     * Infer property metadata from schema.
     *
     * @param string|null $name Column name.
     * @param \Cake\ORM\Table|null $table Table object instance.
     * @return void
     */
    protected function inferFromSchema($name, Table $table = null)
    {
        $schema = static::getSchemaColumnDefinition($name, $table);
        if ($schema === null) {
            // Unable to infer anything.
            return;
        }

        // Property type.
        /** @var \BEdita\Core\Model\Table\PropertyTypesTable $propertyTypesTable */
        $propertyTypesTable = TableRegistry::get('PropertyTypes');
        $this->property_type_name = $propertyTypesTable->detect($name, $table)->name;

        // Description and nullability.
        $this->description = Hash::get($schema, 'comment', null);
        $this->is_nullable = Hash::get($schema, 'null', false);

        // Empty previously cached default value.
        unset($this->default);
    }

    /**
     * Getter for `default` virtual property.
     *
     * @return mixed|null
     */
    protected function _getDefault()
    {
        if (array_key_exists('default', $this->_properties)) {
            // Previously cached value.
            return $this->_properties['default'];
        }

        $schema = static::getSchemaColumnDefinition($this->name, $this->table);
        if ($schema === null) {
            // Unable to obtain schema: return without caching result.
            return null;
        }

        $default = Hash::get($schema, 'default', null);
        $typeName = $this->table->getSchema()->getColumnType($this->name);
        if ($default === 'CURRENT_TIMESTAMP' && in_array($typeName, ['date', 'datetime', 'time', 'timestamp'])) {
            // Default value is not meaningful in this case.
            return $this->_properties['default'] = null;
        }

        $type = Type::build($typeName);
        $driver = $this->table->getConnection()->getDriver();

        return $this->_properties['default'] = $type->toPHP($default, $driver);
    }

    /**
     * Getter for `required` virtual property.
     *
     * @return bool
     */
    protected function _getRequired()
    {
        if (!$this->table) {
            return !$this->is_nullable && $this->default === null;
        }

        $validator = $this->table->getValidator();
        if (!$validator->hasField($this->name)) {
            return false;
        }

        return (bool)$validator->field($this->name)->isPresenceRequired();
    }

    /**
     * {@inheritDoc}
     */
    public function getSchema($accessMode = null)
    {
        $schema = parent::getSchema($accessMode);
        if (!is_array($schema)) {
            return $schema;
        }

        // String max length.
        $path = '';
        if ($this->is_nullable) {
            $path = 'oneOf.0.';
        }
        if (Hash::get($schema, $path . 'type') === 'string' && !Hash::check($schema, $path . 'enum')) {
            $column = static::getSchemaColumnDefinition($this->name, $this->table);
            if ($column !== null && !empty($column['length']) && !array_key_exists('maxLength', $schema)) {
                // Add maximum length validation to strings.
                $schema = Hash::insert($schema, $path . 'maxLength', $column['length']);
            }
        }

        // Default value.
        if ($this->has('default')) {
            $schema['default'] = $this->default;
        }

        return $schema;
    }
}
