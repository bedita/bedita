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
 * Controller entrata modulo Aree, gestione aree e gestione sessioni
 * 
 * @link			http://www.bedita.com
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
	 
	/**
	 * Area tree and sections
	 * 
	 */
	function index($id=null) {
		if (!empty($id)) {
			$ot_id = $this->BEObject->field("object_type_id", array("BEObject.id" => $id));
			$this->loadSectionDetails($id,$ot_id);
			$this->loadContents($id);
			$this->loadSections($id);
			$formToUse = strtolower(Configure::read("objectTypes.".$ot_id.".model"));
		} else {
			$tree = $this->BeTree->getSectionsTree() ;
			$this->set('tree',$tree);
			$formToUse = "area";
		}
		$this->set("formToUse", $formToUse);
	}

	 /**
	  * Preleva l'area selezionata.
	  * Se non viene passato nessun id, presente il form per una nuova area
	  *
	  * @param integer $id
	  */
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

	 /**
	  * empty form for a new section
	  *
	  * @param integer $id
	  */
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
	  * Save data tree
	  */
	function saveTree() {
		$this->checkWriteModulePermission();
		$this->Transaction->begin() ;
		if(@empty($this->data["tree"])) throw new BeditaException(__("No data", true));
		// Get the tree
		$this->_getTreeFromPOST($this->data["tree"], $tree) ;
		// Save data changes
		if(!$this->Tree->moveAll($tree)) throw new BeditaException( __("Error save tree from _POST", true));
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
//		// Verify permits for the object
//		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
//			throw new BeditaException(__("Error modify permissions", true));
		// Format custom properties
		$this->BeCustomProperty->setupForSave() ;
		// Format translations for fields

		if(empty($this->data["syndicate"]))
			$this->data["syndicate"] = 'off';
		
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
				
		// update permits
		$perms = isset($this->data["Permissions"])?$this->data["Permissions"]:array();
//		if(!$this->Permission->saveFromPOST($id, $perms,
//			(empty($this->data['recursiveApplyPermissions'])?false:true), 'area'))  {
//			throw new BeditaException( __("Error saving permissions", true));
//		}
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
//		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
//				throw new BeditaException( __("Error modifying permissions", true));
		// Format custom properties
		$this->BeCustomProperty->setupForSave() ;
		
		$this->Transaction->begin() ;

		if(empty($this->data["syndicate"]))
			$this->data["syndicate"] = 'off';
		
		if(empty($this->data["parent_id"]))
			throw new BeditaException( __("Missing parent", true));
		
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
		// update permits
		$perms = isset($this->data["Permissions"]) ? $this->data["Permissions"] : array();
//		if(!$this->Permission->saveFromPOST($id, $perms,	 
//			(empty($this->data['recursiveApplyPermissions'])?false:true), 'section')) {
//				throw new BeditaException( __("Error saving permissions", true));
//		}
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
	
	 /**
	  * Delete area
	  */
	private function deleteArea() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Area");
		$this->userInfoMessage(__("Area deleted", true)." - ".$objectsListDeleted);
		$this->eventInfo("area [". $objectsListDeleted."] deleted");
	}

	/**
	  * Delete section
	  */
	private function deleteSection() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Section");
		$this->userInfoMessage(__("Section deleted", true)." - ".$objectsListDeleted);
		$this->eventInfo("section [". $objectsListDeleted."] deleted");
	}
	
	

	/* AJAX CALLS */

		
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
	 * called via ajax
	 * Show list of objects for relation, append to section,...
	 * 
	 * @param int $main_object_id, object id of main object used to exclude association with itself 
	 * @param string $relation, relation type
	 * @param string $objectTypes name of objectType to filter. It has to be a string that defined a group of type
	 * 							  defined in bedita.ini.php (i.e. 'related' 'leafs',...)
	 * 							  Used if $this->parmas["form"]["objectType"] and $relation are empty	
	 * 
	 **/
	public function showObjects($main_object_id=null, $relation=null, $main_object_type_id=null, $objectType="related") {
		
		$id = (!empty($this->params["form"]["parent_id"]))? $this->params["form"]["parent_id"] : null;
		
		// default
		$objectTypeIds = Configure::read("objectTypes.related.id");
		
		if (!empty($relation)) {
			
			$relTypes = array_merge(Configure::read("objRelationType"), Configure::read("defaultObjRelationType"));
			
			if (!empty($relTypes[$relation])) {
				
				if (!empty($main_object_id)) {
					$main_object_type_id = $this->BEObject->field("object_type_id", array("id" => $main_object_id));
				}
				
				$objectTypeName = Configure::read("objectTypes." . $main_object_type_id . ".name");
				
				if (!empty($relTypes[$relation][$objectTypeName])) {
					$objectTypeIds = $relTypes[$relation][$objectTypeName];
				} elseif (key_exists("left", $relTypes[$relation]) 
							&& key_exists("right", $relTypes[$relation])
							&& is_array($relTypes[$relation]["left"])
							&& is_array($relTypes[$relation]["right"])
							) {
				
					if (in_array($main_object_type_id, $relTypes[$relation]["left"])) {
						if (!empty($relTypes[$relation]["right"]))
							$objectTypeIds = $relTypes[$relation]["right"];
					} elseif (in_array($main_object_type_id, $relTypes[$relation]["right"])) {
						if (!empty($relTypes[$relation]["left"]))
							$objectTypeIds = $relTypes[$relation]["left"];
					} elseif (empty($relTypes[$relation]["left"])) { 
						$objectTypeIds = $relTypes[$relation]["right"];
					} elseif (empty($relTypes[$relation]["right"])) {
						$objectTypeIds = $relTypes[$relation]["left"];
					} else {
						$objectTypeIds = array(0);	
					}
				}

			}
			
		} else {
			$objectTypeIds = Configure::read("objectTypes." . $objectType . ".id");
		}
		
		// set object_type_id filter
		if (!empty($this->params["form"]["objectType"])) {
			$filter["object_type_id"] = array($this->params["form"]["objectType"]);
		} else {
			$filter["object_type_id"] = $objectTypeIds;
		}
		
		// set lang filter
		if (!empty($this->params["form"]["lang"]))
			$filter["lang"] = $this->params["form"]["lang"]; 
		
		// set search filter
		if (!empty($this->params["form"]["search"]))
			$filter["search"] = addslashes($this->params["form"]["search"]);
		
		$page = (!empty($this->params["form"]["page"]))? $this->params["form"]["page"] : 1;
			
		$objects = $this->BeTree->getChildren($id, null, $filter, "title", true, $page, $dim=20) ;
		
		foreach ($objects["items"] as $key => $obj) {
			if ($obj["id"] != $main_object_id)
				$objects["items"][$key]["moduleName"] = $this->ObjectType->field("module", array("id" => $obj["object_type_id"]));
			else
				unset($objects["items"][$key]);
		}
		$this->set("objectsToAssoc", $objects);
		
		$tree = $this->BeTree->getSectionsTree() ;
		$this->set('tree',$tree);
		
		if (!empty($relation))
			$this->set("relation", $relation);
		
		$this->set("main_object_id", $main_object_id);
		$this->set("object_type_id", $main_object_type_id);
		$this->set("objectType", $objectType);
		$this->set("objectTypeIds", (is_array($objectTypeIds))? $objectTypeIds : array($objectTypeIds) );
		
		$this->layout = null;
		
		$view = (empty($this->params["form"]))? "areas/inc/show_objects.tpl" : "areas/inc/list_contents_to_assoc.tpl";
		$this->render(null, null, VIEWS . $view);
	}
	
	/**
	 * called via ajax
	 * load objects selected to main view to prepare association form
	 *
	 * @param int $main_object_id, object id of main object used to exclude association with itself 
	 */
	public function loadObjectToAssoc($main_object_id=null, $objectType=null, $tplname=null) {
		
		$conditions = array("BEObject.id" => explode( ",", trim($this->params["form"]["object_selected"],",") ));
		
		if (!empty($objectType))
			$conditions["BEObject.object_type_id"] = Configure::read("objectTypes." . $objectType . ".id");
		
		$objects = $this->BEObject->find("all", array(
													"contain" => array("ObjectType"),
													"conditions" => $conditions
												)
										) ;
		$objRelated = array();

		foreach ($objects as $key => $obj) {
			if (empty($main_object_id) || $objects[$key]["BEObject"]["id"] != $main_object_id)
				$obj["BEObject"]["module"] = $obj["ObjectType"]["module"];
				$objRelated[] = array_merge($obj["BEObject"], array("ObjectType" => $obj["ObjectType"]));
		}
		
		$this->set("objsRelated", $objRelated);
		$this->set("rel", $this->params["form"]["relation"]);
		$this->layout = null;
		$tplname = (empty($tplname))? "common_inc/form_assoc_object.tpl" : "areas/inc/" . $tplname;
		$this->render(null, null, VIEWS . $tplname);
	}
	
	 /**
	  * Return associative array representing areas/sections tree
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
		
		$model->contain(array(
					"BEObject" => array("ObjectType", 
										"UserCreated", 
										"UserModified", 
										"Permissions",
										"ObjectProperty",
										"LangText"
										)
						));
		if(!($collection = $model->findById($id))) {
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
									"OK"	=> "/areas/index/{$this->Area->id}",
									"ERROR"	=> "/areas/index/{$this->Area->id}" 
								), 
			"saveSection"	=> 	array(
									"OK"	=> "/areas/index/{$this->Section->id}",
									"ERROR"	=> "/areas/index/{$this->Section->id}"
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