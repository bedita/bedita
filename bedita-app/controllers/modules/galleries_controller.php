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

		$this->checkWriteModulePermission();
		if(empty($this->data))  
			throw new BeditaException( __("No data", true));
		$new = (empty($this->data['id'])) ? true : false;
		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) {
			throw new BeditaException(__("Error modify permissions", true));
		}
		// Data to save
		$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]);
		
		$multimedia = (isset($this->data['RelatedObject']))? $this->data['RelatedObject'] : array() ;
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
		
		if (!empty($multimedia)) {
			
			if (!is_array($multimedia)) {
				throw new BeditaException( __("Error saving object", true), "multimedia is not an array");
			}
			
			foreach($multimedia as $m) {
				if (!empty($m['id'])) {
					if(!$this->Gallery->appendChild($m['id'],null,$m['priority'])) {
						throw new BeditaException( __("Append child", true));
					}
					
					// save modified title and description 
					$m['modified'] = (!isset($m['modified']))?0:((integer)$m['modified']) ;
					if($m['modified']) {
						if(!$this->Gallery->BEObject->updateTitleDescription($m['id'] , $m['title'], $m['description'])) {
							throw new BeditaException( __("Save info child", true));
						}
						
					}
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
									"Content", 
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
				
		$this->set('object',	$obj);
		$this->set('attach',$multimedia);
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