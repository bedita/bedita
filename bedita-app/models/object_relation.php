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

App::import('Sanitize');

/**
 * Object relations model
 *
 */
class ObjectRelation extends BEAppModel
{

    public function afterFind($results) {
        if (!empty($results[0][$this->alias])) {
            foreach ($results as &$r) {
                if (!empty($r[$this->alias]['params'])) {
                    $params = json_decode($r[$this->alias]['params'], true);
                    if(!empty($params) && is_array($params)) {
                        $r[$this->alias]['params'] = $params;
                    } else {
                        unset($r[$this->alias]['params']);
                    }
                }
            }
        }
        return $results;
    }

    /**
     * Escape all arguments that have been passed to this function.
     * 
     * @param any[] ...$values Values to be prepared.
     * @return string[]
     */
    private static function prepareAll() {
        $values = func_get_args();
        foreach ($values as &$val) {
            if ($val === null) {
                $val = 'NULL';
                continue;
            }
            
            if (!is_scalar($val)) {
                $val = json_encode($val);
            }
            $val = sprintf('\'%s\'', Sanitize::escape($val));
        }
        unset($val);

        return $values;
    }

    /**
     * Create relation between objects
     *
     * TODO: sql query, not working with cake ->save() .. why??
     *
     * cake->save() doesn't work beacuse of table structure. It should be id primary key, object_id, related_object_id, switch, priority)
     *
     * @param int $id The left object id
     * @param int $objectId The right object id
     * @param string $switch The switch name
     * @param int $priority The relation priority
     * @param bool $bidirectional If the relation is bidirectional
     * @param array|null $params The additional relation params
     * @return array|false $this->query() output - false on error
     */
    public function createRelation($id, $objectId, $switch, $priority, $bidirectional = true, $params = array()) {
        // #CUSTOM QUERY - TODO: use cake, how??
        list($qId, $qObjectId, $qSwitch, $qPriority, $qParams) = static::prepareAll($id, $objectId, $switch, $priority, $params);
        $q = "INSERT INTO object_relations (id, object_id, switch, priority, params) VALUES ({$qId}, {$qObjectId}, {$qSwitch}, {$qPriority}, {$qParams})";
        $res = $this->query($q);
        if ($res === false) {
            return $res;
        }
        if ($bidirectional) {
            $q = "INSERT INTO object_relations (id, object_id, switch, priority, params) VALUES ({$qObjectId}, {$qId}, {$qSwitch}, {$qPriority}, {$qParams})";
            $res = $this->query($q);
    
            if ($res === false) {
                return $res;
            }
        }

        ClassRegistry::init('BEObject')->clearCacheByIds(array($id, $objectId));

        return $res;
    }

