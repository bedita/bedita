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

App::import(array(
	'type' => 'File',
	'name' => 'MediaProviderInterface',
	'search' => array(BEDITA_CORE_PATH . DS .'views' . DS . 'helpers')
));

/**
 * youtube helper class
 *
 */
class YoutubeHelper extends AppHelper implements MediaProviderInterface {

	var $helpers = array("Html");
	
	public function isSourceAvailable(array $obj) {
		return false;
	}

	public function thumbnail(array $obj, array $htmlAttributes, $URLonly) {
		$this->conf = Configure::getInstance() ;
		$src = sprintf($this->conf->media_providers["youtube"]["params"]["urlthumb"], $obj['video_uid']);
		return (!$URLonly)? $this->Html->image($src, $htmlAttributes) : $src;
	}
	
	/**
	 * embed youtube video
	 *
	 * @param array $obj
	 * @param array $attributes
	 * @return string html embed
	 */
	public function embed(array $obj, array $attributes) {
		$this->conf 	= Configure::getInstance() ;
		if(!isset($this->conf->media_providers["youtube"]["params"])) 
			return "" ;
		
		if (empty($attributes["width"])) { 
			$attributes["width"] = $this->conf->media_providers["youtube"]["params"]["width"];
		}
		if (empty($attributes["height"])) { 
			$attributes["height"] = $this->conf->media_providers["youtube"]["params"]["height"];
		}

		$url = rawurlencode($obj["uri"]);
		$url .= "&format=json&maxwidth=" . $attributes["width"] . "&maxheight=" . $attributes["height"];
		$url = sprintf($this->conf->media_providers["youtube"]["params"]["urlembed"], $url);
		if (!$oEmbed = $this->oEmbedInfo($url)) {
			return false;
		}
						
		return $oEmbed["html"] ;
	}
	
	/**
	 * return object url
	 * 
	 * @param array $obj
	 * @return string youtube url
	 */
	public function source(array $obj) {
		return '';
	}
	
}
 
?>