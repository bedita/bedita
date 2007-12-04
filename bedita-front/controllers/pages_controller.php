<?php

class PagesController extends AppController {

	var $helpers 	= array();
	var $components = array('BeTree');
	var $uses	 	= array();

	protected function checkLogin() {
		// uncomment to define a default user for frontend operations 
//		$this->BeAuth->user=Configure::getInstance()->frontendUser; 
		return true; // no control access...
	}	

	public function index() {
		// test: retrieve all documents & galleries...
		$conf  = Configure::getInstance() ;
		$documents = $this->BeTree->getDiscendents(null, null, $conf->objectTypes['documentAll'])  ;
		$galleries = $this->BeTree->getDiscendents(null, null, $conf->objectTypes['gallery']);

		$this->set('galleries', $galleries['items']);
		$this->set('documents', $documents['items']);
		$this->set('shopname', "barrow!!");
	}
}

?>