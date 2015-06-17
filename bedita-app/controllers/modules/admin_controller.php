<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008, 2010 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 *
 *------------------------------------------------------------------->8-----
 */

/**
 * Administration: system info, eventlogs, plug/unplug module, addons, utility....
 */
class AdminController extends ModulesController {

	public $uses = array('MailJob','MailLog','MailMessage') ;
	public $components = array(
		'Security' => array(
			'requirePost' => array(
				'deleteEventLog',
				'emptySystemLog',
				'deleteAllMailLogs',
				'deleteAllMailUnsent',
				'deleteMailLog',
				'deleteMailJob'
			)
		),
		'BeSystem',
		'BeMail',
		'BeSecurity'
	);
	public $helpers = array('Paginator', 'Text');
	public $paginate = array(
		'EventLog' => array('limit' => 20, 'page' => 1, 'order'=>array('created'=>'desc')),
		'MailJob' => array('limit' => 10, 'page' => 1, 'order'=>array('created'=>'desc'))
	);
	protected $moduleName = 'admin';

	protected function beditaBeforeFilter() {
		// disable Security component for method not in requirePost
		if (!in_array($this->action, $this->Security->requirePost)) {
			$this->Security->validatePost = false;
		} else {
			$this->BeSecurity->validatePost = false;
		}
	}

	public function index() {
		$this->action = "systemEvents";
		$this->systemEvents();
	}

	/**
	 * http request load utility page
	 * ajax request try to execute the utility operation defined in $this->params["form"]["operation"]
	 *
	 * @throws BeditaAjaxException
	 */
	public function utility() {
		$this->checkWriteModulePermission();
		if ($this->params["isAjax"]) {
			if (empty($this->params["form"]["operation"])) {
				throw new BeditaAjaxException(__("Error: utility operation undefined", true), array("output" => "json"));
			}
			try {
                $operation = $this->params['form']['operation'];
                $options = array('log' => true);
                if ($operation === 'cleanupCache') {
                    $options['frontendsToo'] = true;
                    $cleanAll = $this->params['form']['cleanAll'];
                    $options['cleanAll'] = ($cleanAll == 'true');
                }
                $data = ClassRegistry::init('Utility')->call($operation, $options);
				// render info/warn message
				if (!empty($data['log'])) {
					$this->set('detail', nl2br($data['log']));
					$data['msgType'] = 'warn';
				} else {
					$data['msgType'] = 'info';
				}
				$this->set('message', $data['message']);
				$this->set('class', $data['msgType']);
				$data['htmlMsg'] = $this->render(null, null, ELEMENTS . 'message.tpl');
				$this->output = "";
			} catch (BeditaException $ex) {
				$details = $ex->getDetails();
				if (!is_array($details)) {
					$details = array($details);
				}
				$details["output"] = "json";
				throw new BeditaAjaxException($ex->getMessage(), $details);
			}

			$this->view = "View";
			header("Content-Type: application/json");
			$this->set("data", $data);
			$this->eventInfo("utility [". $this->params["form"]["operation"] ."] executed");
			$this->render(null, "ajax", VIEWS . "/pages/json.ctp");
		}
	}

