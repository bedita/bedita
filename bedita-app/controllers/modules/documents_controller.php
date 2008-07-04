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

class DocumentsController extends ModulesController {
	var $name = 'Documents';

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText', 'BeFileHandler');

	var $uses = array('BEObject', 'Document', 'Tree', 'ObjectCategory') ;
	protected $moduleName = 'documents';
	
    public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$types = $conf->objectTypes['documentAll'];
		
		if (!empty($this->params["form"]["searchstring"])) {
			$types["search"] = addslashes($this->params["form"]["searchstring"]);
			$this->set("stringSearched", $this->params["form"]["searchstring"]);
		}
		
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
		
	 }

	 /**
	  * Get document.
	  * If id is null, empty document
	  *
	  * @param integer $id
	  */
	function view($id = null) {
		$conf  = Configure::getInstance() ;
		$this->setup_args(array("id", "integer", &$id)) ;
		$obj = null ;
		$relations = array();
		if($id) {
			$this->Document->restrict(array(
										"BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"Permissions",
															"CustomProperties",
															"LangText"
															),
										"ContentBase" => array("*"),
										"Content","BaseDocument"
										)
									);
			if(!($obj = $this->Document->findById($id))) {
				 throw new BeditaException(sprintf(__("Error loading document: %d", true), $id));
			}
			$relations = $this->objectRelationArray($obj['ObjectRelation']);
		}
		if(isset($obj["LangText"])) {
			$this->BeLangText->setupForView($obj["LangText"]) ;
		}
		$tree = $this->BeTree->getSectionsTree() ;
		if(isset($id)) {
			$parents_id = $this->Tree->getParent($id) ;
			if($parents_id === false) array() ;
			elseif(!is_array($parents_id))
				$parents_id = array($parents_id);
		} else {
			$parents_id = array();
		}
		$galleries = $this->BeTree->getDiscendents(null, null, $conf->objectTypes['gallery'], "", true, 1, 10000);
		$previews = (isset($id)) ? $this->previewsForObject($parents_id,$id) : array();

		$this->set('object',	$obj);
		$this->set('attach', isset($relations['attach']) ? $relations['attach'] : array());
		$this->set('relObjects', isset($relations) ? $relations : array());
		$this->set('galleries', (count($galleries['items'])==0) ? array() : $galleries['items']);
		$this->set('tree', 		$tree);
		$this->set('parents',	$parents_id);
		$this->set('previews',	$previews);
		$this->selfUrlParams = array("id", $id);
		$this->setUsersAndGroups();
	 }

	/**
	 * Creates/updates new document
	 */
	function save() {
		$this->checkWriteModulePermission();
		if(empty($this->data)) 
			throw new BeditaException( __("No data", true));
		$new = (empty($this->data['id'])) ? true : false ;
		// Verify object permits
		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
			throw new BeditaException(__("Error modify permissions", true));
		// Format custom properties
		$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]) ;
		
		$this->Transaction->begin() ;
		// Save data
		$this->data["ObjectCategory"] = $this->ObjectCategory->saveTagList($this->params["form"]["tags"]);
		if(!$this->Document->save($this->data)) {
	 		throw new BeditaException(__("Error saving document", true), $this->Document->validationErrors);
	 	}
		if(!isset($this->data['destination'])) 
			$this->data['destination'] = array() ;
		$this->BeTree->updateTree($this->Document->id, $this->data['destination']);
	 	// update permissions
		if(!isset($this->data['Permissions'])) 
			$this->data['Permissions'] = array() ;
		$this->Permission->saveFromPOST($this->Document->id, $this->data['Permissions'], 
	 			!empty($this->data['recursiveApplyPermissions']), 'event');
 		$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Document saved", true)." - ".$this->data["title"]);
		$this->eventInfo("document [". $this->data["title"]."] saved");
	 }

	 /**
	  * Delete a document.
	  */
	function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Document");
		$this->userInfoMessage(__("Documents deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("documents $objectsListDeleted deleted");
	}

	function addToAreaSection() {
		if(!empty($this->params['form']['objects_selected'])) {
			$objects_to_assoc = split(",",$this->params['form']['objects_selected']);
			$destination = $this->data['destination'];
			$this->addItemsToAreaSection($objects_to_assoc,$destination);
		}
	}

	protected function forward($action, $esito) {
		$REDIRECT = array(
			"cloneObject"	=> 	array(
							"OK"	=> "/documents/view/{$this->Document->id}",
							"ERROR"	=> "/documents/view/{$this->Document->id}" 
							),
			"save"	=> 	array(
							"OK"	=> "/documents/view/{$this->Document->id}",
							"ERROR"	=> "/documents/view/{$this->Document->id}" 
							), 
			"delete" =>	array(
							"OK"	=> "/documents",
							"ERROR"	=> "/documents/view/{@$this->params['pass'][0]}" 
							),
			"addToAreaSection"	=> 	array(
							"OK"	=> "/documents",
							"ERROR"	=> "/documents" 
							),
			"changeStatusObjects"	=> 	array(
							"OK"	=> "/documents",
							"ERROR"	=> "/documents" 
							)
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
}	

?>