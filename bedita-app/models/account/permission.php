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
*/
class Permission extends BEAppModel
{
	var $name = 'Permission';
	
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
	 * Aggiunge e/o modifica un permesso per un oggetto.
	 *
	 * @param integer $id		ID dell'oggetto trattato
	 * @param string $name		userid o nome gruppo
	 * @param string $switch	user(/group
	 * @param integer $flag		bit dei permessi da settare
	 */
	function replace($id, $name, $switch, $flag) {
		return $this->execute("CALL replacePermission({$id}, '{$name}', '{$switch}', {$flag})") ;
	}	
	
	/**
	 * Cancella  un permesso per un oggetto.
	 *
	 * @param integer $id		ID dell'oggetto trattato
	 * @param string $name		userid o nome gruppo
	 * @param string $switch	user(/group
	 */
	function remove($id, $name, $switch) {
		return $this->execute("CALL deletePermission({$id}, '{$name}', '{$switch}')") ;
	}	

	/**
	 * Cancella tutti i permessi di un oggetto.
	 *
	 * @param integer $id		ID dell'oggetto trattato
	 */
	function removeAll($id) {
		return $this->execute("DELETE FROM permissions WHERE object_id = {$id}") ;
	}	

	/**
	 * Aggiunge e/o modifica un permesso per gli oggetti di una ramificazione nell'albero.
	 *
	 * @param integer $id		ID dell'oggetto root
	 * @param string $name		userid o nome gruppo
	 * @param string $switch	user(/group
	 * @param integer $flag		bit dei permessi da settare
	 */
	function replaceTree($id, $name, $switch, $flag) {
		return $this->execute("CALL replacePermissionTree({$id}, '{$name}', '{$switch}', {$flag})") ;
	}	

	/**
	 * Cancella  un permesso per gli oggetti di una ramificazione nell'albero.
	 *
	 * @param integer $id		ID dell'oggetto root
	 * @param string $name		userid o nome gruppo
	 * @param string $switch	user(/group
	 */
	function removeTree($id, $name, $switch) {
		return $this->execute("CALL deletePermissionTree({$id}, '{$name}', '{$switch}')") ;
	}	

	/**
	 * Cancella tutti i permessi per gli oggetti di una ramificazione nell'albero.
	 *
	 * @param integer $id		ID dell'oggetto root
	 */
	function removeAllTree($id) {
		return $this->execute("CALL deleteAllPermissionTree({$id})") ;
	}	
	
	/**
	 * Torna un intero superiore a 0 se l'utente ha i permessi richiesti
	 * su un dato oggetto.
	 *
	 * @param string $userid
	 * @param integer $object_id
	 * @param integer $operations
	 * @return integer
	 */
	function permsByUserid($userid, $object_id, $operations) {
		$ret =  $this->execute("SELECT prmsUserByID('{$userid}', {$object_id}, {$operations}) AS perms") ;
		
		return $ret[0][0]['perms'] ;
	}

	/**
	 * Torna un intero superiore a 0 se il gruppo ha i permessi richiesti
	 * su un dato oggetto.
	 *
	 * @param string $groupid
	 * @param integer $object_id
	 * @param integer $operations
	 * @return integer
	 */
	function permsByGroup($groupid, $object_id, $operations) {
		$ret =  $this->execute("SELECT prmsGroupByName('{$groupid}', {$object_id}, {$operations}) AS perms") ;
		
		return $ret[0][0]['perms'] ;
	}

	/**
	 * Clona i permessi di un oggetto.
	 *
	 * @param integer $id		ID dell'oggetto da clonare
	 * @param integer $idnew	ID dell'oggetto che assume i permessi
	 */
	function clonePermissions($id, $idnew) {
		return $this->execute("CALL clonePermission({$id}, '{$idnew}')") ;
	}	
}
?>
