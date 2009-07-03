<?php

class PagesController extends FrontendController {

	var $helpers 	= array('Rss');
	var $components = array('BeTree','BeNewsletter');
	var $uses = array('BEObject','Tree','MailGroup') ;
	
	/**
	 * load common data, for all frontend pages...
	 */ 
	protected function beditaBeforeFilter() {
		$this->set('feedNames', $this->Section->feedsAvailable(Configure::read("frontendAreaId")));
		$this->set('sectionsTree', $this->loadSectionsTree(Configure::read("frontendAreaId")));
		// uncomment to use ctp file instead of tpl file for templates
//		$this->view = "View"; 
	}

	public function testFormSubscribeNewsletter() {
		$areaId = Configure::read("frontendAreaId");
		$areaNick = $this->BEObject->getNicknameFromId($areaId);
		$mg = $this->MailGroup->getGroupsByArea($areaId, null, null);
		if(!empty($mg)) {
			$this->set("groupsByArea",$mg[$areaNick]);
		}
	}

	public function subscribeNewsletter() {
		try {
			$this->BeNewsletter->subscribe($this->data);
			$this->set('result','subscribed');
		} catch(BeditaException $ex) {
			$this->set('result',$ex->getMessage());
		}
	}

	public function confirmSubscribeNewsletter() {
		try {
			$this->BeNewsletter->confirmSubscribe($this->params['named']);
			$this->set('result','subscribed');
		} catch(BeditaException $ex) {
			$this->set('result',$ex->getMessage());
		}
	}

	public function unsubscribeNewsletter() {
		try {
			$this->BeNewsletter->unsubscribe($this->params['named']);
			$this->set('result','unsubscribed');
		} catch(BeditaException $ex) {
			$this->set('result',$ex->getMessage());
		}
	}

	public function index() {
	}

}

?>