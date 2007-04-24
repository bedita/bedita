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
	
	var $name = 'News' ;
	var $uses	 	= array('ViewShortNews', 'Area');
	var $components = array('Utils');
	var $paginate = array("ViewShortNews" 	=> array("limit" => 20, 
												   		 "order" => array("ViewShortNews.inizio" => "asc")
												   )
					);
	

	/**
	 * lists news paginated
	 *
	 * @param integer $ida		ID dell'area da selezionare. Preleva l'elenco solo di questa area
	 * 
	 */
	function index($ida = null) {

		// set join
		$conditions = $this->ViewShortNews->setJoin($ida);
		$tmp = $this->paginate('ViewShortNews', $conditions);
		
		// collapse record set
		$news = $this->Utils->collapse($tmp);
		
		// get areas tree
		$areas = $this->Area->tree(0x0, 0x0);

		// Setup dei dati da passare al template
		$this->set('Areas', 		$areas);
		$this->set('News',			$news);
		$this->set('ida', 			$ida);
		$this->set('hideGroups', 	true);
	}

	/**
	 * Visualizza il form per la modifica di 
	 *
	 * @param integer $id
	 * 
	 * @todo TUTTO
	 */
	function frmModify($id = null) {
		$this->Session->setFlash("DA IMPLEMENTARE");
		return ;
	}

	/**
	 * Visualizza il form per l'aggiunta di
	 * 
	 * @todo TUTTO
	 */
	function frmAdd() {
		$this->Session->setFlash("DA IMPLEMENTARE");
		return ;
	}

	/**
	 * Visualizza il form per l'aggiunta, modifica, cancellazione dei gruppi
	 * 
	 * @todo TUTTO
	 */
	function frmGroups() {

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