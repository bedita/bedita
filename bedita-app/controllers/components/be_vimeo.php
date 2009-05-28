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
 * Blip TV media component
 *  
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeVimeoComponent extends Object {
	var $controller	;
	var $info = null ;
	
	function startup(&$controller) {
		$this->controller 	= $controller;
	}
	
	
	/**
	 * get vimeo info video
	 *
	 * @return unknown
	 */
	public function getInfoVideo($id, $attributes=array()) {
		$conf = Configure::getInstance() ;
		$this->info = null ;
		
		if(!isset($conf->provider_params["vimeo"])) 
			return false ;
		
		$urlinfo = $conf->provider_params["vimeo"]['urlinfo'];
 
		if (!$info = file_get_contents(sprintf($urlinfo, $id, "php"))) {
			return false;
		}
		
		$info = unserialize($info);
		$this->info = $info[0];
		return $this->info;
	}
	
	/**
	 * get thumbnail
	 * @param $id
	 * @return url, false if error occurs
	 */
	public function getThumbnail($id) {
		if(!$this->getInfoVideo($id)) {
			return false;
		}
		return $this->info['thumbnail_large'];
	}
	
	/**
	 * set data to save multimediamedia object
	 * @param $id
	 * @param $data
	 * @return boolean
	 */
	public function setInfoToSave(&$data) {
		if(!$this->getInfoVideo($data["uid"])) {
			return false;
		}
		
		$data['title'] = (empty($data['title']))? $this->info['title'] : trim($data['title']);
		$data['description'] = (empty($data['description']))? $this->info['caption'] : $data['description'];
		$data['path']		= $this->info['url'] ;
		if (empty($data['thumbnail']))
			$data['thumbnail']	= $this->info['thumbnail_large'];
		$data['name']		= preg_replace("/[\'\"]/", "", $data['title']);
		$data['mime_type']	= "video/".$data["provider"];
		return true;
	}
	
}

?>