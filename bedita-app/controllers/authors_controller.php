<?php
/**
 * Modulo Autori.
 *
 * PHP versions 4 
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * @filesource
 * @copyright		Copyright (c) 2006
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license			
 */
class AuthorsController extends AppController {
	var $components = array('BeAuth');
	var $helpers 	= array('Bevalidation');
	var $uses	 	= array('ViewShortAuthor','Area');
	
	/**
	 * Nome modello
	 *
	 * @var string
	 */
	var $name = 'Authors' ;

	/**
	 * Visualizza una porzione di Autori
	 *
	 * @param integer $ida		ID dell'area da selezionare. Preleva l'elenco solo di questa area
	 * @param integer $idg		ID del gruppo da selezionare. Preleva l'elenco solo di questo gruppo
	 * @param integer $page		pagina dell'elenco richiesta
	 * @param integer $dim		dimensione della pagina
	 * @param string $order		nome campo su cui ordinare la lista. Aggiungere "desc" per invertire l'ordine
	 * 
	 * @todo TUTTO
	 */
	function index($ida = null, $idg = null, $page = 1, $dim = 20, $order = null) {
		// Setup parametri
		$this->setup_args(
			array("ida", "integer", &$ida),
			array("idg", "integer", &$idg),
			array("page", "integer", &$page),
			array("dim", "integer", &$dim),
			array("order", "string", &$order)
		) ;

		// Verifica i permessi d'accesso
		if(!$this->checkLogin()) return ;
		
		// Preleva l'elenco degli eventi richiesto
		if(!$this->ViewShortAuthor->listContents($contents, $ida, $idg, $page, $dim , $order)) {
			$this->Session->setFlash("Errore nel prelievo della lista degli autori");
			return ;
		}

		// Preleva l'albero delle aree e tipologie
		$this->Area->tree($subjects, Area::SUBJECT, (integer)$ida);
				
		// Crea l'URL dello stato corrente
		$selfPlus = $this->createSelfURL(false,
			array("ida", $ida), array("idg", $idg), 	array("page", $page), 
			array("dim", $dim), array("order", $order)
		) ;

		// Setup dei dati da passare al template
		$this->set('Subjects', 		$subjects);
		$this->set('Authors', 		$contents);
		$this->set('selfPlus',		$selfPlus) ;
		$this->set('self',			($this->createSelfURL(false)."?")) ;
	}

	/**
	 * Visualizza il form per la modifica di 
	 *
	 * @param integer $id
	 * 
	 * @todo TUTTO
	 */
	function frmModify($id = null) {
		// Verifica i permessi d'accesso
		if(!$this->checkLogin()) return ;
	
		$this->Session->setFlash("DA IMPLEMENTARE");
		return ;
	}

	/**
	 * Visualizza il form per l'aggiunta di
	 * 
	 * @todo TUTTO
	 */
	function frmAdd() {
		// Verifica i permessi d'accesso
		if(!$this->checkLogin()) return ;

		$this->Session->setFlash("DA IMPLEMENTARE");
		return ;
	}

	/**
	 * Visualizza il form per l'aggiunta, modifica, cancellazione dei gruppi
	 * 
	 * @todo TUTTO
	 */
	function frmGroups() {
		// Verifica i permessi d'accesso
		if(!$this->checkLogin()) return ;

		$this->Session->setFlash("DA IMPLEMENTARE");
		return ;
	}
	
	
	////////////////////////////////////////////////////////////
	////////////////////////////////////////////////////////////
	/**
	* modifica i dati del contenuto o ne aggiunge uno nuovo. 
	* I dati sono passati via POST.
	 * 
	 * @todo TUTTO
	*/
	function edit() {
		// Verifica i permessi d'accesso
		if(!$this->checkLogin()) return ;

		if(empty($this->data)) {
			$this->Session->setFlash("Nessun dato passato");
			return ;
		}

		$this->Session->setFlash("DA IMPLEMENTARE");
		return ;

		$this->redirect($this->data["back"]["OK"]) ;
	}

	/**
	 * Cancella il contenuto passato
	 *
	 * @param integer $id
	 * 
	 * @todo TUTTO
	 */
	function delete($id = null) {
		// Verifica i permessi d'accesso
		if(!$this->checkLogin()) return ;

		$this->Session->setFlash("DA IMPLEMENTARE");
		return ;

		$this->redirect($this->data["back"]["OK"]) ;
	}

	/**
	* modifica i dati dei gruppi, cancella o ne aggiunge uno nuovo. 
	* I dati sono passati via POST.
	 * 
	 * @todo TUTTO
	*/
	function editGroups() {
		// Verifica i permessi d'accesso
		if(!$this->checkLogin()) return ;

		if(empty($this->data)) {
			$this->Session->setFlash("Nessun dato passato");
			return ;
		}

		$this->Session->setFlash("DA IMPLEMENTARE");
		return ;

		$this->redirect($this->data["back"]["OK"]) ;
	}

}

?>