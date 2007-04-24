<?php
/**
 * Modulo Bibliografie.
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
class BibliographiesController extends AppController {
	
	var $name = 'Bibliographies' ;
	var $uses	 	= array('ViewShortBibliography','Area');
	var $components = array('Utils');
	var $paginate = array("ViewShortBibliography" 	=> array("limit" => 20, 
												   		 "order" => array("ViewShortBibliography.titolo" => "asc")
												   )
					);

	
	

	/**
	 * lists bibliogrphy paginated
	 *
	 * @param integer $ida		ID dell'area da selezionare. Preleva l'elenco solo di questa area
	 * @param integer $idg		ID del gruppo da selezionare. Preleva l'elenco solo di questo gruppo
	 *
	 */
	function index($ida = null, $idg = null) {
		
		// set join
		$conditions = $this->ViewShortBibliography->setJoin($ida, $idg);
		$tmp = $this->paginate('ViewShortBibliography', $conditions);
		
		// collapse record set
		$bibliographies = $this->Utils->collapse($tmp);

		// Preleva l'albero delle aree e tipologie
		$categories = $this->Area->tree(Area::CATEGORY, (integer)$ida);
		
		// Setup dei dati da passare al template
		$this->set('Categories', 		$categories);
		$this->set('Bibliographies',	$bibliographies);
		$this->set('ida', 				$ida);
		$this->set('idg', 				$idg);
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