<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\ORM\Inheritance;

use Cake\Database\Type;
use Cake\ORM\Marshaller as CakeMarshaller;
use Cake\ORM\Table;

/**
 * Extends \Cake\ORM\Marshaller providing the property map of the entire inheritance
 * used when entities are hydrated
 */
class Marshaller extends CakeMarshaller
{
    /**
     * {@inheritDoc}
     *
     * Build the map of property of all inheritance chain.
     */
    protected function _buildPropertyMap($data, $options)
    {
        $propertyMap = parent::_buildPropertyMap($data, $options);
        $inheritedTables = $this->_table->inheritedTables();
        if (empty($inheritedTables)) {
            return $propertyMap;
        }

        $inheritedMap = [];
        foreach ($inheritedTables as $table) {
            $inheritedMap += $this->buildTablePropertyMap($table, $data);
        }

        return $propertyMap + $inheritedMap;
    }

    /**
     * Build the map of property of the given table.
     *
     * @param Table $table The table to check for property existance
     * @param array $data The data that has to be marshalled
     * @return array
     */
    protected function buildTablePropertyMap(Table $table, array $data)
    {
        $map = [];
        $schema = $table->getSchema();

        foreach (array_keys($data) as $prop) {
            $columnType = $schema->getColumnType($prop);
            if (!$columnType) {
                continue;
            }

            $map[$prop] = function ($value, $entity) use ($columnType) {
                return Type::build($columnType)->marshal($value);
            };
        }

        return $map;
    }
}
