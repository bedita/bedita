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
 * Administration: system info, eventlogs, plug/unplug module, addons, utility....
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class AdminController extends ModulesController {

	public $uses = array() ;
	public $components = array('BeSystem');
	public $helpers = array('Paginator');
	public $paginate = array(
	'EventLog' => array('limit' => 20, 'page' => 1, 'order'=>array('created'=>'desc'))
	); 
	protected $moduleName = 'admin';
	
	public function index() {
		$this->action = "systemEvents";
		$this->systemEvents();
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
			if(file_exists($versionFile)) {
				require($versionFile);
			} else {
				$config['Bedita.version'] = "--";
			}
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
		));
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
 				
 		if (!empty($this->data["Property"]["id"])) {
 			$conditions[] = "id <> '" . $this->data["Property"]["id"] . "'";
		}
	 	
	 	$countProperties = $propertyModel->find("count", array(
 				"conditions" => $conditions
 		));
		
 		if ($countProperties > 0) {
 			throw new BeditaException(__("Duplicate property name for the same type",true));
		}

	 	if (empty($this->data["Property"]["multiple_choice"]) || $this->data["Property"]["property_type"] != "options") {
	 		$this->data["Property"]["multiple_choice"] = 0;
		}
	 	
	 	$this->Transaction->begin();
	 	if (!$propertyModel->save($this->data)) {
	 		throw new BeditaException(__("Error saving custom property",true), $propertyModel->validationErrors);
	 	}
		
	 	// save options
	 	$propertyModel->PropertyOption->deleteAll("property_id='" . $propertyModel->id . "'");
	 	if ($this->data["Property"]["property_type"] == "options") {
	 		if (empty($this->data["options"])) {
	 			throw new BeditaException(__("Missing options",true));
			}
	 			
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
	 		if (!ClassRegistry::init("Property")->delete($this->data["Property"]["id"])) {
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
		$moduleModel = ClassRegistry::init("Module");
	 	$pluginName = $this->params["form"]["pluginName"];
		$filename = BEDITA_MODULES_PATH . DS . $pluginName . DS . "config" . DS . "bedita_module_setup.php";
		if (!file_exists($filename)) {
			throw new BeditaException(__("Something seems wrong. bedita_module_setup.php didn't found", true));
		}
		include($filename);
		$this->Transaction->begin();
	 	$moduleModel->plugModule($pluginName, $moduleSetup);
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
		$filename = BEDITA_MODULES_PATH . DS . $pluginName . DS . "config" . DS . "bedita_module_setup.php";
		if (!file_exists($filename)) {
			throw new BeditaException(__("Something seems wrong. bedita_module_setup.php didn't found", true));
		}
		include($filename);
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
	 	if ($beLib->isFileNameUsed($this->params["form"]["file"], "models", array($this->params["form"]["path"] . DS))) {
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

	public function viewConfig() {
		include CONFIGS . 'langs.iso.php';
		$this->set('langs_iso',$config['langsIso']);

		$besys = BeLib::getObject("BeSystem");
		// check bedita.sys.php
//		$beditaSysPath = CONFIGS . "bedita.sys.php";
//		if (!file_exists($beditaSysPath)) {
//			$this->set("bedita_sys_err", __("Path not found",true) . ": " . $beditaSysPath);
//		}
//		if (!$besys->checkWritable($beditaSysPath)) {
//			$this->set("bedita_sys_err", __("File not writable, update properly file permits for",true) . " " . $beditaSysPath);
//		}

		// check bedita.cfg.php
		$beditaCfgPath = CONFIGS . "bedita.cfg.php";
		if (!file_exists($beditaCfgPath)){
			$this->set("bedita_cfg_err", __("Path not found",true) . ": " . $beditaCfgPath);
		}
		if (!$besys->checkWritable($beditaCfgPath)) {
			$this->set("bedita_cfg_err", __("File not writable, update properly file permits for",true) . " " . $beditaCfgPath);
		}

		$conf = Configure::getInstance();
		$mediaRoot = $conf->mediaRoot;
		if(empty($mediaRoot)) {
			$this->set("media_root_err",__("media root not set",true));
		}
		if (!$besys->checkAppDirPresence($mediaRoot)) {
			$this->set("media_root_err",__("media root folder not found",true));
		} else if (!$besys->checkWritable($mediaRoot)) {
			$this->set("media_root_err",__("media root folder is not writable: update folder permits properly",true));
		}

		$mediaUrl = $conf->mediaUrl;
		if(empty($mediaUrl)) {
			$this->set("media_url_err",__("media url not set",true));
		}
		$headerResponse = @get_headers($mediaUrl);
		if(empty($headerResponse) || !$headerResponse) {
			$this->set("media_url_err",__("media url is unreachable",true));
		} else if (stristr($headerResponse[0],'HTTP/1.1 4') || stristr($headerResponse[0],'HTTP/1.1 5')) {
			$this->set("media_url_err",__("media url is unreachable",true));
		}

		$beditaUrl = $conf->beditaUrl;
		if(empty($beditaUrl)) {
			$this->set("bedita_url_err",__("bedita url not set",true));
		}
		$headerResponse = @get_headers($beditaUrl);
		if(empty($headerResponse) || !$headerResponse) {
			$this->set("bedita_url_err",__("bedita url is unreachable",true));
		} else if (stristr($headerResponse[0],'HTTP/1.1 4') || stristr($headerResponse[0],'HTTP/1.1 5')) {
			$this->set("bedita_url_err",__("bedita url is unreachable",true));
		}
	}

	public function saveConfig() {
		// sys and cfg array
		$sys = $this->params["form"]["sys"];

		if (empty($sys["mediaRoot"])) {
			throw new BeditaException(__("media root can't be empty", true), $sys);
		}
		if (empty($sys["mediaUrl"])) {
			throw new BeditaException(__("media url can't be empty", true), $sys);
		}

		$sys["mediaRoot"] = rtrim($sys["mediaRoot"], DS);
		$sys["mediaUrl"] = rtrim($sys["mediaUrl"], "/");

		$besys = BeLib::getObject("BeSystem");
		if (!$besys->checkAppDirPresence($sys["mediaRoot"])) {
			throw new BeditaException(__("media root folder doesn't exist", true), $sys);
		}

		if (!$besys->checkWritable($sys["mediaRoot"])) {
			throw new BeditaException(__("media root folder is not writable", true), $sys);
		}

		$headerResponse = @get_headers($sys["mediaUrl"]);
		if(empty($headerResponse) || !$headerResponse) {
			throw new BeditaException(__("bedita url is unreachable", true), $sys);
		}

		if (stristr($headerResponse[0],'HTTP/1.1 4') || stristr($headerResponse[0],'HTTP/1.1 5')) {
			throw new BeditaException(__("bedita url is unreachable", true) . ": " . $headerResponse[0] , $sys);
		}

		$headerResponse = @get_headers($sys["beditaUrl"]);
		if(empty($headerResponse) || !$headerResponse) {
			throw new BeditaException(__("bedita url is unreachable", true), $sys);
		}

		if (stristr($headerResponse[0],'HTTP/1.1 4') || stristr($headerResponse[0],'HTTP/1.1 5')) {
			throw new BeditaException(__("bedita url is unreachable", true) . ": " . $headerResponse[0] , $sys);
		}

		// smtpOptions password
		$conf = Configure::getInstance();
		if(!empty($conf->smtpOptions['password']) && !empty($sys['smtpOptions']) && empty($sys['smtpOptions']['password'])) {
			$sys['smtpOptions']['password'] = $conf->smtpOptions['password'];
		}
		
		// prepare cfg array
		$cfg = array_merge($this->params["form"]["cfg"], $sys);
		
		// check if configs already set
		foreach ($cfg as $k => $v) {
			if(!empty($conf->$k) && ($conf->$k === $v)) {
				unset($cfg[$k]);
			}
		}
		
		// order langs
		sort($cfg['langOptions']);
		// write bedita.cfg.php
		$beditaCfgPath = CONFIGS . "bedita.cfg.php";
		$besys->writeConfigFile($beditaCfgPath, $cfg, true);

		$this->userInfoMessage(__("Configuration saved", true));
	}

	protected function forward($action, $esito) {
	 	 	$REDIRECT = array(
				"deleteEventLog" => 	array(
 								"OK"	=> self::VIEW_FWD.'systemEvents',
	 							"ERROR"	=> self::VIEW_FWD.'systemEvents'
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
							),
				"saveConfig" => 	array(
	 							"OK"	=> "/admin/viewConfig",
	 							"ERROR"	=> "/admin/viewConfig"
	 						)
	 			);
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	return false;
	}
	 
}

?>
