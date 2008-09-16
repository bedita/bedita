<?php

App::import('Core', 'String');
App::import('Core', 'Controller');
App::import('Controller', 'App'); // BeditaException

class DbadminShell extends Shell {

	public function rebuildIndex() {
		
		$conf = Configure::getInstance();
		$searchText = ClassRegistry::init("SearchText");
		$beObj = ClassRegistry::init("BEObject");
		$beObj->contain();
		$res = $beObj->find('all',array("fields"=>array('id')));

		foreach ($res as $r) {
			$id = $r['BEObject']['id'];
			$type = $beObj->getType($id);
			$model = ClassRegistry::init($type);
			$model->{$model->primaryKey}=$id;
			$this->out("id: $id - type: $type");
			$searchText->deleteAll("object_id=".$id);
			$searchText->createSearchText($model);
		}
	}
	
	/**
	 * update lang texts 'status' using master object status....
	 * parameter 'lang' mandatory
	 */
	public function checkLangStatus() {
		
		$lang = $this->params['lang'];
		if(empty($lang)) {
			$this->out("Language parameter -lang mandatory");
			return;
		}
		$this->out('Checking language: '.$lang);
		$langText = ClassRegistry::init("LangText");
		$objTrans = $langText->find('all', 
			array('conditions'=> array("LangText.lang = '$lang'","LangText.name = 'title'")
			,'fields'=>array('BEObject.id', 'BEObject.status')));
		if(empty($objTrans)) {
			$this->out("No translations found");
			return;
		}
		foreach ($objTrans as $obj) {
			$objId = $obj['BEObject']['id'];
			$status = $langText->find(array("LangText.name = 'status'", "LangText.lang = '$lang'", 
				"LangText.object_id = $objId"));
			if($status === false) {
				$newStatus = $obj['BEObject']['status'];
				$l = array(
		                'object_id' => $objId,
		                'lang'      => $lang, 
		                'name'   => 'status',
						'text' => $newStatus
	                );
	            $langText->create();
	            if(!$langText->save($l)) 
	                    throw new BeditaException("Error saving lang text");
				$this->out("Added lang status for obj: $objId - $lang");
			}
		}
		$this->out("Lang texts status updated");
	}

	function help() {
 	
		$this->out('Available functions:');
        $this->out('1. rebuildIndex: rebuild search texts index');
  		$this->out(' ');
 		$this->out("2. checkLangStatus: update lang texts 'status' using master object status");
        $this->out(' ');
        $this->out('    Usage: checkLangStatus -lang <lang>');
        $this->out(' ');
	}
	
}

?>