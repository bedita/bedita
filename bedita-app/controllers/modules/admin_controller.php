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
	 protected $moduleName = 'admin';
	 
	/**
	 * show users
	 */
	 function index() { 	
	 	
		$users = $this->User->findAll() ;
		$this->set('users', 		$users);
	}

	 
	 function saveUser() {

	 	$this->checkWriteModulePermission();

	 	$userGroups=array();
		if(isset($this->data['groups'] )) {
	 		foreach ($this->data['groups'] as $k=>$v)
				array_push($userGroups, $k);
		}

		if(!isset($this->data['User']['id'])) {
			$this->BeAuth->createUser($this->data, $userGroups);
			$this->eventInfo("user ".$this->data['User']['userid']." created");
			$this->userInfoMessage(__("User created",true));
		} else {
			$pass = $this->data['User']['passwd']; 
			if($pass === null || strlen(trim($pass)) < 1 )
				unset($this->data['User']['passwd']);
			$this->BeAuth->updateUser($this->data, $userGroups);
			$this->eventInfo("user ".$this->data['User']['userid']." updated");
			$this->userInfoMessage(__("User updated",true));
		}
	 }
	 
	 function removeUser($userid) {
	 	$this->checkWriteModulePermission();
	 	if(isset($userid)) {
	 		$this->BeAuth->removeUser($userid);
	 		$this->eventInfo("user ".$userid." deleted");
	 	}
	  }

	  function viewUser($id=NULL) {
	  	
	 	if(isset($id)) {
	 		$user = $this->User->findById($id) ;
		  	if(empty($user))
		  		throw new BeditaException(__("Bad data",true));
	 		$userModules = $this->BePermissionModule->getListModules($user['User']['userid']);
	 		
		} else {
			$user = NULL;
			$userModules = NULL;
		}

		$allGroups = $this->Group->findAll();
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
		$this->set('userModules', $userModules) ;
	 }

	private function loadGroups() {
		$config =& Configure::getInstance();
		$groups = $this->Group->findAll() ;
		foreach ($groups as &$g) {
			$immutable=false;
			if (in_array($g['Group']['name'], $config->basicGroups))
				$immutable=true;
			$g['Group']['immutable'] = $immutable;
		}
	  	return $groups;
	}
	 
	/**
	 * show groups
	 */
	 function groups() { 	
		$this->set('groups', $this->loadGroups());
		$this->set('group',  NULL);
		$this->set('modules', $this->allModulesWithFlag());
	 }
	 
	  function viewGroup($id = null) {
		$this->set('groups', $this->loadGroups());
	  	$g = $this->Group->findById($id);
	  	if(empty($g))
	  		throw new BeditaException(__("Bad data",true));
	  	foreach($g['User'] as &$user) {
	  		$u = $this->User->findById($user['id']);
	  		$user['userid'] = $u['User']['userid'];
	  	}
		$this->set('group', $g);
		
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
	 	$this->checkWriteModulePermission();

	  	$this->Transaction->begin();
	  	$newGroup = false;
		$groupId = $this->BeAuth->saveGroup($this->data);
	  	if(!isset($this->data['Group']['id'])) {
			$this->eventInfo("group ".$this->data['Group']['name']." created");
	  		$newGroup = true;
	  	} else {
	  		$this->eventInfo("group ".$this->data['Group']['name']." update");
		}
		if(isset($this->data['ModuleFlags'])) {
	  		$this->BePermissionModule->updateGroupPermission($groupId, $this->data['ModuleFlags']);
	  	}
	  	
	  	$this->userInfoMessage(__("Group ".($newGroup? "created":"updated"),true));
	  	$this->Transaction->commit();
	  }
	  
	  function removeGroup($id) {
	 	$this->checkWriteModulePermission();
	  	$groupName="";
	  	$g = $this->Group->findById($id);
	  	if(isset($g)) {
	  		$groupName=$g['Group']['name'];
	  	}
	  	$this->BeAuth->removeGroup($groupName);
		$this->eventInfo("group ".$groupName." deleted");
		$this->userInfoMessage(__("Group deleted",true));
	  }

	  /**
	 * show system Info
	 */
	 public function systemInfo() { 	
		$this->set('events', $this->EventLog->findAll(NULL, NULL, 'created DESC'));
	 }

	 public function deleteEventLog() { 	
	 	$this->checkWriteModulePermission();
		$this->EventLog->deleteAll("id > 0");
		$this->set('events', array());
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
				"deleteEventLog" => 	array(
 								"OK"	=> self::VIEW_FWD.'systemInfo',
	 							"ERROR"	=> self::VIEW_FWD.'systemInfo'
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

