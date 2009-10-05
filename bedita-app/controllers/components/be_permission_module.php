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
 * Object permits management
 * 
 * Permits are defined by a bit representation (bedita.ini.php):
 * BEDITA_PERMS_READ	0x1
 * BEDITA_PERMS_MODIFY	0x2
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BePermissionModuleComponent extends Object {
	const SWITCH_USER		= 'user' ;
	const SWITCH_GROUP	   = 'group' ;
	
	var $controller			= null ;
	var $Permission 		= null ;
	
	var $PermissionModule	= null ;
	private $groupModel	= null ;
	
	function __construct() {
		if(!class_exists('PermissionModule')) 	
			App::import('Model', 'PermissionModule');
		if(!class_exists('Group'))
			App::import('Model', 'Group');
		
		$this->PermissionModule = new PermissionModule() ;
		$this->groupModel = new Group() ;
		parent::__construct() ;
		
	} 
	
	
	/**
	 * Get list of modules available for user
	 *
	 * @param string $userid	utente che vuole accedere
	 * @param boolean $all		se false solo  i moduli a cui ha accesso (BEDITA_PERMS_READ|BEDITA_PERMS_MODIFY)
	 * 							e status = 'on'
	 * @return array|false
	 */
	function getListModules($userid, $all = false) {
		
		$condition 	=  "prmsModuleUserByID('{$userid}', Module.path, " . (BEDITA_PERMS_READ|BEDITA_PERMS_MODIFY) . ")" ;
		$sql 		=  "SELECT *, {$condition} as flag FROM modules AS Module WHERE prmsModuleUserByID('{$userid}', Module.path, " . (BEDITA_PERMS_READ|BEDITA_PERMS_MODIFY) . ") ORDER BY priority IS NULL, priority" ;
		$modules 	= $this->PermissionModule->query($sql);
		
		$resModules = array();
		for ($i=0; $i < count($modules) ; $i++) {
            $modules[$i]  = $this->PermissionModule->am($modules[$i]);
            $resModules[$modules[$i]['name']] = $modules[$i];
		}
		return $resModules ;
	}
	
	public function getPermissionModulesForGroup($groupId) {
		$conditions=array("ugid"=>$groupId, "switch"=>self::SWITCH_GROUP);
		return $this->PermissionModule->findAll($conditions);
	}

/**
 * change module permissions for a group
 *
 * @param $groupId
 * @param $moduleFlags array ('module' => flag,....)
 */
	public function updateGroupPermission($groupId, $moduleFlags) {
		$conditions=array("ugid"=>$groupId, "switch"=>self::SWITCH_GROUP);
		$this->PermissionModule->deleteAll($conditions);
	  	
		$g = $this->groupModel->findById($groupId);
		$groupName = $g['Group']['name'];
		foreach ($moduleFlags as $mod=>$flag) {
			if(!empty($flag)) {
				$perms =  array(array($groupName, self::SWITCH_GROUP, $flag));
				$this->add($mod, $perms);
			}
		}
	}
	
	/**
	 * @param object $controller
	 */
	function startup(&$controller)
	{
		$this->controller 	= $controller;
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
		$this->array2perms($perms, $formatedPerms) ;
		
		if(!is_array($names)) $names = array($names); 
		foreach ($names as $name) {
			
			for($i=0; $i < count($formatedPerms) ; $i++) {
				$item = &$formatedPerms[$i] ;
				
				if($this->PermissionModule->replace($name, $item['name'], $item['switch'], $item['flag']) === false) {
					return false ;
				}				
			}
		}
		
		return true ;
	}
	
	/**
	 * Remove 1 or more permits to 1 or more modules.
	 * 
	 *
	 * @param mixed $names	If string, name of module
	 * 						if array, {0..N} names of modules
	 * @param array $perms	{1..N} items:
	 * 						name, switch, flag
	 * 							name	userid or name of group
	 * 							switch  PermissionComponent::SWITCH_USER or PermissionComponent::SWITCH_GROUP
	 * @return boolean
	 */
	function remove($names, $perms) {
		$this->array2perms($perms, $arr) ;
		
		if(!is_array($names)) $names = array($names); 
		foreach ($names as $name) {
			
			for($i=0; $i < count($arr) ; $i++) {
				$item = &$arr[$i] ;
				
				if($this->PermissionModule->remove($name, $item['name'], $item['switch']) === false) {
					return false ;
				}				
			}
		}
		
		return true ;
	}
	
	/**
	 * Like remove(), but force removing all permits for modules
	 *
	 * @param mixed $names	If string, name of permit
	 * 						if array, {0..N} names of permits
	 * @param array				{1..N} permits
	 * @return boolean
	 */
	function removeAll($names) {
		if(!is_array($names)) $names = array($names); 
		foreach ($names as $name) {
			if($this->PermissionModule->removeAll($name) === false) {
				return false ;
			}				
		}
		
		return true ;
	}
	
	/**
	 * Load permits for a module $name
	 *
	 * @param string $name	Module
	 * @return array $perms	Permits found or FALSE
	 */
	function load($name) {
		$condition = "Module.name = '{$name}'" ;
		if(($perms = $this->PermissionModule->findAll($condition)) === false) 
			return false ;
		
		$this->perms2arr($perms, $arr) ;
		
		return $arr ;
	}
	
	/**
	 * Verify that operation $op is allowed for user $userid on module $name
	 *
	 * @param string $name	Module
	 * @param string $userid	Userid
	 * @param integer $op		Operation
	 * @return boolean
	 */
	function verify($name, $userid, $op) {
		 return $this->PermissionModule->permsByUserid($userid, $name, $op) ;
	}
	
	/**
	 * Verify that operation $op is allowed for group $groupid on module $name
	 *
	 * @param string $name		Module
	 * @param string $groupid	Group
	 * @param integer $op		Operation
	 * @return boolean
	 */
	function verifyGroup($name, $groupid, $op) {
		 return $this->PermissionModule->permsByGroup($groupid, $name, $op) ;
	}

	/**
	 * Load array of permits $perms, from data in $arr
	 *
	 * @param array $arr	{0..N} item:
	 * 						0:ugid, 1:switch, 2:flag 
	 * @param array $perms	result array:
	 * 						ugid => ; switch => ; flag => 
	 */
	private function array2perms(&$arr, &$perms) {
		$perms = array() ;
		if(!count($arr))  return ;

		foreach ($arr as $item) {
			$perms[] = array(
					'name'		=> $item[0],
					'switch'	=> $item[1],
					'flag'		=> (isset($item[2]))?$item[2]:null,
			) ;
		}
	}
	
	private function perms2arr(&$perms, &$arr) {
		$arr = array() ;
		foreach ($perms as $item) {
			$arr[] = array(
				(($item['PermissionModule']['switch'] == BePermissionModuleComponent::SWITCH_USER)? $item['User']['userid'] : $item['Group']['name']),
				$item['PermissionModule']['switch'],
				$item['PermissionModule']['flag']
			) ;
		}
	}

}

?>