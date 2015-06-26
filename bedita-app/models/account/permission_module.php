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
class PermissionModule extends BEAppModel
{
	const SWITCH_USER = 'user';
	const SWITCH_GROUP = 'group';

	var $name = 'PermissionModule';

	var $belongsTo = array(
		'Module',
		'Group' =>
			array(
				'className'		=> 'Group',
				'conditions'	=> "PermissionModule.switch = 'group' ",
				'foreignKey'	=> 'ugid',
				'fields'		=> 'name'
			),
		'User' =>
			array(
				'className'		=> 'User',
				'conditions'	=> "PermissionModule.switch = 'user' ",
				'foreignKey'	=> 'ugid',
				'fields'		=> 'userid'
			),
	);
	/**
	 * Add/modify permits for a module
	 *
	 * @param string $module	module name
	 * @param string $name		userid or group name
	 * @param string $switch	user/group
	 * @param integer $flag		permits bits
	 * @return boolean
	 */
	function replace($module, $name, $switch, $flag) {
		if ($switch == self::SWITCH_USER) {
			$ugid = $this->User->field("id", array("userid" => $name));
		} elseif ($switch == self::SWITCH_GROUP) {
			$ugid = $this->Group->field("id", array("name" => $name));
		}

		if (empty($ugid)) {
			return false;
		}

		$module_id = $this->Module->field("id", array("name" => $module));
		if (empty($module_id)) {
			return false;
		}

		$permModules = $this->find("first", array(
			"conditions" => array(
				"module_id" => $module_id,
				"ugid" => $ugid,
				"switch" => $switch
			),
			"contain" => array()
		));
		
		if (empty($permModules)) {
			$permModules["PermissionModule"]["module_id"] = $module_id;
			$permModules["PermissionModule"]["ugid"] = $ugid;
			$permModules["PermissionModule"]["switch"] = $switch;
		}

		$permModules["PermissionModule"]["flag"] = $flag;

		if (!$this->save($permModules)) {
			return false;
		}

		return true;
	}	
	
	/**
	 * Delete a permit for a module
	 *
	 * @param string $module	module name
	 * @param string $name		userid or group name
	 * @param string $switch	user/group
	 * @return boolean
	 */
	function remove($module, $name, $switch) {
		if ($switch == self::SWITCH_USER) {
			$ugid = $this->User->field("id", array("userid" => $name));
		} elseif ($switch == self::SWITCH_GROUP) {
			$ugid = $this->Group->field("id", array("name" => $name));
		}
		$module_id = $this->Module->field("id", array("name" => $module));
		$result = $this->deleteAll(array(
			"module_id" => $module_id,
			"ugid" => $ugid,
			"switch" => $switch
		));
		return $result;
	}	

	/**
	 * Delete all permits of a module or an array of modules
	 *
	 * @param mixed $names	string (module name) or array of module names
	 * @return boolean
	 */
	function removeAll($modules) {
		if(!is_array($modules)) {
			$modules = array($modules);
		}
		foreach ($modules as $name) {
			if (!$this->deleteAll(array("Module.name" => $name))) {
				return false;
			}
		}
		return true ;
	}

	/**
	 * Return an integer greater than 0, if user $userid has permits on module $module
	 *
	 * @param string $userid
	 * @param string $module
	 * @param integer $operations
	 * @return integer
	 */
	function permsByUserid($userid, $module, $operations) {
		$module_id = $this->Module->field("id", array("name" => $module));

		$user = $this->User->find("first", array(
			"conditions" => array("User.userid" => $userid),
			"contain" => array("Group")
		));

		$groups = array();
		foreach ($user["Group"] as $g) {
			$groups[] = $g["id"];
		}

		$userModulePerms = $this->field("flag", array(
			"module_id" => $module_id,
			"ugid" => $user["User"]["id"],
			"switch" => "user"
		));
		
		if (empty($userModulePerms)) {
			$userModulePerms = 0;
		}
		$userModulePerms = $userModulePerms & $operations;

		$gPerms = array();
		if(!empty($groups)) {
			$gPerms = $this->find("all", array(
				"fields" => "flag",
				"conditions" => array(
					"module_id" => $module_id,
					"ugid" => $groups,
					"switch" => "group"
				),
				"contain" => array()
			));
		}

		$groupModulePerms = 0;
		if (!empty($gPerms)) {
			foreach ($gPerms as $gP) {
				$groupModulePerms = $groupModulePerms | ($gP["PermissionModule"]["flag"] & $operations);
			}
		}

		return $userModulePerms | $groupModulePerms;
	}

