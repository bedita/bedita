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
 * File upload, save, modify, delete, manage (remote as well).
 * Uses Transaction component.
 * 
 * Data to be passed to save an object with a file:
 * 
 * 		path		temporary file path or URL
 * 		name		Name of original file
 * 		type		MIME type, if not set, try to get from file name (@todo)
 * 		size		file size, if URL, try to read remote file size
 *  
 * Exceptions:
 * 		BEditaFileExistException		// File already exists (thrown in creation)
 * 		BEditaInfoException				// File info not readable (access denied or no data)
 * 		BEditaMIMEException				// MIME type not found or not corresponding to object type
 * 		BEditaURLRxception				// URL rules violated
 * 		BEditaSaveStreamObjException	// Error creating/modifying object 
 * 		BEditaDeleteStreamObjException	// Error deleting object
 * 
 * If paranoid == false: remote info not loaded ['allow_php_fopen' not necessary]. Mime info should be passed with data for URLs.
 * 
 * File paths saved on DB are relative to $config['mediaRoot']
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeFileHandlerComponent extends Object {

	var $components = array('Transaction');
	var $paranoid 	= true ;
	
	// Errors on save
	var $validateErrors = false ;

	function __construct() {
		foreach ($this->components as $component) {
			if(isset($this->{$component})) continue;
			$className = $component . 'Component' ;
			if(!class_exists($className))
				App::import('Component', $component);
			$this->{$component} = new $className() ;
		}
	} 

	function startup(&$controller)
	{
		$conf = Configure::getInstance() ;
		$this->controller 	= $controller;
		if(isset($conf->validate_resource['paranoid'])) $this->paranoid  = (boolean) $conf->validate_resource['paranoid'] ;
	}

	/**
	 * Save object $data
	 * If $data['id'] modify otherwise create
	 * If file is already present, throw an exception.
	 * File data:
	 * 	path: local path or URL (\.+//:\.+) [remote file]
	 * 			if "allow_url_fopen" is not activated, remote file is not accepted
	 * name		Name of file. Empty if path == URL
	 * type		MIME type. Empty if path == URL
	 * size		File size. Empty if path == URL
	 *
	 * @param array $dati	object data
	 *
	 * @return integer or false (id of the object created or modified)
	 */
	function save(&$data, $clone=false, $getInfoUrl=true) {
		if (!empty($data['path'])) {
			if ($this->_isURL($data['path'])) {
				return $this->_createFromURL($data, $clone, $getInfoUrl);
			} else {
				return $this->_createFromFile($data, $clone);
			}
		}
	}	

	/**
	 * Delete object
	 * @param integer $id	object id
	 */
	function del($id) {
		$path = ClassRegistry::init("Stream")->read("path", $id);
		$path = (isset($path['Stream']['path'])) ? $path['Stream']['path'] : $path ;
		if(!empty($path)) {
			// delete local file
			if(!$this->_isURL($path)) {
				$this->_removeFile($path) ;	
			}
		}
		$model = ClassRegistry::init("BEObject")->getType($id) ;
		$mod = ClassRegistry::init($model);
	 	if(!$mod->del($id)) {
			throw new BEditaDeleteStreamObjException(__("Error deleting stream object",true)) ;	
	 	}
	 	return true ;
	}

	/**
	 * Return URL of file object
	 * @param integer $id	object id
	 */
	function url($id) {
		if(!($ret = ClassRegistry::init("Stream")->read("path", $id))) return false ;
		$path = $ret['Stream']['path'] ;
		return ($this->_isURL($path)) ? $path : (Configure::read("mediaUrl").$path);
	}

	/**
	 * Return object path, URL if remote file
	 * @param integer $id	object id
	 */
	function path($id) {
		if(!($ret = ClassRegistry::init("Stream")->read("path", $id))) return false ;
		$path = $ret['Stream']['path'] ;
		return ($this->_isURL($path)) ? $path : (Configure::read("mediaUrl").$path);
	}

	/**
	 * Return object id (object that contains file $path)
	 * @param string $path	File name or URL
	 * @todo VERIFY
	 */
	function isPresent($path, $id = null) {
		if(!$this->_isURL($path)) {
			$path = $this->getPathTargetFile($path);
		}
		$clausoles = array() ;
		$clausoles[] = array("path" => trim($path)) ;
		if(isset($id)) $clausoles[] = array("id " => "not {$id}") ;
		$ret = ClassRegistry::init("Stream")->find($clausoles, 'id') ;
		if(!count($ret)) return false ;
				
		return $ret['Stream']['id'] ;
	}
	
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////
	
	private function _createFromURL(&$data, $clone, $getInfoUrl) {
		// check URL
		if(empty($data['path']) || !$this->_regularURL($data['path'])) 
			throw new BEditaURLException(__("URL not valid",true)) ;

		if($getInfoUrl && $this->paranoid) {
			// Remote file management
			if(!ini_get('allow_url_fopen')) 
				throw new BEditaAllowURLException(__("You can't use remote file",true)) ;
			
			// Get MIME type
			$this->getInfoURL($data);

		}
		
		// check url presence in database
		if (!$clone) {
			// new
			if (empty($data["id"])) {
				if ($this->isPresent($data['path']))
					throw new BEditaFileExistException(__("Media already exists in the system",true)) ;
			// modify
			} elseif (!empty($data["id"])) {
				if ($this->isPresent($data['path'], $data['id']))
					throw new BEditaFileExistException(__("Media already exists in the system",true)) ;
	
				// if present in filesystem delete it
				$stream = ClassRegistry::init("Stream")->read('path', $data['id']);
				if((!empty($stream["Stream"]["path"]) && !$this->_isURL($stream['Stream']['path']))) {
					$this->_removeFile($stream['Stream']['path']) ;		
				}
			}
		}
		
		return $this->_create($data) ;
	}

	private function _createFromFile(&$data, $clone) {
		// if it's new object and missing path
		if(empty($data['path']) && empty($data['id'])) 
			throw new BeditaException(__("Missing temporary file in filesystem.", true));

		if (!file_exists($data["path"]))
			throw new BEditaFileExistException(__("Resource " . $data["path"] . " not valid", true));
			
		// Create destination path
		$sourcePath = $data['path'] ;

		$data["hash_file"] = hash_file("md5", $sourcePath);

		$streamModel = ClassRegistry::init("Stream");
		
		// check if hash file exists
		if (!$clone && ($stream_id = $streamModel->field("id", array("hash_file" => $data["hash_file"]))) ) {
			throw new BEditaFileExistException(__("File already exists in the filesystem",true)) ;
		}
		
		$targetPath	= $this->getPathTargetFile($data['name']);
		
		if (!empty($data["id"])) {
			$ret = $streamModel->read('path', $data["id"]);
				
			// if present a path to a file on filesystem, delete it
			if((!empty($ret['Stream']['path']) && !$this->_isURL($ret['Stream']['path']))) {
				$this->_removeFile($ret['Stream']['path']) ;		
			}
		}

		// Create file
		if(!$this->_putFile($sourcePath, $targetPath)) return false ;
		$data['path'] = $targetPath ;
		// Create object
		return $this->_create($data) ;
	}

	private function _create(&$data) {
	
		if (!$modelType = $this->_getTypeFromMIME($data["mime_type"])) {
			throw new BEditaMIMEException(__("MIME type not found",true).": ".$data['mime_type']) ;
		}
		
		if (!empty($data["id"])) {
			$stream = ClassRegistry::init("Stream")->read(array('mime_type','path'), $data["id"]) ;
			$object_type_id = ClassRegistry::init("BEObject")->field("object_type_id", array("id" => $data["id"]));
			$prevModel = Configure::read("objectTypes." . $object_type_id . ".model");
			
			// change object type
			if ($modelType["name"] != $prevModel) {
				
				
				$data["object_type_id"] = Configure::read("objectTypes." . strtolower($modelType["name"]) . ".id");
				// delete old data from specific table
				$prevMediaModel = ClassRegistry::init($prevModel);
				$prevMediaModel->Behaviors->disable('DeleteObject');
				$prevMediaModel->del($data["id"], false);
				$prevMediaModel->Behaviors->enable('DeleteObject');
				
				// delete file on filesystem
				if(($stream["Stream"]["path"] && !$this->_isURL($stream["Stream"]["path"]))) {
					$this->_removeFile($stream["Stream"]["path"]) ;		
				}
			}
		}
		
		if (method_exists($this, "set" . $modelType["name"] . "Data")) {
			if (!empty($modelType["specificType"])) {
				$this->{"set" . $modelType["name"] . "Data"}($data, $modelType["specificType"]);
			} else {
				$this->{"set" . $modelType["name"] . "Data"}($data);
			}
		}
		
		$data['Category'] = (!empty($data['Category']))? array_merge($data['Category'],$this->getCategoryMediaType($data,$modelType["name"])) : $this->getCategoryMediaType($data,$modelType["name"]);
		
		$mediaModel = ClassRegistry::init($modelType["name"]);
		$mediaModel->create();
		if(!($ret = $mediaModel->save($data))) {
			throw new BEditaSaveStreamObjException(__("Error saving stream object",true), $mediaModel->validationErrors) ;
		}
		return ($mediaModel->{$mediaModel->primaryKey}) ;
	}

	private function setImageData(&$data) {
		$this->getImageSize($data);
	}
	
	private function setApplicationData(&$data, $application_name) {
		$data["application_name"] = $application_name;
		$app_details = Configure::read("validate_resource.mime.Application");
		$data["application_type"] = $app_details[$application_name]["application_type"];
		$data["application_label"] = $app_details[$application_name]["label"];
		if ($application_name == "flash") {
			$this->getImageSize($data);
		}
	}
	
	private function getImageSize(&$data) {
		$path = ($this->_isURL($data["path"]))? $data["path"] : Configure::read("mediaRoot") . $data['path'];
		if ( $imageSize =@ getimagesize($path) )
		{
			if (!empty($imageSize[0]))
				$data["width"] = $imageSize[0];
			if (!empty($imageSize[1]))
				$data["height"] = $imageSize[1];
		}
	}
	
	private function getCategoryMediaType($data, $modelType) {
		$cat = array();
		// if empty mediatype try to get it from modelName
		if (empty($data['mediatype']) && $modelType != "BEFile") {
			$data['mediatype'] = strtolower($modelType);
		}
		
		if (!empty($data['mediatype'])) {
			$category = ClassRegistry::init("Category");
			$objetc_type_id = Configure::read("objectTypes." . strtolower($modelType) . ".id");
			$cat = $category->checkMediaType($objetc_type_id, $data['mediatype']);	
		}
		return $cat;
	}
	
	/**
	 * If $path is an URL, return TRUE
	 *
	 * @param unknown_type $path
	 */
	private function _isURL($path) {
		$conf 		= Configure::getInstance() ;
		
		if(preg_match($conf->validate_resource['URL'], $path)) return true ;
		else return false ;
	}

	/**
	 * If $URL is valid, return TRUE
	 */
	private function _regularURL($URL) {
		$conf 		= Configure::getInstance() ;
		
		foreach ($conf->validate_resource['allow'] as $reg) {
			if(preg_match($reg, $URL)) return true ;
		}

		return false ;	
	}
			
	/**
	 * return array with model name and eventually specific type (see $config[validate_resource][mime][Application]) 
	 * from mime type
	 *
	 * @param string $mime	mime type 
	 */
	private function _getTypeFromMIME($mime) {
		$conf 		= Configure::getInstance() ;
		if(empty($mime))	
			return false ;
		
		$models = $conf->validate_resource['mime'] ;
		foreach ($models as $model => $regs) {
			foreach ($regs as $key => $reg) {
				if (is_array($reg)) {
					foreach ($reg["mime_type"] as $val) {
						if(preg_match($val, $mime)) 
							return array("name" => $model, "specificType" => $key) ;
					}
				} elseif(preg_match($reg, $mime)) {
					return array("name" => $model) ;
				}	
			}
		}
		
		return false ;
	}

	
	/**
	 * get mime type
	 */
	function getInfoURL(&$data) {
		
		if(!(isset($data['name']) && !empty($data['name']))) {
			$data['name']  = basename($data["path"]) ;
		}
		
		if (empty($data['title'])) {
			$data['title'] = $data['name'];
		}
		
		// get mime type
		if (!($headers = @get_headers($data["path"],1)))
			throw new BEditaInfoException(__("URL unattainable",true));

		// if redirect response try to reach the redirect location
		if (stristr($headers[0], "redirect")) {
			if (empty($headers["Location"])) {
				throw new BEditaInfoException(__("URL unattainable",true));
			}
			if (!($headers = @get_headers($headers["Location"],1))) {
				throw new BEditaInfoException(__("URL unattainable",true));
			}
		}

		if (!strstr($headers[0], "200"))
			throw new BEditaInfoException(__("URL unattainable",true));

		$data["mime_type"] = $this->getMimeTypeByExtension($data["path"]);
		if (!$data["mime_type"]) {
			$data["mime_type"] = (!empty($headers["Content-Type"]))? $headers["Content-Type"] : $data["mime_type"] = "beexternalsource";
		}
	}
	
	public function getMimeType($data) {
		if (function_exists("finfo_open")) {
			$file_info = finfo_open(FILEINFO_MIME, APP_PATH.'config'.DS.'magic');
			$mime_type = ($file_info)? finfo_file($file_info,$data["tmp_name"]) : $this->getMimeTypeByExtension($data["name"]);
		} else {
			$mime_type = $this->getMimeTypeByExtension($data["name"]);
		}
		
		// if not retrieved mime type from file or extension, get mime type passed by browser
		if (empty($mime_type))
			$mime_type = $data['type'];
		
		return $mime_type;
	}
	
	public function getMimeTypeByExtension($filename) {
		$mime_type = false;
		include_once APP_PATH.'config'.DS.'mime.types.php';
			$extension = strtolower( pathinfo($filename, PATHINFO_EXTENSION) );
			if (!empty($extension) && array_key_exists($extension,$config["mimeTypes"])) {
				$mime_type = $config["mimeTypes"][$extension];
		}
		return $mime_type;
	}
	
	/**
	 * Create target with source (temporary file), through transactional object
	 *
	 * @param string $sourcePath
	 * @param string $targetPath
	 */
	private function _putFile($sourcePath, $targetPath) {
		if(empty($targetPath)) return false ;
		
		// Temporary directories to create
		$tmp = Configure::read("mediaRoot") . $targetPath ;
		$stack = array() ;
		$dir = dirname($tmp) ;
		
		while($dir != Configure::read("mediaRoot")) {
			if(is_dir($dir)) break ;
			
			array_push($stack, $dir) ;
			
			$dir = dirname($dir) ;
		} 
		unset($dir) ;
		
		// Creating directories
		while(($current = array_pop($stack))) {
			if(!$this->Transaction->mkdir($current)) return false ;
		}
		
		return $this->Transaction->makeFromFile($tmp, $sourcePath) ;
	}	

	/**
	 * Delete a file from file system with transactional object
	 *
	 * @param string $path
	 */
	private function _removeFile($path) {
		$path = Configure::read("mediaRoot") . $path ;
		
		if (file_exists($path)) {
		
			// Remove
			if(!$this->Transaction->rm($path))
				return false ;
			
			// remove thumb cached and cache directory
			$cacheDir = dirname($path) . DS . substr(pathinfo($path, PATHINFO_FILENAME),0,5) . "_" . md5(basename($path));
			if (is_dir($cacheDir)) {
				$cacheFolder = new Folder($cacheDir);
				$cacheFolder->delete();
			}
			
			// If container direcotry is empty, remove it
			$dir = dirname($path) ;
			while($dir != Configure::read("mediaRoot")) {
				// Verify that it's empty
				$vuota = true ;
				if($handle = opendir($dir)) {
				    while (false !== ($file = readdir($handle))) {
	        			if ($file != "." && $file != "..") {
	        				$vuota = false ;
			            	break ;
			        	}
	    			}
	    			closedir($handle);				
				}
				
				// If empty remove, break otherwise
				if($vuota) {
					if(!$this->Transaction->rmdir($dir))
						return false ;
				}else {
					break ;
				}
				
				$dir = dirname($dir) ;
			} 
		}
		
		return true ;
	}


  	/**
  	 * Get path where to save uploaded file
  	 *
  	 * @param string $name 	Nome del file
  	 */
	function getPathTargetFile(&$name)  {
		
		$md5 = md5($name) ;
		//preg_match("/(\w{2,2})(\w{2,2})(\w{2,2})(\w{2,2})/", $md5, $dirs) ;
		preg_match("/(\w{2})(\w{2})/", $md5, $dirs) ;
		array_shift($dirs) ;
		
		$pointPosition = strrpos($name,".");
		$filename = $tmpname = substr($name, 0, $pointPosition);
		$ext = substr($name, $pointPosition);
		$mediaRoot = Configure::read("mediaRoot");
		$dirsString = implode(DS, $dirs);
		$counter = 1;
		while(file_exists($mediaRoot . DS . $dirsString . DS . $filename . $ext)) {
			$filename = $tmpname . "-" . $counter++;
		}
		
		// save new name (passed by reference)
		$name = $filename . $ext;
		$path =  DS . $dirsString . DS . $name ;
		
		return $path ;
	}
   
} ;

?>