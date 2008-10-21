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
 * 
 * @link			http://www.bedita.com
 * @version			$Revision: $
 * @modifiedby 		$LastChangedBy: $
 * @lastmodified	$LastChangedDate: $
 * 
 * $Id: $
 */
class FilesController extends AppController {
	
	var $helpers 	= array('Html');
	var $uses		= array('Stream','BEObject') ;
	var $components = array('Transaction', 'SwfUpload', 'BeUploadToObj');

	function upload () {
		if (!isset($this->params['form']['Filedata'])) 
			return ;
		try {
			$this->Transaction->begin() ;
			$id = $this->BeUploadToObj->upload() ;
			$this->Transaction->commit();
		} catch(BeditaException $ex) {
			header("HTTP/1.0 " . $this->BeUploadToObj->errorCode . " Internal Server Error");
			$errTrace = get_class($ex) . " - " . $ex->getMessage()."\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();   
			$this->handleError($ex->getMessage(), $ex->getMessage(), $errTrace);
			$this->setResult(self::ERROR);
		}
	}
	
	function uploadAjax () {
		$this->layout = null;
		try {
			$this->Transaction->begin() ;
			$this->params['form']['streamUploaded']['lang'] = $this->data["lang"];
			$id = $this->BeUploadToObj->upload($this->params["form"]["streamUploaded"]) ;
			$this->Transaction->commit();
			$this->set("fileId", $id);
			$this->set("fileUploaded", true);
		} catch(BeditaException $ex) {
			$errTrace = get_class($ex) . " - " . $ex->getMessage()."\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();   
			$this->handleError($ex->getMessage(), $ex->getMessage(), $errTrace);
			$this->setResult(self::ERROR);
			$this->set("errorMsg", $ex->getMessage());
		}
	}

	function uploadAjaxMediaProvider () {
		$this->layout = "empty";
		try {
			if (!isset($this->params['form']['uploadByUrl']['url']))
				throw new BEditaException(__("Error during upload: missing url",true)) ;
		
			$this->params['form']['uploadByUrl']['lang'] = $this->data["lang"];
			
			$this->Transaction->begin() ;
			$id = $this->BeUploadToObj->uploadFromMediaProvider($this->params['form']['uploadByUrl']) ;
			$this->Transaction->commit();
			$this->set("fileId", $id);
			
		} catch(BeditaException $ex) {
			$errTrace = get_class($ex) . " - " . $ex->getMessage()."\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();   
			$this->handleError($ex->getMessage(), $ex->getMessage(), $errTrace);
			$this->setResult(self::ERROR);
			$this->set("errorMsg", $ex->getMessage());
		}
	}
	
	/**
	 * Delete a Stream object (using _POST filename to find stream)
	 */
	function deleteFile() {
 		if(!isset($this->params['form']['filename'])) throw new BeditaException(sprintf(__("No data", true), $id));
	 	$this->Transaction->begin() ;
	 	// Get object id from filename
		if(!($id = $this->Stream->getIdFromFilename($this->params['form']['filename']))) throw new BeditaException(sprintf(__("Error get id object: %s", true), $this->params['form']['filename']));
	 	// Delete data
	 	if(!$this->BeFileHandler->del($id)) throw new BeditaException(sprintf(__("Error deleting object: %d", true), $id));
	 	$this->Transaction->commit() ;
	 	$this->layout = "empty" ;
	}

	function open($id) {
		$file = $this->get($id);
		if (isset($file)) {
			$this->redirect($file['File']['path'] . $file['File']['name']);
			exit();
		}
	}

	function get($id) {
		return $this->BEFile->findById($id);
	}

	function beditaBeforeFilter() {
		if(isset($this->params['form']['Filedata'])) { // skip auth check, to avoid session error with swfupload via flash
			$this->skipCheck = true;
		}
	}

	/**
	 * Override AppController handleError to don't save message in session
	 */
	public function handleError($eventMsg, $userMsg, $errTrace) {
		$this->log($errTrace);
		// end transactions if necessary
		if($this->Transaction->started()) {
			$this->Transaction->rollback();
		}
	}
	
	protected function forward($action, $esito) {
		$REDIRECT = array(
			"uploadAjax" =>	array(
	 			"OK"	=> self::VIEW_FWD.'upload_ajax_response',
		 		"ERROR"	=> self::VIEW_FWD.'upload_ajax_response'
		 	),
		 	"uploadAjaxMediaProvider" => array(
	 			"OK"	=> self::VIEW_FWD.'upload_ajax_response',
		 		"ERROR"	=> self::VIEW_FWD.'upload_ajax_response'
		 	)
       );
       if(isset($REDIRECT[$action][$esito])) 
          return $REDIRECT[$action][$esito] ;
       return false;
     }
}
?>