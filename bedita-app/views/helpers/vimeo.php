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
class VimeoHelper extends AppHelper {
	
	var $helpers = array("Html");
	
	function thumbnail(&$obj, $htmlAttributes, $URLonly) {
		$url = rawurlencode($obj["path"]);
		if (!$oEmbed = $this->oEmbedVideo($url)) {
			return false;
		}
		$src = $oEmbed['thumbnail_url'];
		return (!$URLonly)? $this->Html->image($src, $htmlAttributes) : $src;
	}
	
	/**
	 * embed vimeo video
	 * 
	 * @param array $obj
	 * @param array $attributes
	 * @return html embed video
	 */
	function embed(&$obj, $attributes) {
		$conf = Configure::getInstance();
		$url = rawurlencode($obj["path"]);
		if (empty($attributes["width"]) && empty($attributes["height"])) {
			$attributes["width"] = $conf->media_providers["vimeo"]["params"]["width"];
			$attributes["height"] = $conf->media_providers["vimeo"]["params"]["height"];
		}
		foreach ($attributes as $key => $val) {
			$url .= "&" . $key . "=" . $val; 
		}
		if (!$oEmbed = $this->oEmbedVideo($url)) {
			return false;
		}
						
		return $oEmbed["html"] ;
	}
	
	/**
	 * path to vimeo video (can't get flv from api)
	 *
	 * @param array $obj
	 * @return youtube path
	 */
	function sourceEmbed($obj) {
		return $obj['path'] ;
	}
	
	/**
	 * get oEmbed data (http://vimeo.com/api/docs/oembed)
	 * 
	 * @param $url
	 * @return unknown_type
	 */
	private function oEmbedVideo($url) {
		$vimeoParams = Configure::read("media_providers.vimeo.params");
		if (!$json = file_get_contents(sprintf($vimeoParams["urlembed"], $url))) {
			return false;
		}
		$oEmbedInfo = (json_decode($json,true));
		return $oEmbedInfo;
	}
	
}
 
?>