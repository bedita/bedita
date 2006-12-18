<?php
/**
 * Modulo Gallerie.
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
class GalleriesController extends AppController {
	var $components = array('BeAuth');
	var $helpers 	= array('Bevalidation');
	var $uses	 	= array('Area');

	/**
	 * Nome modello
	 *
	 * @var string
	 */
	var $name = 'Galleries' ;

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
	 * @param integer $idg		ID del gruppo da selezionare. Preleva l'elenco solo di questo gruppo
	 * 
	 * @todo TUTTO
	 */
	function index($page = 1, $dim = 10, $order = null, $ida = null, $idg = null) {
		// Verifica i permessi d'accesso
		if(!$this->checkLogin()) return ;
		
		$this->Session->setFlash("DA IMPLEMENTARE");
		return ;
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