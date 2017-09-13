<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008-2015 ChannelWeb Srl, Chialab Srl
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

require_once 'bedita_base.php';

/**
 * Module shell: methods to plug/unplug/list modules
 */
class ModuleShell extends BeditaBaseShell {

    /**
     * Overrides base startup(), don't call initConfig...
     * @see BeditaBaseShell::startup()
     */
    function startup() {
        Configure::write('debug', 1);
    }

    public function plug($plugin = null) {
        $op = (empty($this->params["name"])) ? "list" : "name";
        $moduleModel = ClassRegistry::init("Module");
        $pluggedModules = $moduleModel->find("list", array(
            "fields" => array(
                "id",
                "name"
            ),
            "conditions" => array(
                "module_type" => "plugin"
            )
        ));
        $pluginPaths = App::path('plugins');
        
        if (empty($plugin)) {
            $plugin = $this->mandatoryArgument('name', 'use -name option');
        }
        $pluginsBasePath = false;
        if (! empty($pluginPaths)) {
            foreach ($pluginPaths as $pPath) {
                if (file_exists($pPath . $plugin . DS . 'config' . DS . 'bedita_module_setup.php') && ! in_array($plugin, $pluggedModules)) {
                    $pluginsBasePath = $pPath;
                }
            }
        }
        if (! $pluginsBasePath) {
            $this->out("Plugin doesn't exist");
            return;
        }

        if (in_array($plugin, $pluggedModules)) {
            $this->out("Module " . $plugin . " is already installed.");
            return;
        }

        include $pluginsBasePath . $plugin . DS . 'config' . DS . 'bedita_module_setup.php';
        $beditaVersion = Configure::read('version');
        if (empty($moduleSetup['BEditaMinVersion'])) {
            $this->out('');
            $this->out('WARNING: mandatory "BEditaMinVersion" parameter missing in module config - unable to plug!');
            return;
        }
        if ($beditaVersion < $moduleSetup['BEditaMinVersion']) {
            $this->out('');
            $this->out("WARNING: installed version and version required mismatched!");
            $this->out('BEdita version: ' . $beditaVersion);
            $this->out('Min BEdita version required by ' . $plugin . ': ' . $moduleSetup['BEditaMinVersion']);
            $command = $this->in('Do you want continue anyway?', array(
                'y',
                'n'
            ), 'n');
            if ($command != 'y') {
                $this->out('Bye');
                return;
            }
        }
        $this->out('');
        $this->out('You are about to plug in the module ' . $plugin . ' version ' . $moduleSetup['version']);
        $this->out('Module description: ' . $moduleSetup['description']);
        $this->out('');
        $command = $this->in('Do you wanto to proceed?', array(
            'y',
            'n'
        ), 'y');
        if ($command != 'y') {
            $this->out('Bye');
            return;
        }

        if (! $moduleModel->plugModule($plugin, $moduleSetup)) {
            $this->out('Module install failed!');
            return;
        }

        $this->out('Plugin ' . $plugin . ' installed successfully');
    }

    public function show() {
        $moduleModel = ClassRegistry::init('Module');
        $pluggedModules = $moduleModel->find('list', array(
            'fields' => array('id', 'name'),
            'conditions' => array('module_type' => array('plugin', 'addon'))
            )
        );
        $this->hr();
        $this->out('Plugged modules: ');
        $this->out('');
        if (empty($pluggedModules)) {
            $this->out('No module plugged');
        } else {
            foreach ($pluggedModules as $key => $mod) {
                $this->out('  ' . $mod);
            }
        }
        $this->hr();
        $this->out('');

        $pluginPaths = App::path('plugins');
        $unpluggedModules = array();
        if (! empty($pluginPaths)) {
            foreach ($pluginPaths as $pluginsBasePath) {
                $folder = new Folder($pluginsBasePath);
                $plugins = $folder->read(true, true);
                foreach ($plugins[0] as $plugin) {
                    if (file_exists($pluginsBasePath . $plugin . DS . 'config' . DS . 'bedita_module_setup.php') && ! in_array($plugin, $pluggedModules)) {
                        $unpluggedModules[] = $plugin;
                    }
                }
            }
        }

        if (empty($unpluggedModules)) {
            $this->out('No module to plug');
            return;
        }

        $this->out('Available unplugged modules:');
        $this->out('');
        foreach ($unpluggedModules as $key => $um) {
            $this->out(++$key . '. ' . $um);
        }
        $this->out('');
        $moduleToPlug = $this->in('Choose module to plug. Digit name or corresponding number (enter to quit):');
        if (empty($moduleToPlug)) {
            $this->out('Bye');
            return;
        }
        if (is_numeric($moduleToPlug) && !empty($unpluggedModules[$moduleToPlug-1])) {
            $moduleToPlug = $unpluggedModules[$moduleToPlug-1];
        }
        if (!in_array($moduleToPlug, $unpluggedModules)) {
            $this->out("Plugin doesn't exist");
            return;
        }
        $this->plug($moduleToPlug);
    }

    public function unplug() {
        $name = $this->mandatoryArgument('name', 'use -name option');
        $moduleModel = ClassRegistry::init('Module');
        $dirName = BEDITA_MODULES_PATH . DS . $name;
        if (!file_exists($dirName)) {
            throw new BeditaException(__('plugin folder not found ' . $dirName, true));
        }
        $fileName = $dirName . DS . 'config' . DS . 'bedita_module_setup.php';
        if (!file_exists($fileName)) {
            throw new BeditaException(__('config/bedita_module_setup.php not found', true));
        }
        include($fileName);
        $id = $moduleModel->field('id', array('name' => $name));
        if (empty($id)) {
            $this->out('Module ' . $name . ' not found');
            return;
        }
        $moduleModel->unplugModule($id, $moduleSetup);
        $this->out('Module ' . $name . ' unplugged');
    }

