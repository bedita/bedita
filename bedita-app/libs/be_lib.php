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

if (!class_exists('ClassRegistry')) {
	App::import('Core', array('ClassRegistry'));
}

/**
 * BEdita libs class. Instantiate and put in the registry other classes
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */

class BeLib {
	
	public static function &getInstance() {
		static $instance = array();
		if (!$instance) {
			$instance[0] =& new BeLib();
		}
		return $instance[0];
	}
	
	/**
	 * return an instance of a class (by default search in libs dir)
	 * If class is not instantiated do it and put in CakePHP registry
	 * 
	 * @param string $name class name (file has to be underscorized MyClass => my_class.php)
	 * @param string or array $paths paths where search class file
	 * @return class instance
	 */
	public static function &getObject($name, $paths=BEDITA_LIBS) {
		if (!$libObject = ClassRegistry::getObject($name)) {
			if (!class_exists($name)) {
				$file = Inflector::underscore($name) . ".php";
				$paths = (is_array($paths))? $paths : array($paths);
				if (!App::import("File", $name, true, $paths, $file)) {
					return false;
				}
			}
			$libObject = new $name();
			ClassRegistry::addObject($name, $libObject);
		}
		return $libObject;
	}
	
	/**
	 * check if a class name is a BEdita object type
	 * 
	 * @param string $name the class name
	 * @param mixed $paths array of paths or string path where searching the class
	 * 					   leave empty to use ClassRegistry
	 * @return boolean
	 */
	public function isBeditaObjectType($name, $paths=null) {
		if (!$paths) {
			$classInstance = ClassRegistry::init($name);
		} else {
			$classInstance = $this->getObject($name, $paths);
		}
		if (!$classInstance) {
			return false;
		}
		$parents = class_parents($classInstance);
		if (empty($parents) || !in_array("BEAppObjectModel", $parents)) {
			return false;
		}
		return true;
	}
	
	/**
	 * check if a file name is already used in Configure::$type."Paths"
	 * 
	 * @param string $filename
	 * @param string $type see Configure::*Paths
	 * @param array of path to exclude from search
	 * @return boolean
	 */
	public function isFileNameUsed($filename, $type, $excludePaths=array()) {
		$conf = Configure::getInstance();
		$pathName = strtolower(Inflector::singularize($type)) . "Paths";
		if (!isset($conf->{$pathName})) {
			throw new BeditaException(__("No paths to search for " . $type, true));
		}
		$paths = array_diff($conf->{$pathName},$excludePaths);
		$folder = new Folder();
		foreach ($paths as $p) {
			$folder->cd($p);
			$ls = $folder->ls(true, true);
			if (!empty($ls[1]) && in_array($filename, $ls[1])) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * get plugin path from plugin name
	 * 
	 * @param string $pluginName
	 * @return string
	 */
	public function getPluginPath($pluginName) {
		$pluginPaths = Configure::getInstance()->pluginPaths;
		$folder = new Folder();
		foreach ($pluginPaths as $pluginsBasePath) {
			$folder->cd($pluginsBasePath);
			$ls = $folder->ls(true, true);
			if (!empty($ls[0]) && in_array($pluginName, $ls[0])) {
				return $pluginsBasePath;
			}
		}
		return false;
	}
	
	/**
	 * get array of addons base paths
	 * 
	 * @return array
	 */
	public function getAddonsPaths() {
		$addonsPaths = array();
		if (is_dir(BEDITA_CORE_PATH . DS . 'plugins'.DS.'addons')) {
			$addonsPaths[] = BEDITA_CORE_PATH . DS . 'plugins'.DS.'addons'; 
		}
		
		if (defined("BEDITA_PLUGINS_PATH") && is_dir(BEDITA_PLUGINS_PATH . DS.'addons')) {
			$addonsPaths[] = BEDITA_PLUGINS_PATH . DS.'addons';
		}
		return $addonsPaths;
	}
	
	/**
	 * return an array of available addons
	 * 
	 * @return array in this form
	 * 				array(
	 * 					"models" => array(
	 * 						"objectTypes" => array(
	 * 							"on" => array(
	 * 								0 => array(
	 * 									"model" => model name,
	 *								 	"file" => file name,
	 *									"type" => object type name,
	 *									"path" => path to
	 * 								), 
	 * 								1 => array()
	 * 								....
	 * 							),
	 * 							"off" => array(
	 * 								0 => array(
	 * 									like "on" array,
	 * 									"fileNameUsed" => true if file name is already used for model
	 * 								), 1 => array(...)), ...
	 * 						),
	 * 						"others" => array(
	 * 							0 => array(
	 * 								"file" => file name,
	 *								"path" => path to,
	 *								"fileNameUsed" => true if file name is already used for model
	 * 							)
	 * 						)
	 * 					),
	 * 					
	 * 					"components" => array(like "others" array),
	 * 					"helpers" => array(like "others" array),
	 * 				)
	 */
	public function getAddons() {
		$conf = Configure::getInstance();
		$addonsPaths = $this->getAddonsPaths();
		$addons = array();
		if (!empty($addonsPaths)) {
			$folder = new Folder();
			$items = array("models", "components", "helpers");
			foreach ($addonsPaths as $ap) {
				foreach ($items as $val) {
					$folder->cd($ap . DS . $val);
					$ls = $folder->ls(true, true);
					if ($val == "models") {
						foreach ($ls[1] as $modelFile) {
							$m = new File($ap . DS . $val . DS . $modelFile);
							$name = $m->name();
							$modelName = Inflector::camelize($name);
							if ($this->isBeditaObjectType($modelName, $ap . DS . $val)) {
								$ot = array(
									 	"model" => $modelName,
									 	"file" => $modelFile,
										"type" => $name,
										"path" => $ap . DS . $val
								);
								$used = $this->isFileNameUsed($modelFile, $val, array($ap . DS . $val));
								if (!empty($conf->objectTypes[$name]) && !$used) {
									$addons[$val]["objectTypes"]["on"][] = $ot; 	
								} else {
									$ot["fileNameUsed"] = $used;
									$addons[$val]["objectTypes"]["off"][] = $ot;
								}
							} else {
								$addons[$val]["others"][] = array(
									"file" => $modelFile,
									"path" => $ap . DS . $val,
									"fileNameUsed" => $this->isFileNameUsed($modelFile, $val, array($ap . DS . $val)) 
								);
							}
						}
					} else {
						foreach ($ls[1] as $addonFile) {
							$addons[$val][] = array(
								"file" => $addonFile,
								"path" => $ap . DS . $val,
								"fileNameUsed" => $this->isFileNameUsed($addonFile, $val, array($ap . DS . $val)) 
							);
						}
					}
				}
			}
		}
		
		return $addons;
	}
	
}

?>