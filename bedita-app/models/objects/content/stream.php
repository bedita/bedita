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
 * Basic Stream
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class Stream extends BEAppModel
{

	/**
	 * Get id from filename
	 * @param string $filename
	 */
	function getIdFromFilename($filename) {
		if(!isset($filename)) return false ;
		$rec = $this->recursive ;
		$this->recursive = -1 ;
		if(!($ret = $this->findByName($filename))) return false ;
		$this->recursive = $rec ;
		if(!isset($ret['Stream']['id'])) return false ;
		return $ret['Stream']['id'] ;
	}
	
	public function updateStreamFields($id) {
		
		if (empty($id)) {
			throw new BeditaException(__("Missing stream id"));	
		}
		
		$conf = Configure::getInstance();
		
		$stream = $this->find("first", array(
			"conditions" => array("id" => $id)
		));
		
		if (!empty($stream["Stream"]["path"])) {
			$isURL = (preg_match($conf->validate_resource['URL'], $stream["Stream"]["path"]))? true : false;
			
			if (!$isURL && !file_exists($conf->mediaRoot . $stream["Stream"]["path"])) {
				return false;
			}
			
			if ($isURL && ($provider = $this->getMediaProvider($stream["Stream"]["path"]))) {
				if (empty($stream["Stream"]["name"]) || empty($stream["Stream"]["mime_type"])) {
					$componentName = Inflector::camelize("be_" . $provider);
					App::import("Component", $componentName);
					$providerComponent = new $componentName();
					$providerComponent->setInfoToSave($stream["Stream"]);
				}
			} else {
				if (empty($stream["Stream"]["name"])) {
					$stream["Stream"]["name"] = basename($stream["Stream"]["path"]);
				}
				
				if (empty($stream["Stream"]["mime_type"])) {
					$stream["Stream"]["mime_type"] = $this->getMimeType($conf->mediaRoot . $stream["Stream"]["path"], $stream["Stream"]["name"]);
				}
				
				if (!$isURL) {
					$stream["Stream"]["size"] = filesize($conf->mediaRoot . $stream["Stream"]["path"]);
					$stream["Stream"]["hash_file"] = hash_file("md5", $conf->mediaRoot . $stream["Stream"]["path"]);
				}
			}
			
			$this->create();
			
			if (!$this->save($stream)) {
				throw new BeditaException(__("Error updating stream " . $id, true));
			}
			
			// @todo: save image and application dimensions

			return true;
		}
	}
	
	public function getMediaProvider($url) {
		$conf = Configure::getInstance();
		foreach($conf->media_providers as $provider => $expressions) {
			foreach($expressions["regexp"] as $expression) {
				if(preg_match($expression, $url, $matched)) {
					return $matched[0] ;
				}	
			}
		}
		return false ;
	}
	
	public function getMimeType($path, $filename=null) {
		if (empty($filename)) {
			$filename = basename($path);
		}
		if (function_exists("finfo_open")) {
			$file_info = finfo_open(FILEINFO_MIME, APP_PATH.'config'.DS.'magic');
			$mime_type = ($file_info)? finfo_file($file_info, $path) : $this->getMimeTypeByExtension($filename);
		} else {
			$mime_type = $this->getMimeTypeByExtension($filename);
		}
		return $mime_type;
	}
	
	public function getMimeTypeByExtension($filename) {
		$mime_type = false;
		include(APP_PATH.'config'.DS.'mime.types.php');
		$extension = strtolower( pathinfo($filename, PATHINFO_EXTENSION) );
		if (!empty($extension) && array_key_exists($extension,$config["mimeTypes"])) {
			$mime_type = $config["mimeTypes"][$extension];
		}
		return $mime_type;
	}
	
	
}
?>