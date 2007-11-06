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

	 var $uses = array('User', 'Group','Module') ;

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
			$this->eventInfo("user ".$this->data['User']['userid']." created");
			
		} else {
			$pass = $this->data['User']['passwd']; 
			if($pass === null || strlen(trim($pass)) < 1 )
				unset($this->data['User']['passwd']);
			$this->BeAuth->updateUser($this->data, $userGroups);
			$this->eventInfo("user ".$this->data['User']['userid']." updated");
		}
	 }
	 
	 function removeUser($userid) {
	 	if(isset($userid)) {
	 		$this->BeAuth->removeUser($userid);
	 		$this->eventInfo("user ".$userid." deleted");
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
		$this->set('modules', $this->allModulesWithFlag());
	 }
	 
	  function viewGroup($id) {
	  	$this->set('groups', $this->Group->findAll());
		$this->set('group', $this->Group->findById($id));
		
		$modules = $this->allModulesWithFlag();
		$permsMod = $this->BePermissionModule->getPermissionModulesForGroup($id);
		foreach ($permsMod as $p) {
			$modId = $p['PermissionModule']['module_id'];
			foreach ($modules as &$mod) {
				if($mod['Module']['id'] === $modId)
					$mod['Module']['flag'] = $p['PermissionModule']['flag'];
			}
		}
		$this->set('modules', $modules);
	  }
	 
	  private function allModulesWithFlag() {
		$modules = $this->Module->findAll();
		foreach ($modules as &$mod) 
			$mod['Module']['flag'] = 0;
	  	return $modules;
	  }
	  
	  function saveGroup() {

	  	$this->Transaction->begin();
	  	$groupId = NULL;
	  	if(!isset($this->data['Group']['id'])) {
			$this->Group->save($this->data);
			$groupId = $this->Group->getLastInsertID();
			$this->eventInfo("group ".$this->data['Group']['name']." created");
		} else {
			$this->Group->save($this->data);
			$groupId = $this->Group->getID();
			$this->eventInfo("group ".$this->data['Group']['name']." update");
		}
	  	if(isset($this->data['Module'])) {
	  		$moduleFlags=array();
	  		foreach ($this->data['Module'] as $key=>$val) {
	  			$flag = 0;
				foreach ($val as $flagVal) 
					$flag += $flagVal;
				$moduleFlags[$key]=$flagVal;
	  		}
	  		$this->BePermissionModule->updateGroupPermission($groupId, $moduleFlags);
	  	}
	  	$this->Transaction->end();
	  }
	  
	  function removeGroup($id) {
	  	$groupName="";
	  	$g = $this->Group->findById($id);
	  	if(isset($g)) {
	  		$groupName=$g['Group']['name'];
	  	}
	  	$this->BeAuth->removeGroup($groupName);
		$this->eventInfo("group ".$groupName." deleted");
	  }

	  /**
	 * show system Info
	 */
	 public function systemInfo() { 	
		$this->set('events', $this->EventLog->findAll(NULL, NULL, 'created DESC'));
	 }
	 
	 protected function forward($action, $esito) {
	 	 	$REDIRECT = array(
				"viewGroup" => 	array(
 								"OK"	=> self::VIEW_FWD.'groups',
	 							"ERROR"	=> self::VIEW_FWD.'groups'
	 						),
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

