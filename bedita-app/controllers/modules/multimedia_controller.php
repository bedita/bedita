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
class MultimediaController extends AppController {
	var $name = 'Multimedia';

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText', 'BeFileHandler');

	// This controller does not use a model
	var $uses = array('Stream', 'Image', 'Audio', 'Video', 'BEObject', 'ContentBase', 'Content', 'BaseDocument', 'Tree', 'User', 'Group') ;
	protected $moduleName = 'multimedia';
	
	 /**
	 * Show multimedia item list
	 */
	 function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
	 	// Pagination parameters
		$this->setup_args(
			array("id", "integer", &$id),
			array("page", "integer", &$page),
			array("dim", "integer", &$dim),
			array("order", "string", &$order),
			array("dir", "boolean", &$dir)
		) ;
		$tree = $this->BeTree->expandOneBranch($id) ;
		$ot = &$conf->objectTypes ; 
		$multimedia = $this->BeTree->getDiscendents($id, null, array($ot['image'], $ot['audio'], $ot['video']), $order, $dir, $page, $dim)  ;
		$this->params['toolbar'] = &$multimedia['toolbar'] ;
		// Data for template
		$this->set('tree', 			$tree);
		$this->set('multimedia', 	$multimedia['items']);
		$this->set('toolbar', 		$multimedia['toolbar']);
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

        // Formatta i campi d tradurre
        $this->BeLangText->setupForSave($this->data["LangText"]) ;
        
        $this->Transaction->begin() ;
        
        // save
        if(!$this->BEObject->save($this->data)) {
            throw new BeditaException(__("Error saving multimedia object", true), 
                $this->BEObject->validationErrors);
        }

        // aggiorna i permessi
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
	 * Called by Ajax.
	 * Show multimedia object in the form page
	 * @param string $filename	File to show in the form page
	 */
	function get_item_form($filename = null) {
		$filename = urldecode($this->params['form']['filename']) ;
		if(!($id = $this->Stream->getIdFromFilename($filename))) throw new BeditaException(sprintf(__("Error get id object: %d", true), $id));
		$this->_get_item_form($id) ;
	}

	 
	/**
	 * Called by Ajax.
	 * Show multimedia object in the form page
	 * @param integer $id	Id dell'oggetto da linkare
	 */
	function get_item_form_by_id($id =null) {
		$this->_get_item_form($this->params['form']['id']) ;
	}

	private function _get_item_form($id) {
		$conf  = Configure::getInstance() ;
		foreach ($this->params['form'] as $k =>$v) {
			$$k = $v ;
		}
		$rec = $this->BEObject->recursive ;
		$this->BEObject->recursive = -1 ;
		if(!($ret = $this->BEObject->read('object_type_id', $id))) throw new BeditaException(sprintf(__("Error get object: %d", true), $id));
		$this->BEObject->recursive = $rec ;
		$model = $conf->objectTypeModels[$ret['BEObject']['object_type_id']] ;
		$this->{$model}->bviorHideFields = array('Version', 'Index', 'current', 'images', 'multimedia', 'attachments') ;
		if(!($obj = $this->{$model}->findById($id))) {
			 throw new BeditaException(sprintf(__("Error loading object: %d", true), $id));
		}
		$imagePath 	= $this->BeFileHandler->path($id) ;
		$imageURL 	= $this->BeFileHandler->url($id) ;
		// data for template
		$this->set('object',	@$obj);
		$this->set('imagePath',	@$imagePath);
		$this->set('imageUrl',	@$imageURL);
		$this->set('priority',	@$priority);
		$this->set('index',		@$index);
		$this->set('cols',		@$cols);		
		$this->selfUrlParams = array("id", @$id);    
		$this->layout = "empty" ;
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
		      "save"  =>  array(
                    "OK"    => "/multimedia/view/{$this->BEObject->id}",
                    "ERROR" => "/multimedia/" 
              ), 
		
		      "delete"	=> 	array(
		              "OK"	=> "./",
                      "ERROR"	=> "./view/{@$this->params['pass'][0]}")
	              ) ;
		if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
		return false ;
	}

}