<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
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
			'Link.*'=>""
		);
		$this->paginatedList($id, @$filter, $order, $dir, $page, $dim);
		
		$categories = $this->Category->find("all", array(
			"conditions" => "Category.object_type_id=".$conf->objectTypes['link']["id"],
			"contain" => array()
			)
		);
		
		$this->set("categories", $categories);
		
	}
	
	public function view($id = null) {
		$this->viewObject($this->Link, $id);
	}

	public function save() {
		$this->checkWriteModulePermission();
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

	public function categories() {
		$this->showCategories($this->Link);
	}

	public function saveCategories() {
		$this->checkWriteModulePermission();
		if(empty($this->data["label"])) 
			throw new BeditaException( __("No data", true));
		$this->Transaction->begin() ;
		if(!$this->Category->save($this->data)) {
			throw new BeditaException(__("Error saving tag", true), $this->Category->validationErrors);
		}
		$this->Transaction->commit();
		$this->userInfoMessage(__("Category saved", true)." - ".$this->data["label"]);
		$this->eventInfo("category [" .$this->data["label"] . "] saved");
	}
	
	public function deleteCategories() {
		$this->checkWriteModulePermission();
		if(empty($this->data["id"])) 
			throw new BeditaException( __("No data", true));
		$this->Transaction->begin() ;
		if(!$this->Category->del($this->data["id"])) {
			throw new BeditaException(__("Error saving tag", true), $this->Category->validationErrors);
		}
		$this->Transaction->commit();
		$this->userInfoMessage(__("Category deleted", true) . " -  " . $this->data["label"]);
		$this->eventInfo("Category " . $this->data["id"] . "-" . $this->data["label"] . " deleted");
	}

	public function checkUrl() {
		$this->data = $this->params['form'];
		$this->Link->id = $this->params['form']['id'];
		$http_code = $this->Link->responseForUrl($this->params['form']['url']);
		$http_response_date = date('Y-m-d H:m:s',time());
//		$this->Link->saveField("http_code",$http_code);
//		$this->Link->saveField("http_response_date",$http_response_date);
		$this->set("http_code",$http_code);
		$this->set("http_response_date",$http_response_date);
		$this->layout = null;
	}

	protected function forward($action, $esito) {
		$REDIRECT = array(
				"cloneObject"	=> 	array(
										"OK"	=> "/webmarks/view/{$this->Link->id}",
										"ERROR"	=> "/webmarks/view/{$this->Link->id}" 
										),
				"save"	=> 	array(
										"OK"	=> "/webmarks/view/{$this->Link->id}",
										"ERROR"	=> "/webmarks" 
									), 
				"delete" =>	array(
										"OK"	=> "/webmarks",
										"ERROR"	=> "/webmarks/view/{@$this->params['pass'][0]}" 
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
										"OK"	=> "/webmarks",
										"ERROR"	=> "/webmarks" 
										),
				"changeStatusObjects"	=> 	array(
										"OK"	=> "/webmarks",
										"ERROR"	=> "/webmarks" 
										)
		) ;
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}

}

?>