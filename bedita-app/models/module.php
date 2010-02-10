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
					if (!array_key_exists($plugin, $pluggedModulesList)) {
						$moduleSetup["pluginPath"] = $pluginsBasePath;
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
	
	public function plugModule($pluginName, array $setup=array()) {
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
			$maxid = $otModel->field("id", null, "id DESC");
			$ot_id = ($maxid < 1000)? 1000 : $maxid + 1;  
			foreach ($setup["BEditaObjects"] as $modelName) {
				$objectType = Inflector::underscore($modelName);
				$obj = $otModel->find("count", array(
						"conditions" => array("name" => $objectType),
						"contain" => array()
					)
				);
				if ($obj == 0) {
					$dataO["id"] = $ot_id++;
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
	
}


?>