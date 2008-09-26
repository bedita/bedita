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
							)
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}

}

?>