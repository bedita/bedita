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
App::import('Model', 'ConnectionManager');
/**
 * BeConfigure class handle BEdita configuration 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

class BeConfigure {
	
	/**
	 * initialize BEdita static configuration setting cache and
	 * add plugged model and component path
	 * 
	 * @return unknown_type
	 */
	public function initConfig() {
		if (($cachedConfig = Cache::read('beConfig')) === false) {
			$cachedConfig = $this->cacheConfig();
		} else {
			$this->addModulesPaths($cachedConfig);
		}
	}
	
	/**
	 * cache object types array and module plugged configuration
	 * 
	 * @return array configuration cached 
	 */
	public function cacheConfig() {
		$requiredTables = array("modules", "object_types");
		if (!$this->tableExists($requiredTables)) {
			return;
		}
		$conf = Configure::getInstance();
		$configurations = array();
				
		if (file_exists(BEDITA_CORE_PATH . DS . 'plugins' . DS . 'addons' . DS . 'config' . DS . 'config.php')) {
			include BEDITA_CORE_PATH . DS . 'plugins' . DS . 'addons' . DS . 'config' . DS .'config.php';
		}
		if (defined("BEDITA_PLUGINS_PATH") && file_exists(BEDITA_PLUGINS_PATH . DS . 'addons' . DS . 'config' . DS . 'config.php')) {
			include BEDITA_PLUGINS_PATH . DS . 'addons' . DS . 'config' . DS . 'config.php';
		}

		$moduleModel = ClassRegistry::init("Module");
		$modules = $moduleModel->find("all", array(
				"conditions" => array("module_type" => "plugin")
			)
		);
		if (!empty($modules)) {
			$addPath = array();
			foreach ($modules as $m) {
				foreach ($conf->pluginPaths as $pluginPath) {
					$modulePath = $pluginPath . $m["Module"]["name"];
					if (is_dir($modulePath)) {
						if (file_exists($modulePath . DS . "config" . DS . "config.php")) {
							include $modulePath . DS . "config" . DS . "config.php";
						}
						$config["modules"][$m["Module"]["name"]] = array(
							"id" => $m["Module"]["id"],
							"label" => $m["Module"]["label"],
							"pluginPath" => $modulePath
						);
						break;
					}
				}
			}
		}
		
		$conf->plugged = array();
		if (!empty($config)) {
			$conf->plugged = $config;
		}
		$configurations["plugged"] = $conf->plugged;
		$this->addModulesPaths($configurations);
		
		$objectTypeModel = ClassRegistry::init("ObjectType");
		$ot = $objectTypeModel->find("all");
		if (!empty($ot)) {
			$configurations["objectTypes"] = array();
			foreach ($ot as $type) {
				$modelName = Inflector::camelize($type["ObjectType"]["name"]);
				$configurations["objectTypes"][$type["ObjectType"]["id"]] = $configurations["objectTypes"][$type["ObjectType"]["name"]] = array(
					"id" => $type["ObjectType"]["id"],
					"name" => $type["ObjectType"]["name"],
					"module_name" => $type["ObjectType"]["module_name"],
					"model" => $modelName
				);
				$objModel = ClassRegistry::init($modelName);
				if (!empty($objModel->objectTypesGroups)) {
					foreach($objModel->objectTypesGroups as $group) {
						$configurations["objectTypes"][$group]["id"][] = $type["ObjectType"]["id"];
					}
				}
			}			
			$conf->objectTypes = $configurations["objectTypes"];
		}
		
		Cache::write('beConfig', $configurations);
		
		return $configurations;
	}
	
	/**
	 * add models and components module plugin paths to BEdita core paths
	 * 
	 * @param array $cachedConfig configuration cached
	 */
	public function addModulesPaths(array $cachedConfig) {
		$conf = Configure::getInstance();
		if (!empty($cachedConfig["plugged"]["modules"])) {
			foreach ($cachedConfig["plugged"]["modules"] as $name => $m) {
				$conf->modelPaths[] = $m["pluginPath"] . DS . "models" . DS;
				$conf->componentPaths[] = $m["pluginPath"] . DS . "components" .DS;
			}
		}
		Configure::write($cachedConfig);
	}

	/**
	 * write in configuration the external authorization types supported
	 *
	 * @return array of authorization type
	 */
	public function setExtAuthTypes() {
		$beLib = BeLib::getInstance();
		$addons = $beLib->getAddonsPaths();
		$authTypes = array();
		if (!empty($addons)) {
			$folder = new Folder();
			foreach ($addons as $a) {
				if ($folder->cd($a . DS . "components")) {
					$list = $folder->read(true, true);
					if (!empty($list[1])) {
						foreach ($list[1] as $componentFile) {
							if (strstr($componentFile, "be_auth_")) {
								$componentFile = basename($componentFile, ".php");
								$componentClass = Inflector::camelize($componentFile);
								if (App::import("Component", $componentClass)) {
									$componentClass .= "Component";
									$authComp = new $componentClass();
									if (!$authComp->disabled) {
										$authTypes[] = str_replace("be_auth_", "", $componentFile);
									}
								}
							}
						}
					}
				}
			}
		}
		
		Configure::write("extAuthTypes", $authTypes);
		return $authTypes;
	}

	/**
	 * check if table/s exist in certain database connection
	 *
	 * @param mixed $tableName, table name or array of table names
	 * @param string $datasource, datasource to use
	 * @return boolean
	 */
	public function tableExists($tableName, $datasource="default") {
		$db = ConnectionManager::getDataSource($datasource);
		$tables = $db->listSources();
		if (!is_array($tableName)) {
			$tableName = array($tableName);
		}
		$intersect = array_intersect($tableName, $tables);
		return ($tableName === $intersect)? true : false;
	}
	
	public function loadPluginLocalConfig($pluginName) {
		$pluginPath = BeLib::getInstance()->getPluginPath($pluginName) . $pluginName;	
		
		if ( file_exists($pluginPath . DS . 'config' . DS . 'config_local.php' )){
			include $pluginPath . DS . 'config' . DS . 'config_local.php';
			if (!empty($config)) {
				Configure::write($config);
			}
		}
		
	}

}
?>