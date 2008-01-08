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
 * Controller entrata modulo Documento e gestione documenti
 * 
 */
class DocumentsController extends AppController {
	var $name = 'Documents';

	var $helpers 	= array('BeTree', 'BeToolbar', 'Fck');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText', 'BeFileHandler');

	// This controller does not use a model
	var $uses = array(
		'Stream', 'Area', 'Section',  'BEObject', 'ContentBase', 'Content', 'BaseDocument', 'Document', 'Tree',
		'Image', 'Video', 'Audio', 'BEFile'
		) ;
	protected $moduleName = 'documents';
	
	 /**
	 * Entrata.
	 * Visualizza l'albero delle aree e l'elenco dei documenti
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
		
		$documents = $this->BeTree->getDiscendents($id, null, $conf->objectTypes['documentAll'], $order, $dir, $page, $dim)  ;
		$this->params['toolbar'] = &$documents['toolbar'] ;
		
		// Setup dei dati da passare al template
		$this->set('tree', 		$tree);
		$this->set('documents', $documents['items']);
		$this->set('toolbar', 	$documents['toolbar']);
		$this->set('selfPlus',	$this->createSelfURL(false)) ;
		$this->set('self',		($this->createSelfURL(false)."?")) ;
	 }

	 /**
	  * Preleva il documento selezionato.
	  * Se non viene passato nessun id, presente il form per un nuovo documento
	  *
	  * @param integer $id
	  */
	 function view($id = null) {
	 	
		$conf  = Configure::getInstance() ;
		
	 	// Setup parametri
		$this->setup_args(array("id", "integer", &$id)) ;
	 	
		// Preleva l'area selezionata
		$obj = null ;
		if($id) {
			$this->Document->bviorHideFields = array('Version', 'Index', 'current') ;

			if(!($obj = $this->Document->findById($id))) {
				 throw new BeditaException(sprintf(__("Error loading document: %d", true), $id));
			}
					
			// Se presenti, preleva gli oggetti multimediali del documento
			for($i=0; $i < @count($obj['multimedia']) ; $i++) {
				$m = $this->Document->am($obj['multimedia'][$i]) ;
				
				$type = $conf->objectTypeModels[$m['object_type_id']] ;
				
				$this->{$type}->bviorHideFields = array('UserCreated','UserModified','Permissions','Version','CustomProperties','Index','langObjs', 'images', 'multimedia', 'attachments', 'LangText');
				if(!($Details = $this->{$type}->findById($obj['multimedia'][$i]['id']))) {
					continue ;
				}
				$Details['priority'] = $m['priority'];
				$Details['filename'] = substr($Details['path'],strripos($Details['path'],"/")+1);
			
				$obj['multimedia'][$i]= $Details;
			}

			// Se presenti, preleva gli allegati al documento
			for($i=0; $i < @count($obj['attachments']) ; $i++) {
				$m = $this->Document->am($obj['attachments'][$i]) ;
				 
				$type = $conf->objectTypeModels[$m['object_type_id']] ;
				
				$this->{$type}->bviorHideFields = array('UserCreated','UserModified','Permissions','Version','CustomProperties','Index','langObjs', 'images', 'multimedia', 'attachments', 'LangText');
				if(!($Details = $this->{$type}->findById($obj['attachments'][$i]['id']))) {
					continue ;
				}
				$Details['priority'] = $m['priority'];
				$Details['filename'] = substr($Details['path'],strripos($Details['path'],"/")+1);
			
				$obj['attachments'][$i]= $Details;
			}
		}
		
		// Formatta i campi in lingua
		if(isset($obj["LangText"])) {
			$this->BeLangText->setupForView($obj["LangText"]) ;
		}
		
		// Preleva l'albero delle aree e sezioni
		$tree = $this->BeTree->getSectionsTree() ;

		// Preleva dov'e' inserito il documento 
		if(isset($id)) {
			$parents_id = $this->Tree->getParent($id) ;
			if($parents_id === false) array() ;
			elseif(!is_array($parents_id))
				$parents_id = array($parents_id);
		} else {
			$parents_id = array();
		}
		
		// Setup dei dati da passare al template
		$this->set('object',	$obj);
		$this->set('tree', 		$tree);
		$this->set('parents',	$parents_id);
		$this->set('selfPlus',	$this->createSelfURL(false, array("id", $id) )) ;
		$this->set('self',		($this->createSelfURL(false)."?")) ;
		$this->set('conf',		$conf) ;
		$this->set('CACHE',		'imgcache/');
		$this->set('MEDIA_URL',	MEDIA_URL);
		$this->set('MEDIA_ROOT',MEDIA_ROOT);
		
	 }

