<?php
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
 * @author 			giangi@qwerg.com
 */

/**
 * Short description for class.
 *
 * 
 */
class GroupsController extends AppController {
	var $name = 'Groups';

	var $helpers = array();
	var $components = array('Session');

	// This controller does not use a model
	 var $uses = array("Group");
	
	/**
	 * Torna l'elenco semplice dei gruppi filtrando sulla parte iniziale della stringa.
	 * Utilizzata dagli autocomplete dei nomi gruppi.
	 * Ad eccezione del gruppo "administrator"
	 *
  	 * @param integer $q		parte iniziale stringa gruppo da tornare
  	 * @param integer $d		dimensione lista da tornare
	 * 
	 */
	function names($q = "", $d = 50) {
	 	// Setup parametri
		$this->setup_args(
			array("q", "string", &$q), 
			array("d", "integer", &$d)
		) ;
		$q = trim($q) ;
		
		// Preleva i dati
		$names = $this->Group->findAll("name LIKE '{$q}%' AND name <> 'administrator'",array("name"), "name", $d, 1, 0) ;
		
		// visualizza
		$this->set('names', 		$names);
		
		$this->render(null, "empty", null) ;
	}
}



?>