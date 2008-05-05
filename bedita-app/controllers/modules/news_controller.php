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
	var $uses = array('BEObject','ShortNews','ObjectCategory','Area') ;
	protected $moduleName = 'news';
	
	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$types = array($conf->objectTypes['shortnews']);
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
	 }

	public function view($id = null) {

		$obj = null ;
		if(isset($id)) {
			
			$this->ShortNews->bviorHideFields = array('Version', 'Index', 'current') ;
			$obj = $this->ShortNews->find($id);
			if($obj == null || $obj === false) {
				 throw new BeditaException(__("Error loading news: ", true).$id);
			}
			$relations = $this->objectRelationArray($obj['ObjectRelation']);
			if(isset($obj["LangText"])) {
				$this->BeLangText->setupForView($obj["LangText"]) ;
			}
			if (isset($obj["ObjectCategory"])) {
				$objCat = array();
				foreach ($obj["ObjectCategory"] as $oc) {
					$objCat[] = $oc["id"];
				}
				$obj["ObjectCategory"] = $objCat;
			}
		}

		$this->set('object',	$obj);
		$this->set('tree', 		$this->BeTree->getSectionsTree());
		$this->set('parents',	$this->BeTree->getParents($id));	
		$this->set('attach', isset($relations['attach']) ? $relations['attach'] : array());		
		$this->set('relObjects', isset($relations) ? $relations : array());
		$conf  = Configure::getInstance() ;
		$ot = $conf->objectTypes['shortnews'];
		$areaCategory = $this->ObjectCategory->getCategoriesByArea($ot);
		$this->set("areaCategory", $areaCategory);
		$this->Area->displayField = 'public_name';
		$this->set("areasList", $this->Area->find('list', array("order" => "public_name")));	
		$this->selfUrlParams = array("id", $id);
		$this->setUsersAndGroups();
	 }


	public function save() {
	 	
 		$this->checkWriteModulePermission();
 //echo serialize($this->data) ;
 //exit;
 //$this->data = unserialize('a:8:{s:2:"id";s:0:"";s:4:"lang";s:3:"ita";s:8:"LangText";a:6:{s:3:"ita";a:2:{s:5:"title";s:7:"kjkjkjk";s:11:"description";s:9:"jkjkhjhjh";}s:3:"eng";a:2:{s:5:"title";s:0:"";s:11:"description";s:0:"";}s:3:"spa";a:2:{s:5:"title";s:0:"";s:11:"description";s:0:"";}s:3:"por";a:2:{s:5:"title";s:0:"";s:11:"description";s:0:"";}s:3:"fra";a:2:{s:5:"title";s:0:"";s:11:"description";s:0:"";}s:3:"deu";a:2:{s:5:"title";s:0:"";s:11:"description";s:0:"";}}s:6:"status";s:5:"draft";s:5:"start";s:0:"";s:3:"end";s:0:"";s:8:"nickname";s:0:"";s:14:"ObjectRelation";a:3:{s:11:"event_place";a:1:{s:6:"switch";s:11:"event_place";}s:8:"relators";a:1:{s:6:"switch";s:8:"relators";}s:10:"moderators";a:1:{s:6:"switch";s:10:"moderators";}}}') ;
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
		$this->BeLangText->setupForSave($this->data["LangText"]) ;
	 	// if no Category is checked set an empty array to delete association between news and category
	 	if (!isset($this->data["ObjectCategory"])) $this->data["ObjectCategory"] = array();
	 	
		$this->Transaction->begin() ;

		if(!$this->ShortNews->save($this->data)) {
	 		throw new BeditaException(__("Error saving news", true), $this->ShortNews->validationErrors);
	 	}

		if(!isset($this->data['destination'])) 
			$this->data['destination'] = array() ;
		$this->BeTree->updateTree($this->ShortNews->id, $this->data['destination']);
		
	 	// update permissions
		if(!isset($this->data['Permissions'])) 
			$this->data['Permissions'] = array() ;
		$this->Permission->saveFromPOST($this->ShortNews->id, $this->data['Permissions'], 
	 			!empty($this->data['recursiveApplyPermissions']), 'news');
	 			
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
		$type = $conf->objectTypes['shortnews'];
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

	function addToAreaSection() {
		if(!empty($this->params['form']['objects_to_del'])) {
			$objects_to_assoc = split(",",$this->params['form']['objects_to_del']);
			$destination = $this->data['destination'];
			$this->addItemsToAreaSection($objects_to_assoc,$destination);
		}
	}

	protected function forward($action, $esito) {
		$REDIRECT = array(
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
				"addToAreaSection"	=> 	array(
										"OK"	=> "/news",
										"ERROR"	=> "/news" 
										)
		) ;
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	return false ;
	 }

}

?>