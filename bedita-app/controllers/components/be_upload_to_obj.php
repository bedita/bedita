<?php
/**
 * This component extends:
 * 
 * SwfUploadComponent - A CakePHP Component to use with SWFUpload
 * Copyright (C) 2006-2007 James Revillini <james at revillini dot com>
 * 
 * Save uploaded file into BEDITA object.
 * 
 * Functions:
 * 
 * - Creation of uploaded file object
 * 		Object created with title defined by filename
 * - Html error codes handling: 5XX
 * 		One message for each error type
 * - New error codes (bedita):
 * 		File already uploaded/present
 * 		wrong MIME type
 * 		object saved
 * - Delete objects created (undo) 
 * 
 * Data passed through _FILES in $this->params['Filedata']:
 * 	name			file name
 * 	tmp_name		temporary file name (containing data)
 * 	error			upload error
 * 	size			uploaded file size
 * 
 * through _POST:
 * 	override		true if you want allow to rewrite a file (a file with same filename already exists)
 * 
 * 
 * Error codes, 500 +:
 * 	UPLOAD_ERR_INI_SIZE		1
 * 	UPLOAD_ERR_FORM_SIZE	2
 * 	UPLOAD_ERR_PARTIAL		3
 * 	UPLOAD_ERR_NO_FILE		4
 * 	UPLOAD_ERR_NO_TMP_DIR	6
 * 	UPLOAD_ERR_CANT_WRITE	7
 * 	UPLOAD_ERR_EXTENSION	8
 *  
 *  BEDITA_FILE_EXIST		30		File exists
 * 	BEDITA_MIME				31		wrong MIME type (not recognized or not allowed)
 * 	BEDITA_SAVE_STREAM		32		Object creation error
 * 	BEDITA_DELETE_STREAM	33		Object delete error
  * 	BEDITA_PROVIDER_NOT_FOUND	34	Provider (not recognized or not allowed)
* 
 */

class BeUploadToObjComponent extends SwfUploadComponent {
	var $components	= array('BeFileHandler', 'BeBlipTv') ;

	const BEDITA_FILE_EXIST	= 30 ;
 	const BEDITA_MIME			= 31 ;
 	const BEDITA_SAVE_STREAM 	= 32;
 	const BEDITA_DELETE_STREAM 	= 33	;
 	const BEDITA_PROVIDER_NOT_FOUND	= 34 ;

 	/**
	 * Contructor function
	 * @param Object &$controller pointer to calling controller
	 */
	function startup(&$controller) {
		//keep tabs on mr. controller's params
		$this->params = $controller->params;
		$this->BeFileHandler->startup($controller) ;
	}

	/**
	 * Uploads a file to location.
	 * FLASH returns application/octect-stream as MIME type
	 * @todo: verify MIME type returned by extension or magic file.
	 * @return boolean true if upload was successful, false otherwise.
	 */
	function upload($dataStream=null) {
		$result = false ;
		if(!$this->validate()) {
			$this->errorCode = 500 + $this->params['form']['Filedata']['error'] ;
			return $result ;
		}
		// Prepare data
		$data = &$this->params['form']['Filedata'] ;
		$override = (isset($this->params['form']['override'])) ? ((boolean)$this->params['form']['override']) : false ;
		$data['title']	= (empty($dataStream['title']))? $data['name'] : $dataStream['title'];
		if (!empty($dataStream['description'])) {
			$data["description"] = $dataStream['description'];
		}
		$data['path']	= $data['tmp_name'] ;
		$data['lang']   = $this->params['form']['lang'];
		unset($data['tmp_name']) ;
		unset($data['error']) ;
		// FLASH returns application/octect-stream as MIME type
		if($data['type'] == "application/octet-stream") { 
			$old  = $data['type'] ;
			unset($data['type']) ;
			$this->BeFileHandler->getInfoURL($data['path'], $data) ;
		}
		try {
			$result = $this->BeFileHandler->save($data) ;
		} catch (BEditaFileExistException $e) {
			if($override) {
				// Modify existing object (doesn't create a new one)
				if(!($id = $this->BeFileHandler->isPresent($this->params['form']['Filedata']['path']))) return false ;
				$this->params['form']['Filedata']['id'] = $id ;
				try {
					$this->BeFileHandler->save($data) ;
				} catch (BEditaSaveStreamObjException $e) {
					$this->errorCode = 500 + self::BEDITA_SAVE_STREAM ;
					throw $e;
				}
			} else {
				$this->errorCode = 500 + self::BEDITA_FILE_EXIST ;
				throw $e ;
			}
		} catch (BEditaMIMEException $e) {
			$this->errorCode = 500 + self::BEDITA_MIME ;
			throw $e ;
		} catch (BEditaInfoException $e) {
			$this->errorCode = 500 + self::BEDITA_MIME ;
			throw $e ;
		} catch (BEditaSaveStreamObjException $e) {
			$this->errorCode = 500 + self::BEDITA_SAVE_STREAM ;
			throw $e ;
		} catch (Exception $e) {
			$this->errorCode = 500 ;
			throw $e ;
		}
		return ($result);
	}
	
