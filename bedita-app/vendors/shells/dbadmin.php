<?php

App::import("Model", "BEObject");
App::import("Model", "SearchText");
App::import('Core', 'String');
App::import('Core', 'Controller');
App::import('Controller', 'App'); // BeditaException

class DbadminShell extends Shell {

	public function rebuildIndex() {
		
		$conf = Configure::getInstance();
		$searchText = new SearchText();
		$beObj = new BEObject();
		$types = array('Event', 'Document', 'ShortNews');		

		foreach ($types as $t) {
			if(!class_exists($t)){
				App::import('Model',$t);
			}
			$model = new $t();
			$objTypeId = $conf->objectTypes[strtolower($t)];
			$res = $beObj->findObjs(null, null, array($objTypeId));
			foreach ($res['items'] as $o) {
				$model->id = $o['id'];
				$searchText = new SearchText();
				$searchText->deleteAll("object_id=".$model->id);
				$searchText->createSearchText($model);
			}
			$this->out("Objects of type $t indexed");
		}		
		$this->out("Search text index build");
	}
	
}

?>