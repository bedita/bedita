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
 * Componente per la gestione dell'upload dei file, salvataggio, modifica, delete
 * e interfaccia ai file remoti.
 * I file vanno manipolati utilizzando il componente Transaction....
 * 
 * Dati da passare per salvare/modificare un oggetto co n un file:
 * 
 * 		path		Indica dove il file temporaneo con i dati
 * 					o l'URL dove risiede il file.
 * 		name		Nome del file originale
 * 		type		MIME type, se assente cerca di ricavarlo dal nome file o dall'intestazione (@todo)
 * 		size		Dimensione del file se un URL tenta di leggerla da remoto 
 *  
 * Se le operazioni di salvataggio e cancellazione vanno fate utilizzando questo componente:
 * - Gestisce i file in modo transazionale (modifiche definitive con un $Transaction->commit() )
 * - Esegue il controllo di tipo (MIME) e crea un oggetto di tipo corretto
 * - Per gli URL esegue un controllo (regex) sull'URL
 * - torna in modo corretto e trasparente l'URL al file
 * - Torna le seguenti eccezioni:
 * 		BEditaFileExistException		// File gia' presente nel sistema sistema - nella creazione
 * 		BEditaInfoException				// Informazioni del file non accessibili
 * 		BEditaMIMEException				// MIME type del file non trovato o non corrispondente al tipo di obj
 * 		BEditaURLRxception				// Violazione regole dell'URL
 * 		BEditaSaveStreamObjException	// Errore creazione/ modifica oggetto 
 * 		BEditaDeleteStreamObjException	// Errore cancellazione obj
 * 
 * Se paranoid == false. Non tenta di prelevare le informazioni da remoto e quindi non serve
 * 'allow_php_fopen'. Le informazioni di MIME devono essere passate con i dati per gli URL.
 * 
 * File paths saved on DB are relative to $config['mediaRoot']
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeFileHandlerComponent extends Object {

	var $uses 		= array('BEObject', 'Stream', 'BEFile', 'Image', 'Audio', 'Video') ;
	var $components = array('Transaction');
	var $paranoid 	= true ;
	
	// Errors on save
	var $validateErrors = false ;

	function __construct() {
		foreach ($this->uses as $model) {
			if(!class_exists($model))
				App::import('Model', $model) ;
			$this->{$model} = new $model() ;
		}
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
		if(isset($conf->validate_resorce['paranoid'])) $this->paranoid  = (boolean) $conf->validate_resorce['paranoid'] ;
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
		if(!($path = $this->Stream->read("path", $id))) return true ;
		$path = (isset($path['Stream']['path']))?$path['Stream']['path']:$path ;
		// If file path is local, delete
		if(!$this->_isURL($path)) {
			if(!$this->Transaction->rm(Configure::read("mediaRoot").$path)) return false ;
		}
		$model = $this->BEObject->getType($id) ;
		if(!class_exists($model)) {
			loadModel($model) ;
		}
		$mod = new $model() ;
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
		if(!($ret = $this->Stream->read("path", $id))) return false ;
		$path = $ret['Stream']['path'] ;
		return ($this->_isURL($path)) ? $path : (Configure::read("mediaUrl").$path);
	}

	/**
	 * Return object path, URL if remote file
	 * @param integer $id	object id
	 */
	function path($id) {
		if(!($ret = $this->Stream->read("path", $id))) return false ;
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
		$ret = $this->Stream->find($clausoles, 'id') ;
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
			// Permesso di usare file remoti
			if(!ini_get('allow_url_fopen')) 
				throw new BEditaAllowURLException(__("You can't use remote file",true)) ;
			
			// Preleva MIME type
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
				$stream = $this->Stream->read('path', $data['id']);
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

		// check if hash file exists
		if (!$clone && ($stream_id = $this->Stream->field("id", array("hash_file" => $data["hash_file"]))) ) {
			throw new BEditaFileExistException(__("File already exists in the filesystem",true)) ;
		}
		
		$targetPath	= $this->getPathTargetFile($data['name']);
		
		if (!empty($data["id"])) {
			$ret = $this->Stream->read('path', $data["id"]);
				
			// Se e' presente un path ad file su file system, cancella
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
			throw new BEditaMIMEException(__("MIME type not found",true).": ".$data['mime_type'].
					" - matches: ".$modelType) ;
		}
		
		if (!empty($data["id"])) {
			$stream = $this->Stream->read(array('mime_type','path'), $data["id"]) ;
			$object_type_id = $this->BEObject->field("object_type_id", array("id" => $data["id"]));
			$prevModel = Configure::read("objectTypes." . $object_type_id . ".model");
			
			// change object type
			if ($modelType != $prevModel) {
				
				
				$data["object_type_id"] = Configure::read("objectTypes." . strtolower($modelType) . ".id");
				// delete old data from specific table
				$this->{$prevModel}->Behaviors->disable('DeleteObject');
				$this->{$prevModel}->del($data["id"], false);
				$this->{$prevModel}->Behaviors->enable('DeleteObject');
				
				// delete file on filesystem
				if(($stream["Stream"]["path"] && !$this->_isURL($stream["Stream"]["path"]))) {
					$this->_removeFile($stream["Stream"]["path"]) ;		
				}
			}
		}
		
		switch($modelType) {
			case 'BEFile':
			case 'Audio':
			case 'Video':
				break ;
			case 'Image':
				$path = ($this->_isURL($data["path"]))? $data["path"] : Configure::read("mediaRoot") . $data['path'];
				if ( $imageSize =@ getimagesize($path) )
				{
					if (!empty($imageSize[0]))
						$data["width"] = $imageSize[0];
					if (!empty($imageSize[1]))
						$data["height"] = $imageSize[1];
				}
				break ;
		}
		
		$data['Category'] = (!empty($data['Category']))? array_merge($data['Category'],$this->getCategoryMediaType($data,$modelType)) : $this->getCategoryMediaType($data,$modelType);
		
		$this->{$modelType}->create();
		
		if(!($ret = $this->{$modelType}->save($data))) {
			throw new BEditaSaveStreamObjException(__("Error saving stream object",true), $this->{$modelType}->validationErrors) ;
		}
		return ($this->{$modelType}->{$this->{$modelType}->primaryKey}) ;
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
	 * Torna TRUE se il path e' un URL
	 *
	 * @param unknown_type $path
	 */
	private function _isURL($path) {
		$conf 		= Configure::getInstance() ;
		
		if(preg_match($conf->validate_resorce['URL'], $path)) return true ;
		else return false ;
	}

	/**
	 * Torna true se l'URL supera le regole definite in configurazione
	 */
	private function _regularURL($URL) {
		$conf 		= Configure::getInstance() ;
		
		foreach ($conf->validate_resorce['allow'] as $reg) {
			if(preg_match($reg, $URL)) return true ;
		}

		return false ;	
	}
			
	/**
	 * Torna il nome del model a cui MIME corrisponde
	 *
	 * @param string $mime	MIME  tyep da cercare 
	 * @param string $model	Se presente verifica se puo' tornare il tipo di oggetto dato
	 */
	private function _getTypeFromMIME($mime, $model = null) {
		$conf 		= Configure::getInstance() ;
		if(@empty($mime))	
			return false ;
		if(isset($model) && isset($conf->validate_resorce['mime'][$model] )) {
			$regs = $conf->validate_resorce['mime'][$model] ;
			foreach ($regs as $reg) {
				if(preg_match($reg, $mime)) 
					return $model ;
			}
		} else {
			$models = $conf->validate_resorce['mime'] ;
			foreach ($models as $model => $regs) {
				foreach ($regs as $reg) {
					if(preg_match($reg, $mime)) 
						return $model ;
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
		
		if (!strstr($headers["0"], "200"))
			throw new BEditaInfoException(__("URL unattainable",true));
		
		$data["mime_type"] = (!empty($headers["Content-Type"]))? $headers["Content-Type"] : $data["mime_type"] = "beexternalsource";
		
	}
	
	/**
	 * Crea target con source (file temporaneo) con l'oggetto transazionale
	 *
	 * @param string $sourcePath
	 * @param string $targetPath
	 */
	private function _putFile($sourcePath, $targetPath) {
		if(empty($targetPath)) return false ;
		
		// Determina quali directory creare per registrare il file
		$tmp = Configure::read("mediaRoot") . $targetPath ;
		$stack = array() ;
		$dir = dirname($tmp) ;
		
		while($dir != Configure::read("mediaRoot")) {
			if(is_dir($dir)) break ;
			
			array_push($stack, $dir) ;
			
			$dir = dirname($dir) ;
		} 
		unset($dir) ;
		
		// Crea le directory non ancora presenti
		while(($current = array_pop($stack))) {
			if(!$this->Transaction->mkdir($current)) return false ;
		}
		
		return $this->Transaction->makeFromFile($tmp, $sourcePath) ;
	}	

	/**
	 * Cancella un file da file system con l'oggetto transazionale
	 *
	 * @param string $path
	 */
	private function _removeFile($path) {
		$path = Configure::read("mediaRoot") . $path ;
		
		if (file_exists($path)) {
		
			// Cancella
			if(!$this->Transaction->rm($path))
				return false ;
			
			// Se la directory contenitore e' vuota, la cancella
			$dir = dirname($path) ;
			while($dir != Configure::read("mediaRoot")) {
				// Verifica che sia vuota
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
				
				// Se vuota cancella altrimenti interrompe
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
  	 * Torna il path dove inserire il file uploadato
  	 *
  	 * @param string $name 	Nome del file
  	 */
	function getPathTargetFile(&$name)  {
   		
   		// Determina le directory dove salvare il file
		$md5 = md5($name) ;
		preg_match("/(\w{2,2})(\w{2,2})(\w{2,2})(\w{2,2})/", $md5, $dirs) ;
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


////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////

////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * 		BEditaIOException		// Generic I/O Error
 */
class BEditaIOException extends BeditaException
{
} ;

/**
 * 		BEditaAllowURLException		// Non � permesso l'uso di file remoti
 */
class BEditaAllowURLException extends BeditaException
{
} ;

/**
 * 		BEditaFileExistException		// File gia' presente in sistema - nella creazione
 */
class BEditaFileExistException extends BeditaException
{
}

/**
 * 		BEditaMIMEException				// MIME type del file non trovato o non corrispondente al tipo di obj
 */
class BEditaMIMEException extends BeditaException
{
}

/**
 * 		BEditaURLException				// Violazione regole dell'URL
 */
class BEditaURLException extends BeditaException
{
} ;

/**
 * 		BEditaInfoException				// Informazioni non accessibili
 */
class BEditaInfoException extends BeditaException
{
} ;


class BEditaSaveStreamObjException extends BeditaException
{
} ;

/**
 * 		BEditaDeleteStreamObjException	// Errore cancellazione obj
 */
class BEditaDeleteStreamObjException extends BeditaException
{
}

class BEditaMediaProviderException extends BeditaException
{
}

/**
 * 		BEditaUploadPHPException	// handle php upload errors
 */
class BEditaUploadPHPException extends BeditaException
{
	private $phpError = array(
							UPLOAD_ERR_INI_SIZE		=> "The uploaded file exceeds the upload_max_filesize directive in php.ini",
							UPLOAD_ERR_FORM_SIZE	=> "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
							UPLOAD_ERR_PARTIAL		=> "The uploaded file was only partially uploaded",
							UPLOAD_ERR_NO_FILE		=> "No file was uploaded",
							UPLOAD_ERR_NO_TMP_DIR	=> "Missing a temporary folder",
							UPLOAD_ERR_CANT_WRITE	=> "Failed to write file to disk",
							UPLOAD_ERR_EXTENSION	=> "File upload stopped by extension"
							); 
	
	public function __construct($numberError, $details = NULL, $res  = AppController::ERROR, $code = 0) {
		parent::__construct($this->phpError[$numberError], $details, $res, $code);
	}
}
?>