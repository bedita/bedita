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
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class PermissionModule extends BEAppModel
{
	var $name 		= 'PermissionModule';

	var $belongsTo = array(
		'Module' =>
			array(
				'className'		=> 'Module',
				'fields' 		=> 'label, path'
			),
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
	 */
	function replace($module, $name, $switch, $flag) {
		return $this->query("CALL replacePermissionModule('{$module}', '{$name}', '{$switch}', {$flag})") ;
	}	
	
	/**
	 * Delete a permit for a module
	 *
	 * @param string $module	module name
	 * @param string $name		userid or group name
	 * @param string $switch	user/group
	 */
	function remove($module, $name, $switch) {
		return $this->query("CALL deletePermissionModule('{$module}', '{$name}', '{$switch}')") ;
	}	

	/**
	 * Delete all permits of a module
	 *
	 * @param string $module	module name
	 */
	function removeAll($module) {
		return $this->query("DELETE FROM permission_modules WHERE module_id = (SELECT id FROM modules WHERE name = '{$module}')") ;
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
		$ret =  $this->query("SELECT prmsModuleUserByID('{$userid}', '{$module}', {$operations}) AS perms") ;
		
		return $ret[0][0]['perms'] ;
	}

	/**
	 * Return an integer greater than 0 if group $groupid has permits on module $module
	 *
	 * @param string $groupid
	 * @param string $module
	 * @param integer $operations
	 * @return integer
	 */
	function permsByGroup($groupid, $module, $operations) {
		$ret =  $this->query("SELECT prmsModuleGroupByName('{$groupid}', '{$module}', {$operations}) AS perms") ;
		
		return $ret[0][0]['perms'] ;
	}
	
}
?>