	public function update($type = 'core') {
		$svnConf = Configure::read('rcs.svn');
		if ($this->params["isAjax"]) {
			if (empty($this->params['form']['operation'])) {
				throw new BeditaAjaxException(__("Error: utility operation undefined", true), array("output" => "json"));
			}
			$data = array();
			$data['info'] = array(
				'name' => array_pop(explode(DS, $this->params['form']['operation'])),
				'path' => realpath($this->params['form']['operation'])
			);
			$revisionModel = ClassRegistry::init('Revision');
			$rcs = $revisionModel->getRepository($this->params['form']['operation']);
			if ($rcs !== null) {
				$data['info'] = array_merge($data['info'], $revisionModel->getData($rcs), array('valid' => true));
				if ($rcs->type() == 'svn') {
					if (!empty($svnConf)) {
						$rcs->authorize($svnConf['username'], $svnConf['password']);
					} else {
						$data['error'] = true;
						$data['message'] = 'Empty SVN credentials';
					}
				}
				$res = $rcs->up();
				if (!empty($res)) {
					$data['message'] = $res[0];
					if (count($res) > 1) {
						$data['details'] = implode("\n", array_splice($res, 1));
					}
					if ($rcs->lastCommandCode !== 0) {
						$data['error'] = true;
					}
				}
			} else {
				$data['info']['valid'] = false;
				$data['error'] = true;
				$data['message'] = 'Unable to find a revision control system';
			}

			if (empty($data['error'])) {
				BeLib::remoteUpdateAddons($this->params['form']['operation']);
			}

			$this->view = 'View';
			$this->action = 'json';
			$this->RequestHandler->respondAs('json');
			$this->set('data', $data);
		} else {
			$sel = array();
			if ($type == 'core') {
				$folders = array(ROOT);
			} elseif ($type == 'frontends') {
				$folders = BeLib::getFrontendFolders();
			} elseif ($type == 'modules') {
				$folders = BeLib::getPluginModuleFolders();
			} elseif ('addons') {
				$folders = BeLib::getAddonFolders();
			} else {
				throw new BeditaException(__("Error: could not update this resource", true));
			}
			foreach ($folders as $key => $folder) {
				$data = array(
					'name' => array_pop(explode(DS, $folder)),
					'path' => realpath($folder)
				);
				$revisionModel = ClassRegistry::init('Revision');
				$rcs = $revisionModel->getRepository(realpath($folder));
				if ($rcs !== null) {
					if ($rcs->type() == 'svn') {
						if (!empty($svnConf)) {
							$rcs->authorize($svnConf['username'], $svnConf['password']);
						} else {
							$data['notice'] = 'Empty SVN credentials';
						}
					}
					$data = array_merge($data, $revisionModel->getData($rcs), array('valid' => true));
					if ($rcs->lastCommandCode != 0) {
						if (empty($data['notice'])) {
							$data['notice'] = '';
						} else {
							$data['notice'] .= "\n";
						}
						$data['notice'] .= implode("\n", $rcs->lastError);
					}
				} else {
					$data['valid'] = false;
				}
				$sel[] = $data;
			}

			$this->set('folders', $sel);
			$this->render('update');
		}
	}

	public function updateFrontends() {
		$this->update('frontends');
	}

	public function updateModules() {
		$this->update('modules');
	}

	public function updateAddons() {
		$this->update('addons');
	}

	/**
	 * list core modules to choose which switch on/off
	 *
	 * @return void
	 */
	public function coreModules() {
		$modules = ClassRegistry::init("Module")->find("all", array(
			"conditions" => array("module_type" => "core"),
			"order" => "priority ASC"
		));
		$modules = Set::classicExtract($modules,'{n}.Module');
		$this->set("moduleList", $modules);
	}

	/**
	 * list all modules and allow to sort them
	 *
	 * @return void
	 */
	public function sortModules() {
		$this->checkWriteModulePermission();
		if (!empty($this->data["Modules"])) {
			$Module = ClassRegistry::init("Module");
			$this->Transaction->begin();
			foreach ($this->data["Modules"] as $id => $priority) {
				$Module->id = $id;
				if (!$Module->saveField("priority", $priority)) {
					$name = $Module->findField("name", array("id" => $id));
					$details = array_merge($Module->validationErrors, array("id" => $id));
					throw new BeditaException(__("Error sorting module", true) . " " . $name, $Module->validationErrors);
				}
			}
			$this->Transaction->commit();
			$this->userInfoMessage(__("Modules sorted succesfully", true));
		}
		$modules = ClassRegistry::init("Module")->find("all", array(
			"conditions" => array("status" => "on"),
			"order" => "priority ASC"
		));
		$modules = Set::classicExtract($modules,'{n}.Module');
		$this->set("moduleList", $modules);
	}

