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
 * @author 			d.domenico@channelweb.it
 */

class GalleriesController extends ModulesController {
	var $name = 'Galleries';
	var $helpers 	= array('Beurl', 'BeTree', 'BeToolbar');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText');
	protected $moduleName = 'galleries';
	
	/**
	 * Public methods for the controller
	 */

	public function index($id = null, $order = "", $dir = true, $page = 1, $dim = 10) {
		$conf  = Configure::getInstance() ;
		$types = array($conf->objectTypes['gallery']);
		$this->paginatedList($id, $types, $order, $dir, $page, $dim);
	}

	public function view($id = null) {
		$this->loadGallery($id);
	}

	function select_from_list($id = null, $order = "", $dir = true, $page = 1, $dim = 10) {
		$this->loadGalleries($id,$order,$dir,$page,$dim);
	}	
	
	public function save() {
/*
pr($this->data) ;
pr(serialize($this->data));
exit;

$this->data = unserialize('a:7:{s:2:"id";s:2:"17";s:4:"lang";s:3:"ita";s:8:"LangText";a:6:{s:3:"ita";a:2:{s:5:"title";s:5:"aaaaa";s:11:"description";s:0:"";}s:3:"eng";a:2:{s:5:"title";s:0:"";s:11:"description";s:0:"";}s:3:"spa";a:2:{s:5:"title";s:0:"";s:11:"description";s:0:"";}s:3:"por";a:2:{s:5:"title";s:0:"";s:11:"description";s:0:"";}s:3:"fra";a:2:{s:5:"title";s:0:"";s:11:"description";s:0:"";}s:3:"deu";a:2:{s:5:"title";s:0:"";s:11:"description";s:0:"";}}s:6:"status";s:5:"draft";s:8:"nickname";s:5:"aaaaa";s:14:"ObjectRelation";a:4:{s:6:"attach";a:1:{s:6:"switch";s:6:"attach";}i:0;a:6:{s:2:"id";s:1:"1";s:6:"switch";s:6:"attach";s:8:"modified";s:1:"1";s:8:"priority";s:1:"1";s:5:"title";s:5:"title";s:11:"description";s:4:"desc";}i:1;a:6:{s:2:"id";s:1:"2";s:6:"switch";s:6:"attach";s:8:"modified";s:1:"0";s:8:"priority";s:1:"2";s:5:"title";s:4:"test";s:11:"description";s:10:"desc 1 AAA";}i:2;a:6:{s:2:"id";s:1:"3";s:6:"switch";s:6:"attach";s:8:"modified";s:1:"0";s:8:"priority";s:1:"3";s:5:"title";s:44:"artwork_images_119642_301467_ansel-adams.jpg";s:11:"description";s:6:"test 3";}}s:11:"Permissions";a:2:{i:0;a:5:{s:4:"name";s:13:"administrator";s:17:"BEDITA_PERMS_READ";s:1:"4";s:19:"BEDITA_PERMS_MODIFY";s:1:"2";s:19:"BEDITA_PERMS_DELETE";s:1:"4";s:6:"switch";s:5:"group";}i:1;a:3:{s:4:"name";s:5:"guest";s:6:"switch";s:5:"group";s:17:"BEDITA_PERMS_READ";s:1:"1";}}}') ;
$this->data['ObjectRelation'] [0]['description'] = "'test" ;
// pr($this->data) ;
// exit;
*/
		$this->checkWriteModulePermission();
		if(empty($this->data))  
			throw new BeditaException( __("No data", true));
		$new = (empty($this->data['id'])) ? true : false;
		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) {
			throw new BeditaException(__("Error modify permissions", true));
		}
		// Data to save
		$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]);
		$this->data['title'] = $this->data['LangText'][$this->data['lang']]['title'];
		$this->data['description'] = $this->data['LangText'][$this->data['lang']]['description'];
		$this->BeLangText->setupForSave($this->data["LangText"]);
		$multimedia = (isset($this->data['ObjectRelation']))? $this->data['ObjectRelation'] : array() ;
		unset($this->data['multimedia']);
	
		$this->Transaction->begin();
		if(!$this->Gallery->save($this->data)) {
			throw new BeditaException( __("Error saving gallery", true), $this->Gallery->validationErrors);
		}		
		// update permits
		$perms = isset($this->data["Permissions"])?$this->data["Permissions"]:array();
		if(!$this->Permission->saveFromPOST(
				$this->Gallery->id, $perms,
				(empty($this->data['recursiveApplyPermissions'])?false:true), 'gallery')
			) {
				throw new BeditaException( __("Error saving permissions", true));
		}
		// Insert new multimedia items (remove previous associations)
		
		if(!$this->Gallery->removeChildren()) 
			throw new BeditaException( __("Remove children", true));
		
		foreach($multimedia as $m) {
			if (!empty($m['id'])) {
				if(!$this->Gallery->appendChild($m['id'],null,$m['priority'])) {
					throw new BeditaException( __("Append child", true));
				}
				
				// save modified title and description 
				$m['modified'] = (!isset($m['modified']))?0:((integer)$m['modified']) ;
				if($m['modified']) {
					if(!class_exists('ContentBase')) {
						App::import('Model', 'ContentBase') ;
						
						$this->ContentBase = new ContentBase() ;
					}
									
					if(!$this->Gallery->BEObject->updateTitleDescription($m['id'] , $m['title'], $m['description'])) {
						throw new BeditaException( __("Save info child", true));
					}
					$this->ContentBase->saveLangTextObjectRelation($m['id'], $this->data['lang'], $m['title'], "title") ;
					$this->ContentBase->saveLangTextObjectRelation($m['id'], $this->data['lang'], $m['description'], "description") ;
				}
			}
		}
		$this->Transaction->commit();
	
		$this->userInfoMessage(__("Gallery saved", true) . "<br />" . $this->data["title"]);
		$this->eventInfo("gallery ". $this->data["title"]."saved");
	}

	public function delete() {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteObjects("Gallery");
		$this->userInfoMessage(__("Galleries deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("galleries $objectsListDeleted deleted");
	}

	/**
	 * Private methods
	 */

	private function loadGallery($id) {
		$this->setup_args(array("id", "integer", &$id));
		$conf 		= Configure::getInstance();
		$obj 		= null;
		$multimedia = array();
		// get Gallery data
		if($id) {
			$this->Gallery->restrict(array(
										"BEObject" => array("ObjectType", 
															"UserCreated", 
															"UserModified", 
															"Permissions",
															"CustomProperties",
															"LangText"
															),
										"Collection"
										)
									);
			if(!($obj = $this->Gallery->findById($id))) {
				throw new BeditaException( sprintf(__("Error loading gallery: %d", true), $id));
				return;
			}
			// Get gallery contents
			$types = array($conf->objectTypes['image'], $conf->objectTypes['audio'], $conf->objectTypes['video']) ;
			$children = $this->BeTree->getChildren($id, null, $types, "priority") ;
			$objForGallery = &$children['items'] ;
			$multimedia_id=array();
			foreach($objForGallery as $index => $object) {
				$type = $conf->objectTypeModels[$object['object_type_id']] ;
				$modelLoaded = $this->loadModelByObjectTypeId($object['object_type_id']);
				$modelLoaded->restrict(array(
									"BEObject" => array("ObjectType", 
														"LangText"
														),
									"ContentBase", 
									"Stream"
									)
								);
				if(!($Details = $modelLoaded->findById($object['id']))) continue ;
				$Details['priority'] = $object['priority'];
				$Details['filename'] = substr($Details['path'],strripos($Details['path'],"/")+1);
				$multimedia[$index]=$Details;
				$multimedia_id[]=$object['id'];
			}
		}
		if(isset($obj["LangText"])) $this->BeLangText->setupForView($obj["LangText"]);
		
		$this->set('object',	$obj);
		$this->set('multimedia',$multimedia);
		$this->selfUrlParams = array("id", $id);    
		$this->setUsersAndGroups();
	}

	protected function forward($action, $esito) {
		$REDIRECT = array("save"	=> 	array("OK"	=> "./view/{$this->Gallery->id}","ERROR"	=> "./view/{$this->Gallery->id}"),
						"delete"	=> 	array("OK"	=> "./","ERROR"	=> "./view/{@$this->params['pass'][0]}"));
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito];
		return false;
	}
}

?>