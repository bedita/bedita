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

/**
 * Object relations model
 * 
 */
class ObjectRelation extends BEAppModel
{

    public function afterFind($results) {
        if (!empty($results[0]['RelatedObject'])) {
            foreach ($results as &$r) {
                if (!empty($r['RelatedObject']['params'])) {
                    $params = json_decode($r['RelatedObject']['params'], true);
                    if(!empty($params) && is_array($params)) {
                        $r['RelatedObject']['params'] = $params;
                    } else {
                        unset($r['RelatedObject']['params']);
                    }
                }
            }
        }
        return $results;
    }
    
    /**
	 * Create relation between objects
	 *
	 * TODO: sql query, not working with cake ->save() .. why??
	 *
	 * cake->save() doesn't work beacuse of table structure. It should be id primary key, object_id, related_object_id, switch, priority)
	 * 
	 * @param int $id
	 * @param int $objectId
	 * @param string $switch
	 * @param int $priority
	 * @return unknown, $this->query() output - false on error
	 */
	public function createRelation($id, $objectId, $switch, $priority, $bidirectional = true, $params = array()) {
		// #CUSTOM QUERY - TODO: use cake, how??
		$jParams = json_encode($params);
		$q = "INSERT INTO object_relations (id, object_id, switch, priority, params) VALUES ({$id}, {$objectId}, '{$switch}', {$priority}, '{$jParams}')";
		$res = $this->query($q);
		if($res === false) {
			return $res;
		}
		if(!$bidirectional) {
			return $res;
		}
		$q = "INSERT INTO object_relations (id, object_id, switch, priority, params) VALUES ({$objectId}, {$id}, '{$switch}', {$priority}, '{$jParams}')";
		return $this->query($q);
	}

	/**
	 * Create direct and inverse relation using $switch and $inverseSwitch names
	 * @param int $id, left relation element id
	 * @param int $objectId, right relation element id
	 * @param string $switch, direct name
	 * @param int $priority
	 * @param string $inverseSwitch, inverse name
	 */
	public function createRelationAndInverse($id, $objectId, $switch, $inverseSwitch = null, $priority = null, $params = array()) {

		if($priority == null) {
			$rel = $this->query("SELECT MAX(priority)+1 AS priority FROM object_relations WHERE id={$id} AND switch='{$switch}'");
			$priority = (empty($rel[0][0]["priority"]))? 1 : $rel[0][0]["priority"];
		}
		// #CUSTOM QUERY 
		$jParams = json_encode($params);
		$q = "INSERT INTO object_relations (id, object_id, switch, priority, params) VALUES ({$id}, {$objectId}, '{$switch}', {$priority}, '{$jParams}')";
		$res = $this->query($q);
		if($res === false) {
			return $res;
		}

		if($inverseSwitch == null) {
			$inverseSwitch = $switch;
		}

		$inverseRel = $this->query("SELECT priority FROM object_relations WHERE id={$objectId}
									AND object_id={$id} AND switch='{$inverseSwitch}'");
							
		if (empty($inverseRel[0]["object_relations"]["priority"])) {
			// #CUSTOM QUERY
			$inverseRel = $this->query("SELECT MAX(priority)+1 AS priority FROM object_relations WHERE id={$objectId} AND switch='{$inverseSwitch}'");
			$inversePriority = (empty($inverseRel[0][0]["priority"]))? 1 : $inverseRel[0][0]["priority"];
		} else {
			$inversePriority = $inverseRel[0]["object_relations"]["priority"];
		}						
		// #CUSTOM QUERY
		$q= "INSERT INTO object_relations (id, object_id, switch, priority, params) VALUES ({$objectId}, {$id}, '{$inverseSwitch}', {$inversePriority}, '{$jParams}')" ;
		return $this->query($q);	
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
		$q = "DELETE FROM object_relations WHERE id={$id}";
		$qReverse = "DELETE FROM object_relations WHERE object_id={$id}";
		if ($objectId !== null) {
			$q .= " AND object_id={$objectId}";
			$qReverse .= " AND id={$objectId}";
		}
		if ($switch !== null) {
			$q .= " AND switch='{$switch}'";
			$qReverse .= " AND switch='{$switch}'";
		}
		$res = $this->query($q);
		if ($res === false) {
			return $res;
		}
		if(!$bidirectional) {
			return $res;
		}
		return $this->query($qReverse);
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
        // #CUSTOM QUERY - TODO: use cake, how??
        $q = "DELETE FROM object_relations WHERE id={$id} AND switch='{$switch}'";
        $res = $this->query($q);
        if ($res === false) {
            $this->log('Error executing query: ' . $q, 'error');
            return $res;
        }
        if (empty($inverseSwitch)) {
            $inverseSwitch = $switch;
        }
        $qReverse = "DELETE FROM object_relations WHERE object_id={$id} AND switch='{$inverseSwitch}'";
        $res = $this->query($qReverse);
        if ($res === false) {
            $this->log('Error executing query: ' . $qReverse, 'error');
        }
        return $res;
    }


    public function updateRelationPriority($id, $objectId, $switch, $priority){
        $q = "  UPDATE object_relations
                SET priority={$priority}
                WHERE id={$id} AND object_id={$objectId} AND switch='{$switch}'";
        $res = $this->query($q);
        if ($res === false) {
            return $res;
        }
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
        $jParams = json_encode($params);
        $q = "  UPDATE object_relations
                SET params='{$jParams}'
                WHERE ((id={$id} AND object_id={$objectId}) OR (id={$objectId} AND object_id={$id})) AND switch='{$switch}'";
        $res = $this->query($q);
        if ($res === false) {
            return $res;
        }
    }

    /**
     * Check object relation existence
     * @param int $id
     * @param int $objectId
     * @param string $switch
     * @return true if relation exists, false otherwise
     */
    public function relationExists($id, $objectId, $switch) {
        $actualId = $this->query("SELECT id FROM object_relations WHERE id={$id}
            AND object_id={$objectId} AND switch='{$switch}'");
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
		$pri = $this->query("SELECT priority FROM object_relations WHERE id={$id}
									AND object_id={$objectId} AND switch='{$switch}'");
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
        $pri = $this->query("SELECT params FROM object_relations WHERE id={$id}
                                    AND object_id={$objectId} AND switch='{$switch}'");
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
     * @param string|int $objectType Object type name or object type id
     * @return array
     */
    public function availableRelations($objectType) {
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
        return array_unique($availableRelations);
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

}