    /**
     * Display system info, as well as warnings if some of the requirements aren't met.
     */
    public function systemInfo() {
        Configure::load('requirements');

        $this->beditaVersion();
        $sys = $this->BeSystem->systemInfo();

        $warnings = array();
        $requirements = Configure::read('requirements');
        $phpversion = !empty($sys['phpVersion']) ? $sys['phpVersion'] : phpversion();
        if (version_compare($phpversion, $requirements['phpVersion']) < 0) {
            array_push($warnings, 'phpVersion');
        }
        foreach ($requirements['phpExtensions'] as $ext) {
            $loaded = !empty($sys['phpExtensions']) ? in_array($ext, $sys['phpExtensions']) : extension_loaded($ext);
            if (!$loaded) {
                array_push($warnings, $ext);
            }
        }
        if (!empty($sys['db']) && !empty($sys['dbServer'])) {
            if (!array_key_exists($sys['db'], $requirements['dbVersion'])) {
                array_push($warnings, 'db');
            } elseif (version_compare($sys['dbServer'], $requirements['dbVersion'][$sys['db']]) < 0) {
                array_push($warnings, $sys['db']);
            }
        }

        $this->set(compact('sys', 'warnings'));
    }

	public function systemEvents() {
		$this->set('events', $this->paginate('EventLog'));
	}

	public function systemLogs($maxRows = 10) {
		$this->set('backendLogs', $this->BeSystem->backendSystemLogs($maxRows));
		$this->set('frontendLogs', $this->BeSystem->frontendSystemLogs($maxRows));
		$this->set('maxRows',$maxRows);
	}

	public function emptyFile() {
		$this->checkWriteModulePermission();
		$this->BeSystem->emptyFile($this->data["fileToEmpty"]);
		$this->systemLogs(10);
	}

	public function refreshFile() {
		$this->layout = "ajax";
		$rowLimit = $this->params['form']['rowLimit'];
		$fileToRefresh = $this->params['form']['fileToRefresh'];
		$this->set('log',$this->BeSystem->readLogEntries($fileToRefresh,$rowLimit));
	}

	public function emptySystemLog() {
		$logFiles = $this->BeSystem->logFiles();
		foreach($logFiles as $fileName) {
			$this->BeSystem->emptyFile($fileName);
		}
		$this->set('logs', $this->BeSystem->systemLogs(10));
		$this->set('maxRows',10);
	}

	public function deleteMailJob($id) {
		$this->checkWriteModulePermission();
		if (empty($this->data['MailJob']['id'])) {
			throw new BeditaException(__('Missing data', true));
		}
		$id = $this->data['MailJob']['id'];
		$this->MailJob->delete($id);
		$this->loadMailData();
		$this->userInfoMessage(__("MailJob deleted", true) . " -  " . $id);
		$this->eventInfo("mail job $id deleted");
	}

	public function deleteMailLog() {
		$this->checkWriteModulePermission();
		if (empty($this->data['MailLog']['id'])) {
			throw new BeditaException(__('Missing data', true));
		}
		$id = $this->data['MailLog']['id'];
		$this->MailLog->delete($id);
		$this->loadMailLogData();
		$this->userInfoMessage(__("MailLog deleted", true) . " -  " . $id);
		$this->eventInfo("mail log $id deleted");
	}

	public function deleteAllMailUnsent() {
		$this->checkWriteModulePermission();
		$this->MailJob->deleteAll("mail_message_id IS NULL");
		$this->loadMailData();
		$this->userInfoMessage(__("MailJob deleted", true));
		$this->eventInfo("all mail job deleted");
	}

	public function deleteAllMailLogs() {
		$this->checkWriteModulePermission();
		$this->MailLog->deleteAll("id > 0");
		$this->loadMailLogData();
		$this->userInfoMessage(__("MailLog deleted", true));
		$this->eventInfo("all mail log deleted");
	}

	public function emailLogs() {
		$this->loadMailLogData();
	}

	public function emailInfo() {
		$this->loadMailData();
	}

