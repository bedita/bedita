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

/**
 * Short description for class.
 *
 * Module Multimedia: management of Image, Audio, Video objects
 */
class MultimediaController extends ModulesController {
	var $name = 'Multimedia';

	var $helpers 	= array('BeTree', 'BeToolbar', 'MediaProvider');
	var $components = array('BeTree', 'Permission', 'BeFileHandler', 'SwfUpload', 'BeUploadToObj');

	// This controller does not use a model
	var $uses = array('Stream', 'Image', 'Audio', 'Video', 'BEObject', 'Tree', 'User', 'Group','Category') ;
	protected $moduleName = 'multimedia';
	
	 /**
	 * Show multimedia item list
	 */
	 function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		$this->setup_args(
			array("id", "integer", &$id),
			array("page", "integer", &$page),
			array("dim", "integer", &$dim),
			array("order", "string", &$order),
			array("dir", "boolean", &$dir)
		) ;
		$typesArray = array($conf->objectTypes['image'],$conf->objectTypes['audio'],$conf->objectTypes['video']);
				
		$bedita_items = $this->BeTree->getDiscendents($id, null, $typesArray, $order, $dir, $page, $dim)  ;
		
	 	foreach($bedita_items['items'] as $key => $value) {
			$modelLoaded = $this->loadModelByObjectTypeId($value['object_type_id']);
			$modelLoaded->restrict(array(
									"BEObject" => array("ObjectType"),
									"Content",
									"Stream"
									)
								);
			if(($Details = $modelLoaded->findById($value['id']))) {
				$Details['filename'] = substr($Details['path'],strripos($Details['path'],"/")+1);
				$bedita_items['items'][$key] = array_merge($bedita_items['items'][$key], $Details);	
			}
		}
		$this->params['toolbar'] = &$bedita_items['toolbar'] ;
		// template data
		$this->set('areasectiontree',$this->BeTree->getSectionsTree());
		$this->set('objects', $bedita_items['items']);
	 }

	 /**
	  * Show object for $id
	  * If $id is not passed, show new multimedia object page
	  * @param integer $id
	  */
	function view($id = null) {
		$conf  = Configure::getInstance() ;
		$this->setup_args(array("id", "integer", &$id)) ;
		// Get object by $id
		$obj = null ;
		if($id) {
			$model = $this->BEObject->getType($id);
			$this->{$model}->restrict(array(
									"BEObject" => array("ObjectType",
														"Permissions",
														"UserCreated", 
														"UserModified",
														"RelatedObject",
														"Category"),
									"Content", "Stream"
									)
								);
			if(!($obj = $this->{$model}->findById($id))) {
				 throw new BeditaException(sprintf(__("Error loading object: %d", true), $id));
			}
			if (isset($obj["Category"])) {
				$objCat = array();
				foreach ($obj["Category"] as $oc) {
					$objCat[] = $oc["id"];
				}
				$obj["Category"] = $objCat;
			}
			
			if (!empty($obj['RelatedObject'])) {
				$obj["relations"] = $this->objectRelationArray($obj['RelatedObject']);
			}
			
			$imagePath 	= $this->BeFileHandler->path($id) ;
			$imageURL 	= $this->BeFileHandler->url($id) ;
		}
		// data for template
		$this->set('object',	@$obj);
		$this->set('imagePath',	@$imagePath);
		$this->set('imageUrl',	@$imageURL);
		// get users and groups list. 
		$this->User->displayField = 'userid';
		$this->set("usersList", $this->User->find('list', array("order" => "userid")));
		$this->set("groupsList", $this->Group->find('list', array("order" => "name")));
	 }

	function save() {
		$this->checkWriteModulePermission();
		if(empty($this->data)) 
			throw new BeditaException( __("No data", true));
			
		$new = (empty($this->data['id'])) ? true : false ;
		
		// Verify object permits
		if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
			throw new BeditaException(__("Error modify permissions", true));
		
		$this->Transaction->begin() ;
		// save data
		$this->data["Category"] = $this->Category->saveTagList($this->params["form"]["tags"]);

		if (!empty($this->params['form']['Filedata']['name'])) {		
			$this->Stream->id = $this->BeUploadToObj->upload($this->data) ;
		} elseif (!empty($this->data['url'])) {
			$this->Stream->id = $this->BeUploadToObj->uploadFromMediaProvider($this->data) ;
		} else {
			$model = $this->BEObject->getType($this->data["id"]);
			if(!$this->{$model}->save($this->data)) {
	            throw new BeditaException(__("Error saving multimedia", true), $this->{$model}->validationErrors);
	        }
	        $this->Stream->id = $this->{$model}->id;
		}
		
		// update permissions
		if(!isset($this->data['Permissions'])) 
			$this->data['Permissions'] = array() ;
		$this->Permission->saveFromPOST($this->Stream->id, $this->data['Permissions'], 
				!empty($this->data['recursiveApplyPermissions']), 'document');
		$this->Transaction->commit() ;
		$this->userInfoMessage(__("Multimedia object saved", true)." - ".$this->data["title"]);
		$this->eventInfo("multimedia object [". $this->data["title"]."] saved");
	}

	 /**
	 * Delete multimedia object
	 */
	function delete($id = null) {
		$this->checkWriteModulePermission();
		$objectsListDeleted = $this->deleteMultimediaObjects();
		$this->userInfoMessage(__("Multimedia deleted", true) . " -  " . $objectsListDeleted);
		$this->eventInfo("multimedia $objectsListDeleted deleted");
	}


	/**
	 * Form page to upload multimedia objects
	 */
	function frm_upload() {
	}
	
	/**
	 * Form page to select bedita multimedia objects
	 */
	function frm_upload_bedita() {
		$order = ""; $dir = true; $page = 1; $dim = 20 ;
		$conf  = Configure::getInstance() ;
		$this->setup_args(
			array("page", "integer", &$page),
			array("dim", "integer", &$dim),
			array("order", "string", &$order),
			array("dir", "boolean", &$dir)
		) ;
		$ot = &$conf->objectTypes ; 
		$multimedia = $this->BeTree->getDiscendents(null, null, array($ot['image'], $ot['audio'], $ot['video']), $order, $dir, $page, $dim)  ;
		for($i=0; $i < count($multimedia['items']) ; $i++) {
			$id = $multimedia['items'][$i]['id'] ;
			$ret = $this->Stream->findById($id) ;
			$multimedia['items'][$i] = array_merge($multimedia['items'][$i], $ret['Stream']) ;
			$multimedia['items'][$i]['bedita_type'] = $conf->objectTypeModels[$multimedia['items'][$i]['object_type_id']] ;
		}
		$this->params['toolbar'] = &$multimedia['toolbar'] ;
		// Data for template
		$this->set('multimedia', 	$multimedia['items']);
		$this->set('toolbar', 		$multimedia['toolbar']);
	}

	/**
	 * Form page to upload multimedia through URL
	 */
	function frm_upload_url() {
	}
	 
	protected function forward($action, $esito) {

		$REDIRECT = array(
			"cloneObject"	=> 	array(
							"OK"	=> "/multimedia/view/".@$this->BEObject->id,
							"ERROR"	=> "/multimedia/view/".@$this->BEObject->id 
							),
			"save"  =>  array(
							"OK"    => "/multimedia/view/".@$this->Stream->id,
							"ERROR" => "/multimedia/view/".@$this->data['id'] 
							), 
			"delete"	=> 	array(
							"OK"	=> "./",
							"ERROR"	=> "./view/".@$this->params['pass'][0]
							),
			"changeStatusObjects"	=> 	array(
							"OK"	=> "/multimedia",
							"ERROR"	=> "/multimedia"
							)
						);
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}

}

?>