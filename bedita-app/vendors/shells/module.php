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
 * $Id: bedita.php 2015 2009-05-29 14:18:06Z dante $
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
		
		$pluginsBasePath = BEDITA_CORE_PATH . DS . "plugins";
		
		if ($op == "list") {
		
			$unpluggedModules = array();
			$folder = new Folder($pluginsBasePath);
			$plugins = $folder->ls(true, true);
			foreach ($plugins[0] as $plugin) {
				if (file_exists($pluginsBasePath . DS . $plugin . DS . "config" . DS . "bedita_module_setup.php") && !in_array($plugin, $pluggedModules)) {
					$unpluggedModules[] = $plugin;
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
			if (!file_exists($pluginsBasePath . DS . $plugin . DS . "config" . DS . "bedita_module_setup.php") && !in_array($plugin, $pluggedModules)) {
				$this->out("Plugin doesn't exist");
				return;
			}
			if (in_array($plugin, $pluggedModules)) {
				$this->out("Module " . $plugin . " is already installed.");
				return;
			}
			
			include $pluginsBasePath . DS . $plugin . DS . "config" . DS . "bedita_module_setup.php";
			$this->out("");
			$this->out("You are about to plug in the module " . $plugin . " version " . $moduleSetup["version"]);
			
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
	
}

?>