	public function testSmtp($to) {
		$this->checkWriteModulePermission();
		$mailOptions = Configure::read("mailOptions");
		$mailData = array();
		$mailData['sender'] = $mailOptions["sender"];
		$mailData['from'] = $mailOptions["sender"];
		$mailData['to'] = $to;
		$mailData['subject'] = "Test mail BEdita";
		$mailData['body'] = "Test mail BEdita" . "\n\n--\n" . $mailOptions["signature"];
		$this->BeMail->Email->smtpOptions['port'] = $this->params['form']['sys']['smtpOptions']['port'];
		$this->BeMail->Email->smtpOptions['timeout'] = $this->params['form']['sys']['smtpOptions']['timeout'];
		$this->BeMail->Email->smtpOptions['host'] = $this->params['form']['sys']['smtpOptions']['host'];
		$this->BeMail->Email->smtpOptions['username'] = $this->params['form']['sys']['smtpOptions']['username'];
		if(!empty($this->params['form']['sys']['smtpOptions']['password'])) {
			$this->BeMail->Email->smtpOptions['password'] = $this->params['form']['sys']['smtpOptions']['password'];
		}
		$this->BeMail->sendMail($mailData);
		$this->userInfoMessage(__("Test mail sent to ", true) . $to);
		$this->eventInfo("test mail [". $mailData["title"]."] sent");
	}

	private function loadMailData() {
		$mailJob = ClassRegistry::init("MailJob");
		$this->passedArgs["sort"] = "id";
		$this->passedArgs["direction"] = "desc";
		$this->set('jobs',$this->paginate('MailJob'));
		$this->set('totalJobs',  $mailJob->find("count", array("conditions" => array())));
		$this->set('jobsFailed', $mailJob->find("count", array("conditions" => array("status" => array("failed")))));
		$this->set('jobsSent',   $mailJob->find("count", array("conditions" => array("status" => array("sent")))));
		$this->set('jobsPending',$mailJob->find("count", array("conditions" => array("status" => array("pending")))));
		$this->set('jobsUnsent', $mailJob->find("count", array("conditions" => array("status" => array("unsent")))));
	}

	private function loadMailLogData() {
		$mailLog = ClassRegistry::init("MailLog");
		$this->passedArgs["sort"] = "id";
		$this->passedArgs["direction"] = "desc";
		$this->set('logs',$this->paginate('MailLog'));
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
		$this->set("pluginDir", BEDITA_MODULES_PATH);
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
		$this->set("addons", ClassRegistry::init("Addon")->getAddons());
	}

	/**
	 * Enable addon copying the addon file in the related enabled folder.
	 * If addon is a BEdita object type create also a row on object_types table
	 */
	public function enableAddon() {
		$this->checkWriteModulePermission();
	 	if (empty($this->params["form"])) {
	 		throw new BeditaException(__("Missing form data", true));
	 	}
	 	$filePath = $this->params["form"]["path"] . DS . $this->params["form"]["file"];
		$enabledPath = $this->params["form"]["path"] . DS . "enabled" .  DS . $this->params["form"]["file"];
	 	$beLib = BeLib::getInstance();

		if (!BeLib::getObject("BeSystem")->checkWritable($this->params["form"]["path"] . DS . "enabled")) {
			throw new BeditaException(__("enabled folder isn't writable", true), $this->params["form"]["path"] . DS . "enabled");
		}

		$this->Transaction->begin();
		ClassRegistry::init("Addon")->enable($this->params["form"]["file"],  $this->params["form"]["type"]);
		$this->Transaction->commit();

		$msg = $this->params["form"]["name"] . " " . __("addon plugged succesfully", true);
		$this->userInfoMessage($msg);
		$this->eventInfo($msg);
	}

	/**
	 * Disable addon deleting the addon file from the related enabled folder.
	 * If addon is a BEdita object type remove also the row on object_types table
	 */
	public function disableAddon() {
		$this->checkWriteModulePermission();
	 	if (empty($this->params["form"])) {
	 		throw new BeditaException(__("Missing form data", true));
	 	}

		if (!BeLib::getObject("BeSystem")->checkWritable($this->params["form"]["path"] . DS . "enabled")) {
			throw new BeditaException(__("enabled folder isn't writable", true), $this->params["form"]["path"] . DS . "enabled");
		}

		$this->Transaction->begin();
		ClassRegistry::init("Addon")->disable($this->params["form"]["file"], $this->params["form"]["type"]);
		$this->Transaction->commit();

		// BEdita object type
		if (!empty($this->params["form"]["objectType"])) {
			$this->userInfoMessage($this->params["form"]["name"] . " " . __("disable succesfully, all related objects are been deleted",true));
		} else {
			$this->userInfoMessage($this->params["form"]["name"] . " " . __("disable succesfully", true));
		}

        $this->eventInfo("addon {$this->params['form']['name']} disabled successfully");
	}

