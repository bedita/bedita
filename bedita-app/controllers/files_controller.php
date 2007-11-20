<?php
class FilesController extends AppController {
	var $name = 'Images';
	var $helpers = array('Html');
	var $components = array('Transaction', 'SwfUpload', 'BeUploadToObj');

	function upload () {
/*
//		fwrite(fopen("/tmp/out.txt", "w+"), print_r(serialize($_POST), true) . "\n\n" . print_r(serialize($_FILES), true)) ;
		fwrite(fopen("/tmp/out.txt", "w+"), print_r($_POST, true) . "\n\n" . print_r($_FILES, true)) ;

//$errorCode = 500 + BeUploadToObjComponent::BEDITA_FILE_EXISIST ;
//header("HTTP/1.0 $errorCode Internal Server Error");
return ;
*/		
		if (!isset($this->params['form']['Filedata'])) return ;
		
		$this->Transaction->begin() ;
		try {
			$id = $this->BeUploadToObj->upload($this->params['form']['Filedata']) ;
			
		} catch(Exception $e) {
			header("HTTP/1.0 " . $this->BeUploadToObj->errorCode . " Internal Server Error");
			
			$this->Session->setFlash($this->SwfUpload->errorMessage);
			
			$this->Transaction->rollback();
			return ; 
		}
		
		$this->Transaction->commit();
	}

	/**
	 * Cancella un oggetto di tipo stream a partire da lnome del file
 	 * passato via _POST.
	 */
	function deleteFile() {
		fwrite(fopen("/tmp/out.txt", "w+"), print_r($_POST, true) . "\n\n" . print_r($_FILES, true)) ;
return ;
		
	}
	
	function open($id) {
	    $file = $this->get($id);
	    if (isset($file)) {
	        $this->redirect($file['File'	]['path'] . $file['File']['name']);
	        exit();
	    }
	}

	function get($id) {
	    //get file info
	    $file = $this->File->findById($id);
	    return $file;
	}

	function beforeFilter() {
		if(isset($this->data[0]['Filedata'])) {
			$this->params['form']['Filedata'] = $this->data[0]['Filedata'];
		}
	}
}
?>