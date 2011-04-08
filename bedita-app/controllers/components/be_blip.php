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
class BeBlipComponent extends Object {
	var $controller	;
	var $info = null ;
	var $embed = null ;
	
	function __construct() {
		parent::__construct() ;
	} 

	function startup(&$controller)
	{
		$conf = Configure::getInstance() ;
		
		$this->controller 	= $controller;
	}
	
	
	/**
	 * Get info for a clip video
	 *
	 * @return unknown
	 */
	public function getInfoVideo($id, $attributes=array()) {
		$conf = Configure::getInstance() ;
		$this->info = null ;
		
		if(!isset($conf->media_providers["blip"]["params"])) return false ;
		
		$urlinfo = $conf->media_providers["blip"]["params"]['urlinfo'];
		if (!empty($attributes["width"]))
			$urlinfo .= "&amp;width=" . $attributes["width"];
		elseif (!empty($conf->media_providers["blip"]["params"]["width"]))
			$urlinfo .= "&amp;width=" . $conf->media_providers["blip"]["params"]["width"];
		if (!empty($attributes["height"]))
			$urlinfo .= "&amp;height=" . $attributes["height"];
		elseif (!empty($conf->media_providers["blip"]["params"]["height"]))
			$urlinfo .= "&amp;height=" . $conf->media_providers["blip"]["params"]["height"];  
		
		// Get info
		$json = file_get_contents(sprintf($urlinfo, $id));

		if(!$json) {
			return false;
		}

		// get assoc array
		$ret = json_decode($json, true) ;
		if (empty($ret)) {
			return false ;
		}
		
		$this->info = $ret;
		
		return $this->info  ;
	}
	
	/**
	 * Get embed code for video
	 *
	 * @return unknown
	 */
	public function getEmbedVideo($id, $attributes=array()) {
		$conf = Configure::getInstance() ;
		$this->embed = null ;
		
		if(!isset($this->info)) {
			if(!$this->getInfovideo($id, $attributes) ) return false ;
		}
		
		$this->embed = $this->info["embedCode"];
		
		return $this->embed  ;
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
		return $this->info['thumbnailUrl'];
	}
	
	/**
	 * set data to save multimediamedia object
	 * @param $id
	 * @param $data
	 * @return boolean
	 */
	public function setInfoToSave(&$data) {
		if(!$this->getInfoVideo($data["video_uid"])) {
			return false;
		}
		
		$data['title'] = (empty($data['title']))? $this->info['title'] : trim($data['title']);
		$data['description'] = (empty($data['description']))? $this->info['description'] : $data['description'];
		$data['url']		= $this->info['url'] ;
		if (empty($data['thumbnail']))
			$data['thumbnail']	= $this->info['thumbnailUrl'];
		if (empty($data['duration']))
			$data['duration']	= $this->info['media']['duration']/60;
		$data['name']		= preg_replace("/[\'\"]/", "", $data['title']);
		$data['mime_type']	= "video/".$data["provider"];
		return true;
	}
}

/**
 * EXAMPLE:
 * 
 Array
(
    [title] => comedy central tonight
    [description] => 8-10pm
 LIVE

    [tags] => Array
        (
        )

    [datestamp] => 04-13-08 06:06pm
    [postsId] => 830661
    [postsGuid] => EFA4EC3C-09A5-11DD-803A-B9EA042700A9
    [itemType] => file
    [itemId] => 824273
    [url] => http://blip.tv/file/824273
    [mediaUrl] => http://blip.tv/file/get/Rblog-comedyCentralTonight463.flv?source=2
    [thumbnailUrl] => http://panther2.video.blip.tv/Rblog-comedyCentralTonight935.jpg
    [thumbnail120Url] => http://panther2.video.blip.tv/Rblog-comedyCentralTonight935-417.jpg
    [login] => rblog
    [userId] => 74359
    [showName] => r blog
    [blogUrl] => http://www.rosie.com
    [media] => Array
        (
            [url] => http://blip.tv/file/get/Rblog-comedyCentralTonight463.flv?source=2
            [mimeType] => video/x-flv,video/flv
            [duration] => 68
            [width] => 320
            [height] => 240
        )

    [contentRating] => TV-UN
    [advertising] => 
    [Title] => comedy central tonight
    [posts_id] => 830661
    [item_type] => file
    [item_id] => 824273
    [posts_url] => http://blip.tv/file/824273
    [media_src] => /file/get/Rblog-comedyCentralTonight463.flv?source=2
    [Thumbnail] => Array
        (
            [Url] => http://panther2.video.blip.tv/Rblog-comedyCentralTonight935.jpg
        )

    [User] => rblog
)
* 
 */

?>