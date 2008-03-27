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
 * @author 			ste@channelweb.it
 */

/**
 * Events handling
 */
class EventsController extends ModulesController {

	var $helpers 	= array('BeTree', 'BeToolbar', 'Fck');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText');
	var $uses = array('Event','ObjectCategory','Area','Section','Tree', 'Image', 'Audio', 'Video', 'BEFile') ;
	protected $moduleName = 'events';
	
	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$types = array($conf->objectTypes['event']);
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
	 }

	public function view($id = null) {

		$conf  = Configure::getInstance() ;
		$obj = null ;
		$parents_id = array();
		$relations = array();
		if(isset($id)) {
			$this->Event->bviorHideFields = array('Version', 'Index', 'current') ;
			if(!($obj = $this->Event->findById($id))) {
				 throw new BeditaException(__("Error loading event: ", true).$id);
			}
			if(isset($obj["LangText"])) {
				$this->BeLangText->setupForView($obj["LangText"]) ;
			}
			
			$relations = $this->objectRelationArray($obj['ObjectRelation']);
			
			if (isset($obj["ObjectCategory"])) {
				$objCat = array();
				foreach ($obj["ObjectCategory"] as $oc) {
					$objCat[] = $oc["id"];
				}
				$obj["ObjectCategory"] = $objCat;
			}
			$parents_id = $this->Tree->getParent($id) ;
			if($parents_id === false) array() ;
			elseif(!is_array($parents_id))
				$parents_id = array($parents_id);
		}
		// begin#bedita_items
		$ot = &$conf->objectTypes ; 
		$bedita_items = $this->BeTree->getDiscendents(null, null, array($ot['befile'], $ot['image'], $ot['audio'], $ot['video']))  ;
		foreach($bedita_items['items'] as $key => $value) {
			if(!empty($attach_id) && in_array($value['id'],$attach_id)) {
				unset($bedita_items['items'][$key]);
			} else {
				// get details
				$type = $conf->objectTypeModels[$value['object_type_id']];
				$this->{$type}->bviorHideFields = array('UserCreated','UserModified','Permissions','Version','CustomProperties','Index','langObjs', 'images', 'multimedia', 'attachments');
				if(($Details = $this->{$type}->findById($value['id']))) {
					$Details['filename'] = substr($Details['path'],strripos($Details['path'],"/")+1);
					$bedita_items['items'][$key] = array_merge($bedita_items['items'][$key], $Details);	
				}
			}
		}
		$this->params['toolbar'] = &$bedita_items['toolbar'] ;
		$this->set('bedita_items', 	$bedita_items['items']);
		$this->set('toolbar', 		$bedita_items['toolbar']);
		// end#bedita_items		
		$this->set('object',	$obj);
		$this->set('attach', isset($relations['attach']) ? $relations['attach'] : array());
		$tree = $this->BeTree->getSectionsTree() ;
		$this->set('tree', 		$tree);
		$this->set('parents',	$parents_id);
		$areaCategory = $this->ObjectCategory->getCategoriesByArea($ot['event']);
		$this->set("areaCategory", $areaCategory);
		$this->Area->displayField = 'public_name';
		$this->set("areasList", $this->Area->find('list', array("order" => "public_name")));
		$this->selfUrlParams = array("id", $id);
		$this->setUsersAndGroups();
	 }


	public function save() {
	 	
 		$this->checkWriteModulePermission();
 		
 	 	if(empty($this->data)) 
 	 	    throw new BeditaException( __("No data", true));
 		
		$new = (empty($this->data['id'])) ? true : false ;
		
	 	// verify object permissions
	 	if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
	 			throw new BeditaException(__("Error modify permissions", true));
	 	
	 	// format custom properties
	 	$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]) ;
		$this->data['title'] = $this->data['LangText'][$this->data['lang']]['title'];
		$this->data['description'] = $this->data['LangText'][$this->data['lang']]['description'];
		$this->data['abstract'] = $this->data['LangText'][$this->data['lang']]['abstract'];
		$this->data['body'] = $this->data['LangText'][$this->data['lang']]['body'];
	 	$this->BeLangText->setupForSave($this->data["LangText"]) ;
	 	// if no Category is checked set an empty array to delete association between events and category
	 	if (!isset($this->data["ObjectCategory"])) $this->data["ObjectCategory"] = array();
	 	
		$this->Transaction->begin() ;
		
		if(!$this->Event->save($this->data)) {
	 		throw new BeditaException(__("Error saving event", true), $this->Event->validationErrors);
	 	}

		if(!isset($this->data['destination'])) 
			$this->data['destination'] = array() ;
		$this->BeTree->updateTree($this->Event->id, $this->data['destination']);
		
	 	// update permissions
		if(!isset($this->data['Permissions'])) 
			$this->data['Permissions'] = array() ;
		$this->Permission->saveFromPOST($this->Event->id, $this->data['Permissions'], 
	 			!empty($this->data['recursiveApplyPermissions']), 'event');
	 			
	 	$this->Transaction->commit();
 		$this->userInfoMessage(__("Event saved", true)." - ".$this->data["title"]);
		$this->eventInfo("event [". $this->data["title"]."] saved");
	 }

	public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Event");
		$this->userInfoMessage(__("Events deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("Events $objectsListDeleted deleted");
	}
	
	public function categories() {
		$conf  = Configure::getInstance() ;
		$type = $conf->objectTypes['event'];
		$this->set("categories", $this->ObjectCategory->findAll("ObjectCategory.object_type_id=".$type));
		$this->set("object_type_id", $type);
		$this->Area->displayField = 'public_name';
		$this->set("areasList", $this->Area->find('list', array("order" => "public_name")));
	}

	public function saveCategories() {
		$this->checkWriteModulePermission();
		if(empty($this->data["label"])) 
 	 	    throw new BeditaException( __("No data", true));
		$this->Transaction->begin() ;
		if(!$this->ObjectCategory->save($this->data)) {
			throw new BeditaException(__("Error saving tag", true), $this->ObjectCategory->validationErrors);
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
		if(!$this->ObjectCategory->del($this->data["id"])) {
			throw new BeditaException(__("Error saving tag", true), $this->ObjectCategory->validationErrors);
		}
		$this->Transaction->commit();
		$this->userInfoMessage(__("Category deleted", true) . " -  " . $this->data["label"]);
		$this->eventInfo("Category " . $this->data["id"] . "-" . $this->data["label"] . " deleted");
	}
	
	protected function forward($action, $esito) {
	  	$REDIRECT = array(
	 			"save"				=> 	array(
	 										"OK"	=> "/events/view/{$this->Event->id}",
	 										"ERROR"	=> "/events" 
	 									), 
	 			"delete" 			=>	array(
	 										"OK"	=> "/events",
	 										"ERROR"	=> "/events/view/{@$this->params['pass'][0]}" 
	 									), 
	 			"saveCategories" 	=> array(
	 										"OK"	=> "/events/categories",
	 										"ERROR"	=> "/events/categories"
	 									),
	 			"deleteCategories" 	=> array(
	 										"OK"	=> "/events/categories",
	 										"ERROR"	=> "/events/categories"
	 									)
	 		) ;
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	return false ;
	 }

}
