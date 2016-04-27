<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2010 ChannelWeb Srl, Chialab Srl
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
 * Module Model class
 *
 */
class Module extends BEAppModel {

	/**
	 * get a list of module plugin available (plugged and unplugged)
	 *
	 * @return array ("plugged" => array(), "unplugged" => array() )
	 */
	public function getPluginModules() {
		$pluggedModulesList = $this->find('list', array(
				'fields' => array('name', 'id'),
				'conditions' => array('module_type' => array('plugin', 'addon'))
			)
		);

		$pluginModules = array("plugged" => array(), "unplugged" => array());
		if(!file_exists(BEDITA_MODULES_PATH)) {
			throw new BeditaException(__("Missing plugins directory on filesystem", true) . " " . BEDITA_MODULES_PATH);
		}
		App::Import('Core', 'Folder');
		$folder = new Folder(BEDITA_MODULES_PATH);
		$plugins = $folder->read(true, true);
		foreach ($plugins[0] as $plugin) {
			if (file_exists(BEDITA_MODULES_PATH . DS . $plugin . DS . "config" . DS . "bedita_module_setup.php")) {
				include(BEDITA_MODULES_PATH . DS . $plugin . DS . "config" . DS . "bedita_module_setup.php");
				$moduleSetup["pluginPath"] = BEDITA_MODULES_PATH . DS . $plugin;
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
	public function plugModule($pluginName, array& $setup, $pluginPath=null) {
		if (empty($pluginName) || empty($setup)) {
			return false;
		}

		// check BEdita version compatibility
		if (empty($setup["BEditaMinVersion"])) {
			throw new BeditaException(__("Missing minimum BEdita version required to instal module" . " " . $pluginName, true));
		}
		preg_match('/^\d{1,}(\.\d){1,}/', Configure::read('version'), $matches);
		$beditaVersion = $matches[0];
		if ($setup["BEditaMinVersion"] > $beditaVersion) {
			throw new BeditaException(__($pluginName . " " . "require at least BEdita " . $setup["BEditaMinVersion"], true));
		}
		if (!empty($setup["BEditaMaxVersion"]) && $setup["BEditaMaxVersion"] < $beditaVersion) {
			throw new BeditaException(__($pluginName . " " . "is supported up to BEdita " . $setup["BEditaMaxVersion"], true));
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
		$data["Module"]["url"] = $pluginName;
		$data["Module"]["status"] = "on";
		$data["Module"]["module_type"] = (!empty($setup['type']))? $setup['type'] : 'plugin';
		$data["Module"]["priority"] = $this->field("priority", null, "priority DESC") + 1;
		if (!$this->save($data)) {
			throw new BeditaException(__("error saving module data", true));
		}
		$newModuleId = $this->id;

		if (!empty($setup["BEditaObjects"])) {
			if (!is_array($setup["BEditaObjects"])) {
				$setup["BEditaObjects"] = array($setup["BEditaObjects"]);
			}
			$beLib = BeLib::getInstance();
			$modelPaths = App::path('models');
			$pluginModelsPath = BEDITA_MODULES_PATH . DS  . $pluginName . DS . "models" . DS;
			if (!in_array($pluginModelsPath, $modelPaths)){
				App::build(array("models" => $pluginModelsPath));
			}

			$behaviorPaths = App::path('behaviors');
			$pluginBehaviorsPath = $pluginModelsPath ."behaviors";
			if (!in_array($pluginBehaviorsPath, $behaviorPaths)){
				App::build(array("behaviors" => $pluginBehaviorsPath));
			}

			// check db schema, create tables if needed
			$this->handlePluginSchema($pluginName, $setup, BEDITA_MODULES_PATH);

			$dirPath = BEDITA_MODULES_PATH . DS . $pluginName . DS . "models" . DS;

			$otModel = ClassRegistry::init("ObjectType");
			$ot_id = $otModel->newPluggedId();
			foreach ($setup["BEditaObjects"] as $modelName) {
				$objectType = Inflector::underscore($modelName);
				$filename = $objectType . ".php";
				if (!file_exists($dirPath . $filename)) {
					throw new BeditaException(__('Model file ' . $filename . ' not found', true));
				}

				if ($beLib->isFileNameUsed($filename, "models", array(BEDITA_MODULES_PATH . DS  . $pluginName . DS . "models" . DS))) {
					throw new BeditaException(__($filename . " is already used. Please change your file and model name", true));
				}

				if (!$beLib->isBeditaObjectType($modelName, $dirPath)) {
					throw new BeditaException(__($modelName . " doesn't seem to be a BEdita object. It has to extend BEAppObjectModel", true));
				}

				$obj = $otModel->find("count", array(
						"conditions" => array("name" => $objectType),
						"contain" => array()
					)
				);
				if ($obj == 0) {
					$model = ClassRegistry::init($pluginName . "." . $modelName);
					$objectTypeId = (!empty($model->objectTypeId)) ? $model->objectTypeId : null;
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
					$dataO["module_name"] = $pluginName;
					if (!$otModel->save($dataO)) {
						throw new BeditaException(__("error saving objectTypes", true));
					}
				}
			}
		} else if (!empty($setup["tables"])) {
			$this->handlePluginSchema($pluginName, $setup, BEDITA_MODULES_PATH);
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

		clearCache(null, 'models');
		clearCache(null, 'persistent');

		// recaching configuration
		BeLib::getObject("BeConfigure")->cacheConfig();

		return true;
	}

	private function createPluginSchema($pluginName, $pluginPath) {
		// load and check schema
		$schemaClass = Inflector::camelize($pluginName). "Schema";
		App::import('Model', 'CakeSchema');
		$schemaPath = $pluginPath . DS  . $pluginName . DS . "config". DS. "sql" . DS;
		$schemaFile = $schemaPath . "schema.php";
		if(!file_exists($schemaFile)) {
			throw new BeditaException(__("Missing schema file", true));
		}
		include($schemaFile);
		return new $schemaClass();
	}

	protected function handlePluginSchema($pluginName, array& $setup, $pluginPath) {
		if(empty($setup["tables"])) {
			return;
		}
		$schemaPath = $pluginPath . DS  . $pluginName . DS . "config". DS. "sql" . DS;

		$pluginSchema = $this->createPluginSchema($pluginName, $pluginPath);
		$pluginTables = $pluginSchema->tables;

		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$currentTables = $db->listSources();

		$beSchema = ClassRegistry::init("BeSchema");

		$found = false;
		$numTablesFound = 0;
		$tabsNotFound = "";
		foreach ($pluginTables as $tabName => $tabData) {
			if(in_array($tabName, $currentTables)) {
				$found = true;
				$numTablesFound++;

				$modelName = Inflector::camelize($tabName);
				$model = ClassRegistry::init($modelName);

				$tableMeta = $beSchema->tableMetaData($model, $db);

				// if fields do not match, error
				ksort($tabData);
				ksort($tableMeta);
				$diff1 = array_diff_key($tabData, $tableMeta);
				$diff2 = array_diff_key($tableMeta, $tabData);
                if(!empty($diff1) || !empty($diff2)) {
                    throw new BeditaException(__('Database schema conflict, table has different schema', true)
                        . ": " . $tabName, array_merge($diff1, $diff2));
                }
				ClassRegistry::removeObject($modelName);

			} else {
				$tabsNotFound .= " '" . $tabName . "'";
			}
		}
		if($found && $numTablesFound < count($pluginTables)) {
			throw new BeditaException(__("Some plugin tables are missing", true) . $tabsNotFound);
		}

		if(!$found) {
			// find sql schema for current driver
			$sqlSchema = $schemaPath . $db->config["driver"] . "_schema.sql";
			if(!file_exists($sqlSchema)) {
				throw new BeditaException(__("Database schema for current driver not found", true) . " [".$db->config["driver"]."]");
			}
			// execute script
			$beSchema->executeQuery($db, $sqlSchema);
			$db->cacheSources = false;
		}
	}


	/**
	 * unplug module
	 * delete row on modules table, all module objects and object type
	 * @param $id
	 * @param array $setup
	 * @return void
	 */
	public function unplugModule($id, array& $setup) {
		$name = $this->field("name", array("id" => $id));
		if (!$this->delete($id)) {
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

		// drop tables
		if (!empty($setup["tables"])) {
			$pluginTables = $setup["tables"];
			foreach (array_reverse($pluginTables) as $t) {
				$q = "DROP TABLE " . $t;
				// #CUSTOM QUERY
				$this->query($q);
			}
		}

		clearCache(null, 'models');
		clearCache(null, 'persistent');

		$pluginPath = BEDITA_MODULES_PATH . DS . $name . DS . "models" . DS;
		$appPaths = App::path('models');
		$res = array_search($pluginPath, $appPaths);
		if($res !== false) {
			unset($appPaths[$res]);
			App::build(array("models" => $appPaths), true);
		}

		// recaching configuration
		BeLib::getObject("BeConfigure")->cacheConfig();
	}

}


?>
