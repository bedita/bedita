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
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class MediaProviderHelper extends AppHelper {
	
	var $helpers = array('Html');
	
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
		
		if (!$helper = $this->getProviderHelper($obj))
			return "";
		
		return $helper->thumbnail($obj, $htmlAttributes, $URLonly);
	}
	
	/**
	 * get embed video
	 */
	function embed(&$obj, $params = array(), $attributes = array() ) {
		
		// provider helper to manage video/audio type don't exists
		if (!$helper = $this->getProviderHelper($obj)){
			$obj['uri'] = ($this->checkURL($obj['uri'])) ? $obj['uri'] : Configure::read('mediaUrl').$obj['uri'];
			$beEmbedFlash = $this->getHelper("BeEmbedFlash");
			return  $beEmbedFlash->embed($obj, $params, $attributes);
		}
		
		// provider helper exists and it's setted to use provider helper 
		if (!empty($params['useProviderPlayer'])) {
			return $helper->embed($obj, $attributes);
		} else {
			// try to use internal player
			$obj['uri'] = $this->sourceEmbed($obj);
			$beEmbedFlash = $this->getHelper("BeEmbedFlash");
			$res = $beEmbedFlash->embed($obj, $params, $attributes);
			if ( $res === false ) {
				$res =  $helper->embed($obj, $attributes) ;
			}
			return $res;
		}
	}
	
	/**
	 * get source url
	 */
	function sourceEmbed(&$obj) {
		if (!$helper = $this->getProviderHelper($obj))
			return "";
			
		return $helper->sourceEmbed($obj);
	}
	
	private function getProviderHelper(&$obj) {
		if(empty($obj["provider"])) 
			return false ;
		$helperName = Inflector::camelize($obj["provider"]);
		return $this->getHelper($helperName);
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