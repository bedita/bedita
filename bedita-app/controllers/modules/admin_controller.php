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
 */
class AdminController extends AppController {
	var $name = 'Admin';

	 var $uses = array('User', 'Group') ;

	/**
	 * show users
	 */
	 function index() { 	
		$users = $this->User->findAll() ;
		$this->set('users', 		$users);
	 }

	 
	 function saveUser() {

	 	$userGroups=array();
		if(isset($this->data['groups'] )) {
	 		foreach ($this->data['groups'] as $k=>$v)
				array_push($userGroups, $k);
		}

		if(!isset($this->data['User']['id'])) {
			$this->BeAuth->createUser($this->data, $userGroups);
		} else {
			if(isset($this->data['User']['passwd-new']))
				$this->data['User']['passwd'] = $this->data['User']['passwd-new'];
			$this->BeAuth->updateUser($this->data, $userGroups);
		}
	 }
	 
	 function removeUser($userid) {
	 	if(isset($userid)) {
	 		$this->BeAuth->removeUser($userid); ;
	 	}
	  }

	  function viewUser($id=NULL) {
	 	if(isset($id)) {
	 		$user = $this->User->findById($id) ;
		} else {
			$user = NULL;
		}

		$allGroups = $this->Group->findAll() ;
		$userGroups = array();
		if(isset($user)) {
			foreach ($user['Group'] as $g) {
				array_push($userGroups, $g['name']);
			}
		}
		$formGroups = array();
		foreach ($allGroups as $g) {
			$isGroup=false;
			if(array_search($g['Group']['name'],$userGroups) !== false)
				$isGroup = true;
			$formGroups[$g['Group']['name']] = $isGroup;
		}
		
		$this->set('user',  $user);
		$this->set('formGroups',  $formGroups);
	 }

	 
	/**
	 * show groups
	 */
	 function groups() { 	
		$groups = $this->Group->findAll() ;
		$this->set('groups', 	$groups);
		$this->set('group',  NULL);
	 }
	 
	  function viewGroup($id) {
		$this->action = 'groups';
	  	$this->set('groups', $this->Group->findAll());
		$this->set('group', $this->Group->findById($id));
	  }
	 
	  function saveGroup() {
		if(!isset($this->data['Group']['id'])) {
			$this->Group->save($this->data);
		} else {
			$this->Group->save($this->data);
		}
	  }
	  
	  function removeGroup($id) {
	  	$this->Group->del($id);
	  }

	  /**
	 * show events
	 */
	 function events() { 	
		$this->set('events', array());
	 }
	 
	 function _REDIRECT($action, $esito) {
	 	$REDIRECT = array(
 				"saveUser" => 	array(
 								"OK"	=> "/admin/index",
	 							"ERROR"	=> "/admin/viewUser" 
	 						),
				"removeUser" => 	array(
	 							"OK"	=> "/admin",
	 							"ERROR"	=> "/admin" 
	 						),
 				"saveGroup" => 	array(
 								"OK"	=> "/admin/groups",
	 							"ERROR"	=> "/admin/groups" 
	 						),
				"removeGroup" => 	array(
 								"OK"	=> "/admin/groups",
	 							"ERROR"	=> "/admin/groups" 
	 						)
	 			);
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	return false;
	 }
	 
}

