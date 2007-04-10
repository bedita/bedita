<?php
/**
 * Modulo Test.
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
class TestsController extends AppController {
	var $helpers 	= array('Bevalidation');
	var $uses	 	= array('ViewShortEvent', 'Area');
	
	/**
	 * Nome modello
	 *
	 * @var string
	 */
	var $name = 'Events' ;

	var $paginate = array("ViewShortEvent" 	=> 	
							array(
								"limit" => 20, 
								"order" => array("ViewShortEvent.ID" => "asc")
							),
						); 
						
	/**
	 * Visualizza una porzione di Eventi
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
		
		$results = $this->paginate("ViewShortEvent");
		 
		// Preleva l'elenco degli eventi richiesto
		if(!$this->ViewShortEvent->listContents($contents, $ida, $idg, $page, $dim , $order)) {
			$this->Session->setFlash("Errore nel prelievo della lista degli eventi");
			return ;
		}
		
		// Preleva l'albero delle aree e tipologie
		$this->Area->tree($tipologies, Area::TIPOLOGY, (integer)$ida);
		
		// Crea l'URL delo stato corrente
		$selfPlus = $this->createSelfURL(false,
			array("ida", $ida), array("idg", $idg), 	array("page", $page), 
			array("dim", $dim), array("order", $order)
		) ;

		// Setup dei dati da passare al template
		$this->set('Tipologies', 	$tipologies);
		$this->set('Events', 		$contents);
		$this->set('selfPlus',		$selfPlus) ;
		$this->set('self',			($this->createSelfURL(false)."?")) ;
	}

	
	
}

?>