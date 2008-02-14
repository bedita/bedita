<?
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
 * 
 */

class BeUploadToObjComponent extends SwfUploadComponent {
	var $components	= array('BeFileHandler') ;

	const BEDITA_FILE_EXIST	= 30 ;
 	const BEDITA_MIME			= 31 ;
 	const BEDITA_SAVE_STREAM 	= 32;
 	const BEDITA_DELETE_STREAM 	= 33	;

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
	function upload() {
		$result = false ;
		if(!$this->validate()) {
			$this->errorCode = 500 + $this->params['form']['Filedata']['error'] ;
			return $result ;
		}
		// Prepare data
		$data = &$this->params['form']['Filedata'] ;
		$override = (isset($this->params['form']['override'])) ? ((boolean)$this->params['form']['override']) : false ;
		$data['title']	= $data['name'] ;
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
} ;
?>