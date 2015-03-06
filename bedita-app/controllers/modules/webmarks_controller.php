<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License 
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

/**

 */
class WebmarksController extends ModulesController {
	var $name = 'Webmarks';

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeLangText', 'BeFileHandler', 'BeSecurity');

	var $uses = array('BEObject', 'Link', 'Tree','Category','Area') ;
	protected $moduleName = 'webmarks';
	protected $categorizableModels = array('Link');
	
	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {    	
		$conf  = Configure::getInstance() ;
		$filter = array(
			'object_type_id'=>$conf->objectTypes['link']['id'],
			'Link.*'=>"",
			'count_annotation' => 'EditorNote'
		);
		$this->paginatedList($id, @$filter, $order, $dir, $page, $dim);
		$this->loadCategories($filter["object_type_id"]);
	}
	
	public function view($id = null) {
		$this->viewObject($this->Link, $id);
	}

	public function save() {
		$this->checkWriteModulePermission();
		// normalize url and look for duplicates
		$this->data['url'] = $this->Link->checkUrl($this->data['url']);
		$conditions = array('url' => $this->data['url']);
		if (!empty($this->data["id"])) {
			$conditions[] = "Link.id <>" . $this->data["id"];
		}
		$link =  $this->Link->find('all',array('conditions' => $conditions));
		if(!empty($link)) {
			$this->Link->id = $link[0]['id'];
			$this->userWarnMessage(__("webmark already present", true)." - ".$link[0]['id'] . " - " .$link[0]['title']);
			$this->setResult("WARN");
			return;
		}
		// try to read title from URL directly
		if(empty($this->data['title'])) { 
			$this->data['title'] = $this->Link->readHtmlTitle($this->data['url']);
		}
		$this->Transaction->begin();
		$this->saveObject($this->Link);
		$this->Transaction->commit();
		$this->userInfoMessage(__("webmark saved", true)." - ".$this->data["title"]);
		$this->eventInfo("webmark [". $this->data["title"]."] saved");
	}

	public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Link");
		$this->userInfoMessage(__("News deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("News $objectsListDeleted deleted");
	}

	public function deleteSelected() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Link");
		$this->userInfoMessage(__("News deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("News $objectsListDeleted deleted");
	}

	public function categories() {
		$this->showCategories($this->Link);
	}

	public function checkUrl() {
		$this->data = $this->params['form'];
		$this->Link->id = $this->params['form']['id'];
		$http_code = $this->Link->responseForUrl($this->params['form']['url']);
		$http_response_date = date('Y-m-d H:m:s',time());
		$saved_url = $this->Link->field("url",array("id" => $this->params['form']['id']));
		if($saved_url == $this->params['form']['url']) {
			$this->Link->saveField("http_code",$http_code);
			$this->Link->saveField("http_response_date",$http_response_date);
		}
		$this->set("http_code",$http_code);
		$this->set("http_response_date",$http_response_date);
		$this->layout = null;
	}

	public function checkMultiUrl() {
		$this->doMultiCheck();
	}

	private function doMultiCheck() {
		$objectsToModify = array();
		$objectsListDesc = "";
		$beObject = ClassRegistry::init("BEObject");
		if(!empty($this->params['form']['objects_selected'])) {
			$objectsToModify = $this->params['form']['objects_selected'];
			$this->Transaction->begin() ;
			$modelName = "Link";
			foreach ($objectsToModify as $id) {
				$model = $this->loadModelByType($modelName);
				$model->id = $id;
				$link = $model->findById($id);
				$date = new DateTime();
				if(!$model->saveField("http_code",$model->responseForUrl($link['url'])))
					throw new BeditaException(__("Error checking url for link: ", true) . $id);
				if(!$model->saveField("http_response_date",$date->format(DATE_RFC3339)))
					throw new BeditaException(__("Error checking url for link: ", true) . $id);
				$objectsListDesc .= $id . ",";
			}
			$this->Transaction->commit() ;
		}
		return trim($objectsListDesc, ",");
	}

    protected function forward($action, $result) {
        $moduleRedirect = array(
            'checkMultiUrl' => array(
                'OK' => $this->referer(),
                'ERROR' => $this->referer()
            )
        );
        return $this->moduleForward($action, $result, $moduleRedirect);
    }

}
