<?php

class PagesController extends FrontendController {

	var $helpers 	= array();
	var $uses = array() ;
	
	/**
	 * load common data, for all frontend pages...
	 */ 
	protected function beditaBeforeFilter() {
		
		$conf = Configure::getInstance();
		
		//prende i rami per il menu superiore
		//$this->set("menu", $this-> loadSectionsTree($conf->frontendAreaId, false, array("home","footer"), $depth=10 ));
		$this->set("menu", $this->loadSectionsTree($conf->frontendAreaId,true,array("footer"),1));
		
		$this->set('feedNames', $this->Section->feedsAvailable(Configure::read("frontendAreaId")));
		$this->set('sectionsTree', $this->loadSectionsTree(Configure::read("frontendAreaId")));
		// uncomment to use ctp file instead of tpl file for templates
//		$this->view = "View"; 
	}
	
	public function index() {
		
		$home = $this->loadSectionObjectsByNick("chialab");
		foreach ($home['childContents'] as &$value) {
			if ($value['object_type']=="Document") $this->set('intro', @$value);
			elseif ($value['object_type']=="Card") $this->set('address', @$value);
		}
		
		//prende i progetti in homepage
		$progetti = $this->loadSectionObjectsByNick("progetti");
		$this->set('progetti', @$progetti['childContents']);
		
		

	}
}

?>