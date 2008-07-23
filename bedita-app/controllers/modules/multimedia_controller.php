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
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText', 'BeFileHandler');

	// This controller does not use a model
	var $uses = array('Stream', 'Image', 'Audio', 'Video', 'BEObject', 'ContentBase', 'Content', 'BaseDocument', 'Tree', 'User', 'Group') ;
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
									"ContentBase",
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
			$rec = $this->BEObject->recursive ;
			$this->BEObject->recursive = -1 ;
			if(!($ret = $this->BEObject->read('object_type_id', $id))) 
			     throw new BeditaException(sprintf(__("Error get object: %d", true), $id));
			$this->BEObject->recursive = $rec ;
			$model = $conf->objectTypeModels[$ret['BEObject']['object_type_id']] ;
			$this->{$model}->bviorHideFields = array('Version', 'Index', 'current', 'multimedia', 'attachments') ;
			if(!($obj = $this->{$model}->findById($id))) {
				 throw new BeditaException(sprintf(__("Error loading object: %d", true), $id));
			}
			$imagePath 	= $this->BeFileHandler->path($id) ;
			$imageURL 	= $this->BeFileHandler->url($id) ;
		}
		// Language data
		if(isset($obj["LangText"])) {
			$this->BeLangText->setupForView($obj["LangText"]) ;
		}
		// data for template
		$this->set('object',	@$obj);
		$this->set('imagePath',	@$imagePath);
		$this->set('imageUrl',	@$imageURL);
		$this->selfUrlParams = array("id", $id);    
		// get users and groups list. 
		$this->User->displayField = 'userid';
		$this->set("usersList", $this->User->find('list', array("order" => "userid")));
		$this->set("groupsList", $this->Group->find('list', array("order" => "name")));
	 }

     function save() {
	   
     	$this->checkWriteModulePermission();
        
        if(empty($this->data) || empty($this->data['id'])) 
            throw new BeditaException( __("Bad data", true));
        
        // Verifica i permessi di modifica dell'oggetto
        if(!$this->Permission->verify($this->data['id'], 
            $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
                throw new BeditaException(__("Error modify permissions", true));
        
        // Formatta le custom properties
        $this->BeCustomProperty->setupForSave($this->data["CustomProperties"]) ;

        // Formatta i campi da tradurre
		$this->data['title'] = $this->data['LangText'][$this->data['lang']]['title'];
		$this->data['description'] = $this->data['LangText'][$this->data['lang']]['description'];
        $this->BeLangText->setupForSave($this->data["LangText"]) ;
		
        $this->Transaction->begin() ;
        
        // save
        if(!$this->BEObject->save($this->data)) {
            throw new BeditaException(__("Error saving multimedia object", true), 
                $this->BEObject->validationErrors);
        }

        // update permissions
        $perms = isset($this->data["Permissions"])?$this->data["Permissions"]:array();
        if(!$this->Permission->saveFromPOST(
                $this->BEObject->id, $perms,
                (empty($this->data['recursiveApplyPermissions'])?false:true), 'document')
            ) {
                throw new BeditaException( __("Error saving permissions", true));
        }       

        $this->Transaction->commit() ;
        $this->userInfoMessage(__("Multimedia object saved", true)." - ".$this->data["title"]);
        $this->eventInfo("multimedia object [". $this->data["title"]."] saved");
    }
	 

	 /**
	 * Delete multimedia object
	 */
	function delete($id = null) {
		$this->checkWriteModulePermission();
		if(!isset($this->data['id'])) 
		  throw new BeditaException(sprintf(__("No data", true), $id));
		$this->Transaction->begin() ;
		if(!$this->BeFileHandler->del($this->data['id'])) 
		  throw new BeditaException(sprintf(__("Error deleting object: %d", true), $id));
		$this->Transaction->commit() ;
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
							"OK"	=> "/multimedia/view/{$this->BEObject->id}",
							"ERROR"	=> "/multimedia/view/{$this->BEObject->id}" 
							),
			"save"  =>  array(
							"OK"    => "/multimedia/view/{$this->BEObject->id}",
							"ERROR" => "/multimedia/" 
							), 
			"delete"	=> 	array(
							"OK"	=> "./",
							"ERROR"	=> "./view/{@$this->params['pass'][0]}"
							),
			"addToAreaSection"	=> 	array(
							"OK"	=> "/multimedia",
							"ERROR"	=> "/multimedia" 
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