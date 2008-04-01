<?php
class FilesController extends AppController {
	
	var $helpers 	= array('Html');
	var $uses		= array('Stream') ;
	var $components = array('Transaction', 'SwfUpload', 'BeUploadToObj');

	function upload () {
		if (!isset($this->params['form']['Filedata'])) 
			return ;
		try {
			$this->Transaction->begin() ;
			$id = $this->BeUploadToObj->upload($this->params['form']['Filedata']) ;
			$this->Transaction->commit();
		} catch(BeditaException $ex) {
			header("HTTP/1.0 " . $this->BeUploadToObj->errorCode . " Internal Server Error");
			$errTrace = $ex->getClassName() . " - " . $ex->getMessage()."\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();   
			$this->handleError($ex->getMessage(), $ex->getMessage(), $errTrace);
			$this->setResult(self::ERROR);
		}
	}
	
	function uploadAjax () {
		if (!isset($this->params['form']['Filedata'])) return ;
		$this->layout = "empty";
		try {
			$this->Transaction->begin() ;
			$id = $this->BeUploadToObj->upload($this->params['form']['Filedata']) ;
			$this->Transaction->commit();
			$this->set("fileName", $this->params['form']['Filedata']["name"]);
		} catch(BeditaException $ex) {
			$errTrace = $ex->getClassName() . " - " . $ex->getMessage()."\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();   
			$this->handleError($ex->getMessage(), $ex->getMessage(), $errTrace);
			$this->setResult(self::ERROR);
			$this->set("errorMsg", $ex->getMessage());
		}
	}

	function uploadAjaxMediaProvider () {	
		if (!isset($this->params['form']['url']) || !isset($this->params['form']['title'])) return ;
		$this->params['form']['title'] = trim($this->params['form']['title']) ;
		$this->layout = "empty";
		try {
			$this->Transaction->begin() ;
			$filename = $this->BeUploadToObj->uploadFromMediaProvider($uid) ;
			$this->Transaction->commit();
			$this->set("filename", $filename);
			
		} catch(BeditaException $ex) {
			$errTrace = $ex->getClassName() . " - " . $ex->getMessage()."\nFile: ".$ex->getFile()." - line: ".$ex->getLine()."\nTrace:\n".$ex->getTraceAsString();   
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
		//die("aho");
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
		 	)
       );
       if(isset($REDIRECT[$action][$esito])) 
          return $REDIRECT[$action][$esito] ;
       return false;
     }
}
?>