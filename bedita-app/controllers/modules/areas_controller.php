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
 * Controller entrata modulo Aree, gestione aree e gestione sessioni
 * 
 */
class AreasController extends AppController {
	var $name = 'Areas';

	var $helpers 	= array('BeTree');
	var $components = array('BeTree', 'Permission', 'BeCustomProperty', 'BeLangText');

	 var $uses = array('Area', 'Section', 'Tree') ;
	 protected $moduleName = 'areas';
	 
	/**
	 * Entrata.
	 * Visualizza l'albero delle aree e la possibilita' di 
	 * gestire l'ordine delle sezioni connesse.
	 * 
	 */
	 function index() { 	
		// Preleva l'albero delle aree e sezioni
		$tree = $this->BeTree->getSectionsTree() ;
		
		// Setup dei dati da passare al template
		$this->set('tree', 		$tree);
		$this->set('selfPlus',	$this->createSelfURL(false)) ;
		$this->set('self',		($this->createSelfURL(false)."?")) ;
	 }

	 /**
	  * Preleva l'area selezionata.
	  * Se non viene passato nessun id, presente il form per una nuova area
	  *
	  * @param integer $id
	  */
	 function viewArea($id = null) {
	 	
		$conf  = Configure::getInstance() ;
		
	 	// Setup parametri
		$this->setup_args(array("id", "integer", &$id)) ;
	 	
		// Preleva l'area selezionata
		$area = null ;
		if($id) {
			$this->Area->bviorHideFields = array('ObjectType', 'Version', 'Index', 'current') ;
			if(!($area = $this->Area->findById($id))) {
				 throw new BeditaException(sprintf(__("Error loading area: %d", true), $id));
			}
		}
		
		// Formatta i campi in lingua
		if(isset($area["LangText"])) {
			$this->BeLangText->setupForView($area["LangText"]) ;
		}
		
		// Setup dei dati da passare al template
		$this->set('area', 		$area);
		$this->set('selfPlus',	$this->createSelfURL(false, array("id", $id) )) ;
		$this->set('self',		($this->createSelfURL(false)."?")) ;
		$this->set('conf',		$conf) ;
	 }

	 /**
	  * Preleva la sezione selezionata.
	  * Se non viene passato nessun id, presenta il form per una nuova sezione
	  *
	  * @param integer $id
	  */
	 function viewSection($id = null) {	 	
		// Setup parametri
		$this->setup_args(array("id", "integer", &$id)) ;
	 	
		// Preleva la sezione selezionata
		$section = null ;
		if($id) {
			$this->Section->bviorHideFields = array('ObjectType', 'Version', 'Index', 'current') ;
			if(!($section = $this->Section->findById($id))) {
				throw new BeditaException(sprintf(__("Error loading section: %d", true), $id));
			}
		}
		
		// Formatta i campi in lingua
		if(isset($section["LangText"])) {
			$this->BeLangText->setupForView($section["LangText"]) ;
		}
		
		// Preleva l'albero delle aree e sezioni
		$tree = $this->BeTree->getSectionsTree() ;

		// Preleva dov'e' inserita la sezione 
		if(isset($id)) {
			$parent_id = $this->Tree->getParent($id) ;
		} else {
			$parent_id = 0 ;
		}	


		// Setup dei dati da passare al template
		$this->set('tree', 		$tree);
		$this->set('section',	$section);
		$this->set('parent_id',	$parent_id);
		$this->set('selfPlus',	$this->createSelfURL(false, array("id", $id) )) ;
		$this->set('self',		($this->createSelfURL(false)."?")) ;	
	 }
	
	 /**
	  * Salva La nuova configurazione dell'albero dei contenuti
	  *
	  */
	 function saveTree() {
	 	$this->checkWriteModulePermission();
	 	
		$this->Transaction->begin() ;
	 		
		if(@empty($this->data["tree"])) throw new BeditaException(__("No data", true));

		
	 	// Preleva l'albero
	 	$this->_getTreeFromPOST($this->data["tree"], $tree) ;

	 	// Salva i cambiamenti
	 	if(!$this->Tree->moveAll($tree)) throw new BeditaException( __("Error save tree from _POST", true));

		$this->Transaction->commit() ;
	 	$this->userInfoMessage(__("Area tree saved", true));
	 	$this->eventInfo("area tree saved");
	 }
	 
