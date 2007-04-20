<?php
/**
 *
 * PHP versions 4 
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * @filesource
 * @copyright		Copyright (c) 2006
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license			
*/
class User extends BEAppModel
{
	var $name = 'User';

	var $validate = array(
       'username'  => VALID_NOT_EMPTY, 
   	); 

    var $hasAndBelongsToMany = array('Module' =>
                               array('className'    => 'Module',
                                     'uniq'         => true,
									 'fields'		=> 'id'
                               )
                               );

	function view(&$user, $id) {
		$this->id 	= $id;
		if( !($user = $this->read()) ) return false ;
		
		for($i=0; $i < count($user["Module"]) ; $i++) {
			$user["Module"][$i] = $user["Module"][$i]["id"] ;
		}
		
		return true ;
	}
}
?>
