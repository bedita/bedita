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
 * Translation properties manipulation
 *  
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeLangTextComponent extends Object {

	var $controller = null ;
	var $uses = array('LangText');

	function __construct() {
		foreach ($this->uses as $model) {
			if(!class_exists($model))
				App::import('Model', $model) ;
			$this->{$model} = new $model() ;
		}
	} 

	function startup(&$controller) {
		$this->controller = $controller;
	}

	function setupForSave(&$data) {
		if(!@count($data)) return ;
		$translation = array();
		foreach($data as $lang => $attributes) {
			foreach($attributes as $attribute => $value) {
				if($attribute != 'type' && $value != '') {
					$formatted = array() ;
					$formatted['lang'] = $lang ;
					$formatted['name'] = $attribute ;
					$formatted['text'] = $value ;
					$translation[]=$formatted;
				}
			}
		}
		$data = $translation ;
	}

	function setupForView(&$data) {
		$tmp = array() ;
		for($i=0; $i < count($data) ; $i++) {
			$item = &$data[$i] ;
			if(!isset($tmp[$item["name"]]))	$tmp[$item["name"]] = array() ;
			$tmp[$item["name"]][$item["lang"]] = @$item["text"];
		}
		$data = $tmp ;
	}
	
	function setupForViewLangText(&$data) {
		$tmp = array() ;
		for($i=0; $i < count($data) ; $i++) {
			$item = &$data[$i]['LangText'] ;
			if(!isset($tmp[$item["name"]]))	$tmp[$item["name"]] = array() ;
			$tmp[$item["name"]] = @$item["text"];
			$tmp['id'][$item["name"]]=$item['id'];
		}
		$data = $tmp ;
	}
	
	/**
	 * used in frontend_controller
	 * Maps object available languages 
	 *
	 * @param Object $object object to map 
	 * @param string $lang, current frontend language 
	 * @param array $status, status for languages showed
	 */
	function setObjectLang(&$object, $lang, $status=array('on')) {
		$object["languages"] = array();
		if (!empty($object["LangText"]["status"])) {
			
			foreach ($object["LangText"]["status"] as $langAvailable => $statusLang) {
				
				// main language
				if ($langAvailable == $lang) {
					
					foreach($object["LangText"] as $key => $value) {
						if (!is_numeric($key)) { 
							if (!empty($object[$key]) && $key == "title")
								$object["languages"][$object["lang"]][$key] = $object[$key]; 
							
							$object[$key] = $object["LangText"][$key][$lang];
						}
					}
				// avaible languages
				} elseif (in_array($statusLang, $status)) {
				
					$object["languages"][$langAvailable] = array();
					foreach($object["LangText"] as $key => $value) {
						if ($key == "title") {
							$object["languages"][$langAvailable][$key] = $object["LangText"][$key][$langAvailable];
						}
					}
				}
			}
			
			unset($object["LangText"]);
		} elseif (empty($object["LangText"])) {
			unset($object["LangText"]);
		}
		
	}
}
?>