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
	 * remove old permissions on $groupId and add new $perms
	 *
	 * @param  int $groupId
	 * @param  array $perms array like (array("flag"=> 1, "object_id"), array(...))
	 */
	public function replaceGroupPerms($groupId, array $perms) {
		// remove all group permissions
		if(!$this->deleteAll(array('ugid' => $groupId, 'switch' => 'group'), false)) {
			throw new BeditaException(__("Error removing permissions for group", true) . " $groupId");
		}
		foreach ($perms as $p) {
			$p['ugid'] = $groupId;
			$p['switch'] = 'group';
			$this->create();
			if (!$this->save($p)) {
				throw new BeditaException(__("Error saving permissions for group", true), array($p));
			}
		}
	}
	
	/**
	 * Is object ($objectId) writable by user?
	 *
	 * @param integer $objectId
	 * @param array $userData user data, like array("id" => .., "userid" => ..., "groups" => array("administrator", "frontend",...))
	 * @param $perms permission array defined like in checkPermissionByUser() call
	 * 				 if it's defined use it else get permission by $objectId
	 * @return boolean, true if it's writable
	 */
	public function isWritable($objectId, array &$userData, $perms = array()) {
		// administrator can always write....
		if(!empty($userData['groups']) && in_array("administrator",$userData['groups'])) {
			return true;		
		}
		if (empty($perms)) {
			$perms = $this->isPermissionSet($objectId, Configure::read("objectPermissions.write"));
		}
		return $this->checkPermissionByUser($perms, $userData);
	}

	/**
	 * Is object ($objectId) forbidden to user?
	 * Backend only (check backend_private permission)
	 *
	 * @param integer $objectId
	 * @param array $userData user data, like array("id" => .., "userid" => ..., "groups" => array("administrator", "frontend",...))
	 * @return boolean, true if it's forbidden false if it's allowed
	 */
	public function isForbidden($objectId, array &$userData) {
		// no private objects for administrator
		if (!BACKEND_APP || ( !empty($userData['groups']) && in_array("administrator", $userData['groups'])) ) {
			return false;
		}

		$forbidden = false;
		$privatePermission = Configure::read("objectPermissions.backend_private");

		// check perms on main object ($objectId)
		$perms = $this->isPermissionSet($objectId, $privatePermission);
		$forbidden = !$this->checkPermissionByUser($perms, $userData);
		if ($forbidden) {
			return true;
		}

		// check if some branch parent is allowed, if so object is not forbidden
		$parentsPath = ClassRegistry::init('Tree')->find('list', array(
			'fields' => array('parent_path'),
			'conditions' => array('id' => $objectId)
		));

		if (!empty($parentsPath)) {
			foreach ($parentsPath as $path) {
				$path = trim($path, '/');
				$pathArr = explode('/', $path);
				$branchAllowed = array();
				foreach ($pathArr as $parentId) {
					$perms = $this->isPermissionSet($parentId, $privatePermission);
					$branchAllowed[] = $this->checkPermissionByUser($perms, $userData);
				}

				if (!in_array(false, $branchAllowed)) {
					$forbidden = false;
					break;
				} else {
					$forbidden = true;
				}
			}
		}

		return $forbidden;
	}

	/**
	 * Is object ($objectId) accessible by user in frontend?
	 * 
	 * @param $objectId
	 * @param $userData  user data, like array("id" => .., "userid" => ..., "groups" => array("administrator", "frontend",...))
	 * @param $perms permission array defined like in checkPermissionByUser() call
	 * 				 if it's defined use it else get permission by $objectId
	 * @return boolean, true if it's accessible
	 */
	public function isAccessibleByFrontend($objectId, array &$userData, $perms = array()) {
		if (empty($perms)) {
			$perms = $this->isPermissionSet($objectId, array(
				Configure::read("objectPermissions.frontend_access_with_block"),
				Configure::read("objectPermissions.frontend_access_without_block")
			));
		}
		return $this->checkPermissionByUser($perms, $userData);
	}
	
	public function frontendAccess($objectId, array &$userData) {
		if(empty($userData)) { // not logged
			$perms = $this->isPermissionSet($objectId, array(
				Configure::read("objectPermissions.frontend_access_with_block")
			));
			if(!empty($perms)) { // A) access denied => object has at least one perm 'frontend_access_with_block'
				return "denied";
			}
			$perms = $this->isPermissionSet($objectId, array(
				Configure::read("objectPermissions.frontend_access_without_block")
			));
			if(!empty($perms)) { // B) partial access => object has at least one perm 'frontend_access_without_block'
				return "partial";
			}
			// C) full access => no perms on object
			return "full";
		}
		// logged
		$perms = $this->isPermissionSet($objectId, array(
			Configure::read("objectPermissions.frontend_access_with_block"),
			Configure::read("objectPermissions.frontend_access_without_block")
		));
		// A) full access => empty perms or one perm for user group
		if(empty($perms) || $this->checkPermissionByUser($perms,$userData)) {
		    return "full";
		}
		$perms = $this->isPermissionSet($objectId, array(
			Configure::read("objectPermissions.frontend_access_with_block")
		));
		// B) access denied => perms for groups [others than user's groups], at least one frontend_access_with_block perm
		if(!empty($perms)) {
		    return "denied";
		}
		// C) access partial => perms for groups [others than user's groups], all frontend_access_without_block perm
		return "partial";
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
			if(!empty($p['Group']['name']) && !empty($userData['groups']) && in_array($p['Group']['name'], $userData['groups'])) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * check if a permission over an object is set
	 *
	 * @param integer $objectId
	 * @param array|integer $flag permission
	 * @return array of perms with users and groups or false if no permission is setted
	 */
	public function isPermissionSet($objectId, $flag) {
		if (!is_array($flag)) {
			$flag = array($flag);
		}
		// if frontend app (not staging) and object cache is active
		if (!BACKEND_APP && Configure::read('objectCakeCache') && !Configure::read('staging')) {
			$beObjectCache = BeLib::getObject('BeObjectCache');
			$options = array();
			$perms = $beObjectCache->read($objectId, $options, 'perms');
			if (!$perms && !is_array($perms)) {
				$perms = $this->find('all', array(
					'conditions' => array('object_id' => $objectId)
				));
				$beObjectCache->write($objectId, $options, $perms, 'perms');
			}
			// search $flag inside $perms
			$result = array();
			if (!empty($perms)) {
				foreach ($perms as $p) {
					if (in_array($p['Permission']['flag'], $flag)) {
						$result[] = $p;
					}
				}
			}
		} else {
			$result = $this->find('all', array(
				'conditions' => array('object_id' => $objectId, 'flag' => $flag)
			));
		}

		$ret = (!empty($result)) ? $result : false;
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

	/**
	 * passed an array of BEdita objects add 'count_permission' key
	 * with the number of permissions applied to objects
	 *
	 * @param  array $objects
	 * @param  array $options
	 *         		- flag: if specified count permission with that flag
	 * @return array $objects with added 'count_permission' key
	 */
	public function countPermissions(array $objects, array $options) {
        $conditions = array(
            'object_id' => Set::classicExtract($objects, '{n}.id'),
        );
        if (isset($options['flag'])) {
            $conditions['flag'] = $options['flag'];
        }
        $res = $this->find('all', array(
            'contain' => array(),
            'fields' => array('COUNT(id) AS count', 'object_id'),
            'conditions' => $conditions,
            'group' => array('object_id'),
        ));
        $res = Set::combine($res, '{n}.Permission.object_id', '{n}.0.count');
        foreach ($objects as &$obj) {
            $obj['num_of_permission'] = @$res[$obj['id']] ?: 0;
        }
        return $objects;
	}
}