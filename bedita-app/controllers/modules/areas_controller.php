<?php
/**
 *
 * @filesource
 * @copyright		
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license			
 * @author 			giangi@qwerg.com d.domenico@channelweb.it
 */

/**
 * Short description for class.
 *
 * Controller entrata modulo Aree, gestione aree e gestione sessioni
 * 
 */
class AreasController extends ModulesController {
	var $name = 'Areas';

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText');

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
		
		// Data for template
		$this->set('area',$area);
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
		// Verify permits for the object
		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
			throw new BeditaException(__("Error modify permissions", true));
		// Format custom properties
		$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]) ;
		// Format translations for fields

		if(empty($this->data["syndicate"]))
			$this->data["syndicate"] = 'off';
		
		$this->Transaction->begin() ;
		// Save data
		if(!$this->Area->save($this->data))
			throw new BeditaException( __("Error saving area", true),  $this->Area->validationErrors);
		
		$id = $this->Area->getID();
		
		if(!$new) {
			
			// update contents and children sections priority
			$reorder = (!empty($this->params["form"]['reorder'])) ? $this->params["form"]['reorder'] : array();
			
			$objects = $this->BeTree->getChildren($id, null, Configure::read("objectTypes.leafs.id")) ;
			$idsToReorder = array_keys($reorder);
			
			// remove old children
			foreach ($objects["items"] as $obj) {
				if (!in_array($obj["id"], $idsToReorder)) {
					$this->Tree->removeChild($obj["id"], $id);
				}
			}
			
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
		if(!$this->Permission->saveFromPOST($id, $perms,
			(empty($this->data['recursiveApplyPermissions'])?false:true), 'area'))  {
			throw new BeditaException( __("Error saving permissions", true));
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
		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
				throw new BeditaException( __("Error modifying permissions", true));
		// Format custom properties
		$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]) ;
		
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
			$oldParent = $this->Tree->getParent($id) ;
			if($oldParent != $this->data["parent_id"]) {
				if(!$this->Tree->move($this->data["parent_id"], $oldParent, $id))
					throw new BeditaException( __("Error saving section", true));
			}
			
			// update contents and children sections priority
			$reorder = (!empty($this->params["form"]['reorder'])) ? $this->params["form"]['reorder'] : array();
			
			$objects = $this->BeTree->getChildren($id, null, Configure::read("objectTypes.leafs.id")) ;
			$idsToReorder = array_keys($reorder);
			
			// remove old children
			foreach ($objects["items"] as $obj) {
				if (!in_array($obj["id"], $idsToReorder)) {
					$this->Tree->removeChild($obj["id"], $id);
				}
			}
			
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
		if(!$this->Permission->saveFromPOST($id, $perms,	 
			(empty($this->data['recursiveApplyPermissions'])?false:true), 'section')) {
				throw new BeditaException( __("Error saving permissions", true));
		}
		$this->Transaction->commit() ;
		$this->userInfoMessage(__("Section saved", true)." - ".$this->data["title"]);
		$this->eventInfo("section [". $this->data["title"]."] saved");
	}

	 /**
	  * Delete area
	  */
	function deleteArea() {
		$this->checkWriteModulePermission();
		if(empty($this->data['id'])) {
			throw new BeditaException(__("No data", true));
		}
		if(!$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_DELETE)) {
			throw new BeditaException(__("Error delete permissions", true));
		}
		$this->Transaction->begin() ;
		// delete data
		if(!$this->Area->delete($this->data['id'])) {
			throw new BeditaException( sprintf(__("Error deleting area: %d", true), $this->data['id']));
		}
		$this->Transaction->commit() ;
		$this->userInfoMessage(__("Area deleted", true)." - ".$this->data['id']);
		$this->eventInfo("area [". $this->data['id']."] deleted");
	}

	/**
	  * Delete section
	  */
	function deleteSection() {
		$this->checkWriteModulePermission();
		if(empty($this->data['id'])) {
			throw new BeditaException(__("No data", true));
		}
		if(!$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_DELETE)) {
			throw new BeditaException(__("Error delete permissions", true));
		}
		$this->Transaction->begin() ;
		// delete section
		if(!$this->Section->delete($this->data['id'])) {
			throw new BeditaException( sprintf(__("Error deleting section: %d", true), $this->data['id']));
		}
		$this->Transaction->commit() ;
		$this->userInfoMessage(__("Section deleted", true)." - ".$this->data['id']);
		$this->eventInfo("section [". $this->data['id']."] deleted");
	}

	/* AJAX CALLS */

		
	/**
	 * load section object
	 *
	 * @param int $id
	 */
	public function loadSectionAjax($id) {
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
	 * @param int $master_object_id, object id of main object used to exclude association with itself 
	 * @param string $relation, relation type
	 * @param string $objectTypes name of objectType to filter. It has to be a string that defined a group of type
	 * 							  defined in bedita.ini.php (i.e. 'related' 'leafs',...)
	 * 							  Used if $this->parmas["form"]["objectType"] is empty. In view used for create select.	
	 * 
	 **/
	public function showObjects($main_object_id=null, $relation=null, $objectType="related") {
		
		$id = (!empty($this->params["form"]["parent_id"]))? $this->params["form"]["parent_id"] : null;
		$filter = (!empty($this->params["form"]["objectType"]))? array($this->params["form"]["objectType"]) : Configure::read("objectTypes." . $objectType . ".id");
		if (!empty($this->params["form"]["lang"]))
			$filter = array_merge($filter, array("lang" => $this->params["form"]["lang"])); 
			
		if (!empty($this->params["form"]["search"]))
			$filter = array_merge($filter, array("search" => $this->params["form"]["search"]));
		
		$page = (!empty($this->params["form"]["page"]))? $this->params["form"]["page"] : 1;
			
		$objects = $this->BeTree->getChildren($id, null, $filter, "title", true, $page, $dim=10) ;
		
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
		$this->set("objectType", $objectType);
		
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
	public function loadObjectToAssoc($main_object_id=null, $objectType="related", $tplname=null) {
		
		$conditions = array(
						"BEObject.id" => explode( ",", trim($this->params["form"]["object_selected"],",") ), 
						"BEObject.object_type_id" => Configure::read("objectTypes." . $objectType . ".id")
					);
		
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
										"CustomProperties",
										"LangText"
										)
						));
		if(!($collection = $model->findById($id))) {
			throw new BeditaException(sprintf(__("Error loading section: %d", true), $id));
		}
		
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
		$dim = (!empty($this->params["form"]["dim"]))? $this->params["form"]["dim"] : 20; 
		
		// get content
		$objType = Configure::read("objectTypes.leafs.id");
		$contents = $this->BeTree->getChildren($id, null, $objType, "priority", true, $page, $dim);
		
		foreach ($contents["items"] as $key => $item) {
			$contents["items"][$key]["module"]= $this->ObjectType->field("module", 
				array("id" => $item["object_type_id"]));
		}
		
		$this->set("contents", $contents);
	}
	
	private function loadSections($id) {
		// set pagination
		$page = (!empty($this->params["form"]["page"]))? $this->params["form"]["page"] : 1;
		$dim = (!empty($this->params["form"]["dim"]))? $this->params["form"]["dim"] : 20; 
		
		// get sections children
		$this->set("sections", $this->BeTree->getChildren($id, null, Configure::read("objectTypes.section.id"), "priority", true, $page, $dim));
	}
	
	protected function forward($action, $esito) {
		$REDIRECT = array(
			"saveTree"	=> 	array(
									"OK"	=> "./",
									"ERROR"	=> "./" 
								), 
			"saveArea"	=> 	array(
									"OK"	=> "./index/{$this->Area->id}",
									"ERROR"	=> "./viewArea/{$this->Area->id}" 
								), 
			"saveSection"	=> 	array(
									"OK"	=> "./index/{$this->Section->id}",
									"ERROR"	=> "./viewSection" 
								), 
			"deleteArea"	=> 	array(
									"OK"	=> "./",
									"ERROR"	=> "./viewArea/{@$this->params['pass'][0]}" 
								), 
			"deleteSection"	=> 	array(
									"OK"	=> "./",
									"ERROR"	=> "./viewSection/{@$this->params['pass'][0]}" 
								)
		) ;
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
}	

?>