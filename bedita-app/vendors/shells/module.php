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

App::import("File", "BeLib", true, array(BEDITA_LIBS), "be_lib.php");
BeLib::getObject("BeConfigure")->initConfig();

/**
 * Newsletter shell: methods to import/export newsletter data (for example phplist filters), 
 * other newsletter related utilities
 * 
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class ModuleShell extends Shell {

	public function plug() {
		$op = (empty($this->params["name"]))? "list" : "name";
		$moduleModel = ClassRegistry::init("Module");
		$pluggedModules = $moduleModel->find("list", array(
				"fields" => array("id", "name"),
				"conditions" => array("type" => "plugin")
			)
		);
		
		$pluginPaths = Configure::getInstance()->pluginPaths;
		
		if ($op == "list") {
		
			$unpluggedModules = array();
			foreach ($pluginPaths as $pluginsBasePath) {
				$folder = new Folder($pluginsBasePath);
				$plugins = $folder->ls(true, true);
				foreach ($plugins[0] as $plugin) {
					if (file_exists($pluginsBasePath . $plugin . DS . "config" . DS . "bedita_module_setup.php") && !in_array($plugin, $pluggedModules)) {
						$unpluggedModules[] = $plugin;
					}
				}
			}
	
			if (empty($unpluggedModules)) {
				$this->out("No module to plug");
				return;
			}
			
			$this->out("Current unplugged modules on istance " . Configure::read("projectName") . ":");
			$this->out("");
			foreach ($unpluggedModules as $key => $um) {
				$this->out(++$key . ". " . $um);
			}
			$this->out("");
			$moduleToPlug = $this->in("Choose the module to plug. Digit the name or the corresponding number:");
			
			if (is_numeric($moduleToPlug) && !empty($unpluggedModules[$moduleToPlug-1])) {
				$moduleToPlug = $unpluggedModules[$moduleToPlug-1];
			}
			if (empty($moduleToPlug) || !in_array($moduleToPlug, $unpluggedModules)) {
				$this->out("Plugin doesn't exist");
				return;
			}
			
			$this->params["name"] = $moduleToPlug;
			$this->plug();
			
		} elseif ($op == "name") {
			$plugin = $this->params["name"];
			$pluginsBasePath = false;
			foreach ($pluginPaths as $pPath) {
				if (file_exists($pPath . $plugin . DS . "config" . DS . "bedita_module_setup.php") && !in_array($plugin, $pluggedModules)) {
					$pluginsBasePath = $pPath;	
				}
			}
			if (!$pluginsBasePath) {
				$this->out("Plugin doesn't exist");
				return;
			}
			
			if (in_array($plugin, $pluggedModules)) {
				$this->out("Module " . $plugin . " is already installed.");
				return;
			}
			
			include $pluginsBasePath . $plugin . DS . "config" . DS . "bedita_module_setup.php";
			$beditaVersion = Configure::read("majorVersion");
			if ($beditaVersion != $moduleSetup["BEditaVersion"]) {
				$this->out("");
				$this->out("WARNING: installed version and version required mismatched!");
				$this->out("BEdita version: " . $beditaVersion);
				$this->out("BEdita version required by " . $plugin . ": " . $moduleSetup["BEditaVersion"]);
				$command = $this->in("Do you want continue anyway?", array("yes", "no"), "no");
				if ($command != "yes") {
					$this->out("Bye");
					return;
				}
			}
			$this->out("");
			$this->out("You are about to plug in the module " . $plugin . " version " . $moduleSetup["version"]);
			$this->out("Module description: " . $moduleSetup["description"]);
			$this->out("");
			$command = $this->in("Do you wanto to proceed?", array("yes", "no"), "yes");
			if ($command != "yes") {
				$this->out("Bye");
				return;
			}
			
			if (!$moduleModel->plugModule($plugin, $moduleSetup)) {
				$this->out("Failed installing module");
				return;
			}
			
			$this->out("Plugin " . $plugin . " installed successfully");
		}
	}
	
	public function unplug() {
		
	}
	
	function help() {
        $this->out('Available functions:');
  		$this->out(' ');
        $this->out('0. plug: initialize a new BEdita module plugin');
  		$this->out('    Usage: plug [-list] [-name <module-plugin-name>]');
  		$this->out(' ');
  		$this->out("    -list \t list all pluggable module available (default)");
  		$this->out("    -name <module-plugin-name>   \t name of plugin you want to install");
  		$this->out(' ');
        $this->out('1. unplug: todo');
  		$this->out(' ');
	}
	
}

?>