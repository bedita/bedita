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
class BlipHelper extends AppHelper {
	
	var $helpers = array("Html");
	
	function thumbnail(&$obj, $htmlAttributes, $URLonly) {
		if(!class_exists("BeBlipComponent")){
			App::import('Component', "BeBlip");
		}
		$Component = new BeBlipComponent();
		$Component->getInfoVideo($obj['uid']);
		
		$src = sprintf($Component->info['thumbnailUrl'], $obj['uid']);
		return (!$URLonly)? $this->Html->image($src, $htmlAttributes) : $src;
	}
	
	/**
	 * embed blip video
	 * 
	 * @param array $obj
	 * @param array $attributes
	 * @return html embed video
	 */
	function embed(&$obj, $attributes) {
		if(!class_exists("BeBlipComponent")){
			App::import('Component', "BeBlip");
		}
		$Component = new BeBlipComponent();
		$Component->getEmbedVideo($obj['uid'], $attributes) ;
	
		return $Component->embed ;
	}
	
	/**
	 *
	 * @param unknown_type $obj
	 * @return unknown
	 */
	function sourceEmbed(&$obj) {
		if(!class_exists("BeBlipComponent")){
			App::import('Component', "BeBlip");
		}
		$Component = new BeBlipComponent();
		
		$info = $Component->getInfoVideo($obj['uid']);
	
		if(preg_match("/^http:\/\/blip.tv\/file\/get\/.*\.flv/",$info["mediaUrl"],$matched)) {
			return $matched[0] ;
		}

		return "" ;
	}
	
}
 
?>