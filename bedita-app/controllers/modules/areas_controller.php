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
	 
	function index($id = null, $order = "priority", $dir = true, $page = 1, $dim = 20) {
		if ($id == null && !empty($this->params["named"]["id"])) {
			$id = $this->params["named"]["id"];
		}
		// if empty $id try to get first publication.id
		if (empty($id)) {
			$publication = $this->Area->find("first", array(
				"order" => "BEObject.title asc",
				"contain" => array("BEObject")
			));
			$this->params["named"]["id"] = $id = $publication["id"];
		}
		
		if (!empty($id)) {
			$this->view($id);
		}
		
	}

	public function view($id) {
		$this->action = "index";
		$objectTypeId = $this->BEObject->field("object_type_id", array("BEObject.id" => $id));
		$modelName = Configure::read("objectTypes.".$objectTypeId.".model");
		$this->viewObject($this->{$modelName}, $id);
		$dir = ($this->viewVars["object"]["priority_order"] == "asc")? true : false;
		$this->loadChildren($id, "priority", $dir);
		$this->set("objectType", Configure::read("objectTypes.".$objectTypeId.".name"));
		$this->set('parent_id', $this->Tree->getParent($id));
	}
	
	/**
	 * load paginated contents and no paginated sections of $id publication/section
	 * 
	 * @param int $id
	 * @param string $order
	 * @param bool $dir
	 * @param int $page
	 * @param int $dim 
	 */
	protected function loadChildren($id, $order = "priority", $dir = true, $page = 1, $dim = 20) {
		// get paginated children content (leaf objectTypes) if no other is passed
		if (!empty($this->params["named"]["object_type_id"]) 
				&& $this->params["named"]["object_type_id"] != Configure::read("objectTypes.area.id")
				&& $this->params["named"]["object_type_id"] != Configure::read("objectTypes.section.id")) {
			$filter["object_type_id"] = $this->params["named"]["object_type_id"];
		} else {
			$filter["object_type_id"] = Configure::read("objectTypes.leafs.id");
		}
		$dir = ($this->viewVars["object"]["priority_order"] == "asc")? true : false;
		$this->paginatedList($id, $filter, $order, $dir, $page, $dim);
		
		// get no paginated children sections
		$filter["object_type_id"] = Configure::read("objectTypes.section.id");
		$sections = $this->BeTree->getChildren($id, null, $filter, "priority", $dir, 1, 10000);
		$this->set("sections", $sections["items"]);
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
	
	 
	 /**
	  * Add or modify area
	  */
	function saveArea() {
		$this->checkWriteModulePermission();
		$new = (empty($this->data['id'])) ? true : false;
		$this->Transaction->begin();
		if (empty($this->data["syndicate"])) {
			$this->data["syndicate"] = 'off';
		}
		$this->saveObject($this->Area);
		
		$id = $this->Area->id;
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
	 */
	function saveSection() {
		
		$this->checkWriteModulePermission();
		$new = (empty($this->data['id'])) ? true : false;
		$this->Transaction->begin();
		if (empty($this->data["syndicate"])) {
			$this->data["syndicate"] = 'off';
		}
		if(empty($this->data["parent_id"])) {
			throw new BeditaException( __("Missing parent", true));
		}
		$this->saveObject($this->Section);
		
		$id = $this->Section->id;
		
		// Move section in the right tree position, if necessary
		if(!$new) {
			
			if (!$this->BEObject->isFixed($id)) {
				$oldParent = $this->Tree->getParent($id);
				if($oldParent != $this->data["parent_id"]) {
					if(!$this->Tree->move($this->data["parent_id"], $oldParent, $id)) {
						throw new BeditaException( __("Error moving section in the tree", true));
					}
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

	
	protected function forward($action, $esito) {
		$REDIRECT = array(
			"saveArea"	=> 	array(
									"OK"	=> "/areas/view/{$this->Area->id}",
									"ERROR"	=> $this->referer()
								), 
			"saveSection"	=> 	array(
									"OK"	=> "/areas/view/{$this->Section->id}",
									"ERROR"	=> $this->referer()
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