	 /**
	  * Aggiunge una nuova area o la modifica.
	  * Nei dati devono essere definiti:
	  * URLOK e URLERROR.
	  *
	  */
	 function saveArea() {

	 		$this->checkWriteModulePermission();
	 		
		 	if(empty($this->data)) throw BeditaException( __("No data", true));
	 		
			$new = (empty($this->data['id'])) ? true : false ;
			
		 	// Verifica i permessi di modifica dell'oggetto
		 	if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
		 			throw new BeditaException(__("Error modify permissions", true));
		 	
		 	// Formatta le custom properties
		 	$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]) ;
	
		 	// Formatta i campi d tradurre
		 	$this->BeLangText->setupForSave($this->data["LangText"]) ;
		 	
			$this->Transaction->begin() ;
			
	 		// Salva i dati
		 	if(!$this->Area->save($this->data))
				throw new BeditaException( __("Error saving area", true),  $this->Area->validationErrors);

		 	// aggiorna i permessi
			$perms = isset($this->data["Permissions"])?$this->data["Permissions"]:array();
			if(!$this->Permission->saveFromPOST($this->Area->id, $perms,
		 				(empty($this->data['recursiveApplyPermissions'])?false:true), 'area'))  {
		 				throw new BeditaException( __("Error saving permissions", true));
		 	}
	 		$this->Transaction->commit() ;
	 		$this->userInfoMessage(__("Area saved", true)." - ".$this->data["title"]);
	 		$this->eventInfo("area ". $this->data["title"]."saved");
	 }

	 /**
	  * Aggiunge una nuova sezione o la modifica.
	  * Nei dati devono essere definiti:
	  * URLOK e URLERROR.
	  *
	  */
	 function saveSection() {
	 	
	 		$this->checkWriteModulePermission();
	 		
		 	if(empty($this->data)) throw new BeditaException(__("No data", true));
	 		
			$new = (empty($this->data['id'])) ? true : false ;
			
		 	// Verifica i permessi di modifica dell'oggetto
		 	if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
		 			throw new BeditaException( __("Error modifying permissions", true));
		 	
		 	// Formatta le custom properties
		 	$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]) ;
	
		 	// Formatta i campi da tradurre
		 	$this->BeLangText->setupForSave($this->data["LangText"]) ;
		 	
			$this->Transaction->begin() ;
			
	 		// data["destination"] should be 1 element
	 		if(count($this->data["destination"]) != 1)
				throw new BeditaException( __("Bad data", true));
			
			$destinationId = $this->data["destination"][0];
	 		if($new) 
	 			$this->data["parent_id"] = $destinationId;
		 	if(!$this->Section->save($this->data))
				throw new BeditaException( __("Error saving section", true), $this->Section->validationErrors );
				
		 	// Sposta la sezione nell'albero se necessario
		 	if(!$new) {
		 		$oldParent = $this->Tree->getParent($this->Section->id) ;
		 		if($oldParent != $destinationId) {
		 			if(!$this->Tree->move($destinationId, $oldParent, $this->Section->id))
						throw new BeditaException( __("Error saving section", true));
		 		}
		 	}
		 	
		 	// aggiorna i permessi
			$perms = isset($this->data["Permissions"]) ? $this->data["Permissions"] : array();
		 	if(!$this->Permission->saveFromPOST($this->Section->id, $perms,	 
				(empty($this->data['recursiveApplyPermissions'])?false:true), 'section')) {
		 			throw new BeditaException( __("Error saving permissions", true));
		 	}
	 		$this->Transaction->commit() ;
	 		$this->userInfoMessage(__("Section saved", true)." - ".$this->data["title"]);
	 		$this->eventInfo("section [". $this->data["title"]."] saved");
	 }

	 /**
	  * Cancella un'area.
	  */
	 function deleteArea() {
	 	
	 	$this->checkWriteModulePermission();
		
		if(empty($this->data['id'])) {
			throw new BeditaException(__("No data", true));
		}
		if(!$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_DELETE)) {
			throw new BeditaException(__("Error delete permissions", true));
		}
	 		
	 	$this->Transaction->begin() ;
	 	
		// delete data
	 	if(!$this->Area->delete($this->data['id'])) {
	 		throw new BeditaException( sprintf(__("Error deleting area: %d", true), $this->data['id']));
	 	}
	 	$this->Transaction->commit() ;
	 	$this->userInfoMessage(__("Area deleted", true)." - ".$this->data['id']);
	 	$this->eventInfo("area [". $this->data['id']."] deleted");
	 }

	 /**
	  * Cancella una sezione.
	  */
	 function deleteSection() {
	 	
	 	$this->checkWriteModulePermission();
		
		if(empty($this->data['id'])) {
			throw new BeditaException(__("No data", true));
		}
		if(!$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_DELETE)) {
			throw new BeditaException(__("Error delete permissions", true));
		}
	 		
	 	$this->Transaction->begin() ;
	 	
		// delete section
	 	if(!$this->Section->delete($this->data['id'])) {
	 		throw new BeditaException( sprintf(__("Error deleting section: %d", true), $this->data['id']));
	 	}
	 	$this->Transaction->commit() ;
	 	$this->userInfoMessage(__("Section deleted", true)." - ".$this->data['id']);
	 	$this->eventInfo("section [". $this->data['id']."] deleted");
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
	 			"saveTree"	=> 	array(
	 									"OK"	=> "./",
	 									"ERROR"	=> "./" 
	 								), 
	 			"saveArea"	=> 	array(
	 									"OK"	=> "./viewArea/{$this->Area->id}",
	 									"ERROR"	=> "./viewArea/{$this->Area->id}" 
	 								), 
	 			"saveSection"	=> 	array(
	 									"OK"	=> "./viewSection/{$this->Section->id}",
	 									"ERROR"	=> "./viewSection/{$this->Section->id}" 
	 								), 
	 			"deleteArea"	=> 	array(
	 									"OK"	=> "./",
	 									"ERROR"	=> "./viewArea/{@$this->params['pass'][0]}" 
	 								), 
	 			"deleteSection"	=> 	array(
	 									"OK"	=> "./",
	 									"ERROR"	=> "./viewSection/{@$this->params['pass'][0]}" 
	 								), 
	 		) ;
	 	
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	
	 	return false ;
	 }
	 
}

	