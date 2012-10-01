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


App::uses("BEAppModel", "Model");

/**
 *
 *
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
				throw new BeditaException(__("Error saving permissions"),
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
			throw new BeditaException(__("Error removing permissions"), "object id: $objectId");
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
	 * Is object ($objectId) writable by user?
	 *
	 * @param integer $objectId
	 * @param array $userData user data, like array("id" => .., "userid" => ..., "groups" => array("administrator", "frontend",...))
	 * @param $perms permission array defined like in checkPermissionByUser() call
	 * 				 if it's defined use this else get permission by $objectId
	 * @return boolean, true if it's writable
	 */
	public function isWritable($objectId, array &$userData, $perms=array()) {
		// administrator can always write....
		if(!empty($userData['groups']) && in_array("administrator",$userData['groups'])) {
			return true;
		}
		if (empty($perms)) {
			$perms = $this->isPermissionSetted($objectId, Configure::read("objectPermissions.write"));
		}
		return $this->checkPermissionByUser($perms, $userData);
	}

	/**
	 * Is object ($objectId) accessible by user in frontend?
	 *
	 * @param $objectId
	 * @param $userData  user data, like array("id" => .., "userid" => ..., "groups" => array("administrator", "frontend",...))
	 * @param $perms permission array defined like in checkPermissionByUser() call
	 * 				 if it's defined use this else get permission by $objectId
	 * @return boolean, true if it's accessible
	 */
	public function isAccessibleByFrontend($objectId, array &$userData, $perms=array()) {
		if (empty($perms)) {
			$perms = $this->isPermissionSetted($objectId, array(
				Configure::read("objectPermissions.frontend_access_with_block"),
				Configure::read("objectPermissions.frontend_access_without_block")
			));
		}
		return $this->checkPermissionByUser($perms, $userData);
	}

	/**
	 * check if user or user groups are in $perms array
	 *
	 * @param $perms permission array like return from find("all)
	 * 						array(
	 * 							0 => array("Permission" => array(...), "User" => array(...), "Group" => array(...)),
	 * 							1 => ....
	 * 						)
	 * @param $userData user data, like array("id" => .., "userid" => ..., "groups" => array("administrator", "frontend",...))
	 * @return boolean (true if user have permission false otherwise)
	 */
	public function checkPermissionByUser($perms=array(), array &$userData) {
		if(empty($perms))
			return true;

		foreach ($perms as $p) {
			if(!empty($p['User']['id']) && $userData['id'] == $p['User']['id']) {
				return true;
			}
			if(!empty($p['Group']['name']) && in_array($p['Group']['name'], $userData['groups'])) {
				return true;
			}
		}
		return false;
	}

	/**
	 * check if a permission over an object is set
	 *
	 * @param $objectId
	 * @param $flag permission
	 * @return array of perms with users and groups or false if no permission is setted
	 */
	public function isPermissionSetted($objectId, $flag) {
		$result = $this->find('all', array(
				"conditions" => array("object_id" => $objectId, "flag" => $flag)
			)
		);

		$ret = (!empty($result))? $result : false;

		return $ret;
	}

	/**
	 * Delete a permit for an object
	 *
	 * @param integer $id		object ID
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
				throw new BeditaException(__("Error removing permissions"), "object id: $objectId");
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