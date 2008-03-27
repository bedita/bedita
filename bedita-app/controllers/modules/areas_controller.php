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

	var $helpers 	= array('BeTree');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText');

	var $uses = array('BEObject', 'Area', 'Section', 'Tree', 'User', 'Group') ;
	protected $moduleName = 'areas';
	 
	/**
	 * Area tree and sections
	 * 
	 */
	function index() { 	
		$tree = $this->BeTree->getSectionsTree() ;
		$this->set('tree',$tree);
	}

	 /**
	  * Preleva l'area selezionata.
	  * Se non viene passato nessun id, presente il form per una nuova area
	  *
	  * @param integer $id
	  */
	function viewArea($id = null) {
		$this->setup_args(array("id", "integer", &$id)) ;
		// Get selected area
		$area = null ;
		if($id) {
			$this->Area->bviorHideFields = array('ObjectType', 'Version', 'Index', 'current') ;
			if(!($area = $this->Area->findById($id))) {
				 throw new BeditaException(sprintf(__("Error loading area: %d", true), $id));
			}
		}
		if(isset($area["LangText"])) {
			$this->BeLangText->setupForView($area["LangText"]) ;
		}
		// Data for template
		$this->set('area',$area);
		$this->selfUrlParams = array("id", $id);
		// get users and groups list
		$this->User->displayField = 'userid';
		$this->set("usersList", $this->User->find('list', array("order" => "userid")));
		$this->set("groupsList", $this->Group->find('list', array("order" => "name")));
	}

	 /**
	  * Get selected section.
	  * If id is null, empty section
	  *
	  * @param integer $id
	  */
	function viewSection($id = null) {
		$this->setup_args(array("id", "integer", &$id)) ;
		// Get selected section
		$section = null ;
		if($id) {
			$this->Section->bviorHideFields = array('ObjectType', 'Version', 'Index', 'current') ;
			if(!($section = $this->Section->findById($id))) {
				throw new BeditaException(sprintf(__("Error loading section: %d", true), $id));
			}
		}
		if(isset($section["LangText"])) {
			$this->BeLangText->setupForView($section["LangText"]) ;
		}
		// Get area/section tree
		$tree = $this->BeTree->getSectionsTree() ;
		// Get section position
		if(isset($id)) {
			$parent_id = $this->Tree->getParent($id) ;
		} else {
			$parent_id = 0 ;
		}
		if($id) {
			$conf  = Configure::getInstance() ;
			$ot = &$conf->objectTypes ; 
			$contents = $this->BeTree->getDiscendents($id, null, $ot['documentAll']);
			$content_items = $contents['items'];
		} else {
			$content_items=array();
		}
		// Data for template
		$this->set('tree',$tree);
		$this->set('section',$section);
		$this->set('parent_id',$parent_id);
		$this->set('contents',$content_items);
		$this->selfUrlParams = array("id", $id);	
		// get users and groups list
		$this->User->displayField = 'userid';
		$this->set("usersList", $this->User->find('list', array("order" => "userid")));
		$this->set("groupsList", $this->Group->find('list', array("order" => "name")));
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
			throw BeditaException( __("No data", true));
		$new = (empty($this->data['id'])) ? true : false ;
		// Verify permits for the object
		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
			throw new BeditaException(__("Error modify permissions", true));
		// Format custom properties
		$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]) ;
		// Format translations for fields
		$this->data['title'] = $this->data['LangText'][$this->data['lang']]['title'];
		$this->data['public_name'] = $this->data['LangText'][$this->data['lang']]['public_name'];
		$this->data['description'] = $this->data['LangText'][$this->data['lang']]['description'];
		$this->BeLangText->setupForSave($this->data["LangText"]) ;
		$this->Transaction->begin() ;
		// Save data
		if(!$this->Area->save($this->data))
			throw new BeditaException( __("Error saving area", true),  $this->Area->validationErrors);
		// update permits
		$perms = isset($this->data["Permissions"])?$this->data["Permissions"]:array();
		if(!$this->Permission->saveFromPOST($this->Area->id, $perms,
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
		if(empty($this->data)) throw new BeditaException(__("No data", true));
		$new = (empty($this->data['id'])) ? true : false ;
		// Verify permits for the object
		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
				throw new BeditaException( __("Error modifying permissions", true));
		// Format custom properties
		$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]) ;
		// Format translation data
		$this->data['title'] = $this->data['LangText'][$this->data['lang']]['title'];
		$this->BeLangText->setupForSave($this->data["LangText"]) ;
		$this->Transaction->begin() ;
		// data["destination"] should be 1 element
		if(count($this->data["destination"]) != 1)
			throw new BeditaException( __("Bad data", true));
		$destinationId = $this->data["destination"][0];
		if($new) 
			$this->data["parent_id"] = $destinationId;
		if(!$this->Section->save($this->data))
			throw new BeditaException( __("Error saving section", true), $this->Section->validationErrors );
		// Move section in the right tree position, if necessary
		if(!$new) {
			$oldParent = $this->Tree->getParent($this->Section->id) ;
			if($oldParent != $destinationId) {
				if(!$this->Tree->move($destinationId, $oldParent, $this->Section->id))
					throw new BeditaException( __("Error saving section", true));
			}
			$conf  = Configure::getInstance() ;
			$subsections = $this->BeTree->getChildren($this->Section->id, null, $conf->objectTypes['section']);
			// Insert new contents (remove previous associations)
			$contents = (!empty($this->data['contents'])) ? $this->data['contents'] : array();
			if(!$this->Section->removeChildren()) 
				throw new BeditaException( __("Remove children", true));
			if(!empty($subsections) && !empty($subsections['items'])) {
				$subs = $subsections['items'];
				for($i=0; $i < count($subs); $i++) {
					if(!$this->Section->appendChild($subs[$i]['id'],null,$subs[$i]['priority'])) {
						throw new BeditaException( __("Append child", true));
					}
				}
			}
			for($i=0; $i < count($contents) ; $i++) {
				if(!$this->Section->appendChild($contents[$i]['id'],null,$contents[$i]['priority'])) {
					throw new BeditaException( __("Append child", true));
				}
			}
		}
		// update permits
		$perms = isset($this->data["Permissions"]) ? $this->data["Permissions"] : array();
		if(!$this->Permission->saveFromPOST($this->Section->id, $perms,	 
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

	protected function forward($action, $esito) {
		$REDIRECT = array(
			"saveTree"	=> 	array(
									"OK"	=> "./",
									"ERROR"	=> "./" 
								), 
			"saveArea"	=> 	array(
									"OK"	=> "./viewArea/{$this->Area->id}",
									"ERROR"	=> "./viewArea/{$this->Area->id}" 
								), 
			"saveSection"	=> 	array(
									"OK"	=> "./viewSection/{$this->Section->id}",
									"ERROR"	=> "./viewSection/{$this->Section->id}" 
								), 
			"deleteArea"	=> 	array(
									"OK"	=> "./",
									"ERROR"	=> "./viewArea/{@$this->params['pass'][0]}" 
								), 
			"deleteSection"	=> 	array(
									"OK"	=> "./",
									"ERROR"	=> "./viewSection/{@$this->params['pass'][0]}" 
								), 
		) ;
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
}	