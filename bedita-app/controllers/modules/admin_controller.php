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
 * Administration: users, groups, eventlogs....
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class AdminController extends ModulesController {

	 var $uses = array('User', 'Group','Module') ;
	 var $components = array('BeSystem');
	 var $helpers = array('Paginator');
	 var $paginate = array(
	 	'User' => array('limit' => 20, 'page' => 1, 'order'=>array('created'=>'desc')),
		'Group' => array('limit' => 20, 'page' => 1, 'order'=>array('created'=>'desc')),
	 	'EventLog' => array('limit' => 20, 'page' => 1, 'order'=>array('created'=>'desc'))
	 ); 
	 protected $moduleName = 'admin';
	 
	/**
	 * show users
	 */
	 function index() { 	
		$this->set('users', $this->paginate('User'));
	}
	
	function showUsers() {
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
		$this->set('users', $this->User->findAll());
		$this->set('formGroups',  $formGroups);
		$this->layout = null;
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

	 function saveUserAjax () {
		$this->layout = null;
		$this->checkWriteModulePermission();
		try {
			$this->Transaction->begin() ;
			if(empty($this->data)) {
				throw new BeditaException(__("Empty data",true));
			}
			$userGroups=array();
			if(isset($this->data['groups'] )) {
				foreach ($this->data['groups'] as $k=>$v)
					array_push($userGroups, $k);
			}
			$this->data['User']['passwd'] = substr($this->data['User']['userid'],0,4) . "+pwd";
			$this->BeAuth->createUser($this->data, $userGroups);
			$u = $this->User->findByUserid($this->data['User']['userid']);
			$this->eventInfo("user ".$this->data['User']['userid']." created");
			$this->Transaction->commit();
			$this->set("userId", $u['User']['id']);
			$this->set("userCreated", true);
		} catch(BeditaException $ex) {
			$errTrace = get_class($ex) . " - " . $ex->getMessage()."\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();   
			$this->handleError($ex->getMessage(), $ex->getMessage(), $errTrace);
			$this->setResult(self::ERROR);
			$this->set("errorMsg", $ex->getMessage());
		}
	}
	
	 function removeUser($id) {
	 	$this->checkWriteModulePermission();
	 	if(isset($id)) {
	  		$u = $this->User->findById($id);
		  	if(empty($u))
		  		throw new BeditaException(__("Bad data",true));
		  	$userid = $u['User']['userid'];
		  	if($userid === $this->BeAuth->user["userid"])
		  		throw new BeditaException(__("Auto-remove forbidden",true));
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
		$groups = $this->paginate('Group');
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
	 	$this->beditaVersion();
		$this->set('events', $this->paginate('EventLog'));
		$this->set('sys', $this->BeSystem->systemInfo());
	 }

	 private function beditaVersion() {
	 	$c = Configure::getInstance();
		if (!isset($c->Bedita['version'])) {
			$versionFile = ROOT . DS . APP_DIR . DS . 'config' . DS . 'bedita.version.php';
			if(file_exists($versionFile))
				require($versionFile);
			else
				$config['Bedita.version'] = "--";
			$c->write($config);
		}
	 }
	 
	 public function deleteEventLog() { 	
	 	$this->checkWriteModulePermission();
	 	$this->beditaVersion();
	 	$this->EventLog->deleteAll("id > 0");
		$this->set('events', array());
		$this->set('sys', $this->BeSystem->systemInfo());
	 }
	
	public function loadUsersGroupsAjax() {
		if($this->params['form']['itype'] == 'user') {
			if(!class_exists('User')) {
				App::import('Model', 'User') ;
			}
			$this->User = new User();
			$this->User->displayField = 'userid';
			$this->set("itemsList", $this->User->find('list', array("order" => "userid")));
		} else if($this->params['form']['itype'] == 'group') {
			if(!class_exists('Group')) {
				App::import('Model', 'Group') ;
			}
			$this->Group = new Group();
			$this->set("itemsList", $this->Group->find('list', array("order" => "name")));
		}
		$this->layout=null;
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
	 						),
				"saveUserAjax" =>	array(
					 			"OK"	=> self::VIEW_FWD.'save_user_ajax_response',
								"ERROR"	=> self::VIEW_FWD.'save_user_ajax_response'
							),
				"loadUsersGroupsAjax" =>	array(
					 			"OK"	=> self::VIEW_FWD.'load_ugs_ajax',
								"ERROR"	=> self::VIEW_FWD.'load_ugs_ajax'
							)
	 			);
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	return false;
	 }
	 
}

?>
