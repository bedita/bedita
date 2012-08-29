<?php

class PagesController extends FrontendController {

	var $helpers = array("BeFront");
	var $uses = array() ;
	
	/**
	 * load common data, for all frontend pages...
	 */ 
	protected function beditaBeforeFilter() {
		// uncomment to use ctp file instead of tpl file for templates
//		$this->view = "View";
		$this->set('feedNames', $this->Section->feedsAvailable(Configure::read("frontendAreaId")));
		$this->set('sectionsTree', $this->loadSectionsTree(Configure::read("frontendAreaId")));
		
		if ($this->RequestHandler->isAjax()){
			return;
		}
		
		$conf = Configure::getInstance();
		
		$this->loadPublications();
	
		
		$this->baseLevel = true;
		$this->set("menu", $this->loadSectionsTree($conf->frontendAreaId,true,array("footer","home"),1));
		$this->baseLevel = false;
	}
	
	public function index() {
		
		$intro = $this->loadSectionObjectsByNick("home");
		$this->set('intro', @$intro['childContents']);
		
	}
	
	function getMedia($name) {
		
		//if(!$this->RequestHandler->isAjax()) {
		 //   throw new BeditaException(__("try to call an ajax method through an http request", true));
		//}
		$this->layout = "ajax";
		$media_id = is_numeric($name) ? $name : $this->BEObject->getIdFromNickname($name);
		$object_type_id = $this->BEObject->findObjectTypeId($media_id);
		$conf = Configure::getInstance();
		$media_obj_type_ids = array(
			$conf->objectTypes["image"]["id"],
			$conf->objectTypes["video"]["id"],
			$conf->objectTypes["audio"]["id"],
			$conf->objectTypes["b_e_file"]["id"],
			$conf->objectTypes["application"]["id"]
		);
		if (in_array($object_type_id, $media_obj_type_ids)) {
			$this->set("object", $this->loadObj($media_id));
		}
	}
}

?>