    public function status() {
        $name = $this->mandatoryArgument('name', 'use -name option');
        $moduleModel = ClassRegistry::init('Module');
        $status = $moduleModel->field('status', array('name' => $name));
        if (empty($status)) {
            $this->out('Module ' . $name . ' not found');
            return;
        }
        $newStatus = ($status == 'on') ? 'off' : 'on';
        $confirm = $this->in('Changing "' . $name . '" status from "' . 
            $status . '" to "' . $newStatus . '" confirm? [y/n]' );
        if (empty($confirm) || strtolower($confirm) !== 'y') {
            $this->out('Module status unchanged');
            return;
        }
        $id = $moduleModel->field('id', array('name' => $name));
        $moduleModel->id = $id;
        $moduleModel->saveField('status', $newStatus);
        $this->out('Module status changed');
    }

	private function findPluginPath($pluginName) {
		$res = null;
		$pluginPaths = App::pluginPath($pluginName);

        if(file_exists($pluginPaths . DS . "config")) {
			$res = $pluginPaths;
		}
		return $res;
	}	


	public function schema() {
		$pluginName = !empty($this->params['name']) ? $this->params['name'] : null;
		$pluginPath = $this->findPluginPath($pluginName);
		if($pluginPath == null) {
			$this->out("Plugin $pluginName not found");
			return;
		}

		$configPath = $pluginPath . DS . "config" . DS;
		$setupFile =  $configPath . "bedita_module_setup.php";
		if(!file_exists($setupFile)) {
			$this->out("Plugin setup file for $pluginName not found");
			return;
		}
		include($setupFile);
		if(empty($moduleSetup["tables"])) {
			$this->out("No tables defined for plugin $pluginName");
			return;
		}

        App::import('Core','ConnectionManager');
		$db =& ConnectionManager::getDataSource("default");
		$options = array();
		$tables = $moduleSetup["tables"];
		$beSchema = ClassRegistry::init("BeSchema");
		$conf = Configure::getInstance();
		
		$modelPaths = App::path('models');
		if (!in_array($pluginPath . DS . "model" . DS, $modelPaths)){
			App::build(array("models" => $pluginPath . DS . "model" . DS));
		}
		
		foreach ($tables as $t) {
			$modelName = Inflector::camelize($t);
			$model = ClassRegistry::init($modelName);
			$options["tables"][$t] = $beSchema->tableMetadata($model, $db);
			ClassRegistry::removeObject($modelName);
		}
		
		$schemaFile = $configPath . "sql". DS . "schema.php";
		$skip = false;		
		if(file_exists($schemaFile)) {
			$command = $this->in("Schema file $schemaFile exists. Overwrite?", array("y", "n"), "y");
			if ($command == "n") {
				$skip = true;
				$this->out("Skipping schema file generation");
			}
		}
		
		if(!$skip) {		
			$this->out("Creating schema file: $schemaFile");
			$name = Inflector::camelize($pluginName);
			$options['name'] = $name;
			$options['path'] = $configPath . "sql";
			$beSchema->path = $options['path'];
			$beSchema->write($options);
		}
		require_once($schemaFile);
		$schemaName = $name . "Schema";
		$schema = new $schemaName();

		// sql schema
		$sqlSchema = $configPath . "sql". DS . $db->config["driver"] . "_schema.sql";
		$skip = false;		
		if(file_exists($sqlSchema)) {
			$command = $this->in("Schema file $sqlSchema exists. Overwrite?", array("y", "n"), "y");
			if ($command == "n") {
				$skip = true;
				$this->out("Skipping schema file generation");
			}
		}
		
		if(!$skip) {		
			$this->out("Creating schema file: $sqlSchema");		
			$contents = "#" . $schema->name . " sql generated on: " . date('Y-m-d H:i:s') . " : " . time() . "\n\n";
			$contents .= $db->dropSchema($schema) . "\n\n". $db->createSchema($schema);
			$file = new File($sqlSchema, true);
			$file->write($contents);
		}
		$this->out("Done");
	}

    function help() {
        $this->out('Available functions:');
        $this->out(' ');
        $this->out('0. plug: plug a new module plugin');
        $this->out('    Usage: plug -name <module-plugin-name>');
        $this->out(' ');
        $this->out("    -name <module-plugin-name>   \t name of plugin you want to install");
        $this->out(' ');
        $this->out('1. unplug: unplug module');
        $this->out('    Usage: unplug -name <module-plugin-name>');
        $this->out(' ');
        $this->out("    -name <module-plugin-name>   \t plugin name to unplug");
        $this->out(' ');
        $this->out('2. schema: generate schema files for a plugin');
        $this->out('    Usage: schema [-list] [-name <module-plugin-name>]');
        $this->out(' ');
        $this->out("    -name <module-plugin-name>   \t plugin name");
        $this->out(' ');
        $this->out('3. show: list all available modules and install selected plugin');
        $this->out(' ');
        $this->out('4. status: change module status');
        $this->out('    Usage: status -name <module-plugin-name>');
        $this->out(' ');
        $this->out("    -name <module-plugin-name>   \t plugin name to activate/deactivate");
        $this->out(' ');
    }

}

?>