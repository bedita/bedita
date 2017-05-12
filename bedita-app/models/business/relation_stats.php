<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
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

/**
 * Class that provides relation statistics
 */
class RelationStats extends BEAppModel
{
    public $useTable = false;

    public $relations = array();
    public $relationNames = array();

    /**
     * Constructor: retrieve all relations data
     */
    public function __construct() {
        parent::__construct();
        $this->relations = BeConfigure::mergeAllRelations();
        $this->relationNames = array_keys($this->relations);
        sort($this->relationNames);
        $orderedRelations = array();
        foreach ($this->relationNames as $relationName) {
            sort($this->relations[$relationName]['left']);
            sort($this->relations[$relationName]['right']);
            $orderedRelations[$relationName] = $this->relations[$relationName];
        }
        $this->relations = $orderedRelations;
    }

    /**
     * Return relation data by relation name
     *
     * @param string $relationName relation name
     * @return array relation data
     */
    public function getRelation($relationName) {
        return (!empty($this->relations[$relationName])) ? $this->relations[$relationName] : null;
    }

    /**
     * Return all relations
     *
     * @return array relations
     */
    public function getRelations() {
        return $this->relations;
    }

    /*
     * Return relation names
     *
     * @return array string relation names
     */
    public function getRelationNames() {
        return $this->relationNames;
    }

    /**
     * Return relation data as string
     *
     * @param string $relationName relation name
     * @return string relation data
     */
    public function getDescription($relationName) {
        $relationData = $this->getRelation($relationName);
        $description = 'Relation "' . $relationName . '"';
        $description.= "\n" . json_encode($relationData);
        if (empty($relationData['left'])) {
            $description.= "\n" . '(left related models: ' . trim(implode(',',$this->objectTypesRelatedNames())) . ')';
        }
        if (empty($relationData['right'])) {
            $description.= "\n" . '(right related models: ' . trim(implode(',',$this->objectTypesRelatedNames())) . ')';
        }
        return $description;
    }

    /**
     * Return ObjectRelation records by relation $relationName, for specific objects types:
     *     left side of relation object types => $leftObjectTypes
     *     right side of relation object types => $rightObjectTypes
     *
     * @param string $relationName relation name
     * @param array $leftObjectTypes int representing object types for left side of relation $relationName
     * @param array $rightObjectTypes int representing object types for right side of relation $relationName
     * @return array ObjectRelation relations
     */
    public function getObjectRelationsByNameAndObjectTypes($relationName, $leftObjectTypes, $rightObjectTypes) {
        return ClassRegistry::init('ObjectRelation')->find('all', array(
            'conditions' => array(
                'switch' => $relationName
            ),
            'joins' => array(
                array(
                    'table' => 'objects',
                    'alias' => 'ObjectLeft',
                    'type' => 'inner',
                    'conditions' => array(
                        'ObjectLeft.id = ObjectRelation.id',
                        'ObjectLeft.object_type_id' => $leftObjectTypes
                    )
                ),
                array(
                    'table' => 'objects',
                    'alias' => 'ObjectRight',
                    'type' => 'inner',
                    'conditions' => array(
                        'ObjectRight.id = ObjectRelation.object_id',
                        'ObjectRight.object_type_id' => $rightObjectTypes
                    )
                )
            )
        ));
    }

    /**
     * Return ObjectRelation count by $relationName relation.
     * If $objectTypesFilter is specified, return count by object_type_id in $objectTypesFilter.
     *
     * @param string $relationName relation name
     * @return int count of ObjectRelation by $relationName relation (and object types in $objectTypesFilter, if specified)
     */
    public function getObjectRelationsCount($relationName, $objectTypesFilter = null) {
        $options = array('conditions' => array('switch' => $relationName));
        if (!empty($objectTypesFilter)) {
            $options['joins'] = array(
                array(
                    'table' => 'objects',
                    'alias' => 'ObjectLeft',
                    'type' => 'inner',
                    'conditions' => array(
                        'ObjectLeft.id = ObjectRelation.id',
                        'ObjectLeft.object_type_id' => $objectTypesFilter
                    )
                )
            );
        }
        return ClassRegistry::init('ObjectRelation')->find('count', $options);
    }

    /**
     * Return ObjectRelation array count by $relationName relation group by object_type_id
     *
     * @param string $relationName relation name
     * @return array count of ObjectRelation by $relationName relation group by object_type_id
     */
    public function getObjectRelationsCountGroupByType($relationName) {
        $result = array();
        $query = ClassRegistry::init('BEObject')->find('all', array(
            'fields' => array('COUNT(BEObject.id) AS items', 'BEObject.object_type_id'),
            'joins' => array(
                array(
                    'table' => 'object_relations',
                    'alias' => 'ObjectRelation',
                    'type' => 'inner',
                    'conditions' => array(
                        'BEObject.id = ObjectRelation.id',
                        'ObjectRelation.switch' => $relationName
                    )
                )
            ),
            'contain' => array(),
            'group' => array('BEObject.object_type_id')
        ));
        foreach ($query as $r) {
            $result[$r['BEObject']['object_type_id']] = $r[0]['items'];
        }
        return $result;
    }

    /**
     * Return object type ids for relation by $side (can be 'left' or 'right')
     * If $side array is empty, return configure objectTypes.related.id array
     *
     * @param array $relationData relation data
     * @param string $side can be 'left' or 'right'
     * @return array int object type ids
     */
    public function objectTypesIdsForObjectNames($relationData, $side = 'left') {
        $objectTypesIds = array();
        if (!empty($relationData[$side])) {
            $objectNames = $relationData[$side];
            if (!empty($objectNames)) {
                foreach ($objectNames as $objectName) {
                    $ot = Configure::read('objectTypes.' . $objectName . '.id');
                    if (!empty($ot)) {
                        $objectTypesIds[] = $ot;
                    }
                }
            }
        } else {
            $objectTypesIds = Configure::read('objectTypes.related.id');
        }
        return $objectTypesIds;
    }

    /**
     * Return ordered object types related names, getting them from config keys objectTypes.related.id
     *
     * @return array related object types names
     */
    private function objectTypesRelatedNames() {
        $result = array();
        $objectTypes = Configure::read('objectTypes');
        foreach ($objectTypes as $key => $data) {
            if (is_int($key)) {
                $result[] = $data['name'];
            }
        }
        sort($result);
        return $result;
    }
}
