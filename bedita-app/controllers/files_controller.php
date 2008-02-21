<?php
class FilesController extends AppController {
	var $name = 'Images';
	var $helpers 	= array('Html');
	var $uses		= array('Stream') ;
	var $components = array('Transaction', 'SwfUpload', 'BeUploadToObj');

	function upload () {
		if (!isset($this->params['form']['Filedata'])) return ;
		$this->Transaction->begin() ;
		try {
			$id = $this->BeUploadToObj->upload($this->params['form']['Filedata']) ;
		} catch(BeditaException $ex) {
			header("HTTP/1.0 " . $this->BeUploadToObj->errorCode . " Internal Server Error");
			$errTrace = $ex->getClassName() . " - " . $ex->getMessage()."\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();   
			$this->handleError($ex->getMessage(), $ex->getMessage(), $errTrace);
			$this->setResult(self::ERROR);
			return ; 
		}
		$this->Transaction->commit();
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
}
?>