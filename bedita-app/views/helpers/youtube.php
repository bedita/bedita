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
class YoutubeHelper extends AppHelper {

	var $helpers = array("Html");
	
	function thumbnail($obj, $htmlAttributes, $URLonly) {
		$this->conf = Configure::getInstance() ;
		$src = sprintf($this->conf->media_providers["youtube"]["params"]["urlthumb"], $obj['video_uid']);
		return (!$URLonly)? $this->Html->image($src, $htmlAttributes) : $src;
	}
	
	/**
	 * embed youtube video
	 *
	 * @param array $obj
	 * @param array $attributes
	 * @return html embed
	 */
	function embed($obj, $attributes) {
		$this->conf 	= Configure::getInstance() ;
		if(!isset($this->conf->media_providers["youtube"]["params"])) 
			return "" ;
		
		if (empty($attributes["width"])) { 
			$attributes["width"] = $this->conf->media_providers["youtube"]["params"]["width"];
		}
		if (empty($attributes["height"])) { 
			$attributes["height"] = $this->conf->media_providers["youtube"]["params"]["height"];
		}
		
		// object / embed
		$url = 'https://www.youtube.com/v/'.$obj['video_uid'].'?version=3&feature=player_embedded&feature=player_embedded';//&autoplay=1'
		$html  = '<object style="height: '.$attributes["height"].'px; width: '.$attributes["width"].'px">';
		$html .= '	<param name="movie" value="'.$url.'">';
		$html .= '	<param name="allowFullScreen" value="true">';
		$html .= '	<param name="allowScriptAccess" value="always">';
		$html .= '	<embed src="'.$url.'" type="application/x-shockwave-flash" allowfullscreen="true" allowScriptAccess="always" width="'.$attributes["width"].'" height="'.$attributes["height"].'">';
		$html .= '</object>';

		// iFrame
		//$html = '<iframe width="'.$attributes["width"].'" height="'.$attributes["height"].'" src="http://www.youtube.com/embed/'.$obj['video_uid'].'" frameborder="0" allowfullscreen></iframe>';
		
		return $html;
	}
	
	/**
	 *
	 * @param array $obj
	 * @return youtube url
	 */
	function sourceEmbed(array &$obj) {
		return $obj['uri'] ;
	}
	
}
 
?>