	public function updateAddon() {
		$this->checkWriteModulePermission();
		if (empty($this->params["form"])) {
	 		throw new BeditaException(__("Missing form data", true));
	 	}

		if (!BeLib::getObject("BeSystem")->checkWritable($this->params["form"]["path"] . DS . "enabled")) {
			throw new BeditaException(__("enabled folder isn't writable", true), $this->params["form"]["path"] . DS . "enabled");
		}

		ClassRegistry::init("Addon")->update($this->params["form"]["file"], $this->params["form"]["type"]);
		$this->userInfoMessage($this->params["form"]["name"] . " " . __("updated succesfully", true));
		$this->eventInfo("addon ". $this->params["form"]["model"]." updated succesfully");
	}

	public function diffAddon() {
		$Addon = ClassRegistry::init("Addon");
		$addonPath = $Addon->getFolderByType($this->params["named"]["type"]) . DS . $this->params["named"]["filename"];
		$addonEnabledPath = $Addon->getEnabledFolderByType($this->params["named"]["type"]) . DS . $this->params["named"]["filename"];

		$addon = file_get_contents($addonPath);
		$addonEnabled = file_get_contents($addonEnabledPath);

		App::import("Vendor", "finediff");
		$opcodes = FineDiff::getDiffOpcodes($addonEnabled, $addon, FineDiff::$paragraphGranularity);
		$diff = FineDiff::renderDiffToHTMLFromOpcodes($addonEnabled, $opcodes);
		$this->set("diff", $diff);
	}


	public function viewConfig() {
		include CONFIGS . 'langs.iso.php';
		$this->set('langs_iso',$config['langsIso']);

		$besys = BeLib::getObject("BeSystem");

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

		// .po
		$poLangs = array();
		$localePath = APP."locale".DS;
		$folder = new Folder($localePath);
		$ls = $folder->read();
		foreach ($ls[0] as $loc) {
			if($loc[0] != '.') { // only "regular" dirs...
				$poLangs[] = $loc;
			}
		}
		$this->set("po_langs",$poLangs);
	}

