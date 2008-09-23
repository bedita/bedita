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
	var $components = array('BeLangText', 'BeFileHandler');

	var $uses = array('BEObject', 'Document', 'Tree') ;
	protected $moduleName = 'documents';
	
    public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {    	
    	$conf  = Configure::getInstance() ;
		$types = array($conf->objectTypes['document']["id"]);
		
		if (!empty($this->params["form"]["searchstring"])) {
			$types["search"] = addslashes($this->params["form"]["searchstring"]);
			$this->set("stringSearched", $this->params["form"]["searchstring"]);
		}
		
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
	 }

	 public function view($id = null) {
		$this->viewObject($this->Document, $id);
	 }

	public function save() {
		$this->checkWriteModulePermission();
		$this->Transaction->begin();
		$this->saveObject($this->Document);
	 	$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Document saved", true)." - ".$this->data["title"]);
		$this->eventInfo("document [". $this->data["title"]."] saved");
	 }

	public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Document");
		$this->userInfoMessage(__("Documents deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("documents $objectsListDeleted deleted");
	}


	protected function forward($action, $esito) {
		$REDIRECT = array(
			"cloneObject"	=> 	array(
							"OK"	=> "/documents/view/".@$this->Document->id,
							"ERROR"	=> "/documents/view/".@$this->Document->id 
							),
			"view"	=> 	array(
							"ERROR"	=> "/documents" 
							), 
			"save"	=> 	array(
							"OK"	=> "/documents/view/".@$this->Document->id,
							"ERROR"	=> "/documents/view/".@$this->Document->id 
							), 
			"delete" =>	array(
							"OK"	=> "/documents",
							"ERROR"	=> "/documents/view/".@$this->params['pass'][0]
							),
			"addItemsToAreaSection"	=> 	array(
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