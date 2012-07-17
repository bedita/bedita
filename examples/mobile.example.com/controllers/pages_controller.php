<?php

class PagesController extends FrontendController {

	var $helpers = array("BeFront");
	var $uses = array();
	
	/**
	 * load common data, for all frontend pages...
	 */ 
	protected function beditaBeforeFilter() {
		$this->set('feedNames', $this->Section->feedsAvailable(Configure::read("frontendAreaId")));
		$this->set('sectionsTree', $this->loadSectionsTree(Configure::read("frontendAreaId")));
		$this->set("referer", $this->referer());
	}

	protected function beditaBeforeRender() {
		// force layout to handle title page with jquery.mobile
		$this->layout = "default";
		if (!empty($this->viewVars["section"]["contentRequested"])) {
			$this->viewVars["section"]["currentContent"]["relations"]["attach"] = Set::combine($this->viewVars["section"]["currentContent"]["relations"]["attach"], "{n}.nickname", "{n}", "{n}.ObjectType.name");
		}
	}

	protected function homePageBeforeRender() {
		$this->set("home", true);
		$this->action = "homePage";
	}

	public function tags() {
		$this->loadTags();
	}

}

?>