<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008-2014 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 *
 *------------------------------------------------------------------->8-----
 */
namespace BEdita\Model\Behavior;

use Cake\ORM\Behavior;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\ORM\Query;
use Cake\ORM\Entity;
use Cake\Utility\Inflector;
use Cake\Event\Event;

/**
 * BEditaObjectBehavior
 */
class BEditaObjectBehavior extends Behavior {

    protected $table;

    public function __construct(Table $table, array $config = []) {
        parent::__construct($table, $config);
        $this->table = $table;
    }

    /**
     * Format Cake\ORM\Entity or array
     * Flat object chain
     *
     * @param \Cake\ORM\Entity|array $row
     * @return \Cake\ORM\Entity|array
     */
    public function formatObject($row) {
        // flat object chain
        foreach ($this->table->getObjectChain() as $tableName) {
            $property = Inflector::underscore(Inflector::singularize($tableName));
            // if $row is an \Cake\ORM\Entity
            if (is_object($row)) {
                if ($row->$property) {
                    $row->set($row->$property->toArray(), ['guard' => false]);
                    $row->unsetProperty($property);
                }
            // else it's an array (Entity was not hydrated)
            } else {
                if (isset($row[$property])) {
                    $row += $row[$property];
                    unset($row[$property]);
                }
            }
        }
        return $row;
    }

    /**
     * Add condition on object_type_id
     * If $options['formatResults'] == false (default true),
     * it doesn't make any formatResults operation
     *
     * @param \Cake\Event\Event $event
     * @param \Cake\ORM\Query $query The query object
     * @param array  $options
     * @param boolean $primary Indicates whether or not this is the root query, or an associated query
     */
    public function beforeFind(Event $event, Query $query, array $options, $primary) {
        $query->where(['object_type_id' => $this->table->objectTypeId()]);

        $defaultOptions = ['formatResults' => true];
        $options = array_merge($defaultOptions, $options);

        if ($options['formatResults']) {
            $query->formatResults(function($results) {
                return $results->map(function($row) {
                    return $this->formatObject($row);
                });
            });
        }
    }

    /**
     * Dynamically build object chain entities
     *
     * @param \Cake\Event\Event $event
     * @param \Cake\ORM\Entity $entity
     * @param array $options
     */
    public function beforeSave(Event $event, Entity $entity, $options) {
        foreach ($this->table->getObjectChain() as $tableName) {
            $table = TableRegistry::get($tableName);
            $tableData = [];
            foreach ($entity->visibleProperties() as $field) {
                if ($table->hasField($field)) {
                    $tableData[$field] = $entity->$field;
                    if ($field != 'id') {
                        $entity->unsetProperty($field);
                    }
                }
            }

            if (!empty($tableData)) {
                $chainEntity = $table->newEntity($tableData);
                $dataField = Inflector::underscore(Inflector::singularize($tableName));
                $entity->set($dataField, $chainEntity);
            }
        }
    }

}
