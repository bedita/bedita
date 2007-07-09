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

	var $helpers 	= array('Bevalidation');
	var $components = array('BeAuth', 'BeTree');

	// This controller does not use a model
	 var $uses = array('Area', 'Section') ;

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
		$this->set('Tree', 		$tree);
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
/*
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
		
		// Preleva l'albero delle aree e tipologie
		if(!$this->Area->tree($tree)) {
			$this->Session->setFlash("Errore nel prelievo dell'albero delle aree e sezioni");
			return ;
		}

		// Setup dei dati da passare al template
		$this->set('Area', 		$area);
		$this->set('selfPlus',	$this->createSelfURL(false, array("id", $id) )) ;
		$this->set('self',		($this->createSelfURL(false)."?")) ;
*/		
	 }

	 /**
	  * Preleva la sezione selezionata.
	  * Se non viene passato nessun id, presenta il form per una nuova sezione
	  *
	  * @param integer $id
	  */
	 function viewSection($id = null) {
/*	 	
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
		
		// Preleva l'albero delle aree e tipologie
		if(!$this->Area->tree($tree)) {
			$this->Session->setFlash("Errore nel prelievo dell'albero delle aree e sezioni");
			return ;
		}
		
		// Setup dei dati da passare al template
		$this->set('Tree', 		$tree);
		$this->set('Section',	$section);
		$this->set('selfPlus',	$this->createSelfURL(false, array("id", $id) )) ;
		$this->set('self',		($this->createSelfURL(false)."?")) ;
*/		
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
	 	
	 	if(empty($this->data)) {
			$this->redirect($URLERROR);
			return ;
	 	}

	 	// Salva i dati
	 	if(!$this->Area->save($this->data)) {
			$this->Session->setFlash($this->Area->validationErrors);
	 		
			$this->redirect($URLERROR);
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
	 	
	 	if(empty($id)) {
			$this->redirect($URLERROR);
			return ;
	 	}

	 	// Cancellla i dati
	 	if(!$this->Area->delete($id)) {
			$this->Session->setFlash($this->Area->validationErrors);
	 		
			$this->redirect($URLERROR);
			return ;
	 	}
	 	
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

}

	