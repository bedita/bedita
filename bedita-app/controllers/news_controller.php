<?php
/**
 * Modulo News.
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
class NewsController extends AppController {
	var $components = array('BeAuth');
	var $helpers 	= array('Bevalidation');
	var $uses	 	= array('ViewShortNews', 'Area');

	/**
	 * Nome modello
	 *
	 * @var string
	 */
	var $name = 'News' ;

	/**
	 * Definisce l'utilizzo di Smarty
	 *
	 */
	function __construct() {
		parent::__construct() ;
		$this->view 	= 'Smarty';
	}

	/**
	 * Visualizza una porzione .....
	 *
	 * @param integer $page		pagina dell'elenco richiesta
	 * @param integer $dim		dimensione della pagina
	 * @param string $order		nome campo su cui ordinare la lista. Aggiungere "desc" per invertire l'ordine
	 * @param integer $ida		ID dell'area da selezionare. Preleva l'elenco solo di questa area
	 * 
	 * @todo TUTTO
	 */
	function index($ida = null, $page = 1, $dim = 20, $order = null) {
		// Setup parametri
		$this->setup_args(
			array("ida", "integer", &$ida),
			array("page", "integer", &$page),
			array("dim", "integer", &$dim),
			array("order", "string", &$order)
		) ;

		// Verifica i permessi d'accesso
		if(!$this->checkLogin()) return ;
		
		// Preleva l'elenco dei documenti richiesto
		if(!$this->ViewShortNews->listContents($contents, $ida, null, $page, $dim , $order)) {
			$this->Session->setFlash("Errore nel prelievo della lista delle news");
			return ;
		}
		
		// Preleva l'albero delle aree e tipologie
		$this->Area->tree($areas, 0x0, 0x0);

		// Crea l'URL dello stato corrente
		$selfPlus = $this->createSelfURL(false,
			array("ida", $ida), array("page", $page), 
			array("dim", $dim), array("order", $order)
		) ;

		// Setup dei dati da passare al template
		$this->set('Areas', 		$areas);
		$this->set('News',			$contents);
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