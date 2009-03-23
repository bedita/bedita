<?php

class PagesController extends FrontendController {

	var $helpers 	= array('Rss');
	var $components = array('BeTree');
	var $uses = array('BEObject','Tree') ;
	
	protected function checkLogin() {
		$this->BeAuth->user=Configure::getInstance()->frontendUser; 
		return true;
	}	

	/**
	 * load common data, for all frontend pages...
	 */ 
	protected function beditaBeforeFilter() {
		$this->set('feedNames', $this->Section->feedsAvailable(Configure::read("frontendAreaId")));
		$this->set('sectionsTree', $this->loadSectionsTree(Configure::read("frontendAreaId")));
	}
	
	public function index() {
	}
	
}

?>