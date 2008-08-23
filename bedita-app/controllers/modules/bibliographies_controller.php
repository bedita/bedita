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
 * @author 			andrea@chialab.it
 */

class BibliographiesController extends ModulesController {
	var $name = 'Bibliographies';

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText', 'BeFileHandler');

	var $uses = array('BEObject', 'Bibliography', 'Tree', 'ObjectCategory') ;
	protected $moduleName = 'bibliographies';
	
    public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$types = $conf->objectTypes['bibliography'];
		
		if (!empty($this->params["form"]["searchstring"])) {
			$types["search"] = addslashes($this->params["form"]["searchstring"]);
			$this->set("stringSearched", $this->params["form"]["searchstring"]);
		}
		
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
		
	 }

	 /**
	  * Get biblio.
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
			$this->Bibliography->restrict(array(
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
			if(!($obj = $this->Bibliography->findById($id))) {
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
		$status = (!empty($obj['status'])) ? $obj['status'] : null;
		$previews = (isset($id)) ? $this->previewsForObject($parents_id,$id,$status) : array();

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
		if(!$this->Bibliography->save($this->data)) {
			throw new BeditaException(__("Error saving bibliography", true), $this->Bibliography->validationErrors);
		}
		if(!($this->data['status']=='fixed')) {
			if(!isset($this->data['destination'])) 
				$this->data['destination'] = array() ;
			$this->BeTree->updateTree($this->Bibliography->id, $this->data['destination']);
		}
	 	// update permissions
		if(!isset($this->data['Permissions'])) 
			$this->data['Permissions'] = array() ;
		$this->Permission->saveFromPOST($this->Bibliography->id, $this->data['Permissions'], 
	 			!empty($this->data['recursiveApplyPermissions']), 'event');
 		$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Bibliography saved", true)." - ".$this->data["title"]);
		$this->eventInfo("bibliography [". $this->data["title"]."] saved");
	 }

	 /**
	  * Delete a document.
	  */
	function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Bibliography");
		$this->userInfoMessage(__("Bibliography deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("bibliographies $objectsListDeleted deleted");
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
							"OK"	=> "/bibliographies/view/{$this->Bibliography->id}",
							"ERROR"	=> "/bibliographies/view/{$this->Bibliography->id}" 
							),
			"save"	=> 	array(
							"OK"	=> "/bibliographies/view/{$this->Bibliography->id}",
							"ERROR"	=> "/bibliographies/view/{$this->Bibliography->id}" 
							), 
			"delete" =>	array(
							"OK"	=> "/bibliographies",
							"ERROR"	=> "/bibliographies/view/{@$this->params['pass'][0]}" 
							),
			"addToAreaSection"	=> 	array(
							"OK"	=> "/bibliographies",
							"ERROR"	=> "/bibliographies" 
							),
			"changeStatusObjects"	=> 	array(
							"OK"	=> "/bibliographies",
							"ERROR"	=> "/bibliographies" 
							)
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
}	

?>