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
 * Translation properties manipulation
 *  
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeLangTextComponent extends Component {

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

	/**
	 * setup data for save
	 * 
	 * @param array $data
	 */
	public function setupForSave(&$data) {
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

	/**
	 * setup data for view
	 * 
	 * @param array $data
	 */
	public function setupForView(&$data) {
		$tmp = array() ;
		for($i=0; $i < count($data) ; $i++) {
			$item = &$data[$i] ;
			if(!isset($tmp[$item["name"]]))	$tmp[$item["name"]] = array() ;
			$tmp[$item["name"]][$item["lang"]] = @$item["text"];
		}
		$data = $tmp ;
	}

	/**
	 * setup LangText data for view
	 * 
	 * @param array $data
	 */
	public function setupForViewLangText(&$data) {
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
	public function setObjectLang(&$object, $lang, $status=array('on')) {
		$object["languages"] = array();
		if (!empty($object["LangText"]["status"])) {
			
			foreach ($object["LangText"]["status"] as $langAvailable => $statusLang) {
				
				if (in_array($statusLang, $status)) {
					// if main language substitute $object corresponding fields (not status) 
					if ($langAvailable == $lang) {
						
						foreach($object["LangText"] as $key => $value) {
							if (!is_numeric($key)) { 
								if (!empty($object[$key])) {
									if($key == "title") {
										$object["languages"][$object["lang"]][$key] = $object[$key];
									}
									if($key != "status" && !empty($object["LangText"][$key][$lang])) {
										$object[$key] = $object["LangText"][$key][$lang];
									}
								}
							}
						}
						$object["curr_lang"] = $lang; //displayed language != from original.... 
					// available languages
					} else {
						$object["languages"][$langAvailable] = array();
						foreach($object["LangText"] as $key => $value) {
							if ($key == "title") {
								$object["languages"][$langAvailable][$key] = $object["LangText"][$key][$langAvailable];
							}
						}
					}
				}
			}
		}
		unset($object["LangText"]);
	}
}
?>