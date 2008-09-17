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

	var $uses = array('BEObject', 'Bibliography', 'Tree', 'Category') ;
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

	 public function view($id = null) {

		$this->viewObject($this->Book, $id);

	 }

	public function save() {
		$this->checkWriteModulePermission();
		$this->Transaction->begin();
		$this->saveObject($this->Document);
	 	$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Book saved", true)." - ".$this->data["title"]);
		$this->eventInfo("Book [". $this->data["title"]."] saved");
	 }
	 
	public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Bibliography");
		$this->userInfoMessage(__("Bibliography deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("bibliographies $objectsListDeleted deleted");
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
			"addItemsToAreaSection"	=> 	array(
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