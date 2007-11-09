<?php
class FilesController extends AppController {
	var $name = 'Images';
	var $helpers = array('Html');
	var $components = array('SwfUpload');
//	var $components = array('Transaction', 'BeFileHandler');
//	function upload () {
//		if(empty($this->data))
//			throw new BEditaActionException($this, __("No data", true));
//
//		$this->Transaction->begin() ;
//		$this->BeFileHandler->save($this->data) ;
//		$this->Transaction->end();
//	}

	function upload () {
		if (isset($this->params['form']['Filedata'])) {
			// upload the file
			// use these to configure the upload path, web path, and overwrite settings if necessary
			$this->SwfUpload->uploadpath = MEDIA_ROOT . DS;
			$this->SwfUpload->webpath = MEDIA_URL . DS;
			//$this->SwfUpload->overwrite = true;  //by default, SwfUploadComponent does NOT overwrite files

			if ($this->SwfUpload->upload()) {
				// save the file to the db, or do whateve ryou want to do with the data
				$this->params['form']['Filedata']['name'] = $this->SwfUpload->filename;
				$this->params['form']['Filedata']['path'] = $this->SwfUpload->webpath;
				$this->params['form']['Filedata']['fspath'] = $this->SwfUpload->uploadpath . $this->SwfUpload->filename;
				$this->data['File'] = $this->params['form']['Filedata'];
				if (!($file = $this->Image->save($this->data))) {
					$this->Session->setFlash('Database save failed');
				} else {
					$this->Session->setFlash('File Uploaded: ' . $this->SwfUpload->filename . '; Database id is ' . $this->File->getLastInsertId() . '.');
				}
			} else {
	    			$this->Session->setFlash($this->SwfUpload->errorMessage);
			}
		}
	}

	function open($id) {
	    $file = $this->get($id);
	    if (isset($file)) {
	        $this->redirect($file['File']['path'] . $file['File']['name']);
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