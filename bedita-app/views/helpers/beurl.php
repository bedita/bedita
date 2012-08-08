<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
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
 * Url helper class
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeurlHelper extends AppHelper {
	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	var $helpers = array('Html','Javascript');
		
	/**
	* get current page url
	* 
	* @return string url
	*/
	public function here() {
		$newUrl = str_replace($this->Html->base, "", $this->Html->here) ;
		if($newUrl != "/") {
			$pos = strpos($newUrl,"/");
			if(!$pos || $pos > 0) 
				$newUrl = "/" . $newUrl;
		}
		return $newUrl ;
	}

	/**
	 * get url to here
	 * 
	 * @param mixed string|array $cleanFromFields, field or array of fields passed by name that you want to clean in url
	 * @return string url
	 */
	public function getUrl($cleanFromFields=null) {
		$paramsNamed = $this->params["named"];
		if (!empty($cleanFromFields)) {
			$paramsNamed = $this->cleanPassedArgs($cleanFromFields);
		}
		$data = array_merge($this->params["pass"], $paramsNamed);
		$url = Router::url($data);
		return $this->output($url);
	}
	
	/**
	 * add css and js scripts for modules
	 * 
	 * @return string html
	 */
	public function addModuleScripts() {
		$view = ClassRegistry::getObject('view');
		$moduleName = $view->getVar("moduleName");
		$conf = Configure::getInstance();
		$scriptsBasePath = APP . "webroot";
		$plugin = false;
		$output = "";
		if (!empty($conf->plugged["modules"])) {
			$modeRewrite = $conf->App['baseUrl'];
			foreach ($conf->plugged["modules"] as $name => $m) {
				$cssBase = "module_color";
				$webrootPath = $m["pluginPath"] . DS . "webroot";
				$name_ok = $name;
				if(!empty($modeRewrite)) {
					$name_ok = "index.php/" . $name;
				}
				if (file_exists($webrootPath . DS . "css" . DS . $cssBase . ".css")) {
					$output .= $this->Html->css("/" . $name_ok . "/css/" . $cssBase) . "\n";
				}
				if ($name == $moduleName) {
					$cssPath = $webrootPath . DS . "css" . DS . "module.css";
					$cssLink = "/" . $name_ok . "/css/module";
					$jsPath = $webrootPath . DS . "js" . DS . "module.js";
					$jsLink = "/" . $name_ok . "/js/module";
					$plugin = true;
				}
			}
		}
		if (!$plugin) {
			$cssPath = $scriptsBasePath . DS . "css" . DS . "module." . $moduleName . ".css";
			$cssLink = "module." . $moduleName;
			$jsPath = $scriptsBasePath . DS . "js" . DS . "module." . $moduleName . ".js";
			$jsLink = "module." . $moduleName;
		}
		
		if (file_exists($cssPath)) {
			$output .= $this->Html->css($cssLink) . "\n";
		}
		if (file_exists($jsPath)) {
			$output .= $this->Javascript->link($jsLink) . "\n";
		}
		
		return $this->output($output);
	}
	
	
	/**
	 * return array without params passed to the method
	 * 
	 * @param array $cleanFromFields
	 * @return array of params cleaned
	 */
	private function cleanPassedArgs($cleanFromFields) {
		$paramsNamed = $this->params["named"];
		if (!is_array($cleanFromFields)) {
			if (isset($paramsNamed[$cleanFromFields])) {
				unset($paramsNamed[$cleanFromFields]);
			}
		} else {
			foreach ($cleanFromFields as $field) {
				if (isset($paramsNamed[$field])) {
					unset($paramsNamed[$field]);
				}
			}
		}
		return $paramsNamed;
	}
	
}

?>