	public function saveConfig() {
		$this->checkWriteModulePermission();
		// sys and cfg array
		$sys = $this->params["form"]["sys"];

		$warnMsg = array();
		if (empty($sys["mediaRoot"])) {
			$warnMsg[] = __("media root can't be empty", true);
		}
		if (empty($sys["mediaUrl"])) {
			$warnMsg[] = __("media url can't be empty", true);
		}

		$sys["mediaRoot"] = rtrim($sys["mediaRoot"], DS);
		$sys["mediaUrl"] = rtrim($sys["mediaUrl"], "/");

		$besys = BeLib::getObject("BeSystem");
		if (!$besys->checkAppDirPresence($sys["mediaRoot"])) {
			$warnMsg[] = __("media root folder doesn't exist", true) . " - " . $sys["mediaRoot"];
		}

		if (!$besys->checkWritable($sys["mediaRoot"])) {
			$warnMsg[] = __("media root folder is not writable", true) . " - " . $sys["mediaRoot"];
		}

		$headerResponse = @get_headers($sys["mediaUrl"]);
		if(empty($headerResponse) || !$headerResponse) {
			$warnMsg[] = __("media url is unreachable", true) . " - " . $sys["mediaUrl"];
		}

		if (stristr($headerResponse[0],'HTTP/1.1 4') || stristr($headerResponse[0],'HTTP/1.1 5')) {
			$warnMsg[] = __("media url is unreachable", true) . ": " . $headerResponse[0] . " - " . $sys["mediaUrl"];
		}

		$headerResponse = @get_headers($sys["beditaUrl"]);
		if(empty($headerResponse) || !$headerResponse) {
			$warnMsg[] = __("bedita url is unreachable", true) . " - " . $sys["beditaUrl"];
		}

		if (stristr($headerResponse[0],'HTTP/1.1 4') || stristr($headerResponse[0],'HTTP/1.1 5')) {
			$warnMsg[] = __("bedita url is unreachable", true) . ": " . $headerResponse[0] . " - " . $sys["beditaUrl"];
		}

		// smtpOptions password
		$conf = Configure::getInstance();
		if(!empty($conf->smtpOptions['password']) && !empty($sys['smtpOptions']) && empty($sys['smtpOptions']['password'])) {
			$sys['smtpOptions']['password'] = $conf->smtpOptions['password'];
		}

		// prepare cfg array
		$cfg = array_merge($this->params["form"]["cfg"], $sys);

		// from string to boolean - $cfg["langOptionsIso"]
		$cfg["langOptionsIso"] = ($cfg["langOptionsIso"] === "true") ? true : false;

		if($cfg["langOptionsIso"]) {
			$cfg["langOptions"] = $conf->langOptionsDefault;
		}

		// order langs
		if(!empty($cfg["langOptions"])) {
			ksort($cfg["langOptions"]);
		}

		// check if configs already set
		foreach ($cfg as $k => $v) {
			if (!empty($conf->$k) && ($conf->$k === $v)) {
				unset($cfg[$k]);
			} else {
				// sanitize from script
				$cfg[$k] = Sanitize::stripScripts($v);
			}
		}

		// write bedita.cfg.php
		$beditaCfgPath = CONFIGS . "bedita.cfg.php";
		$besys->writeConfigFile($beditaCfgPath, $cfg, true);

		foreach ($warnMsg as $w) {
			$this->userWarnMessage($w);
			$this->eventWarn($w);
		}
		if(!empty($warnMsg)) {
			$this->log("Warnings saving configuration, params " . var_export($warnMsg, true));
		} else {
			$this->userInfoMessage(__("Configuration saved", true));
		}
	}

	
	public function customRelations() {
		//
	}

	public function saveCustomRelation() {
		$this->checkWriteModulePermission();
		$formData = $this->data;
		unset($formData['_csrfToken']);
		$beLib = BeLib::getInstance();
		$relName = $beLib->friendlyUrlString($formData['name']);
		if (empty($relName)) {
			throw new BeditaException(__('Relation name is mandatory', true), $formData);
		}
		// check name that not match core relation name
		$coreRelations = Configure::read('defaultObjRelationType');
		if (!empty($coreRelations[$relName])) {
			throw new BeditaException("'" . $relName . "' " . __("is a core relation, you can't override them", true), $formData);
		}

		if (empty($formData['left']) || empty($formData['right'])) {
			throw new BeditaException(__("source and/or target objects group can't be empty", true), $formData);
		}

		unset($formData['name']);
		if (!empty($formData['inverse'])) {
			$formData['inverse'] = $beLib->friendlyUrlString($formData['inverse']);
		}
		if (in_array('related', $formData['left'])) {
			$formData['left'] = array();
		}
		if (in_array('related', $formData['right'])) {
			$formData['right'] = array();
		}

		// format params
		$relParams = array();
		foreach ($formData['params'] as $key => $p) {
			$p['name'] = trim($p['name']);
			if (!empty($p['name'])) {
				if ($p['type'] == 'options') {
					$p['options'] = trim(trim($p['options']),',');
					$p['options'] = explode(',', $p['options']);
					if (count($p['options']) < 2) {
						throw new BeditaException(__('For type options you have to define at least two options', true), $formData['params'][$key]);
					}
					$p['options'] = array_map('trim', $p['options']);
					$relParams[$p['name']] = $p['options'];
				} else {
					$relParams[] = $p['name'];
				}
			}
		}
		$formData['params'] = $relParams;
		$relToSave[$relName] = $formData;
		$relToSave[$relName]['hidden'] = (empty($relToSave[$relName]['hidden']))? false : true;

		$relations = Configure::read('objRelationType');
		if (empty($relations)) {
			$relations = array();
		}
		$relations = array_merge($relations, $relToSave);

		$cfg = array('objRelationType' => $relations);

		// write bedita.cfg.php
		$beditaCfgPath = CONFIGS . "bedita.cfg.php";
		BeLib::getObject("BeSystem")->writeConfigFile($beditaCfgPath, $cfg, true);
		// recaching configuration
		BeLib::getObject("BeConfigure")->cacheConfig();
		$this->userInfoMessage(__("Relation saved", true));
	}

