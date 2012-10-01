<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2011 ChannelWeb Srl, Chialab Srl
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

App::uses("BEAppModel", "Model");

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
	 * @param int $id, if empty update all streams
	 * @return array of updated streams (empty array if no array updated)
	 */
	public function updateStreamFields($id = null) {

		$conditions = array();
		if (!empty($id)) {
			$conditions = array("id" => $id);
		}

		$conf = Configure::getInstance();

		$streams = $this->find("all", array(
			"conditions" => $conditions
		));

		$streamsUpdated = array();

		if (!empty($streams)) {
			foreach ($streams as $stream) {
				if (!empty($stream["Stream"]["uri"])) {
					$isURL = (preg_match($conf->validate_resource['URL'], $stream["Stream"]["uri"]))? true : false;
					$uri = $stream["Stream"]["uri"];
					$hasFile = file_exists($conf->mediaRoot . $uri);

					if ($isURL || $hasFile) {

						if($hasFile) {
							// check & correct file name
							$oldName = $stream["Stream"]["name"];
							$p = strrpos($stream["Stream"]["name"], ".");
							if($p === false) {
								$newName = BeLib::getInstance()->friendlyUrlString($oldName);
							} else {
								$newName = BeLib::getInstance()->friendlyUrlString(substr($oldName, 0, $p));
								$newName .=  "." . BeLib::getInstance()->friendlyUrlString(substr($oldName, $p+1));
							}
							if($newName !== $oldName) {
								$stream["Stream"]["name"] = $newName;
								$slash = strrpos($uri, "/");
								$newUri = substr($uri, 0, $slash+1) . $newName;
								if(rename($conf->mediaRoot . $uri, $conf->mediaRoot . $newUri) === false) {
									throw new BeditaException(__("Error renaming stream") . " id: " . $id . " file: " . $fileName);
								}
								$stream["Stream"]["uri"] = $newUri;
							}
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
							throw new BeditaException(__("Error updating stream " . $id));
						}

						$streamsUpdated[] = $stream;
					}
				}
			}
		}
		return $streamsUpdated;
	}


	/**
	 * Clears media cache
	 *
	 * @param int $id, object id of which clear cache
	 *
	 * @return mixed, false if no stream was found
	 *				  empty array if cleaning operation proceeds without errors
	 *				  array with errors if something goes wrong. Itcontains:
	 *						'failed' => array of objects data on which clear media failed. Each item contains:
	 *							'id' =>  object id,
	 *							'error' => message error
	 */
	public function clearMediaCache($id = null) {
		$conditions = array();
		if (!empty($id)) {
			$conditions['Stream.id'] = $id;
		}
		$streams = $this->find("all", array('conditions' => $conditions));
		if (empty($streams)) {
			return false;
		}
		$results = array();
		$folder = new Folder();
		foreach ($streams as $s) {
			if(!empty($s["Stream"]["uri"])) {
				$filePath = Configure::read("mediaRoot") . $s["Stream"]["uri"];
				if (DS != "/") {
					$filePath = str_replace("/", DS, $filePath);
				}
				if(file_exists($filePath)) {
					$filenameBase = pathinfo($filePath, PATHINFO_FILENAME);
					$filenameMD5 = md5($s["Stream"]["name"]);
					$cacheDir = dirname($filePath) . DS . substr($filenameBase, 0, 5) . "_" . $filenameMD5;
					if(file_exists($cacheDir)) {
		        		if(!$folder->delete($cacheDir)) {
		                	$results['failed'][] = array(
								'id' => $s["Stream"]['id'],
								'error' => __("Error deleting dir") . " " . $cacheDir
							);
		            	}
	        		}
	        	}
			}
		}
		return $results;
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
				$file_info = finfo_open(FILEINFO_MIME_TYPE);
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