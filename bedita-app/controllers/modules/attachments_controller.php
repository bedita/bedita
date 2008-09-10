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

	var $uses = array('BEObject', 'Tree', 'User', 'Group') ;
	protected $moduleName = 'attachments';
	
	 /**
	 * Entrata.
	 * Visualizza l'albero delle aree e l'elenco degli oggetti File
	 * 
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
		$typesArray = array($conf->objectTypes['befile']);
		
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
			
			$this->{$model}->bviorHideFields = array('Version', 'current', 'multimedia', 'attachments') ;
			if(!($obj = $this->{$model}->findById($id))) {
				 throw new BeditaException(sprintf(__("Error loading object: %d", true), $id));
			}
			
			$imagePath 	= $this->BeFileHandler->path($id) ;
			$imageURL 	= $this->BeFileHandler->url($id) ;
		}
	
		// Setup dei dati da passare al template
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
        
        if(empty($this->data) || empty($this->data['id'])) 
            throw new BeditaException( __("Bad data", true));
        
        // Verifica i permessi di modifica dell'oggetto
        if(!$this->Permission->verify($this->data['id'], 
            $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
                throw new BeditaException(__("Error modify permissions", true));
        
        // Format custom properties
        $this->BeCustomProperty->setupForSave($this->data["CustomProperties"]) ;

        // Format lang text fields
		$this->data['title'] = $this->data['LangText'][$this->data['lang']]['title'];
		$this->data['description'] = $this->data['LangText'][$this->data['lang']]['description'];
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

?>