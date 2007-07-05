<?php
/**
 *
 * PHP versions 5
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * @filesource
 * @copyright		Copyright (c) 2007
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license
 * @author 		giangi giangi@qwerg.com
 * 
 * Gestione dei permessi sui moduli presenti in BEdita			
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
		return $this->execute("CALL replacePermissionModule('{$module}', '{$name}', '{$switch}', {$flag})") ;
	}	
	
	/**
	 * Cancella  un permesso per un modulo.
	 *
	 * @param string $module	nome del modulo
	 * @param string $name		userid o nome gruppo
	 * @param string $switch	user(/group
	 */
	function remove($module, $name, $switch) {
		return $this->execute("CALL deletePermissionModule('{$module}', '{$name}', '{$switch}')") ;
	}	

	/**
	 * Cancella tutti i permessi di un modulo.
	 *
	 * @param string $module	nome del modulo
	 */
	function removeAll($module) {
		return $this->execute("DELETE FROM permission_modules WHERE module_id = (SELECT id FROM modules WHERE label = '{$module}')") ;
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
		$ret =  $this->execute("SELECT prmsModuleUserByID('{$userid}', '{$module}', {$operations}) AS perms") ;
		
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
		$ret =  $this->execute("SELECT prmsModuleGroupByName('{$groupid}', '{$module}', {$operations}) AS perms") ;
		
		return $ret[0][0]['perms'] ;
	}
	
}
?>
