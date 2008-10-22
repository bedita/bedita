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
					if(strlen($value) <= 255)
						$formatted['text'] = $value ;
					else
						$formatted['long_text'] = $value ;
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
			$tmp[$item["name"]][$item["lang"]] = (!@empty($item["text"])) ? @$item["text"] : @$item["long_text"] ;
		}
		$data = $tmp ;
	}
	
	function setupForViewLangText(&$data) {
		$tmp = array() ;
		for($i=0; $i < count($data) ; $i++) {
			$item = &$data[$i]['LangText'] ;
			if(!isset($tmp[$item["name"]]))	$tmp[$item["name"]] = array() ;
			$tmp[$item["name"]] = (!@empty($item["text"])) ? @$item["text"] : @$item["long_text"] ;
			$tmp['id'][$item["name"]]=$item['id'];
		}
		$data = $tmp ;
	}
	
	function objectForLang($id,$lang,&$object) {
		$tmpobj = $this->LangText->find('all',
			array(
				'fields'=>array('name','text','long_text'),
				'conditions'=>array("LangText.object_id = '$id'","LangText.lang = '$lang'")
			)
		);
		foreach($tmpobj as $k => $v) {
			$key = $v['LangText']['name'];
			$value= (!empty($v['LangText']['text']))?$v['LangText']['text']:$v['LangText']['long_text'];
			if(!empty($value)) $object[$key]=$value;
		}
	}
}
?>