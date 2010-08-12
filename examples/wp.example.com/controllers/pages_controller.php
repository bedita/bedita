<?php

class PagesController extends FrontendController {

	var $helpers 	= array("BeFront");
	var $uses = array() ;
	protected $sectionOptions = array(
		"showAllContents" => true, 
		"itemsByType" => false, 
		"childrenParams" => array("dim" => 10)
	);
	
	/**
	 * load common data, for all frontend pages...
	 */ 
	protected function beditaBeforeFilter() {
		// uncomment to use ctp file instead of tpl file for templates
//		$this->view = "View"; 
		$this->set('feedNames', $this->Section->feedsAvailable(Configure::read("frontendAreaId")));
		$this->set('sectionsTree', $this->loadSectionsTree(Configure::read("frontendAreaId")));
		$options = array(
			"filter" => array("object_type_id" => Configure::read("objectTypes.image.id")),
			"dim" => 1
		);
		$pubImg = $this->loadSectionObjects(Configure::read("frontendAreaId"), $options);
		$this->set("headerImage", (!empty($pubImg["childContents"][0]))? $pubImg["childContents"][0] : array() );
	}

	protected function beditaBeforeRender() {
		if (!empty($this->viewVars["section"]["contentRequested"]) && !empty($this->viewVars["section"]["childContents"])) {
			for ($i=0; $i < count($this->viewVars["section"]["childContents"]); $i++) {
				if ($this->viewVars["section"]["currentContent"]["id"] == $this->viewVars["section"]["childContents"][$i]["id"]) {
					if ($i > 0) {
						$this->set("previousItem", $this->viewVars["section"]["childContents"][$i-1]);
					}
					if (!empty($this->viewVars["section"]["childContents"][$i+1])) {
						$this->set("nextItem", $this->viewVars["section"]["childContents"][$i+1]);
					}

					break;
				}
			}
		}
	}
}

?>
