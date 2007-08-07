<?php
/* SVN FILE: $Id: pages_controller.php 2951 2006-05-25 22:12:33Z phpnut $ */

/**
 *
 * PHP version 5
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
 * Controller entrata modulo Aree e gestione aree
 * 
 */
class AreasController extends AppController {
	var $name = 'Areas';

	var $helpers 	= array('Bevalidation', 'BeTree');
	var $components = array('BeAuth', 'BeTree', 'Transaction', 'Permission', 'BeCustomProperty', 'BeLangText');

	// This controller does not use a model
	 var $uses = array('Area', 'Section', 'Tree') ;

	/**
	 * Entrata.
	 * Visualizza l'albero delle aree e la possibilitˆ di 
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
		$this->setup_args(array("id", "integer", $id)) ;
	 	
		// Preleva l'area selezionata
		$area = null ;
		if($id) {
			$this->Area->bviorHideFields = array('ObjectType', 'Version', 'Index', 'current') ;
			if(!($area = $this->Area->findById($id))) {
				$this->Session->setFlash("Errore nel prelievo dell'area: {$id}");
				return ;		
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
		$this->setup_args(array("id", "integer", $id)) ;
	 	
		// Preleva la sezione selezionata
		$section = null ;
		if($id) {
			$this->Section->bviorHideFields = array('ObjectType', 'Version', 'Index', 'current') ;
			if(!($section = $this->Section->findById($id))) {
				$this->Session->setFlash("Errore nel prelievo dell'area: {$id}");
				return ;		
			}
		}
		
		// Preleva l'albero delle aree e sezioni
		$tree = $this->BeTree->getSectionsTree() ;
		
		// Setup dei dati da passare al template
		$this->set('tree', 		$tree);
		$this->set('section',	$section);
		$this->set('selfPlus',	$this->createSelfURL(false, array("id", $id) )) ;
		$this->set('self',		($this->createSelfURL(false)."?")) ;	
	 }
	
	 /**
	  * Salva La nuova configurazione dell'albero dei contenuti
	  *
	  */
	 function saveTree() {
	 	// URL di ritorno
	 	$URLOK 		= (isset($this->data['URLOK'])) ? $this->data['URLOK'] : "./" ;
	 	$URLERROR 	= (isset($this->data['URLERROR'])) ? $this->data['URLERROR'] : "./" ;
	 	
	 	try {
			$this->Transaction->begin() ;
	 		
		 	if(@empty($this->data["tree"])) throw new Exception("No data");
		
		 	// Preleva l'albero
		 	$this->_getTreeFromPOST($this->data["tree"], $tree) ;

		 	// Salva i cambiamenti
		 	if(!$this->Tree->moveAll($tree)) throw new Exception("Error save tree from _POST");

			$this->Transaction->commit() ;
			
	 	} catch (Exception $e) {
			$this->Session->setFlash($e->getMessage());
			$this->Transaction->rollback() ;
				
			$this->redirect($URLERROR);
			
			return ;
	 	}
	 	
	 	$this->Transaction->commit() ;
		$this->redirect($URLOK);
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
	 
	 /**
	  * Aggiunge una nuova area o la modifica.
	  * Nei dati devono essere definiti:
	  * URLOK e URLERROR.
	  *
	  */
	 function saveArea() {	 	
	 	// URL di ritorno
	 	$URLOK 		= (isset($this->data['URLOK'])) ? $this->data['URLOK'] : "./" ;
	 	$URLERROR 	= (isset($this->data['URLERROR'])) ? $this->data['URLERROR'] : "./" ;
	 	
	 	try {
		 	if(empty($this->data)) throw new Exception("No data");
	 		
			$new = (empty($this->data['id'])) ? true : false ;
			
		 	// Verifica i permessi di modifica dell'oggetto
		 	if(!$new && !$this->Permission->verify($this->data['id'], $this->BeAuth->user['userid'], BEDITA_PERMS_MODIFY)) 
		 			throw new Exception("Error modify permissions");
		 	
		 	// Formatta le custom properties
		 	$this->BeCustomProperty->setupForSave($this->data["CustomProperties"]) ;
	
		 	// Formatta i campi d tradurre
		 	$this->BeLangText->setupForSave($this->data["LangText"]) ;
		 	
		 	
			$this->Transaction->begin() ;
			
	 		// Salva i dati
		 	if(!$this->Area->save($this->data)) throw new Exception($this->Area->validationErrors);
			
		 	// Inserisce nell'albero
		 	if($new) {
		 		if(!$this->Tree->appendChild($this->Area->id, null)) throw new Exception("Append Area in to tree");
		 	}
		 	
		 	// aggiorna i permessi
		 	if(!$this->Permission->saveFromPOST(
		 			$this->Area->id, 
		 			(isset($this->data["Permissions"]))?$this->data["Permissions"]:array(),
		 			(empty($this->data['recursiveApplyPermissions'])?false:true))
		 		) {
		 			throw new Exception("Error save permissions");
		 	}	 	
	 		$this->Transaction->commit() ;

	 	} catch (Exception $e) {
			$this->Session->setFlash($e->getMessage());
			$this->Transaction->rollback() ;
				
			$this->redirect($URLERROR);
			
			return ;
	 	}
	 	
		$this->redirect($URLOK);
	 }
	 
	 /**
	  * Aggiunge una nuova sezione o la modifica.
	  * Nei dati devono essere definiti:
	  * URLOK e URLERROR.
	  *
	  */
	 function saveSection() {
	 	// URL di ritorno
	 	$URLOK 		= (isset($this->data['URLOK'])) ? $this->data['URLOK'] : "./" ;
	 	$URLERROR 	= (isset($this->data['URLERROR'])) ? $this->data['URLERROR'] : "./" ;
	 	
	 	if(empty($this->data)) {
			$this->redirect($URLERROR);
			return ;
	 	}

	 	// Salva i dati
	 	if(!$this->Section->save($this->data)) {
			$this->Session->setFlash($this->Section->validationErrors);
	 		
			$this->redirect($URLERROR);
	 	}
	 	
	 	$this->redirect($URLOK);
	 }
	 
	 /**
	  * Cancella un'area.
	  */
	 function deleteArea($id = null) {
		$this->setup_args(array("id", "integer", $id)) ;
		
		// URL di ritorno
	 	$URLOK 		= (isset($this->data['URLOK'])) ? $this->data['URLOK'] : "./" ;
	 	$URLERROR 	= (isset($this->data['URLERROR'])) ? $this->data['URLERROR'] : "./" ;
	 	
	 	try {
		 	if(empty($id)) throw new Exception("No data");
	 		
		 	// Cancellla i dati
		 	if(!$this->Area->delete($id)) throw new Exception("Error delete Area: {$id}");
		 	
	 	} catch (Exception $e) {
			$this->Session->setFlash($e->getMessage());
			$this->Transaction->rollback() ;
				
			$this->redirect($URLERROR);
			
			return ;
	 	}
	 	
	 	$this->Transaction->commit() ;
		$this->redirect($URLOK);
	 }

	 /**
	  * Cancella una sezione.
	  */
	 function deleteSection($id = null) {
		$this->setup_args(array("id", "integer", $id)) ;
	 	
	 	// URL di ritorno
	 	$URLOK 		= (isset($this->data['URLOK'])) ? $this->data['URLOK'] : "./" ;
	 	$URLERROR 	= (isset($this->data['URLERROR'])) ? $this->data['URLERROR'] : "./" ;
	 	
	 	if(empty($id)) {
			$this->redirect($URLERROR);
			return ;
	 	}

	 	// Cancellla i dati
	 	if(!$this->Section->delete($id)) {
			$this->Session->setFlash($this->Area->validationErrors);
	 		
			$this->redirect($URLERROR);
			return ;
	 	}
	 	
	 	$this->redirect($URLOK);
	 }

	 /**
	  * 
	  */
	 function elimina() {
	 	$result = "test1|test1\ntest2|test2\ntest3|test3" ;
	 	
//	 	header('Content-Type: text/plain');
	 	
	 	echo $result ;
	 	exit ;
	 }
}

	