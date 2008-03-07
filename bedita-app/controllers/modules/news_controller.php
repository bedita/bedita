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
	var $uses = array('ShortNews') ;
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
		$this->BeLangText->setupForSave($this->data["LangText"]) ;
	 	
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
	 		) ;
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	return false ;
	 }

}
