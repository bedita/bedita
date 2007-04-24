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
	
	var $name = 'Authors' ;
	var $uses	 	= array('ViewShortAuthor','Area','ViewSubject');
	var $components = array('Utils');
	
	var $paginate = array("ViewShortAuthor" 	=> array("limit" => 20, 
												   		 "order" => array("ViewShortAuthor.cognome" => "asc")
												   )
					);
	

	/**
	 * List authors paginated
	 *
	 * @param integer $ida		ID dell'area da selezionare. Preleva l'elenco solo di questa area
	 * @param integer $idg		ID del gruppo da selezionare. Preleva l'elenco solo di questo gruppo
	 */
	function index($ida = null, $idg = null) {
		
		// set join
		$conditions = $this->ViewShortAuthor->setJoin($ida, $idg);
		$tmp = $this->paginate('ViewShortAuthor', $conditions);

		// collapse record set
		$authors = $this->Utils->collapse($tmp);

		// get areas tree
		$subjects = $this->Area->tree(Area::SUBJECT, (integer)$ida); 

		// set tpl vars
		$this->set('Subjects', 		$subjects);
		$this->set('Authors', 		$authors);
		$this->set('ida', 			$ida);
		$this->set('idg', 			$idg);
		
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