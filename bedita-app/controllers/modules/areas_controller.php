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
 * Controller module Publications: managing of publications, sections and sessions
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class AreasController extends ModulesController {
	var $name = 'Areas';

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'BeCustomProperty', 'BeLangText');

	var $uses = array('BEObject', 'Area', 'Section', 'Tree', 'User', 'Group', 'ObjectType') ;
	protected $moduleName = 'areas';
	 
	function index() {
		$tree = $this->BeTree->getSectionsTree() ;
		$this->set('tree',$tree);
		$this->set("formToUse", "area");
	}

	public function view($id) {
		$this->action = "index";
		$ot_id = $this->BEObject->field("object_type_id", array("BEObject.id" => $id));
		$this->loadSectionDetails($id,$ot_id);
		$this->loadContents($id);
		$this->loadSections($id);
		$formToUse = strtolower(Configure::read("objectTypes.".$ot_id.".model"));
		$this->set("formToUse", $formToUse);
	}
	
	function viewArea($id = null) {
		// Get selected area
		$area = null ;
		if($id) {
			$this->Area->containLevel("detailed");
			if(!($area = $this->Area->findById($id))) {
				 throw new BeditaException(sprintf(__("Error loading area: %d", true), $id));
			}
		}
		
		$property = $this->BeCustomProperty->setupForView($area, Configure::read("objectTypes.area.id"));
		
		// Data for template
		$this->set('area',$area);
		$this->set('objectProperty', $property);
		// get users and groups list
		$this->User->displayField = 'userid';
		$this->set("usersList", $this->User->find('list', array("order" => "userid")));
		$this->set("groupsList", $this->Group->find('list', array("order" => "name")));
	}

	function viewSection($id=null) {
		if (!empty($id)) {
			$this->loadSectionDetails($id,Configure::read("objectTypes.section.id"));
		} else {
			$sec = null;
			$this->set('objectProperty', $this->BeCustomProperty->setupForView($sec, Configure::read("objectTypes.section.id"))) ;
			$this->set('tree',$this->BeTree->getSectionsTree());
		}
	}
	
	
	function saveTree() {
		$this->checkWriteModulePermission();
		$this->Transaction->begin() ;
		if(@empty($this->data["tree"])) throw new BeditaException(__("No data", true));
		// Get the tree
		$this->_getTreeFromPOST($this->data["tree"], $tree) ;
		// Save data changes
		if(!$this->Tree->moveAll($tree)) throw new BeditaException( __("Error saving tree from _POST", true));
		$this->Transaction->commit() ;
		$this->userInfoMessage(__("Area tree saved", true));
		$this->eventInfo("area tree saved");
	}
	 
	 /**
	  * Add or modify area
	  * URLOK and URLERROR should be defined
	  */
	function saveArea() {
		$this->checkWriteModulePermission();
		if(empty($this->data))
			throw new BeditaException( __("No data", true));
		$new = (empty($this->data['id'])) ? true : false ;
		// Verify permissions for the object
		if(!$new) { 
			$this->checkObjectWritePermission($this->data['id']);
		}
		// Format custom properties
		$this->BeCustomProperty->setupForSave() ;
		// Format translations for fields

		if(empty($this->data["syndicate"]))
			$this->data["syndicate"] = 'off';
			
		if(!isset($this->data['Permissions'])) 
			$this->data['Permissions'] = array() ;
		
		$this->Transaction->begin() ;
		// Save data
		if(!$this->Area->save($this->data))

			throw new BeditaException( __("Error saving area", true),  $this->Area->validationErrors);
		
		$id = $this->Area->getID();
		
		if(!$new) {
			
			// remove children
			if (!empty($this->params["form"]["contentsToRemove"])) {
				$childrenToRemove = explode(",", trim($this->params["form"]["contentsToRemove"],","));
				foreach ($childrenToRemove as $idToRemove) {
					$this->Tree->removeChild($idToRemove, $id);
				}
			}

			$reorder = (!empty($this->params["form"]['reorder'])) ? $this->params["form"]['reorder'] : array();
			
			// add new children and reorder priority
			foreach ($reorder as $r) {
			 	if (!$this->Tree->find("first", array("conditions" => "id=".$r["id"]." AND parent_id=".$id))) {
					$this->Tree->appendChild($r["id"], $id);
				}
				if (!$this->Tree->setPriority($r['id'], $r['priority'], $id)) {
					throw new BeditaException( __("Error during reorder children priority", true), $r["id"]);
				}
			}
		}
		
		$this->Transaction->commit() ;
		$this->userInfoMessage(__("Area saved", true)." - ".$this->data["title"]);
		$this->eventInfo("area ". $this->data["title"]."saved");
	}

	/**
	 * Save/modify section.
	 * URLOK and URLERROR should be defined.
	 */
	function saveSection() {
		
		$this->checkWriteModulePermission();
		if(empty($this->data)) 
			throw new BeditaException(__("No data", true));
		$new = (empty($this->data['id'])) ? true : false ;
		// Verify permissions for the object
		if(!$new) { 
			$this->checkObjectWritePermission($this->data['id']);
		}
		// Format custom properties
		$this->BeCustomProperty->setupForSave() ;
		
		if(!isset($this->data['Permissions'])) 
			$this->data['Permissions'] = array() ;

		if(empty($this->data["syndicate"]))
			$this->data["syndicate"] = 'off';
		
		if(empty($this->data["parent_id"]))
			throw new BeditaException( __("Missing parent", true));
		
		$this->Transaction->begin() ;
			
		if(!$this->Section->save($this->data))

			throw new BeditaException( __("Error saving section", true), $this->Section->validationErrors );

		
		$id = $this->Section->getID();
		// Move section in the right tree position, if necessary
		if(!$new) {
			
			if (!$this->BEObject->isFixed($id)) {
				$oldParent = $this->Tree->getParent($id) ;
				if($oldParent != $this->data["parent_id"]) {
					if(!$this->Tree->move($this->data["parent_id"], $oldParent, $id))
						throw new BeditaException( __("Error saving section", true));
				}
			}
			
			// remove children
			if (!empty($this->params["form"]["contentsToRemove"])) {
				$childrenToRemove = explode(",", trim($this->params["form"]["contentsToRemove"],","));
				foreach ($childrenToRemove as $idToRemove) {
					$this->Tree->removeChild($idToRemove, $id);
				}
			}

			$reorder = (!empty($this->params["form"]['reorder'])) ? $this->params["form"]['reorder'] : array();
			
			// add new children and reorder priority
			foreach ($reorder as $r) {
			 	if (!$this->Tree->find("first", array("conditions" => "id=".$r["id"]." AND parent_id=".$id))) {
					$this->Tree->appendChild($r["id"], $id);
				}
				if (!$this->Tree->setPriority($r['id'], $r['priority'], $id)) {
					throw new BeditaException( __("Error during reorder children priority", true), $r["id"]);
				}
			}
		}
		
		$this->Transaction->commit() ;
		$this->userInfoMessage(__("Section saved", true)." - ".$this->data["title"]);
		$this->eventInfo("section [". $this->data["title"]."] saved");
	}

	function delete() {
		if(empty($this->data['id'])) {
			throw new BeditaException(__("No data", true));
		}
		$ot_id = $this->BEObject->field("object_type_id", array("BEObject.id" => $this->data['id']));
		switch ($ot_id) {
			case Configure::read("objectTypes.area.id"):
				$this->deleteArea();
				break;
				
			case Configure::read("objectTypes.section.id"):
				$this->deleteSection();
				break;
		}
	}
	
	private function deleteArea() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Area");
		$this->userInfoMessage(__("Area deleted", true)." - ".$objectsListDeleted);
		$this->eventInfo("area [". $objectsListDeleted."] deleted");
	}

	private function deleteSection() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Section");
		$this->userInfoMessage(__("Section deleted", true)." - ".$objectsListDeleted);
		$this->eventInfo("section [". $objectsListDeleted."] deleted");
	}
	
			
	/**
	 * load section object
	 *
	 * @param int $id
	 */
	public function loadSectionAjax($id) {
		// Load languages
		if(Configure::read("langOptionsIso") == true) {
			Configure::load('langs.iso') ;
		}

		$this->layout = null;
		
		$tplFile = "form_area.tpl";
		
		if (!empty($id)) {
			$ot_id = $this->BEObject->field("object_type_id", array("BEObject.id" => $id));
			$this->loadSectionDetails($id, $ot_id);
			$tplFile = "form_" . strtolower(Configure::read("objectTypes.".$ot_id.".model")) . ".tpl";
		}
		
		$this->render(null, null, VIEWS . "areas/inc/" . $tplFile);
		
	}


	/**
	 * load contents for a section
	 *
	 * @param int $id
	 * 
	 */
	public function listContentAjax($id) {
		$this->layout = null;
		if (!empty($id)) {
			$this->loadContents($id);
		}
		$this->render(null, null, VIEWS."areas/inc/list_content_ajax.tpl");
	}
	
	
	/**
	 * load children section 
	 *
	 * @param int $id
	 * 
	 */
	public function listSectionAjax($id) {
		$this->layout = null;
		if (!empty($id)) {
			$this->loadSections($id);
		}
		$this->render(null, null, VIEWS."areas/inc/list_sections_ajax.tpl");
	}
	
		
	 /**
	  * Return associative array representing publications/sections tree
	  *
	  * @param unknown_type $data
	  * @param unknown_type $tree
	  */
	private function _getTreeFromPOST(&$data, &$tree) {
		$tree = array() ;
		$IDs  = array() ;
		// Creating subtrees
		$arr = preg_split("/;/", $data) ;
		for($i = 0 ; $i < count($arr) ; $i++) {
			$item = array() ;
			$tmp = split(" ", $arr[$i] ) ;
			foreach($tmp as $val) {
				$t  = split("=", $val) ;
				$item[$t[0]] = ($t[1] == "null") ? null : ((integer)$t[1]) ; 
			}
			$IDs[$item["id"]] 				= $item ;
			$IDs[$item["id"]]["children"] 	= array() ;
		}
		// Creating the tree
		foreach ($IDs as $id => $item) {
			if(!isset($item["parent"])) {
				$tree[] = $item ;
				$IDs[$id] = &$tree[count($tree)-1] ;
			}
			if(isset($IDs[$item["parent"]])) {
				$IDs[$item["parent"]]["children"][] = $item ;
				$IDs[$id] = &$IDs[$item["parent"]]["children"][count($IDs[$item["parent"]]["children"])-1] ;
			}
		}
		unset($IDs) ;
	}

	
	/**
	 * get section details and set for template, get all tree
	 *
	 * @param int $id
	 * 			
	 */
	private function loadSectionDetails($id, $objectTypeId) {
			
		$model = ClassRegistry::init(Configure::read("objectTypes.".$objectTypeId.".model"));
		$model->containLevel("detailed");
		if(!($collection = $model->findById($id))) {
			throw new BeditaException(sprintf(__("Error loading section: %d", true), $id));
		}
		// additional control on id -- if a row on a table model is missing (e.g. sections) field id can be null
		if(empty($collection['id'])) {
			throw new BeditaException(sprintf(__("Error loading section: %d", true), $id));
		}
		
		$this->set('objectProperty', $this->BeCustomProperty->setupForView($collection));
		$this->set('object',$collection);
		$this->set('tree', $this->BeTree->getSectionsTree());
		$this->set('parent_id', $this->Tree->getParent($id));
	}
	
	/**
	 * get contents for a section/publication
	 *
	 * @param unknown_type $id
	 */
	private function loadContents($id) {
		// set pagination
		$page = (!empty($this->params["form"]["page"]))? $this->params["form"]["page"] : 1;
		$dim = (!empty($this->params["form"]["dim"]))? $this->params["form"]["dim"] : 1000000; 
		
		// get content
		$filter["object_type_id"] = Configure::read("objectTypes.leafs.id");

		$priorityOrder = $this->Section->field("priority_order", array("id" => $id));
		if(empty($priorityOrder))
			$priorityOrder = "asc";
		$contents = $this->BeTree->getChildren($id, null, $filter, "priority", ($priorityOrder == "asc"), $page, $dim);
		
		foreach ($contents["items"] as $key => $item) {
			$contents["items"][$key]["module"]= $this->ObjectType->field("module", 
				array("id" => $item["object_type_id"]));
		}
		
		$this->set("priorityOrder", $priorityOrder);
		$this->set("contents", $contents);
		$this->set("dim", $dim);
		$this->set("page", $page);
		$this->set("selectedId", $id);
	}
	
	private function loadSections($id) {
		// set pagination
		$page = (!empty($this->params["form"]["page"]))? $this->params["form"]["page"] : 1;
		$dim = (!empty($this->params["form"]["dim"]))? $this->params["form"]["dim"] : 20; 
		$filter["object_type_id"] = Configure::read("objectTypes.section.id");
		$priorityOrder = $this->Section->field("priority_order", array("id" => $id));
		if(empty($priorityOrder))
			$priorityOrder = "asc";
		// get sections children
		$this->set("sections", $this->BeTree->getChildren($id, null, $filter, "priority", ($priorityOrder == "asc"), $page, $dim));
		$this->set("dimSec", $dim);
		$this->set("pageSec", $page);
	}
	
	protected function forward($action, $esito) {
		$REDIRECT = array(
			"saveTree"	=> 	array(
									"OK"	=> "./",
									"ERROR"	=> "./" 
								), 
			"saveArea"	=> 	array(
									"OK"	=> "/areas/view/{$this->Area->id}",
									"ERROR"	=> "/areas/view/{$this->Area->id}" 
								), 
			"saveSection"	=> 	array(
									"OK"	=> "/areas/view/{$this->Section->id}",
									"ERROR"	=> "/areas/view/{$this->Section->id}"
								), 
			"delete"	=> 	array(
									"OK"	=> "./",
									"ERROR"	=> "/areas/view/" . @$this->data["id"]
								), 
			"deleteSection"	=> 	array(
									"OK"	=> "./",
									"ERROR"	=> $this->referer() 
								)
		) ;
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
}	

?>