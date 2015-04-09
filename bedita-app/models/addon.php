<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008-2012 ChannelWeb Srl, Chialab Srl
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
 * Handle BEdita addons (models, components, etc,..) to enable/disable it and so on
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class Addon extends AppModel {
    
	public $useTable = false;
	
	/**
	 * return an array of available addons
	 * 
	 * @return array in this form
	 * 				array(
	 * 					"models" => array(
	 * 						"objectTypes" => array(
	 * 							"on" => array(
	 * 								0 => array(
	 * 									"name" => class name,
	 *								 	"file" => file name,
	 *									"objectType" => object type name,
	 *									"path" => path where to find addon file available,
	 *									"enabledPath" => path where to find addon file enabled,
	 *									"type" => "models",
	 *									"fileNameUsed" => true if file name is already used for model
	 * 								), 
	 * 								1 => array()
	 * 								....
	 * 							),
	 * 							"off" => array(
	 * 								0 => array(
	 * 									like "on" array
	 * 								), 1 => array(...)), ...
	 * 						),
	 * 						"others" => array(
	 * 							"on" => array(
	 * 								0 => array(
	 * 									"name" => class name,
	 *								 	"file" => file name,
	 *									"objectType" => object type name,
	 *									"path" => path where to find addon file available,
	 *									"enabledPath" => path where to find addon file enabled,
	 *									"type" => "models",
	 *									"fileNameUsed" => true if file name is already used for model
	 * 								), 
	 * 								1 => array()
	 * 								....
	 * 							),
	 * 							"off" => array(
	 * 								0 => array(
	 * 									like "on" array
	 * 								), 1 => array(...)), ...
	 * 						)
	 * 					),
	 * 					
	 * 					"components" => array(like "others" array),
	 * 					"helpers" => array(like "others" array),
	 *					"behaviors" => array(like "others" array),
	 * 				)
	 */
	public function getAddons() {
		$conf = Configure::getInstance();
		$addons = array();
		$folder = new Folder();
		$items = array("models", "components", "helpers", "models" . DS . "behaviors");
		$Belib = BElib::getInstance();
		foreach ($items as $val) {
			if ($folder->cd(BEDITA_ADDONS_PATH . DS . $val)) {
				$ls = $folder->read(true, true);
				foreach ($ls[1] as $addonFileName) {
					$addonFile = new File(BEDITA_ADDONS_PATH . DS . $val . DS . $addonFileName);
					if ($addonFile->ext() == "php") {
						$name = $addonFile->name();
						$addonName = Inflector::camelize($name);
						$type = (strstr($val, "behaviors"))? "behaviors" : $val;
						
						$addonItem = array(
							"name" => $addonName,
							"file" => $addonFileName,
							"path" => BEDITA_ADDONS_PATH . DS . $val,
							"enabledPath" => BEDITA_ADDONS_PATH . DS . $val . DS . "enabled",
							"type" => $type,
							"update" => false
						);
						
						$alreadyUsed = $Belib->isFileNameUsed($addonFileName, $type, array(BEDITA_ADDONS_PATH . DS . $val . DS . "enabled" . DS));
						$addonItem["fileNameUsed"] = $alreadyUsed;
						
						// check if addon file enabled has to be updated
						$addonFileEnabled = new File(BEDITA_ADDONS_PATH . DS . $val . DS . "enabled" . DS . $addonFileName);
						$isEnabled = $addonFileEnabled->exists();
						if ($isEnabled) {
							if ($addonFile->lastChange() > $addonFileEnabled->lastChange() && $addonFile->md5() != $addonFileEnabled->md5()) {
								$addonItem["update"] = true;
							}
						}
						
						if ($type == "models") {
							if ($Belib->isBeditaObjectType($addonName, BEDITA_ADDONS_PATH . DS . $val)) {
								$addonItem["objectType"] = $name;
								if (!empty($conf->objectTypes[$name]) && !$alreadyUsed && $isEnabled) {
									$addons[$type]["objectTypes"]["on"][] = $addonItem;
								} else {
									$addons[$type]["objectTypes"]["off"][] = $addonItem;
								}
							} else {
								if ($isEnabled) {
									$addons[$type]["others"]["on"][] = $addonItem;
								} else {
									$addons[$type]["others"]["off"][] = $addonItem;
								}
							}
						} else {
							if ($isEnabled) {
								$addons[$type]["on"][] = $addonItem;
							} else {
								$addons[$type]["off"][] = $addonItem;
							}
						}
					}
				}
				
			}
		}
		return $addons;
	}
	
	/**
	 * enable addon copying the addon file in the related enabled folder.
	 * If addon is a BEdita object type a row on object_types table is created
	 * 
	 * @param string $fileName, addon file name
	 * @param string $addonType, the type of addon, i.e. models, helpers, components, ... 
	 * @throws BeditaException 
	 */
	public function enable($fileName, $addonType) {
		if (empty($fileName) || empty($addonType)) {
			throw new BeditaException(__("Missing mandatory data"), "file name and/or addon type");
		}
		
		$filePath = $this->getFolderByType($addonType);
		if (empty($filePath)) {
			throw new BeditaException($addonType . " " . __("addon folder doesn't found", true));
		}
		$filePath .= DS . $fileName;
		
		$addonFile = new File($filePath);
		if (!$addonFile->exists()) {
			throw new BeditaException($filePath . " " . __("doesn't found", true));
		}
		$addonFolder = $addonFile->Folder()->path;		
		$addonClassName = Inflector::camelize($addonFile->name());
		$BeLib = BeLib::getInstance();
		
		// BEdita object type
		if ($BeLib->isBeditaObjectType($addonClassName, $addonFolder)) {
			$model = $BeLib->getObject($addonClassName, $addonFolder);
			$data["name"] = $addonFile->name();
			if (!empty($model->module)) {
				$data["module_name"] = $model->module;
			}
			$ObjectType = ClassRegistry::init("ObjectType");
			$data["id"] = $ObjectType->newPluggedId();
			if (!$ObjectType->save($data)) {
				throw new BeditaException(__("Error saving object type", true));
			}
		}
	 	
		$enabledPath =  $this->getEnabledFolderByType($addonType) . DS . $addonFile->name;
		if (!$addonFile->copy($enabledPath)) {
			if (!empty($data["id"])) {
				$ObjectType->delete($data["id"]);
			}
			throw new BeditaException(__("Error copying addon to enabled folder", true), array("file path" => $filePath, "enabled path" => $enabledPath));
		}
		
	 	BeLib::getObject("BeConfigure")->cacheConfig();
	}
	
	/**
	 * disable addon deleting the addon file from the related enabled folder.
	 * If addon is a BEdita object the row on object_types table and all objects of that type are removed
	 * 
	 * @param string $fileName, addon file name
	 * @param string $addonType, the type of addon, i.e. models, helpers, components, ... 
	 * @throws BeditaException 
	 */
	public function disable($fileName, $addonType) {
		if (empty($fileName) || empty($addonType)) {
			throw new BeditaException(__("Missing mandatory data"), "file name and/or addon type");
		}
		$enabledPath = $this->getEnabledFolderByType($addonType) .  DS . $fileName;		
		
		$addonFile = new File($enabledPath);
		$addonClassName = Inflector::camelize($addonFile->name());
		$BeLib = BeLib::getInstance();
		
		// BEdita object type
		if ($BeLib->isBeditaObjectType($addonClassName, $addonFile->Folder()->path)) {
			$ObjectType = ClassRegistry::init("ObjectType");
			$ObjectType->purgeType($addonFile->name());
		}
		
		// delete addon file to respective enabled folder
		if (!$addonFile->delete()) {
			throw new BeditaException(__("Error deleting addon to enabled folder", true), array("file path" => $filePath, "enabled path" => $enabledPath));
		}
		
		BeLib::getObject("BeConfigure")->cacheConfig();
	}
	
	/**
	 * update addon overriding the enabled addon file
	 * 
	 * @param string $fileName, addon file name
	 * @param string $addonType, the type of addon, i.e. models, helpers, components, ... 
	 * @throws BeditaException 
	 */
	public function update($fileName, $addonType) {
		if (empty($fileName) || empty($addonType)) {
			throw new BeditaException(__("Missing mandatory data"), "file name and/or addon type");
		}
		
		$filePath = $this->getFolderByType($addonType);
		if (empty($filePath)) {
			throw new BeditaException($addonType . " " . __("addon folder doesn't found", true));
		}
		$filePath .= DS . $fileName;
		
		$addonFile = new File($filePath);
		if (!$addonFile->exists()) {
			throw new BeditaException($filePath . " " . __("doesn't found", true));
		}
		
		$enabledPath =  $this->getEnabledFolderByType($addonType) . DS . $addonFile->name;
		if (!$addonFile->copy($enabledPath)) {
			throw new BeditaException(__("Error copying addon to enabled folder", true), array("file path" => $filePath, "enabled path" => $enabledPath));
		}
	}


	/**
	 * get an array with folders on which $fileName is found starting from BEDITA_ADDONS_PATH
	 * 
	 * @param string $fileName, the file name
	 * @return array 
	 */
	public function getFolderByFile($fileName) {
		$Folder = new Folder(BEDITA_ADDONS_PATH);
		return $Folder->findRecursive($fileName);
	}
	
	/**
	 * get the folder relative to an addon
	 * 
	 * @param string $addonType, the addon type i.e. model, helper, component,... 
	 * @return mixed, the folder string or false if folder doesn't found 
	 */
	public function getFolderByType($addonType) {
        $addonType = Inflector::pluralize($addonType);
        switch ($addonType) {
            case 'behaviors':
                $addonType = 'models' . DS . 'behaviors';
                break;
        }
		$path = BEDITA_ADDONS_PATH . DS . $addonType;
		if (!BeLib::getObject("BeSystem")->checkAppDirPresence($path)) {
			$path = false;
		}
		return $path;
	}
	
	/**
	 * get the "enabled" folder relative to an addon
	 * 
	 * @param string $addonType, the addon type i.e. model, helper, component,... 
	 * @return mixed, the folder string or false if folder doesn't found 
	 */
	public function getEnabledFolderByType($addonType) {
		$addonPath = $this->getFolderByType($addonType);
		if (!$addonPath) {
			return false;
		}
		$enabledPath = $addonPath . DS . "enabled";
		if (!BeLib::getObject("BeSystem")->checkAppDirPresence($enabledPath)) {
			$enabledPath = false;
		}
		return $enabledPath;
	}
	
}
?>
