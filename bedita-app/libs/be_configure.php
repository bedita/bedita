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
				
		if (file_exists(BEDITA_ADDONS_PATH . DS . 'config' . DS . 'config.php')) {
			include BEDITA_ADDONS_PATH . DS . 'config' . DS . 'config.php';
		}

		$moduleModel = ClassRegistry::init("Module");
		$modules = $moduleModel->find("all", array(
				"conditions" => array("module_type" => "plugin")
			)
		);
		if (!empty($modules)) {
			$pluginConfig = array();
			foreach ($modules as $m) {
				$modulePath = BEDITA_MODULES_PATH . DS . $m["Module"]["name"];
				if (is_dir($modulePath)) {
					if (file_exists($modulePath . DS . "config" . DS . "config.php")) {
						include $modulePath . DS . "config" . DS . "config.php";
						if (!empty($config["objRelationType"])) {
							$pluginConfig["objRelationType"] = (empty($pluginConfig["objRelationType"]))? $config["objRelationType"] : array_merge($pluginConfig["objRelationType"], $config["objRelationType"]);
						}
					}
					$pluginConfig["modules"][$m["Module"]["name"]] = array(
						"id" => $m["Module"]["id"],
						"label" => $m["Module"]["label"],
						"pluginPath" => $modulePath
					);
				}
			}
		}
		
		$conf->plugged = array();
		if (!empty($pluginConfig)) {
			$conf->plugged = $pluginConfig;
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
		if (!empty($cachedConfig["plugged"]["modules"])) {
			$additionalPaths["models"] = array();
			$additionalPaths["components"] = array();
			foreach ($cachedConfig["plugged"]["modules"] as $name => $m) {
				$additionalPaths["models"][] = $m["pluginPath"] . DS . "models" . DS;
				$additionalPaths["components"][] = $m["pluginPath"] . DS . "components" .DS;
			}
			App::build($additionalPaths);
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
		$authTypes = array();
		$folder = new Folder();
		if ($folder->cd(BEDITA_ADDONS_PATH . DS . "components")) {
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
	
	/**
	 * load local configuration of module plugged
	 * file has to be named config_local.php and has to be in config folder of plugin
	 * 
	 * @param string $pluginName
	 */
	public function loadPluginLocalConfig($pluginName) {
		$pluginPath = BEDITA_MODULES_PATH . DS . $pluginName;
		if ( file_exists($pluginPath . DS . 'config' . DS . 'config_local.php' )){
			include $pluginPath . DS . 'config' . DS . 'config_local.php';
			if (!empty($config)) {
				Configure::write($config);
			}
		}
	}

}
?>
