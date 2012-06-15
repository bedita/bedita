<?php

class PagesController extends FrontendController {

	var $helpers = array("BeFront", "Gmaps");
	var $uses = array();
	
	/**
	 * load common data, for all frontend pages...
	 */ 
	protected function beditaBeforeFilter() {
		$this->set('feedNames', $this->Section->feedsAvailable(Configure::read("frontendAreaId")));
		$this->set('sectionsTree', $this->loadSectionsTree(Configure::read("frontendAreaId")));
		if (!empty($this->params["isAjax"])) {
			$this->layout = "ajax";
		}
		$this->set("referer", $this->referer());
	}

	protected function beditaBeforeRender() {
		if (!empty($this->viewVars["section"]["contentRequested"])) {
			$this->viewVars["section"]["currentContent"]["relations"]["attach"] = Set::combine($this->viewVars["section"]["currentContent"]["relations"]["attach"], "{n}.nickname", "{n}", "{n}.ObjectType.name");
		}
	}

	protected function homePageBeforeFilter() {
		foreach (Configure::read("objectTypes.multimedia.id") as $media_id) {
			if (empty($objectTypeCondition)) {
				$objectTypeCondition = "<> " . $media_id;
			} else {
				$objectTypeCondition .= " AND object_type_id <> " . $media_id;
			}
		}
		$this->sectionOptions["childrenParams"]["filter"] = array(
			"object_type_id" => $objectTypeCondition
		);
	}

	protected function homePageBeforeRender() {
		if (!empty($this->viewVars["section"]) && empty($this->viewVars["section"]["contentRequested"])) {
			$result = $this->loadSectionObjects($this->viewVars["section"]["id"], array(
				"filter" => array(
					"object_type_id" => Configure::read("objectTypes.image.id")
				),
				"dim" => 1
			));

			if (!empty($result["childContents"])) {
				$this->set("homeImage", $result["childContents"][0]);
			}
		}
		$this->set("home", true);
	}

	public function menu() {
		$topMenu = $this->loadSectionObjectsByNick("header-liguria");
		$bottomMenu = $this->loadSectionObjectsByNick("footer-liguria");
		$this->set("topMenu", $topMenu["childSections"]);
		$this->set("bottomMenu", $bottomMenu["childSections"]);
	}

	public function tags() {
		$this->loadTags();
	}

	public function credits() {}

}

?>