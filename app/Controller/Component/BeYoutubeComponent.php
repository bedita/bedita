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
 * Youtube media component
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeYoutubeComponent extends Component {
 	
	var $controller	;
	var $info = null ;
	
	function startup(&$controller) {
		$this->controller 	= $controller;
	}

	/**
	 * Get thumbnail
	 * 
	 * @param int $uid
	 * @return array
	 */
	public function getThumbnail($uid) {
		$youtubeParams = Configure::read("media_providers.youtube.params");
		$thumbnail = sprintf($youtubeParams["urlthumb"], $uid);
		return $thumbnail;
	}

	/**
	 * set data to save multimedia object
	 * 
	 * @param array $data
	 * @return boolean
	 */
	public function setInfoToSave(&$data) {
		$data['title']		= (!empty($data['title'])) ? trim($data['title']) : 'youtube video';
		$data['name']		= preg_replace("/[\'\"]/", "", $data['title']) ;
		$data['mime_type']	= "video/" . $data["provider"] ;
		if (empty($data['thumbnail']))
			$data['thumbnail']	= $this->getThumbnail($data["video_uid"]);
		return true;
	}
}
?>