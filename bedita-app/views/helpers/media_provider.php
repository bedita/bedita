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
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class MediaProviderHelper extends AppHelper {
	
	var $helpers = array('Html','Youtube','Blip','Vimeo','BeEmbedFlash');
	
	var $conf = null ;

	function __construct() {
		$this->conf 	= Configure::getInstance() ;
	}
	
	/**
	 * get img tag for thumbnail
	 */
	function thumbnail(&$obj, $htmlAttributes = array(), $URLonly=false ) {
		if (!empty($obj["thumbnail"]) && preg_match(Configure::read("validate_resource.URL"), $obj["thumbnail"]))
			return (!$URLonly)? $this->Html->image($obj["thumbnail"], $htmlAttributes) : $obj["thumbnail"];
		
		if (!$helperName = $this->getHelperName($obj))
			return "";
		
		return $this->{$helperName}->thumbnail($obj, $htmlAttributes, $URLonly);
	}
	
	/**
	 * get embed video
	 */
	function embed(&$obj, $params = array(), $attributes = array() ) {
		
		//caso in cui non esiste un helper specifico per gestire il tipo di video
		if (!$helperName = $this->getHelperName($obj)){
			$obj['path'] = ($this->checkURL($obj['path'])) ? $obj['path'] : Configure::read('mediaUrl').$obj['path'];
			return  $this->BeEmbedFlash->embed($obj, $params, $attributes);
		}
		
		//esiste l'helper ed � stato l'uso del player remoto specifico 
		if (!empty($params['useProviderPlayer'])) {
			return $this->{$helperName}->embed($obj, $attributes);
		}else {
			//esiste l'helper, ma non essendo stato forzato il player esterno prova a riprodurlo usando prima il player interno
			$obj['path'] = $this->sourceEmbed($obj);
			$res = $this->BeEmbedFlash->embed($obj, $params, $attributes);
			if ( $res === false ) {
				$res =  $this->{$helperName}->embed($obj, $attributes) ;
			}
			return $res;
		}
	}
	
	/**
	 * get source url
	 */
	function sourceEmbed(&$obj) {
		if (!$helperName = $this->getHelperName($obj))
			return "";
			
		return $this->{$helperName}->sourceEmbed($obj);
	}
	
	private function getHelperName(&$obj) {
		if(empty($obj['provider'])) 
			return false ;
		$helperName = Inflector::camelize($obj['provider']);
		if (!isset($this->{$helperName})) {
			return false;
		}
		return $helperName;
	}
	
	private function checkURL($url) {
		foreach (Configure::read('validate_resource.allow') as $reg) {
			if(preg_match($reg, $url)) 
				return true;
		}
		return false;
	}
	
}

?>