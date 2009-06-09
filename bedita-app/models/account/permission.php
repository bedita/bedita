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
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class Permission extends BEAppModel
{
	
	var $belongsTo = array(
		'User' =>
			array(
				'className'		=> 'User',
				'conditions'	=> "Permission.switch = 'user' ",
				'foreignKey'	=> 'ugid'
			),
		'Group' =>
			array(
				'className'		=> 'Group',
				'conditions'	=> "Permission.switch = 'group' ",
				'foreignKey'	=> 'ugid'
			),
	);

	/**
	 * Add object permissions
	 *
	 * @param integer $objId	object id
	 * @param array $perms		array like (array("flag"=>1, "switch" => "group", "name" => "guest"), array(...))
	 */
	public function add($objectId, $perms) {
		foreach ($perms as $d) {
			$d["object_id"] = $objectId;
			$this->create();
			if($d["switch"] == "group") {
				$group = ClassRegistry::init("Group");
				$d["ugid"] = $group->field('id', array('name'=>$d["name"]));
			} else {
				$user = ClassRegistry::init("User");
				$d["ugid"] = $user->field('id', array('userid'=>$d["name"]));
			}
			if(!$this->save($d)) {
				throw new BeditaException(__("Error saving permissions", true), 
					"obj: $objectId - permissions: ". var_export($perms, true));
				;
			}
		}
	}
	
	/**
	 * Remove all object permissions.
	 *
	 * @param integer $objectId		object ID
	 */
	public function removeAll($objectId) {
		if(!$this->deleteAll(array("object_id" => $objectId), false))
			throw new BeditaException(__("Error removing permissions", true), "object id: $objectId");
	}	
	
	/**
	 * Updates/replaces object permissions
	 *
	 * @param integer $objId	object id
	 * @param array $perms		array like (array("flag"=>1, "switch" => "group", "name" => "guest"), array(...))
	 */
	public function replace($objectId, $perms) {
		$this->removeAll($objectId);
		$this->add($objectId, $perms);
	}	
	
	/**
	 * Is the current object in POST writable by user??
	 *
	 * @param integer $objectId
	 * @param array $userData      user data, like array("id" => .., "userid" => ..., "groups" => array("administrator", "frontend",...))
	 * @return boolean
	 */
	public function isWritable($objectId, array &$userData) {
		// administrator can always write....
		if(!empty($userData['groups']) && in_array("administrator",$userData['groups'])) {
			return true;		
		}
		$perms = $this->find('all', array("conditions" => 
			array("object_id" => $objectId, "flag" => OBJ_PERMS_WRITE)));
		if(empty($perms))
			return true;

		$res = false;
		foreach ($perms as $p) {
			if(!empty($p['User']['id']) && $userData['id'] == $p['User']['id']) {
				return true;
			}
			if(!empty($p['Group']['name']) && in_array($p['Group']['name'], $userData['groups'])) {
				return true;
			}
		}
		return $res;
	}
	
	/**
	 * Cancella  un permesso per un oggetto.
	 *
	 * @param integer $id		ID dell'oggetto trattato
	 * @param array $perms		array like (array("flag"=>1, "switch" => "group", "name" => "guest"), array(...))
	 */
	public function remove($objectId, $perms) {

		foreach ($perms as $p) {
			$conditions = array("object_id" => $objectId, "switch" => $p["switch"]);
			if (isset($p["flag"])) {
				$conditions["flag"] = $p["flag"];
			}
			if($p["switch"] == "group") {
				$group = ClassRegistry::init("Group");
				$conditions["ugid"] = $group->field('id', array('name' => $p["name"]));
			} else {
				$user = ClassRegistry::init("User");
				$conditions["ugid"] = $user->field('id', array('userid' => $p["name"]));
			}
			if(!$this->deleteAll($conditions, false))
				throw new BeditaException(__("Error removing permissions", true), "object id: $objectId");
		}
	}	


	/**
	 * Load all object permissions
	 *
	 * @param integer $objectId
	 * @return array (permissions)
	 */
	public function load($objectId) {
		return $this->find('all', array("conditions" => array("object_id" => $objectId)));
	}

}
?>
