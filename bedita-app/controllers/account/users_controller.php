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
class UsersController extends AppController {
	var $name = 'Users';

	var $helpers = array();
	var $components = array('Session');

	// This controller does not use a model
	 var $uses = array("User");
	
	/**
	 * Torna l'elenco semplice degli userid filtrando sulla parte iniziale della stringa.
	 * Utilizzata dagli autocomplete degli userid.
	 *
  	 * @param integer $q		parte iniziale stringa userid da tornare
  	 * @param integer $d		dimensione lista da tornare
	 * 
	 */
	function userids($q = "", $d = 20) {
	 	// Setup parametri
		$this->setup_args(
			array("q", "string", &$q), 
			array("d", "integer", &$d)
		) ;
		$q = trim($q) ;
		
		// Preleva i dati
		$userids = $this->User->findAll("userid LIKE '{$q}%'",array("userid"), "userid", $d, 1, 0) ;
		
		// visualizza
		$this->set('userids', 		$userids);
		
		$this->render(null, "empty", null) ;
	}
}



?>