	 /**
	  * Aggiunge un nuovo documento o la modifica.
	  * Nei dati devono essere definiti:
	  * URLOK e URLERROR.
	  *
	  */
	 function save() {
/*
pr(serialize($this->data)) ;
exit;
*/
//$this->data = unserialize('a:16:{s:2:"id";s:1:"5";s:6:"status";s:2:"on";s:5:"start";s:10:"22-05-2007";s:3:"end";s:10:"22-05-2008";s:4:"lang";s:2:"it";s:5:"title";s:23:"Primo Documento di Test";s:8:"nickname";s:25:"PrimoDocumentoDiTest_7583";s:8:"subtitle";s:11:"sottotitolo";s:9:"shortDesc";s:0:"";s:11:"destination";a:1:{i:0;s:1:"3";}s:8:"longDesc";s:0:"";s:7:"formato";s:3:"txt";s:10:"multimedia";a:1:{i:0;a:2:{s:2:"id";s:5:"10003";s:8:"priority";s:1:"1";}}s:10:"gallery_id";s:1:"0";s:16:"CustomProperties";a:2:{s:4:"test";a:3:{s:4:"name";s:0:"";s:4:"type";s:7:"integer";s:5:"value";s:2:"10";}s:8:"testBool";a:3:{s:4:"name";s:0:"";s:4:"type";s:4:"bool";s:5:"value";s:1:"1";}}s:11:"Permissions";a:1:{i:0;a:5:{s:4:"name";s:13:"administrator";s:17:"BEDITA_PERMS_READ";s:1:"4";s:19:"BEDITA_PERMS_MODIFY";s:1:"2";s:19:"BEDITA_PERMS_DELETE";s:1:"4";s:6:"switch";s:5:"group";}}}') ;
/*
pr($this->data);
exit;
*/
	 	
	 		$this->checkWriteModulePermission();

	 	 	if(empty($this->data)) throw new BeditaException( __("No data", true));
	 		
			$new = (empty($this->data['id'])) ? true : false ;
			
		 	// Verifica i permessi di modifica dell'oggetto
		 	if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
		 			throw new BeditaException(__("Error modify permissions", true));
		 	
		 	// Formatta le custom properties
		 	$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]) ;
	
		 	// Formatta i campi d tradurre
		 	$this->BeLangText->setupForSave($this->data["LangText"]) ;
		 	
		 	if(!isset($this->data["attachments"])) $this->data["attachments"] = array() ;
		 	if(!isset($this->data["multimedia"])) $this->data["multimedia"] = array() ;
		 	
			$this->Transaction->begin() ;
	 		
			// Salva i dati
		 	if(!$this->Document->save($this->data)) {
		 		throw new BeditaException(__("Error saving document", true), $this->Document->validationErrors);
		 	}

			/**
 			* inserimento nell'albero
 			*/
			if(($parents = $this->Tree->getParent($this->Document->id)) !== false) {
				if(!is_array($parents)) $parents = array($parents) ;
			} else {
				$parents = array() ;
			}
			if(!isset($this->data['destination'])) $this->data['destination'] = array() ;

			// rimuove
			$remove = array_diff($parents, $this->data['destination']) ;
			foreach ($remove as $parent_id) {
				$this->Tree->removeChild($this->Document->id, $parent_id) ;
			}
			
			// Inserisce
			$add = array_diff($this->data['destination'], $parents) ;
			foreach ($add as $parent_id) {
				$this->Tree->appendChild($this->Document->id, $parent_id) ;
			}
			
		 	// aggiorna i permessi
			$perms = isset($this->data["Permissions"])?$this->data["Permissions"]:array();
			if(!$this->Permission->saveFromPOST(
		 			$this->Document->id, $perms,
		 			(empty($this->data['recursiveApplyPermissions'])?false:true), 'document')
		 		) {
		 			throw new BeditaException( __("Error saving permissions", true));
		 	}	 	
	 		$this->Transaction->commit() ;
	 }
	 	 
	 /**
	  * Delete a document.
	  */
	 function delete($id = null) {
	 	
	 	$this->checkWriteModulePermission();
	 	
		$this->setup_args(array("id", "integer", &$id)) ;
		
	 	if(empty($id)) 
	 		throw new BeditaException(__("No data", true));
	 		
	 	$this->Transaction->begin() ;
	 	
	 	// Cancellla i dati
	 	if(!$this->Document->delete($id)) 
	 		throw new BeditaException(sprintf(__("Error deleting document: %d", true), $id));
		 	
	 	$this->Transaction->commit() ;
	 }

	 /**
	  * Torna un'array associativo che rappresneta l'albero aree/sezioni
	  * a partire dai dati passati via POST.
	  *
	  * @param unknown_type $data
	  * @param unknown_type $tree
	  */
	 private function _getTreeFromPOST(&$data, &$tree) {
	 	$tree = array() ;
	 	$IDs  = array() ;
	 	
	 	// Crea i diversi rami
	 	$arr = preg_split("/;/", $data) ;
	 	for($i = 0 ; $i < count($arr) ; $i++) {
	 		$item = array() ;
	 		$tmp = split(" ", $arr[$i] ) ;
	 		foreach($tmp as $val) {
	 			$t  = split("=", $val) ;
	 			$item[$t[0]] = ($t[1] == "null") ? null : ((integer)$t[1]) ; 
	 		}
	 		
	 		$IDs[$item["id"]] 				= $item ;
	 		$IDs[$item["id"]]["children"] 	= array() ;
	 	}

		// Crea l'albero
		foreach ($IDs as $id => $item) {
			if(!isset($item["parent"])) {
				$tree[] = $item ;
				$IDs[$id] = &$tree[count($tree)-1] ;
			}
			
			if(isset($IDs[$item["parent"]])) {
				$IDs[$item["parent"]]["children"][] = $item ;
				$IDs[$id] = &$IDs[$item["parent"]]["children"][count($IDs[$item["parent"]]["children"])-1] ;
			}
		}
		
		unset($IDs) ;
	 }

	 protected function forward($action, $esito) {
	  	$REDIRECT = array(
	 			"save"	=> 	array(
	 									"OK"	=> "./view/{$this->Document->id}",
	 									"ERROR"	=> "./view/{$this->Document->id}" 
	 								), 
	 			"delete"	=> 	array(
	 									"OK"	=> "./",
	 									"ERROR"	=> "./view/{@$this->params['pass'][0]}" 
	 								), 
	 		) ;
	 	
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	
	 	return false ;
	 }

	 
	 function preRenderFilter($view, $layout) {
	 	$i=0;
	 }
	 
}

	