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
}
?>
