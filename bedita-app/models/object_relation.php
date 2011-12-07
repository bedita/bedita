<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
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
}
?>