	/**
	 * Return an integer greater than 0 if group $groupid has permits on module $module
	 *
	 * @param mixed $groupid Group.id or Group.name
	 * @param string $module
	 * @param integer $operations
	 * @return integer
	 */
	function permsByGroup($groupid, $module, $operations) {
		if (!is_numeric($groupid)) {
			$groupid = $this->Group->field("id", array("name" => $groupid));
		}
		$module_id = $this->Module->field("id", array("name" => $module));
		
		$groupModulePerms = $this->field("flag", array(
			"module_id" => $module_id,
			"ugid" => $groupid,
			"switch" => "group"
		));
		
		if (empty($groupModulePerms)) {
			$groupModulePerms = 0;
		}

		return $groupModulePerms & $operations;
	}

	/**
	 * Get list of modules available for user
	 *
	 * @param string $userid
	 * @return array
	 */
	function getListModules($userid) {
		$user = $this->User->find("first", array(
			"conditions" => array("User.userid" => $userid),
			"contain" => array("Group")
		));

		$groups = array();
		foreach ($user["Group"] as $g) {
			$groups[] = $g["id"];
		}

		$uPerms = $this->find("all", array(
			"conditions" =>	array("ugid" => $user["User"]["id"], "switch" => "user"),
			"fields" => array("module_id", "flag"),
			"contain" => array()
		));

		$gPerms = array();
		if(!empty($groups)) {
			$gPerms = $this->find("all", array(
				"fields" => array("module_id", "flag"),
				"conditions" => array(
					"ugid" => $groups,
					"switch" => "group"
				),
				"contain" => array()
			));
		}

		$perms = array();
		// check groups' permissions
		// restructure $gPerms array to group by module_id
		$gPerms = Set::combine($gPerms, '{n}.PermissionModule.flag', '{n}.PermissionModule', '{n}.PermissionModule.module_id');
		foreach ($gPerms as $idMod => $modulePerms) {
			$moduleP = 0x0;
			// $allmodulePerms = Set::extract('/flag', $gp);
			foreach ($modulePerms as $flag => $p) {
				$moduleP = $moduleP | $flag;
			}
			$perms[$idMod] = $moduleP;
		}

		// check user's permissions
		foreach ($uPerms as $up) {
			$idMod = $up["PermissionModule"]["module_id"];
			$flag = $up["PermissionModule"]["flag"];
			if (!empty($perms[$idMod])) {
				$perms[$idMod] = $perms[$idMod] & $flag;
			} else {
				$perms[$idMod] = $flag;
			}
		}

		$modules = $this->Module->find("all", array(
			"conditions" => array("status" => "on", 'module_type' => array('core', 'plugin')),
			"order" => "priority ASC"
		));

		$resModules = array();
		foreach ($modules as $mod) {
			$idMod = $mod["Module"]["id"];
			if(!empty($perms[$idMod])) {
				$resModules[$mod["Module"]["name"]] = $mod["Module"];
				$resModules[$mod["Module"]["name"]]["flag"] = $perms[$idMod];
			}
		}

		return $resModules;
	}

	public function getPermissionModulesForGroup($groupId) {
		$conditions = array("ugid" => $groupId, "switch" => self::SWITCH_GROUP);
		return $this->find("all", array("conditions" => $conditions, "contain" => array()));
	}

	/**
	* change module permissions for a group
	*
	* @param $groupId
	* @param $moduleFlags array ('moduleName' => flag,....)
	*/
	public function updateGroupPermission($groupId, $moduleFlags) {
		$moduleIds = $this->Module->find('list', array(
			'fields' => array('id'),
			'conditions' => array('name' => array_keys($moduleFlags))
		));
		// replace only module permission flagged by $moduleFlags
		$conditions = array("ugid" => $groupId, "switch" => self::SWITCH_GROUP, "module_id" => $moduleIds);
		$this->deleteAll($conditions);
		$this->Group->contain = array();
		$g = $this->Group->findById($groupId);
		$groupName = $g['Group']['name'];
		foreach ($moduleFlags as $mod=>$flag) {
			if(!empty($flag)) {
				$perms = array(array(
					"name" => $groupName,
					"switch" => self::SWITCH_GROUP,
					"flag" => $flag
				));
				$this->add($mod, $perms);
			}
		}
	}

	/**
	 * Add 1 or more permits to 1 or more modules.
	 *
	 *
	 * @param mixed $names	If string, name of module
	 * 						if array, {0..N} names of modules
	 * @param array $perms	{1..N} items:
	 * 						name, switch, flag
	 * 							name	userid or name of group
	 * 							switch  PermissionComponent::SWITCH_USER or PermissionComponent::SWITCH_GROUP
	 * 							flag	set of bits with the operations defined above
	 * @return boolean
	 */
	function add($names, &$perms) {
		if(!is_array($names)) {
			$names = array($names);
		}

		foreach ($names as $name) {
			foreach ($perms as $item) {
				if (empty($item["flag"])) {
					$item["flag"] = null;
				}
				$this->create();
				if($this->replace($name, $item['name'], $item['switch'], $item['flag']) === false) {
					return false;
				}
			}
		}

		return true;
	}

}
?>