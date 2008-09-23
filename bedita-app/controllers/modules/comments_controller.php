<?php
class CommentsController extends ModulesController {
	
	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'Permission', 'BeLangText');
	
	protected $moduleName = 'comments';
	
	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$types = $conf->objectTypes['comment']["id"];
		
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
	 }
	 
	public function view($id = null) {
		$obj = null ;
		$relations = array();
		if($id) {
			$this->Comment->containLevel("detailed");
			if(!($obj = $this->Comment->findById($id))) {
				 throw new BeditaException(sprintf(__("Error loading comment: %d", true), $id));
			}
			$relations = $this->objectRelationArray($obj['RelatedObject']);
		}
		$this->set('object',	$obj);
		$this->set('relObjects', $relations);
	 }
	 
	public function save() {
		$this->checkWriteModulePermission();
		if(empty($this->data)) 
			throw new BeditaException( __("No data", true));
		$new = (empty($this->data['id'])) ? true : false ;
		// Verify object permits
		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
			throw new BeditaException(__("Error modify permissions", true));
		
		$this->Transaction->begin() ;
		// Save data
		if(!$this->Comment->save($this->data)) {
	 		throw new BeditaException(__("Error saving comment", true), $this->Comment->validationErrors);
	 	}
 		$this->Transaction->commit() ;
 		$this->userInfoMessage(__("Comment saved", true)." - ".$this->data["title"]);
		$this->eventInfo("comment [". $this->data["title"]."] saved");
	 }
	 	
	public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Comment");
		$this->userInfoMessage(__("Comments deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("Comments $objectsListDeleted deleted");
	} 
	 
	protected function forward($action, $esito) {
		$REDIRECT = array(
			"save"	=> 	array(
							"OK"	=> "/comments/view/{$this->Comment->id}",
							"ERROR"	=> "/comments/view" 
							), 
			"delete" =>	array(
							"OK"	=> "/comments",
							"ERROR"	=> "/comments/view/{@$this->params['pass'][0]}" 
							),
			"changeStatusObjects"	=> 	array(
							"OK"	=> "/comments",
							"ERROR"	=> "/comments" 
							)
		);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}
	
}
?>