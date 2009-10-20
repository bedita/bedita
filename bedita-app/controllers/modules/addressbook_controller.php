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
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class AddressbookController extends ModulesController {
	
	var $name = 'Addressbook';
	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'BeCustomProperty', 'BeLangText', 'BeFileHandler');

	var $uses = array('BEObject','Tree', 'Category', 'Card', 'MailGroup') ;
	protected $moduleName = 'addressbook';
	
    public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$filter["object_type_id"] = $conf->objectTypes['card']["id"];
		$filter["Card.country"] = "";
		$filter["Card.company_name"] = "";
		$filter["object_user"] = "";
		$filter["count_annotation"] = "EditorNote";
		$this->paginatedList($id, $filter, $order, $dir, $page, $dim); 

		$categories = $this->Category->find("all", array(
			"conditions" => "Category.object_type_id=".$conf->objectTypes['card']["id"],
			"contain" => array()
			)
		);
		
		$this->set("categories", $categories);
	 }

	function view($id = null) {
		$this->viewObject($this->Card, $id);
		$this->set("groupsByArea", $this->MailGroup->getGroupsByArea(null, $id));
	}

	function save() {
		$this->checkWriteModulePermission();
		$this->Transaction->begin();
		$kind = ($this->data['company']==0) ? 'person' : 'cmp';
		if($kind == 'person') {
			if(!empty($this->data['person']['name']) || !empty($this->data['person']['name'])) {
				$this->data['title'] = $this->data['person']['name']." ".$this->data['person']['surname'];
			}
			$this->data['birthdate'] = $this->data['person']['birthdate'];
			$this->data['deathdate'] = $this->data['person']['deathdate'];
		} else {
			if(!empty($this->data['cmp']['company_name'])) {
				$this->data['title'] = $this->data['cmp']['company_name'];
			}
			$this->data['company_name'] = $this->data['cmp']['company_name'];
		}

		$this->data['name'] = $this->data[$kind]['name'];
		$this->data['surname'] = $this->data[$kind]['surname'];
		$this->data['person_title'] = $this->data[$kind]['person_title'];
		$this->data['company_name'] = $this->data[$kind]['company_name'];
		if(empty($this->data['User'][0])) {
			$this->data['User'] = array();
		}
		
		$this->saveObject($this->Card);
	 	$this->Transaction->commit();
	 	if(empty($this->data["title"])) {
	 		$this->data["title"] = "";
	 	}
		$this->userInfoMessage(__("Card saved", true)." - ".$this->data["title"]);
		$this->eventInfo("card [". $this->data["title"]."] saved");
	}

	function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Card");
		$this->userInfoMessage(__("Card deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("card $objectsListDeleted deleted");
	}

	function deleteSelected() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Card");
		$this->userInfoMessage(__("Cards deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("cards $objectsListDeleted deleted");
	}
	
	public function categories() {
		$this->showCategories($this->Card);
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

	public function cloneObject() {
		unset($this->data['ObjectUser']);
		parent::cloneObject();
	}


	protected function forward($action, $esito) {
		$REDIRECT = array(
			"cloneObject"	=> 	array(
							"OK"	=> "/addressbook/view/".@$this->Card->id,
							"ERROR"	=> "/addressbook/view/".@$this->Card->id 
							),
			"save"	=> 	array(
							"OK"	=> "/addressbook/view/".@$this->Card->id,
							"ERROR"	=> "/addressbook/view/".@$this->Card->id 
							), 
			"delete" =>	array(
							"OK"	=> $this->Session->read('backFromView'),
							"ERROR"	=> "/addressbook/view/".@$this->params['pass'][0]
							),
			"deleteSelected" =>	array(
							"OK"	=> $this->referer(),
							"ERROR"	=> $this->referer() 
							),
			"changeStatusObjects"	=> 	array(
							"OK"	=> $this->referer(),
							"ERROR"	=> $this->referer() 
							),			
 			"saveCategories" 	=> array(
 							"OK"	=> "/addressbook/categories",
 							"ERROR"	=> "/addressbook/categories"
 									),
 			"deleteCategories" 	=> array(
 							"OK"	=> "/addressbook/categories",
 							"ERROR"	=> "/addressbook/categories"
 									),
			"addItemsToAreaSection"	=> 	array(
							"OK"	=> $this->referer(),
							"ERROR"	=> $this->referer() 
 			)
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}

}

?>