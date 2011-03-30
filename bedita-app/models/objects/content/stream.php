<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2011 ChannelWeb Srl, Chialab Srl
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
	 * @var array of mime types
	 */
	private $mimeTypes = array();
	
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
	
	/**
	 * update fields in streams table
	 * 
	 * @param int $id
	 * @return boolean (true if row is updated, false if not)
	 */
	public function updateStreamFields($id) {
		
		if (empty($id)) {
			throw new BeditaException(__("Missing stream id"));	
		}
		
		$conf = Configure::getInstance();
		
		$stream = $this->find("first", array(
			"conditions" => array("id" => $id)
		));
		
		if (!empty($stream["Stream"]["uri"])) {
			$isURL = (preg_match($conf->validate_resource['URL'], $stream["Stream"]["uri"]))? true : false;
			
			if (!$isURL && !file_exists($conf->mediaRoot . $stream["Stream"]["uri"])) {
				return false;
			}
			
			if ($isURL && ($mediaProvider = $this->getMediaProvider($stream["Stream"]["uri"]))) {
				if (empty($stream["Stream"]["name"]) || empty($stream["Stream"]["mime_type"])) {
					$componentName = Inflector::camelize("be_" . $mediaProvider["provider"]);
					$stream["Stream"] = array_merge($stream["Stream"],$mediaProvider);
					App::import("Component", $componentName);
					$componentName .= "Component";
					$providerComponent = new $componentName();
					$providerComponent->setInfoToSave($stream["Stream"]);
				}
			} else {
				if (empty($stream["Stream"]["name"])) {
					$stream["Stream"]["name"] = basename($stream["Stream"]["uri"]);
				}
				
				if (empty($stream["Stream"]["mime_type"])) {
					$stream["Stream"]["mime_type"] = $this->getMimeType($conf->mediaRoot . $stream["Stream"]["uri"], $stream["Stream"]["name"]);
				}
				
				if (!$isURL) {
					$stream["Stream"]["file_size"] = filesize($conf->mediaRoot . $stream["Stream"]["uri"]);
					$stream["Stream"]["hash_file"] = hash_file("md5", $conf->mediaRoot . $stream["Stream"]["uri"]);
				}
			}
			
			$this->create();
			
			if (!$this->save($stream)) {
				throw new BeditaException(__("Error updating stream " . $id, true));
			}

			return true;
		}
		
		return false;
	}
	
	/**
	 * return media provider array or false if $uri it's not managed
	 * 
	 * @param $uri
	 * @return mixed array("provider" => "", "uri" => "", "video_uid" => "") or false if not found
	 */
	public function getMediaProvider($uri) {
		$conf = Configure::getInstance();
		foreach($conf->media_providers as $provider => $expressions) {
			foreach($expressions["regexp"] as $expression) {
				if(preg_match($expression, $uri, $matched)) {
					$mediaProvider = array("provider" => $provider, "uri" => $matched[0], "video_uid" => $matched[1]);
					return $mediaProvider;
				}	
			}
		}
		return false ;
	}
	
	/**
	 * get mime type by finfo_open (if function exist) or by file extension 
	 * 
	 * @param $path full path of file
	 * @param $filename
	 * @return string or false if mime_type not found
	 */
	public function getMimeType($path, $filename=null) {
		if (empty($filename)) {
			$filename = basename($path);
		}
		if (function_exists("finfo_open")) {
			if(PHP_VERSION < 5.3) {
				$file_info = finfo_open(FILEINFO_MIME, APP_PATH.'config'.DS.'magic');
			} else {
				$file_info = finfo_open(FILEINFO_MIME);
			}
			$mime_type = ($file_info)? finfo_file($file_info, $path) : $this->getMimeTypeByExtension($filename);
		} else {
			$mime_type = $this->getMimeTypeByExtension($filename);
		}
		return $mime_type;
	}
	
	/**
	 * get mime type by file extension
	 * 
	 * @param $filename
	 * @return string or false if mime_type not found
	 */
	public function getMimeTypeByExtension($filename) {
		$mime_type = false;
		if (empty($this->mimeTypes)) {
			$this->setMimeTypes();
		}		
		$extension = strtolower( pathinfo($filename, PATHINFO_EXTENSION) );
		if (!empty($extension) && array_key_exists($extension,$this->mimeTypes)) {
			$mime_type = $this->mimeTypes[$extension];
		}
		return $mime_type;
	}
	
	/**
	 * set Stream::mimeTypes array using bedita-app/config/mimi.types.php file
	 * @return void
	 */
	public function setMimeTypes() {
		include(BEDITA_CORE_PATH.DS.'config'.DS.'mime.types.php');
		$this->mimeTypes = $config["mimeTypes"];
	}
	
	/**
	 * get Stream::mimeTypes
	 * @return array of mime type
	 */
	public function getMimeTypes() {
		return $this->mimeTypes;
	}
	
	
}
?>