<?php
/**
 *
 * @copyright	bedita
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license			
 * @author 			s.rosanelli@channelweb.it
 */

/**
 * Administration: users, groups, eventlogs....
 * 
 */
class AdminController extends AppController {
	var $name = 'Admin';

	var $helpers 	= array();
	var $components = array();

	 var $uses = array('User', 'Group') ;

	/**
	 * show users
	 */
	 function index() { 	
		$users = $this->User->findAll() ;
		$this->set('users', 		$users);
	 }

	/**
	 * show groups
	 */
	 function groups() { 	
		$groups = $this->Group->findAll() ;
		$this->set('groups', 	$groups);
	 }
	 
	/**
	 * show events
	 */
	 function events() { 	
		$this->set('events', array());
	 }
	 
	 function _REDIRECT($action, $esito) {
	 	$REDIRECT = array() ;
	 	
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	
	 	return false;
	 }
	 
}

	