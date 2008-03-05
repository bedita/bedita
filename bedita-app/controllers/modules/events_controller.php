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
class EventsController extends AppController {

	var $helpers 	= array('BeTree', 'BeToolbar', 'Fck');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText');
	var $uses = array('Event') ;
	protected $moduleName = 'events';
	
	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$types = array($conf->objectTypes['event']);
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
	 }

	public function view($id = null) {

		$obj = null ;
		if(isset($id)) {
			$this->Event->bviorHideFields = array('Version', 'Index', 'current') ;
			if(!($obj = $this->Event->findById($id))) {
				 throw new BeditaException(__("Error loading event: ", true).$id);
			}
			if(isset($obj["LangText"])) {
				$this->BeLangText->setupForView($obj["LangText"]) ;
			}
		}

		$this->set('object',	$obj);
		$this->set('tree', 		$this->BeTree->getSectionsTree());
		$this->set('parents',	$this->BeTree->getParents($id));		
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
		
		$objectsToDel = array();
		$objectsListDesc = "";
		if(!empty($this->params['form']['objects_to_del'])) {
			
			$objectsListDesc = $this->params['form']['objects_to_del'];
			$objectsToDel = split(",",$objectsListDesc);
			
		} else {
			if(empty($this->data['id'])) 
				throw new BeditaException(__("No data", true));
			if(!$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_DELETE)) {
				throw new BeditaException(__("Error delete permissions", true));
			}
			$objectsToDel = array($this->data['id']);
			$objectsListDesc = $this->data['id'];
		}

		$this->Transaction->begin() ;

		foreach ($objectsToDel as $id) {
			if(!$this->Event->delete($id))
				throw new BeditaException(__("Error deleting event: ", true) . $id);
		}
		
		$this->Transaction->commit() ;
		$this->userInfoMessage(__("Events deleted", true) . " -  " . $objectsListDesc);
		$this->eventInfo("Events $objectsListDesc deleted");
	}

	protected function forward($action, $esito) {
	  	$REDIRECT = array(
	 			"save"	=> 	array(
	 									"OK"	=> "/events/view/{$this->Event->id}",
	 									"ERROR"	=> "/events" 
	 								), 
	 			"delete" =>	array(
	 									"OK"	=> "/events",
	 									"ERROR"	=> "/events/view/{@$this->params['pass'][0]}" 
	 								), 
	 		) ;
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	return false ;
	 }

}
