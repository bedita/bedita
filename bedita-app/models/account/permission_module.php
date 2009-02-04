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
	 * Aggiunge e/o modifica un permesso per un modulo.
	 *
	 * @param string $module	nome del modulo
	 * @param string $name		userid o nome gruppo
	 * @param string $switch	user(/group
	 * @param integer $flag		bit dei permessi da settare
	 */
	function replace($module, $name, $switch, $flag) {
		return $this->query("CALL replacePermissionModule('{$module}', '{$name}', '{$switch}', {$flag})") ;
	}	
	
	/**
	 * Cancella  un permesso per un modulo.
	 *
	 * @param string $module	nome del modulo
	 * @param string $name		userid o nome gruppo
	 * @param string $switch	user(/group
	 */
	function remove($module, $name, $switch) {
		return $this->query("CALL deletePermissionModule('{$module}', '{$name}', '{$switch}')") ;
	}	

	/**
	 * Cancella tutti i permessi di un modulo.
	 *
	 * @param string $module	nome del modulo
	 */
	function removeAll($module) {
		return $this->query("DELETE FROM permission_modules WHERE module_id = (SELECT id FROM modules WHERE name = '{$module}')") ;
	}	

	/**
	 * Torna un intero superiore a 0 se l'utente ha i permessi richiesti
	 * su un dato oggetto.
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
	 * Torna un intero superiore a 0 se il gruppo ha i permessi richiesti
	 * su un dato oggetto.
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
