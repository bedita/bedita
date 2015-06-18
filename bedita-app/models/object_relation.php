<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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
 * Relation object
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class ObjectRelation extends BEAppModel
{
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
	public function createRelation ($id, $objectId, $switch, $priority, $bidirectional = true) {
		// #CUSTOM QUERY - TODO: use cake, how??
		$q = "INSERT INTO object_relations (id, object_id, switch, priority) VALUES ({$id}, {$objectId}, '{$switch}', {$priority})";
		$res = $this->query($q);
		if($res === false) {
			return $res;
		}
		if(!$bidirectional) {
			return $res;
		}
		$q = "INSERT INTO object_relations (id, object_id, switch, priority) VALUES ({$objectId}, {$id}, '{$switch}', {$priority})";
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
	public function createRelationAndInverse ($id, $objectId, $switch, $inverseSwitch = null, $priority = null) {

		if($priority == null) {
			$rel = $this->query("SELECT MAX(priority)+1 AS priority FROM object_relations WHERE id={$id} AND switch='{$switch}'");
			$priority = (empty($rel[0][0]["priority"]))? 1 : $rel[0][0]["priority"];
		}
		// #CUSTOM QUERY 
		$q = "INSERT INTO object_relations (id, object_id, switch, priority) VALUES ({$id}, {$objectId}, '{$switch}', {$priority})";
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
		$q= "INSERT INTO object_relations (id, object_id, switch, priority) VALUES ({$objectId}, {$id}, '{$inverseSwitch}', {$inversePriority})" ;
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
     * @param string $inverseSwitch - relation inverse name, null if name is the same
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
	
}
?>