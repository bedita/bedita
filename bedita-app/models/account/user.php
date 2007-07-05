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
class User extends BEAppModel
{
	var $name = 'User';

	var $validate = array(
	'userid'  => VALID_NOT_EMPTY,
	'pasword'  => VALID_NOT_EMPTY,
	);

	var $hasAndBelongsToMany = array(
		'Group' =>
			array(
				'className'		=> 'Group',
				'uniq'			=> true,
				'fields'		=> 'id, name'
			)
	);

	var $hasMany = array(
		'Permission' =>
			array(
				'className'		=> 'Permission',
				'condition'		=> "Permission.switch = 'user' ",
				'fields'		=> 'Permission.object_id, Permission.switch, Permission.flag',
				'foreignKey'	=> 'id',
				'dependent'		=> true
			),
		'ObjectUser' =>
			array(
				'className'		=> 'ObjectUser',
				'condition'		=> "",
				'dependent'		=> true
			)
	);

	
	/**
	 * Viene riformattato il risultato:
	 * 		id => ; passwd => ; realname => ; userid => ; groups => array({1..N} nomi_grupppi)
	 *
	 * @param unknown_type $user
	 */
	function compact(&$user) {
		unset($user['ObjectUser']) ;
		unset($user['Permission']) ;
		
		$user['User']['groups'] = array() ;
		foreach ($user['Group'] as $group) {
			$user['User']['groups'][] = $group['name'] ;
		}
		
		unset($user['Group']) ;
		
		$user = $user['User'] ;
	}
}
?>
