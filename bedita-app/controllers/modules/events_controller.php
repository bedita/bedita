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
 * @author 			ChannelWeb srl
 */

/**
 * Events handling
 */
class EventsController extends ModulesController {

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText');
	var $uses = array('BEObject','Event','Category','Area','Tree') ;
	protected $moduleName = 'events';
	
	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$types = array($conf->objectTypes['event']["id"]);
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
	 }

	public function view($id = null) {
		$this->viewObject($this->Event, $id);
	}

	public function save() {
 		$this->checkWriteModulePermission();
		$this->Transaction->begin() ;
		$this->saveObject($this->Event);
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
		$type = $conf->objectTypes['event']["id"];
		$this->set("categories", $this->Category->findAll("Category.object_type_id=".$type));
		$this->set("object_type_id", $type);
		$this->set("areasList", $this->BEObject->find('list', array(
										"conditions" => "object_type_id=" . Configure::read("objectTypes.area.id"), 
										"order" => "title", 
										"fields" => "BEObject.title"
										)
									)
								);
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


	protected function forward($action, $esito) {
	  	$REDIRECT = array(
				"cloneObject"	=> 	array(
										"OK"	=> "/events/view/{$this->Event->id}",
										"ERROR"	=> "/events/view/{$this->Event->id}" 
										),
                "view"              =>  array(
                                            "ERROR" => "/events"
                                        ), 
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
	 									),
	 			"addItemsToAreaSection"	=> 	array(
										"OK"	=> "/events",
										"ERROR"	=> "/events" 
										),
				"changeStatusObjects"	=> 	array(
										"OK"	=> "/events",
										"ERROR"	=> "/events" 
										)
	 		) ;
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	return false ;
	 }

}

?>
