<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008, 2010 ChannelWeb Srl, Chialab Srl
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
 *
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
		
		// Format custom properties
		$this->BeCustomProperty->setupUserPropertyForSave() ;
		
		if(!isset($this->data['User']['id'])) {
			if (!$this->BeAuth->checkConfirmPassword($this->params['form']['pwd'], $this->data['User']['passwd'])) {
				throw new BeditaException(__("Passwords mismatch",true));
			}
			$this->data['User']['passwd'] = trim($this->data['User']['passwd']);
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
		  	if(empty($userdetail)) {
		  		throw new BeditaException(__("Bad data",true));
			}
	 		$userdetailModules = ClassRegistry::init("PermissionModule")->getListModules($userdetail['User']['userid']);
	 		
		} else {
			$userdetail = NULL;
			$userdetailModules = NULL;
		}

		$allGroups = $this->Group->find("all", array(
				"contain" => array()
			)
		);
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
		if (is_array($userdetail["ObjectUser"])) {
			$this->set('objectUser', $this->objectRelationArray($userdetail["ObjectUser"]));
		}
		$this->set('formGroups',  $formGroups);
		$this->set('authGroups',  $authGroups);
		$this->set('userdetailModules', $userdetailModules) ;
		
		$property = $this->BeCustomProperty->setupUserPropertyForView($userdetail);
		$this->set('userProperty',  $property);
		
		BeLib::getObject("BeConfigure")->setExtAuthTypes();
	 }

	private function loadGroups() {
		return $this->paginate('Group');
	}
	 
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
		$permsMod = ClassRegistry::init("PermissionModule")->getPermissionModulesForGroup($id);
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
	  		ClassRegistry::init("PermissionModule")->updateGroupPermission($groupId, $this->data['ModuleFlags']);
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

	 public function systemInfo() { 	
	 	$this->beditaVersion();
		$this->set('sys', $this->BeSystem->systemInfo());
	 }

	 public function systemEvents() { 	
		$this->set('events', $this->paginate('EventLog'));
	 }
	 
	 private function beditaVersion() {
	 	$c = Configure::getInstance();
		if (!isset($c->Bedita['version'])) {
			$versionFile = APP . 'config' . DS . 'bedita.version.php';
			if(file_exists($versionFile))
				require($versionFile);
			else
				$config['Bedita.version'] = "--";
			$c->write('Bedita.version', $config['Bedita.version']);
		}
	 }
	 
	 public function deleteEventLog() { 	
	 	$this->checkWriteModulePermission();
	 	$this->beditaVersion();
	 	$this->EventLog->deleteAll("id > 0");
		$this->set('events', $this->paginate('EventLog'));
		$this->set('sys', $this->BeSystem->systemInfo());
	 }

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
	 	
	 	$objTypeId = $this->data["Property"]["object_type_id"];
	 	if(empty($objTypeId)){
	 		$objTypeId = null;
	 	}
	 	
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
 			throw new BeditaException(__("Duplicate property name for the same type",true));

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
	 			$propOpt[] = array("property_id" => $propertyModel->id, "property_option" => trim($opt));
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

	/**
	 * list all plugged/unplugged plugin modules
	 * @return void
	 */
	public function pluginModules() {
	 	$moduleModel = ClassRegistry::init("Module");
		$pluginModules = $moduleModel->getPluginModules();
		$this->set("pluginModules", $pluginModules);
	}
	 
	/**
	 * plug in a module 
	 * 
	 * @return void
	 */
	public function plugModule() {
		$this->checkWriteModulePermission();
		if (empty($this->params["form"]["pluginPath"])) {
			throw new BeditaExceptions(__("Missing plugin path", true));
		}
		$moduleModel = ClassRegistry::init("Module");
	 	$pluginName = $this->params["form"]["pluginName"];
		include($this->params["form"]["pluginPath"] . $pluginName . DS . "config" . DS . "bedita_module_setup.php");
		$this->Transaction->begin();
	 	$moduleModel->plugModule($pluginName, $moduleSetup, $this->params["form"]["pluginPath"]);
	 	$this->Transaction->commit();
	 	$this->eventInfo("module ".$pluginName." plugged succesfully");
		$this->userInfoMessage($pluginName . " " . __("plugged succesfully",true));
	}
	
	/**
	 * switch off => on and back a plugin module
	 * @return void
	 */
	public function toggleModule() {
		$this->checkWriteModulePermission();
		if (empty($this->data)) {
			throw new BeditaException(__("Missing data", true));
		}
		$moduleModel = ClassRegistry::init("Module");
		$this->Transaction->begin();
		if (!$moduleModel->save($this->data)) {
			throw new BeditaException(__("Error saving module data"));
		}
		$this->Transaction->commit();
		BeLib::getObject("BeConfigure")->cacheConfig();
		$this->eventInfo("module ".$this->params["form"]["pluginName"]." turned " . $this->data["status"]);
		$msg = ($this->data["status"] == "on")? __("turned on", true) : __("turned off", true);; 
		$this->userInfoMessage($this->params["form"]["pluginName"]." " .$msg);
	}
	 
	/**
	 * plug out a module
	 * @return void
	 */
	public function unplugModule() {
		$this->checkWriteModulePermission();
		if (empty($this->data["id"])) {
			throw new BeditaException(__("Missing data", true));
		}
		$moduleModel = ClassRegistry::init("Module");
		$pluginName = $this->params["form"]["pluginName"];
		$pluginPath = $this->params["form"]["pluginPath"];		
		include($pluginPath . $pluginName . DS . "config" . DS . "bedita_module_setup.php");
		$this->Transaction->begin();
	 	$moduleModel->unplugModule($this->data["id"], $moduleSetup);
	 	$this->Transaction->commit();
	 	$this->eventInfo("module ".$this->params["form"]["pluginName"]." unplugged succesfully");
		$this->userInfoMessage($this->params["form"]["pluginName"] . " " . __("unplugged succesfully",true));
	}
	
	/**
	 * list all available addons
	 * @return void
	 */
	public function addons() {
		$beLib = BeLib::getInstance();
		$this->set("addons", $beLib->getAddons());
	}
	
	/**
	 * enable addon BEdita object type
	 * @return void
	 */
	public function enableAddon() {
	 	if (empty($this->params["form"])) {
	 		throw new BeditaException(__("Missing form data", true));
	 	}
	 	$filePath = $this->params["form"]["path"] . DS . $this->params["form"]["file"];
	 	$beLib = BeLib::getInstance();
	 	if ($beLib->isFileNameUsed($this->params["form"]["file"], "model", array($this->params["form"]["path"]))) {
	 		throw new BeditaException(__($this->params["form"]["file"] . " model is already present in the system. Can't create a new object type", true));
	 	}
	 	if (!$beLib->isBeditaObjectType($this->params["form"]["model"], $this->params["form"]["path"])) {
	 		throw new BeditaException(__($this->params["form"]["model"] . " doesn't seem to be a BEdita object. It has to be extend BEAppObjectModel", true));
	 	}
	 	$model = $beLib->getObject($this->params["form"]["model"]);
	 	$data["name"] = $this->params["form"]["type"];
	 	if (!empty($model->module)) {
	 		$data["module_name"] = $model->module;
	 	}
	 	$objectType = ClassRegistry::init("ObjectType");
	 	$data["id"] = $objectType->newPluggedId();
	 	if (!$objectType->save($data)) {
	 		throw new BeditaException(__("Error saving object type", true));
	 	}
	 	
	 	BeLib::getObject("BeConfigure")->cacheConfig();
	}
	 
	/**
	 * disable addon BEdita object type
	 * @return void
	 */
	public function disableAddon() {
	 	if (empty($this->params["form"]["type"])) {
	 		throw new BeditaException(__("Missing form data", true));
	 	}
	 	$otModel = ClassRegistry::init("ObjectType");
	 	$this->Transaction->begin();
	 	$otModel->purgeType($this->params["form"]["type"]);
	 	$this->Transaction->commit($this->params["form"]["type"]);
	 	$this->eventInfo("addon ". $this->params["form"]["model"]." disable succesfully");
		$this->userInfoMessage($this->params["form"]["model"] . " " . __("disable succesfully, all related objects are been deleted",true));
		BeLib::getObject("BeConfigure")->cacheConfig();
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
 								"OK"	=> self::VIEW_FWD.'systemEvents',
	 							"ERROR"	=> self::VIEW_FWD.'systemEvents'
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
							),
				"plugModule" => array(
								"OK" => "/admin/pluginModules",
								"ERROR" => "/admin/pluginModules",
							),
				"toggleModule" => array(
								"OK" => "/admin/pluginModules",
								"ERROR" => "/admin/pluginModules",
							),
				"unplugModule" => array(
								"OK" => "/admin/pluginModules",
								"ERROR" => "/admin/pluginModules",
							),
				"enableAddon" => array(
								"OK" => "/admin/addons",
								"ERROR" => "/admin/addons",
							),
				"disableAddon" => array(
								"OK" => "/admin/addons",
								"ERROR" => "/admin/addons",
							)
	 			);
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	return false;
	}
	 
}

?>