	/**
	 * Create obj stream from media provider.
	 * Form must to have: url, title, lang.
	 * @return boolean true if upload was successful, false otherwise.
	 */
	function uploadFromMediaProvider(&$name) {
/*
$this->params['form']['url']   =  "http://www.blip.tv/file/829287?utm_source=featured_ep&utm_medium=featured_ep" ; 
//$this->params['form']['title']  =  "title" ; 
$this->params['form']['lang']  =  "ita" ; 
*/
		$result = false ;
		if(!$this->recognizeMediaProvider($this->params['form']['url'], $provider, $name)) {
			throw new BEditaMediaProviderException(__("Multimedia provider unsupported",true)) ;
		}
	
		// Prepare data
		switch($provider) {
			case 'youtube': {
				$data['title']		= (!empty($this->params['form']['title'])) ? trim($this->params['form']['title']) : 'youtube video';
				$data['name']		= preg_replace("/[\'\"]/", "", $data['title']) ;
				$data['type']		= "video/$provider" ;
				$data['path']		= $this->params['form']['url'] ;
				$data['lang'] 	  	= $this->params['form']['lang'];
				$data['provider']	=  $provider ;
				$data['uid']  	 	=  $name ;
			} break ;
			case 'blip': {
				if(!($this->BeBlipTv->getInfoVideo($name) )) {
					throw new BEditaMediaProviderException(__("Multimedia  not found",true)) ;
				}
				
				if(@empty($this->params['form']['title'])) $data['title'] = $this->BeBlipTv->info['title'] ;
				else $data['title'] = trim($this->params['form']['title']) ;
								
				$data['name']		= preg_replace("/[\'\"]/", "", $data['title']) ;
				$data['type']		= "video/$provider" ;
				$data['path']		= $this->BeBlipTv->info['url'] ;
				$data['lang'] 	  	= $this->params['form']['lang'];
				$data['provider']	=  $provider ;
				$data['uid']  	 	=  $name ;
			} break ;
		}

		if($this->BeFileHandler->isPresent($data['path'])) 
			throw new BEditaFileExistException(__("Video url is already in the system",true)) ;

		App::import('Model', 'Video') ;
		$Video = new Video() ;
			
		$Video->id = false ;
		if(!($ret = $Video->save($data))) {
			$this->validateErrors = $Video->validationErrors ;
			throw new BEditaSaveStreamObjException(__("Error saving stream object",true)) ;
		}
		$result =  ($data['name']) ;

		return ($result);
	}
	
	/**
	 * recognize provider from url
	 */
	private function recognizeMediaProvider($url, &$provider, &$uid) {
		$conf 		= Configure::getInstance() ;
		
		foreach($conf->media_providers as $provider => $expressions) {
			foreach($expressions as $expression) {
				if(preg_match($expression, $url, $matched)) {
					$uid = $matched[1] ;
					
					return true ;
				}	
			}
		}
		
		return false ;
	}
} ;
?>