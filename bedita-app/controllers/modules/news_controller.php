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
 * Short news handling
 */
class NewsController extends ModulesController {

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText');
	var $uses = array('BEObject','ShortNews','Category','Area') ;
	protected $moduleName = 'news';
	
	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$types = array($conf->objectTypes['shortnews']["id"]);
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
	 }

	public function view($id = null) {
    	$this->viewObject($this->ShortNews, $id);
	 }


	public function save() {
        $this->checkWriteModulePermission();
		$this->Transaction->begin();
		$this->saveObject($this->ShortNews);
	 	$this->Transaction->commit();
 		$this->userInfoMessage(__("News saved", true)." - ".$this->data["title"]);
		$this->eventInfo("news [". $this->data["title"]."] saved");
	 }
	

	 public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("ShortNews");
		$this->userInfoMessage(__("News deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("News $objectsListDeleted deleted");
	}

	public function categories() {
		$conf  = Configure::getInstance() ;
		$type = $conf->objectTypes['shortnews']["id"];
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
										"OK"	=> "/news/view/{$this->ShortNews->id}",
										"ERROR"	=> "/news/view/{$this->ShortNews->id}" 
										),
				"save"	=> 	array(
										"OK"	=> "/news/view/{$this->ShortNews->id}",
										"ERROR"	=> "/news" 
									), 
				"delete" =>	array(
										"OK"	=> "/news",
										"ERROR"	=> "/news/view/{@$this->params['pass'][0]}" 
									), 
				"saveCategories" 	=> array(
										"OK"	=> "/news/categories",
										"ERROR"	=> "/news/categories"
										),
				"deleteCategories" 	=> array(
										"OK"	=> "/news/categories",
										"ERROR"	=> "/news/categories"
										),
				"addItemsToAreaSection"	=> 	array(
										"OK"	=> "/news",
										"ERROR"	=> "/news" 
										),
				"changeStatusObjects"	=> 	array(
										"OK"	=> "/news",
										"ERROR"	=> "/news" 
										)
		) ;
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	return false ;
	 }

}

?>