	public function deleteCustomRelation() {
		$this->checkWriteModulePermission();
		if (empty($this->data['name'])) {
			throw new BeditaException(__('Missing relation name to delete', true));
		}

		$name = $this->data['name'];
		$relations = Configure::read('objRelationType');
		if (empty($relations[$name])) {
			throw new BeditaException(__('Missing relation to delete', true));
		}

		unset($relations[$name]);
		$cfg = array('objRelationType' => $relations);

		// write bedita.cfg.php
		$beditaCfgPath = CONFIGS . "bedita.cfg.php";
		BeLib::getObject("BeSystem")->writeConfigFile($beditaCfgPath, $cfg, true);
		// recaching configuration
		BeLib::getObject("BeConfigure")->cacheConfig();
		$this->userInfoMessage(__("Relation deleted", true));
	}

    protected function forward($action, $result) {
        $moduleRedirect = array(
            'deleteAllMailUnsent' => array(
                'OK' => '/admin/emailInfo',
                'ERROR' => '/admin/emailInfo'
            ),
            'deleteAllMailLogs' => array(
                'OK' => '/admin/emailLogs',
                'ERROR' => '/admin/emailLogs'
            ),
            'deleteMailJob' => array(
                'OK' => '/admin/emailInfo',
                'ERROR' => '/admin/emailInfo'
            ),
            'deleteMailLog' => array(
                'OK' => '/admin/emailLogs',
                'ERROR' => '/admin/emailLogs'
            ),
            'emptyFile' => array(
                'OK' => '/admin/systemLogs',
                'ERROR' => '/admin/systemLogs'
            ),
            'emptySystemLog' => array(
                'OK' => '/admin/systemLogs',
                'ERROR' => '/admin/systemLogs'
            ),
            'deleteEventLog' => array(
                'OK' => '/admin/systemEvents',
                'ERROR' => '/admin/systemEvents'
            ),
            'saveCustomProperties' => array(
                'OK' => '/admin/customproperties',
                'ERROR' => '/admin/customproperties'
            ),
            'deleteCustomProperties' => array(
                'OK' => '/admin/customproperties',
                'ERROR' => '/admin/customproperties'
            ),
            'plugModule' => array(
                'OK' => '/admin/pluginModules',
                'ERROR' => '/admin/pluginModules'
            ),
            'toggleModule' => array(
                'OK' => $this->referer(),
                'ERROR' => $this->referer()
            ),
            'unplugModule' => array(
                'OK' => '/admin/pluginModules',
                'ERROR' => '/admin/pluginModules'
            ),
            'enableAddon' => array(
                'OK' => '/admin/addons',
                'ERROR' => '/admin/addons'
            ),
            'disableAddon' => array(
                'OK' => '/admin/addons',
                'ERROR' => '/admin/addons'
            ),
            'updateAddon' => array(
                'OK' => '/admin/addons',
                'ERROR' => '/admin/addons'
            ),
            'saveConfig' => array(
                'OK' => '/admin/viewConfig',
                'ERROR' => '/admin/viewConfig'
            ),
            'testSmtp' => array(
                'OK' => '/admin/viewConfig',
                'ERROR' => '/admin/viewConfig'
            ),
            'saveCustomRelation' => array(
                'OK' => '/admin/customRelations',
                'ERROR' => '/admin/customRelations'
            ),
            'deleteCustomRelation' => array(
                'OK' => '/admin/customRelations',
                'ERROR' => '/admin/customRelations'
            )
        );
        return $this->moduleForward($action, $result, $moduleRedirect);
    }

}

?>