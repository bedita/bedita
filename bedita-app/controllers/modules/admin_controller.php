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
		$authGroups = array();
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
			if($g['Group']['backend_auth'] == 1)
				$authGroups[] = $g['Group']['name'];
		}
		$this->set('users', $this->User->findAll());
		$this->set('formGroups',  $formGroups);
		$this->set('authGroups',  $authGroups);
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
			if (!$this->BeAuth->checkConfirmPassword($this->params['form']['pwd'], $this->data['User']['passwd']))
				throw new BeditaException(__("Passwords mismatch",true));
			$this->BeAuth->createUser($this->data, $userGroups);
			$this->eventInfo("user ".$this->data['User']['userid']." created");
			$this->userInfoMessage(__("User created",true));
		} else {
			$pass = trim($this->data['User']['passwd']);
			$confirmPass = trim($this->params['form']['pwd']);
			if(empty($pass) && empty($confirmPass)) {
				unset($this->data['User']['passwd']);
			} elseif (!$this->BeAuth->checkConfirmPassword($this->params['form']['pwd'], $this->data['User']['passwd'])) {
				throw new BeditaException(__("Passwords mismatch",true));
			}
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
	 		$userdetail = $this->User->findById($id) ;
		  	if(empty($userdetail))
		  		throw new BeditaException(__("Bad data",true));
	 		$userdetailModules = $this->BePermissionModule->getListModules($userdetail['User']['userid']);
	 		
		} else {
			$userdetail = NULL;
			$userdetailModules = NULL;
		}

		$allGroups = $this->Group->findAll();
		$userGroups = array();
		if(isset($userdetail)) {
			foreach ($userdetail['Group'] as $g) {
				array_push($userGroups, $g['name']);
			}
		}
		$formGroups = array();
		$authGroups = array();
		foreach ($allGroups as $g) {
			$isGroup=false;
			if(array_search($g['Group']['name'],$userGroups) !== false)
				$isGroup = true;
			$formGroups[$g['Group']['name']] = $isGroup;
			if($g['Group']['backend_auth'] == 1)
				$authGroups[] = $g['Group']['name'];
		}
		
		$this->set('userdetail',  $userdetail['User']);
		$this->set('formGroups',  $formGroups);
		$this->set('authGroups',  $authGroups);
		$this->set('userdetailModules', $userdetailModules) ;
	 }

	private function loadGroups() {
		return $this->paginate('Group');
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
	  	$groupName = $this->Group->field("name", array("id" => $id));
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
			$versionFile = APP . 'config' . DS . 'bedita.version.php';
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

 	/**
	 * show customproperties
	 */
	public function customproperties() { 	
		$properties = ClassRegistry::init("Property")->find("all", array(
							"contain" => "PropertyOption"
						)
					);
		
		$this->set("properties", $properties);
	}
	 
	public function saveCustomProperties() {
		$this->checkWriteModulePermission();
		if (empty($this->data["Property"]))
	 		throw new BeditaException(__("Empty data",true));
	 		
	 	$propertyModel = ClassRegistry::init("Property");
	 	
	 	$conditions = array(
 					"name" => $this->data["Property"]["name"],
	 				"object_type_id" => $this->data["Property"]["object_type_id"]
 				);
 				
 		if (!empty($this->data["Property"]["id"]))
 			$conditions[] = "id <> '" . $this->data["Property"]["id"] . "'";
	 	
	 	$countProperties = $propertyModel->find("count", array(
 				"conditions" => $conditions
 			) 
 		);
		
 		if ($countProperties > 0)
 			throw new BeditaException(__("Duplicate property name for the same object",true));

	 	if (empty($this->data["Property"]["multiple_choice"]) || $this->data["Property"]["property_type"] != "options")
	 		$this->data["Property"]["multiple_choice"] = 0;
	 	
	 	$this->Transaction->begin();
	 	if (!$propertyModel->save($this->data)) {
	 		throw new BeditaException(__("Error saving custom property",true), $propertyModel->validationErrors);
	 	}
		
	 	// save options
	 	$propertyModel->PropertyOption->deleteAll("property_id='" . $propertyModel->id . "'");
	 	if ($this->data["Property"]["property_type"] == "options") {
	 		if (empty($this->data["options"]))
	 			throw new BeditaException(__("Missing options",true));
	 			
	 		$optionArr = explode(",", trim($this->data["options"],","));
	 		foreach ($optionArr as $opt) {
	 			$propOpt[] = array("property_id" => $propertyModel->id, "property_option" => $opt);
	 		}
	 		if (!$propertyModel->PropertyOption->saveAll($propOpt)) {
	 			throw new BeditaException(__("Error saving options",true));
	 		}
	 	}
	 	
	 	$this->Transaction->commit();
	 	
	 	$this->eventInfo("property ".$this->data['Property']['name']." saved");
		$this->userInfoMessage(__("Custom property saved",true));	 	
	 }

	 function deleteCustomProperties() {
	 	$this->checkWriteModulePermission();
	 	if (!empty($this->data["Property"]["id"])) {
	 		if (!ClassRegistry::init("Property")->del($this->data["Property"]["id"])) {
	 			throw new BeditaException(__("Error deleting custom property " . $this->data["Property"]["name"],true));
	 		}
	 	}
	 }

	 protected function forward($action, $esito) {
	 	 	$REDIRECT = array(
				"viewGroup" => 	array(
 								"OK"	=> self::VIEW_FWD.'groups',
	 							"ERROR"	=> self::VIEW_FWD.'groups'
	 						),
	 	 		"saveUser" => 	array(
 								"OK"	=> "/admin/index",
	 							"ERROR"	=> $this->referer() 
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
				"saveCustomProperties" =>	array(
					 			"OK"	=> '/admin/customproperties',
								"ERROR"	=> '/admin/customproperties'
							),
				"deleteCustomProperties" =>	array(
					 			"OK"	=> '/admin/customproperties',
								"ERROR"	=> '/admin/customproperties'
							)
	 			);
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	return false;
	 }
	 
}

?>
