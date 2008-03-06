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
	var $uses = array('Area', 'Section', 'BEObject', 'ContentBase', 'Content', 'BaseDocument', 'Gallery', 'Tree', 'Image', 'Audio', 'Video', 'User', 'Group');
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
		$this->checkWriteModulePermission();
		if(empty($this->data))  
			throw new BeditaException( __("No data", true));
		$new = (empty($this->data['id'])) ? true : false;
		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) {
			throw new BeditaException(__("Error modify permissions", true));
		}
		// Data to save
		$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]);
		$this->BeLangText->setupForSave($this->data["LangText"]);
		$multimedia = (isset($this->data['multimedia']))?$this->data['multimedia']:array() ;
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
		for($i=0; $i < count($multimedia) ; $i++) {
			if(!$this->Gallery->appendChild($multimedia[$i]['id'],null,$multimedia[$i]['priority'])) {
				throw new BeditaException( __("Append child", true));
			}
		}
		$this->Transaction->commit();
		$this->userInfoMessage(__("Gallery saved", true)." - ".$this->data["title"]);
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
			$this->Gallery->bviorHideFields = array('Version', 'Index', 'current');
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
				$this->{$type}->bviorHideFields = array('UserCreated','UserModified','Permissions','Version','CustomProperties','Index','langObjs', 'images', 'multimedia', 'attachments');
				if(!($Details = $this->{$type}->findById($object['id']))) continue ;
				$Details['priority'] = $object['priority'];
				$Details['filename'] = substr($Details['path'],strripos($Details['path'],"/")+1);
				$multimedia[$index]=$Details;
				$multimedia_id[]=$object['id'];
			}
		}
		if(isset($obj["LangText"])) $this->BeLangText->setupForView($obj["LangText"]);
		// begin#bedita_items
		$ot = &$conf->objectTypes ; 
		$bedita_items = $this->BeTree->getDiscendents(null, null, array($ot['image'], $ot['audio'], $ot['video']))  ;
		foreach($bedita_items['items'] as $key => $value) {
			if(!empty($multimedia_id) && in_array($value['id'],$multimedia_id)) {
				unset($bedita_items['items'][$key]);
			} else {
				// get details
				$type = $conf->objectTypeModels[$value['object_type_id']];
				$this->{$type}->bviorHideFields = array('UserCreated','UserModified','Permissions','Version','CustomProperties','Index','langObjs', 'images', 'multimedia', 'attachments');
				if(($Details = $this->{$type}->findById($value['id']))) {
					$Details['filename'] = substr($Details['path'],strripos($Details['path'],"/")+1);
					$bedita_items['items'][$key] = array_merge($bedita_items['items'][$key], $Details);	
				}
			}
		}
		$this->params['toolbar'] = &$bedita_items['toolbar'] ;
		$this->set('bedita_items', 	$bedita_items['items']);
		$this->set('toolbar', 		$bedita_items['toolbar']);
		// end#bedita_items
		$this->set('object',	$obj);
		$this->set('multimedia',$multimedia);
		$this->selfUrlParams = array("id", $id);    
		// get users and groups list. 
		$this->User->displayField = 'userid';
		$this->set("usersList", $this->User->find('list', array("order" => "userid")));
		$this->set("groupsList", $this->Group->find('list', array("order" => "name")));
	}

	protected function forward($action, $esito) {
		$REDIRECT = array("save"	=> 	array("OK"	=> "./view/{$this->Gallery->id}","ERROR"	=> "./view/{$this->Gallery->id}"),
						"delete"	=> 	array("OK"	=> "./","ERROR"	=> "./view/{@$this->params['pass'][0]}"));
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito];
		return false;
	}
}