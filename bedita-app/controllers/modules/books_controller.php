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
		$types = $conf->objectTypes['book']["id"];
		
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
		$this->saveObject($this->Book);
	 	$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Book saved", true)." - ".$this->data["title"]);
		$this->eventInfo("Book [". $this->data["title"]."] saved");
	 }

	public function delete() {
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