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
				"conditions" => array("type" => "plugin")
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
					"module" => $type["ObjectType"]["module"],
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
}
?>