    /**
     * Create direct and inverse relation using $switch and $inverseSwitch names
     * @param int $id Left relation element id
     * @param int $objectId Right relation element id
     * @param string $switch Direct name
     * @param $inverseSwitch Inverse name
     * @param int $priority The relation priority
     * @param array|null $params The addtional relation params
     * @param string $inverseSwitch, inverse name
     * @return array|false 
     */
    public function createRelationAndInverse($id, $objectId, $switch, $inverseSwitch = null, $priority = null, $params = array()) {
        if ($inverseSwitch == null) {
            $inverseSwitch = $switch;
        }

        list($qId, $qObjectId, $qSwitch, $qInverseSwitch, $qPriority, $qParams) = static::prepareAll($id, $objectId, $switch, $inverseSwitch, $priority, $params);
        if ($priority == null) {
            $rel = $this->query("SELECT MAX(priority)+1 AS priority FROM object_relations WHERE id={$qId} AND switch={$qSwitch}");
            $qPriority = $priority = (empty($rel[0][0]["priority"]))? 1 : $rel[0][0]["priority"];
        }

        // #CUSTOM QUERY
        $q = "INSERT INTO object_relations (id, object_id, switch, priority, params) VALUES ({$qId}, {$qObjectId}, {$qSwitch}, {$qPriority}, {$qParams})";
        $res = $this->query($q);
        if ($res === false) {
            return $res;
        }

        $inverseRel = $this->query("SELECT priority FROM object_relations WHERE id={$qObjectId}
                                    AND object_id={$qId} AND switch={$qInverseSwitch}");
        if (empty($inverseRel[0]["object_relations"]["priority"])) {
            // #CUSTOM QUERY
            $inverseRel = $this->query("SELECT MAX(priority)+1 AS priority FROM object_relations WHERE id={$qObjectId} AND switch={$qInverseSwitch}");
            $qInversePriority = (empty($inverseRel[0][0]["priority"])) ? 1 : $inverseRel[0][0]["priority"];
        } else {
            $qInversePriority = $inverseRel[0]["object_relations"]["priority"];
        }

        // #CUSTOM QUERY
        $q= "INSERT INTO object_relations (id, object_id, switch, priority, params) VALUES ({$qObjectId}, {$qId}, {$qInverseSwitch}, {$qInversePriority}, {$qParams})" ;
        $res = $this->query($q);

        if ($res === false) {
            return $res;
        }

        ClassRegistry::init('BEObject')->clearCacheByIds(array($id, $objectId));

        return $res;
    }

    /**
     * delete relation between objects
     *
     * @param int $id
     * @param int $objectId
     * @param string $switch
     * @param bool $bidirectional
     * @return bool
     */
    public function deleteRelation($id, $objectId=null, $switch=null, $bidirectional = true) {
        // #CUSTOM QUERY - TODO: use cake, how?? changing table structure (id primary key, object_id, related_object_id, switch, priority)
        list($qId, $qObjectId, $qSwitch) = static::prepareAll($id, $objectId, $switch);
        $clearObjects = array($id);
        $q = "DELETE FROM object_relations WHERE id={$qId}";
        $qReverse = "DELETE FROM object_relations WHERE object_id={$qId}";
        if ($objectId !== null) {
            $q .= " AND object_id={$qObjectId}";
            $qReverse .= " AND id={$qObjectId}";
            $clearObjects[] = $objectId;
        }
        if ($switch !== null) {
            $q .= " AND switch={$qSwitch}";
            $qReverse .= " AND switch={$qSwitch}";
        }
        $res = $this->query($q);
        if ($res === false) {
            return $res;
        }
        if (!$bidirectional) {
            return $res;
        }
        $res = $this->query($qReverse);

        if ($res === false) {
            return $res;
        }

        ClassRegistry::init('BEObject')->clearCacheByIds($clearObjects);

        return $res;
    }

    /**
     * Delete relations
     * If $objectId is defined remove relations between $id and $objectId else remove all relations of $id
     * If $switch is defined remove relation $switch and its inverse
     *
     * @param int $id
     * @param int $objectId
     * @param string $switch
     * @param bool $bidirectional
     * @return bool
     */
    public function deleteRelationAndInverse($id, $objectId = null, $switch = null) {
        // #CUSTOM QUERY - TODO: use cake, how?? changing table structure (id primary key, object_id, related_object_id, switch, priority)
        list($qId, $qObjectId, $qSwitch) = static::prepareAll($id, $objectId, $switch);
        $clearObjects = array($id);
        $q = "DELETE FROM object_relations WHERE id={$qId}";
        $qReverse = "DELETE FROM object_relations WHERE object_id={$qId}";
        if ($objectId !== null) {
            $q .= " AND object_id={$qObjectId}";
            $qReverse .= " AND id={$qObjectId}";
            $clearObjects[] = $objectId;
        }
        if ($switch !== null) {
            $inverseSwitch = $this->inverseOf($switch);
            if (empty($inverseSwitch)) {
                return false;
            }
            list($qInverseSwitch) = static::prepareAll($inverseSwitch);
            $q .= " AND switch={$qSwitch}";
            $qReverse .= " AND switch={$qInverseSwitch}";
        }
        $res = $this->query($q);
        if ($res === false) {
            return $res;
        }

        $res = $this->query($qReverse);
        if ($res === false) {
            return $res;
        }

        ClassRegistry::init('BEObject')->clearCacheByIds($clearObjects);

        return $res;
    }

    /**
     * delete a specific relation to an object
     *
     * @param int $id - object id
     * @param string $switch - relation direct name
     * @param string $inverseSwitch - relation inverse name, null if name ids the same
     * @return bool
     */
    public function deleteObjectRelation($id, $switch, $inverseSwitch = null) {
        if (empty($inverseSwitch)) {
            $inverseSwitch = $switch;
        }
        // #CUSTOM QUERY - TODO: use cake, how??
        list($qId, $qSwitch, $qInverseSwitch) = static::prepareAll($id, $switch, $inverseSwitch);
        $q = "DELETE FROM object_relations WHERE id={$qId} AND switch={$qSwitch}";
        $res = $this->query($q);
        if ($res === false) {
            $this->log('Error executing query: ' . $q, 'error');
            return $res;
        }
        $qReverse = "DELETE FROM object_relations WHERE object_id={$qId} AND switch={$qInverseSwitch}";
        $res = $this->query($qReverse);
        if ($res === false) {
            $this->log('Error executing query: ' . $qReverse, 'error');
            return $res;
        }

        ClassRegistry::init('BEObject')->clearCacheByIds(array($id));

        return $res;
    }

    /**
     * Updates priority of a relation between objects.
     *
     * @param int $id
     * @param int $objectId
     * @param string $switch
     * @param int $priority
     * @return false on failure
     */
    public function updateRelationPriority($id, $objectId, $switch, $priority) {
        return $this->updateRelation($id, $objectId, $switch, compact('priority'));
    }

    /**
     * Updates parameters of a relation between objects.
     *
     * @param int $id
     * @param int $objectId
     * @param string $switch
     * @param array $params
     * @return false on failure
     */
    public function updateRelationParams($id, $objectId, $switch, $params=array()) {
        return $this->updateRelation($id, $objectId, $switch, compact('params'));
    }

    /**
     * Update a relation using an array of fields to update
     * $set can contains 'params' and 'priority'
     * If $set['params'] is defined then params is also updated in the inverse relation
     *
     * @param int $id the main object id
     * @param int $objectId the related object id
     * @param string $switch the relation name
     * @param array $set array of fields to update
     * @return boolean
     */
    public function updateRelation($id, $objectId, $switch, array $set) {
        if (empty($set)) {
            return false;
        }
        $updateData = array();
        if (array_key_exists('params', $set)) {
            list($qParams) = static::prepareAll($set['params']);
            $updateData[] = sprintf('params=%s', $qParams);
        }
        if (array_key_exists('priority', $set)) {
            list($qPriority) = static::prepareAll($set['priority']);
            $updateData[] = sprintf('priority=%s', $qPriority);
        }

        if (empty($updateData)) {
            return false;
        }

        list($qId, $qObjectId, $qSwitch) = static::prepareAll($id, $objectId, $switch);
        $updateData = implode(', ', $updateData);

        $q = "UPDATE object_relations SET {$updateData} WHERE id={$qId} AND object_id={$qObjectId} AND switch={$qSwitch}";
        $result = $this->query($q);

        // update params in inverse relation
        if ($result !== false && array_key_exists('params', $set)) {
            $switchInverse = $this->inverseOf($switch);
            list($qSwitchInverse, $qParams) = static::prepareAll($switchInverse, $set['params']);
            $q = "UPDATE object_relations
                SET params={$qParams}
                WHERE id={$qObjectId} AND object_id={$qId} AND switch={$qSwitchInverse}";
            $result = $this->query($q);
        }

        if ($result === false) {
            return false;
        }

        ClassRegistry::init('BEObject')->clearCacheByIds(array($id, $objectId));

        return $result;
    }

    /**
     * Check object relation existence
     * @param int $id
     * @param int $objectId
     * @param string $switch
     * @return true if relation exists, false otherwise
     */
    public function relationExists($id, $objectId, $switch) {
        list($qId, $qObjectId, $qSwitch) = static::prepareAll($id, $objectId, $switch);
        $actualId = $this->query("SELECT id FROM object_relations WHERE id={$qId}
            AND object_id={$qObjectId} AND switch={$qSwitch}", false);
        if (empty($actualId[0]['object_relations']['id'])) {
            return false;
        }
        return true;
    }

    /**
     * Get current priority for a specific relation
     * @param int $id
     * @param int $objectId
     * @param string $switch
     * @return priority value or false if field is NULL or relation missing
     */
    public function relationPriority($id, $objectId, $switch) {
        list($qId, $qObjectId, $qSwitch) = static::prepareAll($id, $objectId, $switch);
        $pri = $this->query("SELECT * FROM object_relations WHERE id={$qId}
                                    AND object_id={$qObjectId} AND switch={$qSwitch}", false);
        if(empty($pri[0]["object_relations"]["priority"])) {
            return false;
        }
        return $pri[0]["object_relations"]["priority"];
    }

    /**
     * Get current parameters for a specific relation
     * @param int $id
     * @param int $objectId
     * @param string $switch
     * @param bool $assoc true to return an associative array, false to return an object (default: true)
     * @return parameters as an associative array or object, or false if field is NULL or relation missing
     */
    public function relationParams($id, $objectId, $switch, $assoc=true) {
        list($qId, $qObjectId, $qSwitch) = static::prepareAll($id, $objectId, $switch);
        $pri = $this->query("SELECT params FROM object_relations WHERE id={$qId}
                                    AND object_id={$qObjectId} AND switch={$qSwitch}", false);
        if(empty($pri[0]["object_relations"]["params"])) {
            return false;
        }
        return json_decode($pri[0]["object_relations"]["params"], $assoc);
    }

    /**
     * Returns array of available relations for an $objectType
     * relations with 'hidden' => true are excluded
     * If 'inverse' relation is defined ('inverse' => 'inverseName'), then types on 'right' side
     * will have 'inverseName' relation
     *
     * array returned is like
     * array('relation_name' => 'relation label', ...)
     *
     * @see self::sortRelations() for $orderBy example
     * @param string|int $objectType Object type name or object type id
     * @param array $orderBy sort relations returned
     * @return array
     */
    public function availableRelations($objectType, array $orderBy = array()) {
        $allRelations = BeLib::getObject('BeConfigure')->mergeAllRelations();
        $availableRelations = array();
        if (is_numeric($objectType)) {
            $objectType = Configure::read('objectTypes.' . $objectType . '.name');
        }
        foreach ($allRelations as $relation => $rule) {
            if (empty($rule['hidden'])) {
                $relLabel = (!empty($rule['label']))? $rule['label'] : $relation;
                // no rule defined
                if (empty($rule[$objectType]) && empty($rule['left']) && empty($rule['right'])) {
                    $availableRelations[$relation] = $relLabel;
                // rule on objectType
                } elseif (!empty($rule[$objectType])) {
                    $availableRelations[$relation] = $relLabel;
                // rule on sideA / sideB
                } else {
                    $addRelation = array();
                    if (array_key_exists('left', $rule)) {
                        if(is_array($rule['left']) && (in_array($objectType, $rule['left']) || (empty($rule['left'])))) {
                            $addRelation[$relation] = $relLabel;
                        } else if($rule['left'] === $objectType) {
                            $addRelation[$relation] = $relLabel;
                        }
                    }
                    if (array_key_exists('right', $rule)) {
                        if (!empty($rule['inverse'])) {
                            $rightRel = $rule['inverse'];
                            $rightRelLabel = (!empty($rule['inverseLabel']))? $rule['inverseLabel'] : $rule['inverse'];
                        } else {
                            $rightRel = $relation;
                            $rightRelLabel = (!empty($rule['label']))? $rule['label'] : $relation;
                        }
                        if(is_array($rule['right']) && (in_array($objectType, $rule['right']) || (empty($rule['right'])))) {
                            $addRelation[$rightRel] = $rightRelLabel;
                        } else if($rule['right'] === $objectType) {
                            $addRelation[$rightRel] = $rightRelLabel;
                        }
                    }
                    $availableRelations= array_merge($availableRelations, $addRelation);
                }
            }
        }

        $availableRelations = array_unique($availableRelations);
        if (!empty($orderBy)) {
            $availableRelations = $this->sortRelations($availableRelations, $orderBy);
        }

        return $availableRelations;
    }

    /**
     * Sort $relations using $orderBy array
     * $relations must be an array with relation name as keys
     * $orderBy must be an array with a list of relation.
     * The order of that list is used to sort the relations.
     *
     * Example
     *
     * ```
     * $relations = array('attach' => array(), 'seealso' => array(), ....)
     * $orderBy = array('seealso')
     * ```
     *
     * return
     *
     * ```
     * array('seealso' => array(), 'attach' => array(), ...)
     * ```
     *
     * @param array $relations
     * @param array $orderBy
     * @return array
     */
    public function sortRelations(array $relations, array $orderBy) {
        // keep in $orderBy only relation available in $relations
        $orderBy = array_intersect_key(
            array_flip($orderBy),
            $relations
        );

        return array_merge($orderBy, $relations);
    }

    /**
     * passed an array of BEdita objects add 'num_of_relations_name' key
     * with the number of each relations passed in options applied to objects
     *
     * @param  array $objects
     * @param  array $options list of options accepted
     *             - relations: array of relation name as array('attach', 'seealso')
     * @return array $objects
     */
    public function countRelations(array $objects, array $options) {
        if (!empty($options['relations'])) {
            foreach ($objects as &$obj) {
                foreach ($options['relations'] as $rel) {
                    $obj['num_of_relations_' . $rel] = $this->find('count', array(
                        'conditions' => array('id' => $obj['id'], 'switch' => $rel)
                    ));
                }
            }
        }
        return $objects;
    }

    /**
     * Check if relation $name is valid for object type $objectType
     * Return true if it's valid, false otherwise
     *
     * @param string $name the relation name (it can be also the inverse name)
     * @param string $objectType the object type name
     * @return boolean
     */
    public function isValid($name, $objectType) {
        $isValid = false;
        $relations = BeLib::getObject('BeConfigure')->mergeAllRelations();
        $objectTypes = Configure::read('objectTypes');
        if (empty($objectTypes[$objectType])) {
            return false;
        }
        $inRelatedGroup = in_array($objectTypes[$objectType]['id'], $objectTypes['related']['id']);
        // direct relation
        if (!empty($relations[$name])) {
            if (!empty($relations[$name]['inverse'])) {
                $isValid = (empty($relations[$name]['left']) && $inRelatedGroup) || in_array($objectType, $relations[$name]['left']);
            } else {
                $isValidLeft = (empty($relations[$name]['left']) && $inRelatedGroup) || in_array($objectType, $relations[$name]['left']);
                $isValidRight = (empty($relations[$name]['right']) && $inRelatedGroup) || in_array($objectType, $relations[$name]['right']);
                $isValid = $isValidLeft || $isValidRight;
            }
        } else {
            // check if $name is the inverse
            $inverseFound = array();
            foreach ($relations as $relName => $relData) {
                if (!empty($relData['inverse']) && $relData['inverse'] == $name) {
                    $inverseFound = $relData;
                    break;
                }
            }
            if (empty($inverseFound)) {
                $isValid = false;
            } else {
                $isValid = (empty($inverseFound['right']) && $inRelatedGroup) || in_array($objectType, $inverseFound['right']);
            }
        }

        return $isValid;
    }

    /**
     * Return the inverse name of relation named $name
     * Passing an array of relation ($relation) it searches inside that else search in all relations.
     *
     * @param string $name a direct or inverse relation name
     * @param array $relations an array of relations on which search
     * @return string|false
     */
    public function inverseOf($name, $relations = array()) {
        if (empty($relations)) {
            $relations = BeLib::getObject('BeConfigure')->mergeAllRelations();
        }
        $inverse = $name;
        if (!empty($relations[$name])) {
            $inverse = !empty($relations[$name]['inverse']) ? $relations[$name]['inverse'] : $name;
        } else {
            foreach ($relations as $directName => $relData) {
                if (!empty($relData['inverse']) && $relData['inverse'] == $name) {
                    $inverse = $directName;
                    break;
                }
            }
        }
        return $inverse;
    }

}
