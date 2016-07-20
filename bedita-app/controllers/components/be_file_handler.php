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
 * File upload, save, modify, delete, manage (remote as well).
 * Uses Transaction component.
 * 
 * Data to be passed to save an object with a file:
 * 
 * 		path		temporary file path or URL
 * 		name		Name of original file
 * 		mime_type		MIME type, if not set, try to get from file name (@todo)
 * 		file_size		file size, if URL, try to read remote file size
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
	 * mime_type		MIME type. Empty if path == URL
	 * file_size		File size. Empty if path == URL
	 *
	 * @param array $data	object data
	 *
	 * @return integer or false (id of the object created or modified)
	 */
	public function save(&$data, $clone=false, $getInfoUrl=true) {
		if (!empty($data['uri'])) {
			if ($this->_isURL($data['uri'])) {
				return $this->_createFromURL($data, $clone, $getInfoUrl);
			} else {
				return $this->_createFromFile($data, $clone);
			}
		} else {
		    return $this->_create($data);
		}
	}	

	/**
	 * Delete object
	 * @param integer $id	object id
	 * @return boolean
	 * @throws BEditaDeleteStreamObjException
	 */
	public function del($id) {
		$path = ClassRegistry::init("Stream")->read("uri", $id);
		$path = (isset($path['Stream']['uri'])) ? $path['Stream']['uri'] : $path ;
		if(!empty($path)) {
			// delete local file
			if(!$this->_isURL($path)) {
				$this->_removeFile($path) ;	
			}
		}
		$model = ClassRegistry::init("BEObject")->getType($id) ;
		$mod = ClassRegistry::init($model);
	 	if(!$mod->delete($id)) {
			throw new BEditaDeleteStreamObjException(__("Error deleting stream object",true)) ;	
	 	}
	 	return true ;
	}

	/**
	 * Return URL of file object
	 * @param integer $id	object id
	 * @return string
	 */
	public function url($id) {
		if(!($ret = ClassRegistry::init("Stream")->read("uri", $id))) return false ;
		$path = $ret['Stream']['uri'] ;
		return ($this->_isURL($path)) ? $path : (Configure::read("mediaUrl").$path);
	}

	/**
	 * Return object path, URL if remote file
	 * @param integer $id	object id
	 * @return string
	 */
	public function path($id) {
		if(!($ret = ClassRegistry::init("Stream")->read("uri", $id))) return false ;
		$path = $ret['Stream']['uri'] ;
		return ($this->_isURL($path)) ? $path : (Configure::read("mediaUrl").$path);
	}

	/**
	 * Return object id (object that contains file $path)
	 * @param string $path	File name or URL
	 * @return int
	 * @todo VERIFY
	 */
	public function isPresent($path, $id = null) {
		if(!$this->_isURL($path)) {
			$path = $this->getPathTargetFile($path);
		}
		$clausoles = array() ;
		$clausoles[] = array("uri" => trim($path)) ;
		if(isset($id)) $clausoles[] = array("id " => "not {$id}") ;
		$ret = ClassRegistry::init("Stream")->find($clausoles, 'id') ;
		if(!count($ret)) return false ;
				
		return $ret['Stream']['id'] ;
	}
	
	////////////////////////////////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////////////////////////////////

	/**
	 * Create (or clone) data for file identified by url
	 * 
	 * @param array $data
	 * @param boolean $clone
	 * @param boolean $getInfoUrl
	 * @return int (int or other type, according to primaryKey type for model)
	 * @throws BEditaURLException
	 * @throws BEditaAllowURLException
	 * @throws BEditaFileExistException
	 */
	private function _createFromURL(&$data, $clone, $getInfoUrl) {
		// check URL
		if(empty($data['uri']) || !$this->_regularURL($data['uri'])) {
			throw new BEditaURLException(__("URL not valid",true)) ;
		}
		if($getInfoUrl && $this->paranoid) {
			// Remote file management
			if(!ini_get('allow_url_fopen')) {
				throw new BEditaAllowURLException(__("You can't use remote file",true)) ;
			}
			// Get MIME type
			$this->getInfoURL($data);
		}
		// check url presence in database
		if (!$clone) {
			// new
			if (empty($data["id"])) {
				if ($this->isPresent($data['uri'])) {
					throw new BEditaFileExistException(__("Media already exists in the system",true)) ;
				}
			// modify
			} elseif (!empty($data["id"])) {
				if ($this->isPresent($data['uri'], $data['id'])) {
					throw new BEditaFileExistException(__("Media already exists in the system",true)) ;
				}
				// if present in filesystem delete it
				$stream = ClassRegistry::init("Stream")->read('uri', $data['id']);
				if((!empty($stream["Stream"]["uri"]) && !$this->_isURL($stream['Stream']['uri']))) {
					$this->_removeFile($stream['Stream']['uri']) ;
				}
			}
		}
		return $this->_create($data) ;
	}

	/**
	 * Create (or clone) data for file
	 * 
	 * @param array $data
	 * @param boolean $clone
	 * @return mixed boolean|int (int or other type, according to primaryKey type for model)
	 * @throws BeditaException
	 * @throws BEditaFileExistException
	 */
	private function _createFromFile(&$data, $clone) {
		// if it's new object and missing uri
		if(empty($data['uri']) && empty($data['id'])) {
			throw new BeditaException(__("Missing temporary file in filesystem.", true));
		}
		
		if (!file_exists($data["uri"])) {
			throw new BEditaFileExistException(__("Resource " . $data["uri"] . " not valid", true));
		}
		$conf = Configure::getInstance() ;
		if(in_array($data['mime_type'],$conf->forbiddenUploadFiles["mimeTypes"])) {
			throw new BeditaException($data['mime_type'] . " " . __("mime type not allowed for upload", true));
		}
		if(preg_match($conf->forbiddenUploadFiles["extensions"],$data['name'])) {
			throw new BeditaException(__("File extension not allowed for upload.", true));
		}
		// Create destination path
		$sourcePath = $data['uri'] ;
		$data["hash_file"] = hash_file("md5", $sourcePath);
		$streamModel = ClassRegistry::init("Stream");
		// check if hash file exists
		if (!$clone && ($stream_id = $streamModel->field("id", array("hash_file" => $data["hash_file"]))) ) {
			throw new BEditaFileExistException(__("File already exists in the filesystem",true),array("id"=>$stream_id)) ;
		}
		$targetPath	= $this->getPathTargetFile($data['name']);
		// Create file
		if (!$this->putFile($sourcePath, $targetPath)) {
            return false;
        }
        // if update an object remove old file (if any)
        if (!empty($data["id"])) {
            $ret = $streamModel->read('uri', $data["id"]);
            // if present a path to a file on filesystem, delete it
            if((!empty($ret['Stream']['uri']) && !$this->_isURL($ret['Stream']['uri']))) {
                $this->_removeFile($ret['Stream']['uri']) ;
            }
        }
		$data['uri'] = (DS == "/")? $targetPath : str_replace(DS, "/", $targetPath);
		// Create object
		return $this->_create($data) ;
	}

	/**
	 * Create object for $data
	 * 
	 * @param array $data
	 * @return int (or other type, according to primaryKey type for model)
	 * @throws BEditaMIMEException
	 * @throws BEditaSaveStreamObjException
	 */
	private function _create(&$data) {
		if (!$modelType = $this->_getTypeFromMIME($data["mime_type"])) {
		    if (!empty($data["object_type_id"])) {
		        $modelType["name"] = Configure::read("objectTypes." . $data["object_type_id"] . ".model");
		    } else {
			    throw new BEditaMIMEException(__("MIME type not found",true).": ".$data['mime_type']) ;
		    }
		}
		if (!empty($data["id"])) {
			$stream = ClassRegistry::init("Stream")->read(array('mime_type','uri'), $data["id"]) ;
			$object_type_id = ClassRegistry::init("BEObject")->field("object_type_id", array("id" => $data["id"]));
			$prevModel = Configure::read("objectTypes." . $object_type_id . ".model");
			// change object type
			if ($modelType["name"] != $prevModel) {
				$data["object_type_id"] = Configure::read("objectTypes." . Inflector::underscore($modelType["name"]) . ".id");
				// delete old data from specific table
				$prevMediaModel = ClassRegistry::init($prevModel);
				$prevMediaModel->Behaviors->disable('DeleteObject');
				$prevMediaModel->delete($data["id"], false);
				$prevMediaModel->Behaviors->enable('DeleteObject');
				// delete file on filesystem
				if(($stream["Stream"]["uri"] && !$this->_isURL($stream["Stream"]["uri"]))) {
					$this->_removeFile($stream["Stream"]["uri"]) ;
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

	/**
	 * Set image size for $data
	 * 
	 * @param $data $data
	 */
	private function setImageData(&$data) {
		$this->getImageSize($data);
	}

	/**
	 * Set application data (name, type, label)
	 * 
	 * @param array $data
	 * @param string $application_name
	 */
	private function setApplicationData(&$data, $application_name) {
		$data["application_name"] = $application_name;
		$app_details = Configure::read("validate_resource.mime.Application");
		$data["application_type"] = $app_details[$application_name]["application_type"];
		$data["application_label"] = $app_details[$application_name]["label"];
		if ($application_name == "flash") {
			$this->getImageSize($data);
		}
	}

	/**
	 * get image size for $data
	 * 
	 * @param array $data
	 */
	private function getImageSize(&$data) {
		$path = ($this->_isURL($data["uri"]))? $data["uri"] : Configure::read("mediaRoot") . $data['uri'];
		if ( $imageSize =@ getimagesize($path) )
		{
			if (!empty($imageSize[0]))
				$data["width"] = $imageSize[0];
			if (!empty($imageSize[1]))
				$data["height"] = $imageSize[1];
		}
	}

	/**
	 * get category data for media $data
	 * 
	 * @param array $data
	 * @param string $modelType
	 * @return array category
	 */
	private function getCategoryMediaType($data, $modelType) {
		$cat = array();
		// if empty mediatype get it from mime type or model name
		if (empty($data['mediatype'])) {
			include(BEDITA_CORE_PATH . DS . "config" . DS . "mediatype.ini.php");
			if(!empty($config["mediaTypeMapping"][$data['mime_type']])) {
				$data['mediatype'] = $config["mediaTypeMapping"][$data['mime_type']];
			} else if($modelType != "BEFile") {
				$data['mediatype'] = Inflector::underscore($modelType);
			}
		}

		//check and assign category		
		if (!empty($data['mediatype'])) {
			$category = ClassRegistry::init("Category");
			$objetc_type_id = Configure::read("objectTypes." . Inflector::underscore($modelType) . ".id");
			$cat = $category->checkMediaType($objetc_type_id, $data['mediatype']);	
		}
		return $cat;
	}

	/**
	 * If $path is an URL, return TRUE
	 *
	 * @param string $path
	 * @return boolean
	 */
	private function _isURL($path) {
		$conf 		= Configure::getInstance() ;
		if(preg_match($conf->validate_resource['URL'], $path)) {
			return true ;
		} else {
			return false ;
		}
	}

	/**
	 * If $URL is valid, return TRUE
	 * 
	 * @param string $URL
	 * @return boolean
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
	 * @return mixed array|boolean
	 */
	private function _getTypeFromMIME($mime) {
		$conf 		= Configure::getInstance() ;
		if(empty($mime)) {
			return false ;
		}
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
	 * put mime type checking uri
	 * 
	 * @param array $data
	 * @throws BEditaInfoException
	 */
	public function getInfoURL(&$data) {
		if(!(isset($data['name']) && !empty($data['name']))) {
			$data['name']  = basename($data["uri"]) ;
		}
		if (empty($data['title'])) {
			$data['title'] = $data['name'];
		}
		// get mime type
		if (!($headers = @get_headers($data["uri"],1))) {
			throw new BEditaInfoException(__("URL unattainable",true));
		}
		// if redirect response try to reach the redirect location
		if (stristr($headers[0], "redirect")) {
			if (empty($headers["Location"])) {
				throw new BEditaInfoException(__("URL unattainable",true));
			}
			if (!($headers = @get_headers($headers["Location"],1))) {
				throw new BEditaInfoException(__("URL unattainable",true));
			}
		}
		if (!strstr($headers[0], "200") && !strstr($headers[0], "302")) {
			throw new BEditaInfoException(__("URL unattainable",true));
		}
		$data["mime_type"] = ClassRegistry::init("Stream")->getMimeTypeByExtension($data["uri"]);
		if (!$data["mime_type"]) {
			$data["mime_type"] = (!empty($headers["Content-Type"]))? $headers["Content-Type"] : $data["mime_type"] = "beexternalsource";
		}
	}

	/**
	 * get mime type of stream
	 * 
	 * @param array $data
	 * @return string
	 */
	public function getMimeType($data) {
		$mime_type = ClassRegistry::init("Stream")->getMimeType($data["tmp_name"], $data["name"]);
		// if not retrieved mime type from file or extension, get mime type passed by browser
		if (empty($mime_type)) {
			$mime_type = $data['type'];
		}
		return $mime_type;
	}

    /**
     * Create target with source (temporary file), through transactional object
     *
     * @param string $sourcePath
     * @param string $targetPath
     * @return mixed boolean|string
     */
    public function putFile($sourcePath, $targetPath) {
        if (empty($targetPath)) {
            return false;
        }

        // Temporary directories to create
        $tmp = Configure::read('mediaRoot') . $targetPath;
        $stack = array();
        $dir = dirname($tmp);
        while ($dir != Configure::read('mediaRoot')) {
            if (is_dir($dir)) {
                break;
            }
            array_push($stack, $dir);
            $dir = dirname($dir);
        } 
        unset($dir);

        // Creating directories
        while (($current = array_pop($stack))) {
			if (!$this->Transaction->mkdir($current)) {
                return false;
            }
        }
        return $this->Transaction->makeFromFile($tmp, $sourcePath);
    }

	/**
	 * Delete a file from file system with transactional object
	 *
	 * @param string $path
	 * @return boolean
	 */
    private function _removeFile($path) {
        // #769 remove from cache if 'thumbs' cache is set
        $cacheThumbs = Cache::settings('thumbs');
        if (!empty($cacheThumbs)) {
            Cache::delete($path, 'thumbs');
        }
	    if (DS != "/") {
			$path = str_replace("/", DS, $path);
		}
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
				} else {
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
    * @param string $name The file name
    * @param string $prefix The prefix used for building path
    * @return string
    */
    public function getPathTargetFile(&$name, $prefix = null)  {
        $targetFilePath = BeLib::getInstance()->uniqueFilePath($name, $prefix);
        $name = pathinfo($targetFilePath, PATHINFO_BASENAME);
        return $targetFilePath;
    }

	/**
	 * build friendly url name from filename
	 * nameFile.ext become name-file.jpg
	 * nameFile become name-file
	 *
	 * @param  string $filename
	 * @return string
	 */
	public function buildNameFromFile($filename) {
		$tmp = $this->splitFilename($filename);
		if(!empty($tmp[1])) {
			$name = BeLib::getInstance()->friendlyUrlString($tmp[0]) . '.' . $tmp[1];
		} else {
			$name = BeLib::getInstance()->friendlyUrlString($tmp[0]);
		}
		return $name;
	}

	/**
	 * Split file name by dot [to separate file name from file extension]
	 * 
	 * @param string $filename
	 * @return array
	 */
	public function splitFilename($filename) {
		$pos = strrpos($filename, '.');
		if ($pos === false) { // dot is not found in the filename
			return array($filename, ''); // no extension
		} else {
			$basename = substr($filename, 0, $pos);
			$extension = substr($filename, $pos+1);
			return array($basename, $extension);
		}
	}
}
?>