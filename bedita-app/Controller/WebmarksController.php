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
	var $components = array('BeLangText', 'BeFileHandler');

	var $uses = array('BEObject', 'Link', 'Tree','Category','Area') ;
	protected $moduleName = 'webmarks';
	
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
		$this->request->data['url'] = $this->Link->checkUrl($this->request->data['url']);
		$conditions = array('url' => $this->request->data['url']);
		if (!empty($this->request->data["id"])) {
			$conditions[] = "Link.id <>" . $this->request->data["id"];
		}
		$link =  $this->Link->find('all',array('conditions' => $conditions));
		if(!empty($link)) {
			$this->Link->id = $link[0]['id'];
			$this->userWarnMessage(__("webmark already present")." - ".$link[0]['id'] . " - " .$link[0]['title']);
			$this->setResult("WARN");
			return;
		}
		// try to read title from URL directly
		if(empty($this->request->data['title'])) { 
			$this->request->data['title'] = $this->Link->readHtmlTitle($this->request->data['url']);
		}
		$this->Transaction->begin();
		$this->saveObject($this->Link);
		$this->Transaction->commit();
		$this->userInfoMessage(__("webmark saved")." - ".$this->request->data["title"]);
		$this->eventInfo("webmark [". $this->request->data["title"]."] saved");
	}

	public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Link");
		$this->userInfoMessage(__("News deleted") . " -  " . $objectsListDeleted);
		$this->eventInfo("News $objectsListDeleted deleted");
	}

	public function deleteSelected() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Link");
		$this->userInfoMessage(__("News deleted") . " -  " . $objectsListDeleted);
		$this->eventInfo("News $objectsListDeleted deleted");
	}

	public function categories() {
		$this->showCategories($this->Link);
	}

	public function saveCategories() {
		$this->checkWriteModulePermission();
		if(empty($this->request->data["label"])) 
			throw new BeditaException( __("No data"));
		$this->Transaction->begin() ;
		if(!$this->Category->save($this->request->data)) {
			throw new BeditaException(__("Error saving tag"), $this->Category->validationErrors);
		}
		$this->Transaction->commit();
		$this->userInfoMessage(__("Category saved")." - ".$this->request->data["label"]);
		$this->eventInfo("category [" .$this->request->data["label"] . "] saved");
	}
	
	public function deleteCategories() {
		$this->checkWriteModulePermission();
		if(empty($this->request->data["id"])) 
			throw new BeditaException( __("No data"));
		$this->Transaction->begin() ;
		if(!$this->Category->delete($this->request->data["id"])) {
			throw new BeditaException(__("Error saving tag"), $this->Category->validationErrors);
		}
		$this->Transaction->commit();
		$this->userInfoMessage(__("Category deleted") . " -  " . $this->request->data["label"]);
		$this->eventInfo("Category " . $this->request->data["id"] . "-" . $this->request->data["label"] . " deleted");
	}

	public function checkUrl() {
		$this->request->data = $this->request->params['form'];
		$this->Link->id = $this->request->params['form']['id'];
		$http_code = $this->Link->responseForUrl($this->request->params['form']['url']);
		$http_response_date = date('Y-m-d H:m:s',time());
		$saved_url = $this->Link->field("url",array("id" => $this->request->params['form']['id']));
		if($saved_url == $this->request->params['form']['url']) {
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
		if(!empty($this->request->params['form']['objects_selected'])) {
			$objectsToModify = $this->request->params['form']['objects_selected'];
			$this->Transaction->begin() ;
			$modelName = "Link";
			foreach ($objectsToModify as $id) {
				$model = $this->loadModelByType($modelName);
				$model->id = $id;
				$link = $model->findById($id);
				$date = new DateTime();
				if(!$model->saveField("http_code",$model->responseForUrl($link['url'])))
					throw new BeditaException(__("Error checking url for link: ") . $id);
				if(!$model->saveField("http_response_date",$date->format(DATE_RFC3339)))
					throw new BeditaException(__("Error checking url for link: ") . $id);
				$objectsListDesc .= $id . ",";
			}
			$this->Transaction->commit() ;
		}
		return trim($objectsListDesc, ",");
	}

	protected function forward($action, $esito) {
		$REDIRECT = array(
				"cloneObject"	=> 	array(
										"OK"	=> "/webmarks/view/{$this->Link->id}",
										"WARN"	=> "/webmarks/view/{$this->Link->id}", 
										"ERROR"	=> "/webmarks/view/{$this->Link->id}" 
										),
				"save"	=> 	array(
										"OK"	=> "/webmarks/view/{$this->Link->id}",
										"WARN"	=> "/webmarks/view/{$this->Link->id}", 
										"ERROR"	=> "/webmarks/view/" 
									),
				"delete" =>	array(
										"OK"	=> $this->fullBaseUrl . $this->Session->read('backFromView'),
										"ERROR"	=> $this->referer() 
									),
				"deleteSelected" =>	array(
										"OK"	=> $this->referer(),
										"ERROR"	=> $this->referer() 
									),
				"saveCategories" 	=> array(
										"OK"	=> "/webmarks/categories",
										"ERROR"	=> "/webmarks/categories"
										),
				"deleteCategories" 	=> array(
										"OK"	=> "/webmarks/categories",
										"ERROR"	=> "/webmarks/categories"
										),
				"addItemsToAreaSection"	=> 	array(
										"OK"	=> $this->referer(),
										"ERROR"	=> $this->referer() 
										),
				"moveItemsToAreaSection"	=> 	array(
										"OK"	=> $this->referer(),
										"ERROR"	=> $this->referer() 
										),
				"removeItemsFromAreaSection"	=> 	array(
										"OK"	=> $this->referer(),
										"ERROR"	=> $this->referer() 
										),
				"changeStatusObjects"	=> 	array(
										"OK"	=> $this->referer(),
										"ERROR"	=> $this->referer() 
										),
				"checkMultiUrl"		=> 	array(
										"OK"	=> $this->referer(),
										"ERROR"	=> $this->referer() 
										),
				"assocCategory"	=> 	array(
										"OK"	=> $this->referer(),
										"ERROR"	=> $this->referer() 
										),
				"disassocCategory"	=> 	array(
										"OK"	=> $this->referer(),
										"ERROR"	=> $this->referer() 
										)
		) ;
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}

}

?>