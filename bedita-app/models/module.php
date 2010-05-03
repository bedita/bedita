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
 * Module Model class
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class Module extends BEAppModel {
	
	/**
	 * get a list of module plugin available (plugged and unplugged)
	 * 
	 * @return array ("plugged" => array(), "unplugged" => array() )  
	 */
	public function getPluginModules() {
		$pluggedModulesList = $this->find("list", array(
				"fields" => array("name", "id"),
				"conditions" => array("type" => "plugin")
			)
		);
		
		$pluginPaths = Configure::getInstance()->pluginPaths;
		
		$pluginModules = array("plugged" => array(), "unplugged" => array());
		foreach ($pluginPaths as $pluginsBasePath) {
			$folder = new Folder($pluginsBasePath);
			$plugins = $folder->ls(true, true);
			foreach ($plugins[0] as $plugin) {
				if (file_exists($pluginsBasePath . $plugin . DS . "config" . DS . "bedita_module_setup.php")) {
					include($pluginsBasePath . $plugin . DS . "config" . DS . "bedita_module_setup.php");
					$moduleSetup["pluginPath"] = $pluginsBasePath;
					if (!array_key_exists($plugin, $pluggedModulesList)) {
						$moduleSetup["pluginName"] = $plugin;
						$pluginModules["unplugged"][] = $moduleSetup;
					} else {
						$mod = $this->find("first", array(
								"conditions" => array("id" => $pluggedModulesList[$plugin])
							)
						);
						$pluginModules["plugged"][] = array_merge($mod["Module"], array("info" => $moduleSetup));
					}
				}
			}
		}
		
		return $pluginModules;
	}
	
	/**
	 * plug a module 
	 * insert module, eventually insert new object types, set modify permission at administrator group
	 * 
	 * @param string $pluginName
	 * @param array $setup
	 * @return bool
	 */
	public function plugModule($pluginName, array $setup=array(), $pluginPath=null) {
		if (empty($pluginName) || empty($setup)) {
			return false;
		}
		$c = $this->find("count", array(
				"conditions" => array("name" => $pluginName)
			)
		);
		if ($c > 0) {
			throw new BeditaException(__("A module with name " . $pluginName . " already exist", true));
		}
		
		$data["Module"]["name"] = $pluginName;
		$data["Module"]["label"] = (!empty($setup["publicName"]))? $setup["publicName"] : $pluginName;
		$data["Module"]["path"] = $pluginName;
		$data["Module"]["status"] = "on";
		$data["Module"]["type"] = "plugin";
		$data["Module"]["priority"] = $this->field("priority", null, "priority DESC") + 1;
		if (!$this->save($data)) {
			throw new BeditaException(__("error saving module data", true));
		}
		$newModuleId = $this->id;
		
		if (!empty($setup["BEditaObjects"])) {
			if (!is_array($setup["BEditaObjects"])) {
				$setup["BEditaObjects"] = array($setup["BEditaObjects"]);
			}
			$otModel = ClassRegistry::init("ObjectType");
			$ot_id = $otModel->newPluggedId();  
			foreach ($setup["BEditaObjects"] as $modelName) {
				$objectType = Inflector::underscore($modelName);
				
				$beLib = BeLib::getInstance();
				if (empty($pluginPath)) {
					$pluginPath = $beLib->getPluginPath($pluginName);
				}
				
				$filename = $objectType . ".php";
				$dirPath = $pluginPath . $pluginName . DS . "models" . DS;
				
				if (!file_exists($dirPath . $filename)) {
					throw new BeditaException(__("File " . $filename . " doesn't find.", true));
				}
				
				if ($beLib->isFileNameUsed($filename, "model")) {
					throw new BeditaException(__($filename . " is already used. Please change your file and model name", true));
				}
				
				if (!$beLib->isBeditaObjectType($modelName, $dirPath)) {
					throw new BeditaException(__($modelName . " doesn't seem to be a BEdita object. It has to be extend BEAppObjectModel", true));
				}
				
				$obj = $otModel->find("count", array(
						"conditions" => array("name" => $objectType),
						"contain" => array()
					)
				);
				if ($obj == 0) {
					$model = ClassRegistry::init($pluginName . "." . $modelName);
					$objectTypeId = $model->objectTypeId;
					if(!empty($objectTypeId)) {
						$obj = $otModel->find("count", array(
								"conditions" => array("id" => $objectTypeId),
								"contain" => array()
							)
						);
						if ($obj > 0) {
							throw new BeditaException(__("objectTypeId " . $objectTypeId . " is already in use: change objectTypeId for model " . $modelName, true));
						}
					} else {
						$objectTypeId = $ot_id++;
					}
					$dataO["id"] = $objectTypeId;
					$dataO["name"] = $objectType;
					$dataO["module"] = $pluginName;
					if (!$otModel->save($dataO)) {
						throw new BeditaException(__("error saving objectTypes", true));
					}
				}
			}
		}
		
		// set admin permission
		$group_id = ClassRegistry::init("Group")->field("id", array("name" => "administrator"));
		$permMod = ClassRegistry::init("PermissionModule");
		$dataPM["module_id"] = $newModuleId;
		$dataPM["ugid"] = $group_id;
		$dataPM["switch"] = "group";
		$dataPM["flag"] = 3;
		if (!$permMod->save($dataPM)) {
			throw new BeditaException(__("Error saving admin permission", true));
		}
		
		// recaching configuration
		BeLib::getObject("BeConfigure")->cacheConfig();
		
		return true;
	}
	
	/**
	 * unplug module
	 * delete row on modules table, all module objects and object type
	 * @param $id
	 * @param array $setup
	 * @return unknown_type
	 */
	public function unplugModule($id, array $setup=array()) {
		if (!$this->del($id)) {
			throw new BeditaException(__("Error deleting module " . $setup["publicName"], true));
		}
		
		if (!empty($setup["BEditaObjects"])) {
			if (!is_array($setup["BEditaObjects"])) {
				$setup["BEditaObjects"] = array($setup["BEditaObjects"]);
			}
			$otModel = ClassRegistry::init("ObjectType");
			$beObject = ClassRegistry::init("BEObject");
			foreach ($setup["BEditaObjects"] as $modelName) {
				$objectType = Inflector::underscore($modelName);
				$otModel->purgeType($objectType);
			}
		}
		
		// recaching configuration
		BeLib::getObject("BeConfigure")->cacheConfig();
	}
	
}


?>