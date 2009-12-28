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
class Module extends AppModel {
	
	public function plugModule($pluginName, array $setup=array()) {
		if (empty($pluginName) || empty($setup)) {
			return false;
		}
		$c = $this->find("count", array(
				"conditions" => array("name" => $pluginName)
			)
		);
		if ($c > 0) {
			return false;
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
		
		if (!empty($setup["BeditaObjects"])) {
			if (!is_array($setup["BeditaObjects"])) {
				$setup["BeditaObjects"] = array($setup["BeditaObjects"]);
			}
			$otModel = ClassRegistry::init("ObjectType");
			$maxid = $otModel->field("id", null, "id DESC");
			$ot_id = ($maxid < 1000)? 1000 : $maxid + 1;  
			foreach ($setup["BeditaObjects"] as $modelName) {
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