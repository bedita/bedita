<?php

class PagesController extends FrontendController {

    public $helpers = array('BeFront');
    public $uses = array() ;
	
    /**
     * Load common data for all frontend pages
     */ 
    protected function beditaBeforeFilter() {
        $this->set('feedNames', $this->Section->feedsAvailable(Configure::read('frontendAreaId')));
        $this->set('sectionsTree', $this->loadSectionsTree(Configure::read('frontendAreaId')));
    }
}
