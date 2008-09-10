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

class BooksController extends ModulesController {
	var $name = 'Books';

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText', 'BeFileHandler');

	var $uses = array('BEObject', 'Book', 'Tree', 'Category') ;
	protected $moduleName = 'books';
	
    public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$types = $conf->objectTypes['book'];
		
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
			$this->Book->restrict(array(
										"BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"Permissions",
															"CustomProperties",
															"LangText"
															),
										"Content" => array("*")
										)
									);
			if(!($obj = $this->Book->findById($id))) {
				 throw new BeditaException(sprintf(__("Error loading document: %d", true), $id));
			}
			$relations = $this->objectRelationArray($obj['RelatedObject']);
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
		$this->data["Category"] = $this->Category->saveTagList($this->params["form"]["tags"]);
		if(!$this->Book->save($this->data)) {
			throw new BeditaException(__("Error saving Book", true), $this->Book->validationErrors);
		}
		if(!($this->data['status']=='fixed')) {
			if(!isset($this->data['destination'])) 
				$this->data['destination'] = array() ;
			$this->BeTree->updateTree($this->Book->id, $this->data['destination']);
		}
	 	// update permissions
		if(!isset($this->data['Permissions'])) 
			$this->data['Permissions'] = array() ;
		$this->Permission->saveFromPOST($this->Book->id, $this->data['Permissions'], 
	 			!empty($this->data['recursiveApplyPermissions']), 'event');
 		$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Book saved", true)." - ".$this->data["title"]);
		$this->eventInfo("Book [". $this->data["title"]."] saved");
	 }

	 /**
	  * Delete a document.
	  */
	function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Book");
		$this->userInfoMessage(__("Book deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("books $objectsListDeleted deleted");
	}




	/**
	 * load all books test da eliminare
	 *
	 * @param array $filters
	 * 
	 */
	public function listAllBooks($filters = null) {
		$this->layout = null;
		$this->render(null, null, VIEWS."books/inc/list_all_books.tpl");
	}





	protected function forward($action, $esito) {
		$REDIRECT = array(
			"cloneObject"	=> 	array(
							"OK"	=> "/books/view/{$this->Book->id}",
							"ERROR"	=> "/books/view/{$this->Book->id}" 
							),
			"save"	=> 	array(
							"OK"	=> "/books/view/{$this->Book->id}",
							"ERROR"	=> "/books/view/{$this->Book->id}" 
							), 
			"delete" =>	array(
							"OK"	=> "/books",
							"ERROR"	=> "/books/view/{@$this->params['pass'][0]}" 
							),
			"addItemsToAreaSection"	=> 	array(
							"OK"	=> "/books",
							"ERROR"	=> "/books" 
							),
			"changeStatusObjects"	=> 	array(
							"OK"	=> "/books",
							"ERROR"	=> "/books" 
							)
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
}	

?>