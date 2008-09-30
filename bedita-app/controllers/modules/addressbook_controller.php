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
 * @author 			andrea@chialab.it, dante@channelweb.it
 */

class AddressbookController extends ModulesController {
	
	var $name = 'Addressbook';
	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText', 'BeFileHandler');

	var $uses = array('BEObject','Tree', 'Category', 'Card', 'MailGroup') ;
	protected $moduleName = 'addressbook';
	
    public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$types = $conf->objectTypes['card']['id'];
		
		if (!empty($this->params["form"]["searchstring"])) {
			$types["search"] = addslashes($this->params["form"]["searchstring"]);
			$this->set("stringSearched", $this->params["form"]["searchstring"]);
		}
		
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
	 }

	 /**
	  * Get address.
	  * If id is null, empty document
	  *
	  * @param integer $id
	  */
	function view($id = null) {
		$this->viewObject($this->Card, $id);
		$this->set("groupsByArea", $this->MailGroup->getGroupsByArea(null, $id));
	}

	/**
	 * Creates/updates card
	 */
	function save() {
		$this->checkWriteModulePermission();
		$this->Transaction->begin();
		$kind = ($this->data['company']==0) ? 'person' : 'cmp';
		if($kind == 'person') {
			$this->data['title'] = $this->data['person']['name']." ".$this->data['person']['surname'];
			$this->data['birthdate'] = $this->data['person']['birthdate'];
			$this->data['deathdate'] = $this->data['person']['deathdate'];
		} else {
			$this->data['title'] = $this->data['cmp']['company_name'];
			$this->data['company_name'] = $this->data['cmp']['company_name'];
		}
		$this->data['name'] = $this->data[$kind]['name'];
		$this->data['surname'] = $this->data[$kind]['surname'];
		$this->data['person_title'] = $this->data[$kind]['person_title'];

		$this->saveObject($this->Card);
	 	$this->Transaction->commit();
		$this->userInfoMessage(__("Card saved", true)." - ".$this->data["title"]);
		$this->eventInfo("card [". $this->data["title"]."] saved");
	}

	/**
	  * Delete a card.
	  */
	function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Card");
		$this->userInfoMessage(__("Cards deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("cards $objectsListDeleted deleted");
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
							"OK"	=> "/addressbook/view/".@$this->Card->id,
							"ERROR"	=> "/addressbook/view/".@$this->Card->id 
							),
			"save"	=> 	array(
							"OK"	=> "/addressbook/view/".@$this->Card->id,
							"ERROR"	=> "/addressbook/view/".@$this->Card->id 
							), 
			"delete" =>	array(
							"OK"	=> "/addressbook",
							"ERROR"	=> "/addressbook/view/".@$this->params['pass'][0]
							),
			"changeStatusObjects"	=> 	array(
							"OK"	=> "/addressbook",
							"ERROR"	=> "/addressbook" 
							),			
 			"saveCategories" 	=> array(
 										"OK"	=> "/addressbook/categories",
 										"ERROR"	=> "/addressbook/categories"
 									),
 			"deleteCategories" 	=> array(
 										"OK"	=> "/addressbook/categories",
 										"ERROR"	=> "/addressbook/categories"
 									),


		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}

}

?>