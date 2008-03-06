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
 * Controller entrata modulo Allegati e gestione degli oggetti File
 * 
 */
class AttachmentsController extends ModulesController {
	var $name = 'Attachments';

	var $helpers 	= array('BeTree', 'BeToolbar');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText', 'BeFileHandler');

	// This controller does not use a model
	var $uses = array('Stream', 'BEFile', 'Image', 'Audio', 'Video', 'BEObject', 'ContentBase', 'Content', 'BaseDocument', 'Tree', 'User', 'Group') ;
	protected $moduleName = 'attachments';
	
	 /**
	 * Entrata.
	 * Visualizza l'albero delle aree e l'elenco degli oggetti File
	 * 
	 */
	 function index($id = null, $order = "", $dir = true, $page = 1, $dim = 20) {
		$conf  = Configure::getInstance() ;
		
	 	// Setup parametri
		$this->setup_args(
			array("id", "integer", &$id),
			array("page", "integer", &$page),
			array("dim", "integer", &$dim),
			array("order", "string", &$order),
			array("dir", "boolean", &$dir)
		) ;
		
		// Preleva l'albero delle aree e sezioni
		$tree = $this->BeTree->expandOneBranch($id) ;
		
		$ot = &$conf->objectTypes ; 
		$multimedia = $this->BeTree->getDiscendents($id, null, array($ot['befile']), $order, $dir, $page, $dim)  ;
		$this->params['toolbar'] = &$documents['toolbar'] ;
		
		// Setup dei dati da passare al template
		$this->set('tree', 			$tree);
		$this->set('multimedia', 	$multimedia['items']);
		$this->set('toolbar', 		$multimedia['toolbar']);
	 }


	 /**
	  * Preleva l'oggetto selezionato.
	  * Se non viene passato nessun id, presente il form per un nuovo oggetto
	  *
	  * @param integer $id
	  */
	 function view($id = null) {
		$conf  = Configure::getInstance() ;
		
	 	// Setup parametri
		$this->setup_args(array("id", "integer", &$id)) ;
	 	
		// Preleva l'oggetto selezionato
		$obj = null ;
		if($id) {
			// Preleva il tipo di oggetto
			$rec = $this->BEObject->recursive ;
			$this->BEObject->recursive = -1 ;
			if(!($ret = $this->BEObject->read('object_type_id', $id))) throw new BeditaException(sprintf(__("Error get object: %d", true), $id));
			$this->BEObject->recursive = $rec ;
			$model = $conf->objectTypeModels[$ret['BEObject']['object_type_id']] ;
			
			$this->{$model}->bviorHideFields = array('Version', 'Index', 'current', 'multimedia', 'attachments') ;
			if(!($obj = $this->{$model}->findById($id))) {
				 throw new BeditaException(sprintf(__("Error loading object: %d", true), $id));
			}
			
			$imagePath 	= $this->BeFileHandler->path($id) ;
			$imageURL 	= $this->BeFileHandler->url($id) ;
		}
	
		// Formatta i campi in lingua
		if(isset($obj["LangText"])) {
			$this->BeLangText->setupForView($obj["LangText"]) ;
		}
		
		// Setup dei dati da passare al template
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
        
        // Format custom properties
        $this->BeCustomProperty->setupForSave($this->data["CustomProperties"]) ;

        // Format lang text fields
        $this->BeLangText->setupForSave($this->data["LangText"]) ;
        
        $this->Transaction->begin() ;
        
        // save
        if(!$this->BEObject->save($this->data)) {
            throw new BeditaException(__("Error saving attachment", true), 
                $this->BEObject->validationErrors);
        }

        // permissions
        $perms = isset($this->data["Permissions"])?$this->data["Permissions"]:array();
        if(!$this->Permission->saveFromPOST(
                $this->BEObject->id, $perms,
                (empty($this->data['recursiveApplyPermissions'])?false:true), 'document')
            ) {
                throw new BeditaException( __("Error saving permissions", true));
        }       

        $this->Transaction->commit() ;
        $this->userInfoMessage(__("Attachment saved", true)." - ".$this->data["title"]);
        $this->eventInfo("Attachment [". $this->data["title"]."] saved");
    }
	 
	 
	 /**
	  * Cancella un'area.
	  */
	 function delete($id = null) {
	 	$this->checkWriteModulePermission();
	 	if(!isset($this->data['id'])) throw new BeditaException(sprintf(__("No data", true), $id));
		
	 	$this->Transaction->begin() ;
		 	
	 	// Cancellla i dati
	 	if(!$this->BeFileHandler->del($this->data['id'])) throw new BeditaException(sprintf(__("Error deleting object: %d", true), $id));
		 	
	 	$this->Transaction->commit() ;
	 }

	/**
	 * Accesso via Ajax.
	 * Utilizzato per visualizzare l'item di un oggetto file in un form
	 * 
	 * @param string $filename	Il nome del file da visualizare nel form
	 */
	function get_item_form($filename = null) {
		$filename = urldecode($this->params['form']['filename']) ;
		if(!($id = $this->Stream->getIdFromFilename($filename))) throw new BeditaException(sprintf(__("Error get id object: %d", true), $id));
		$this->_get_item_form($id) ;
	}
	 
	/**
	 * Accesso via Ajax.
	 * Utilizzato per visualizzare l'item di un oggetto file in un form
	 * 
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
		// Get object type
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
	 * Presenta il form per l'upload di oggetti multimedia
	 *
	 */
	function frm_upload() {
	}
	
	/**
	 * Presenta il form per la selezione di oggetti multimedia
	 *
	 */
	function frm_upload_bedita() {
		$order = ""; $dir = true; $page = 1; $dim = 20 ;
		$conf  = Configure::getInstance() ;
		
	 	// Setup parametri
		$this->setup_args(
			array("page", "integer", &$page),
			array("dim", "integer", &$dim),
			array("order", "string", &$order),
			array("dir", "boolean", &$dir)
		) ;
		
		$ot = &$conf->objectTypes ; 
		$attachments = $this->BeTree->getDiscendents(null, null, array($ot['befile']), $order, $dir, $page, $dim)  ;
		for($i=0; $i < count($attachments['items']) ; $i++) {
			$id = $attachments['items'][$i]['id'] ;
			$ret = $this->Stream->findById($id) ;
			
			$attachments['items'][$i] = array_merge($attachments['items'][$i], $ret['Stream']) ;
			$attachments['items'][$i]['bedita_type'] = $conf->objectTypeModels[$attachments['items'][$i]['object_type_id']] ;
		}
		$this->params['toolbar'] = &$attachments['toolbar'] ;
		
		// Setup dei dati da passare al template
		$this->set('attachments', 	$attachments['items']);
		$this->set('toolbar', 		$attachments['toolbar']);
	}

	/**
	 * Presenta il form per l'aggiunta di oggetti multimedia tramite URL
	 *
	 */
	function frm_upload_url() {
	}
	 
	protected function forward($action, $esito) {
	  	$REDIRECT = array(
		      "save"  =>  array(
                    "OK"    => "/attachments/view/{$this->BEObject->id}",
                    "ERROR" => "/attachments/" 
              ), 

              "delete"	=> 	array(
	 				"OK"	=> "./",
	 				"ERROR"	=> "./view/{@$this->params['pass'][0]}" 
	 		    ) 
	 		) ;
	 	
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	
	 	return false ;
	 }
	 
}

	