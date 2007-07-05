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
 * @author 			giangi@qwerg.com
 */

/**
 * Short description for class.
 *
 * Controller principale, d'entrata
 * 
 */
class AuthenticationsController extends AppController {
	var $name = 'Authentications';

	var $helpers = array('Bevalidation', 'Form');
	var $components = array('Session', 'BeAuth');

	// This controller does not use a model
	 var $uses = array("User");

	/**
	 * Comando che esegue il login.
	 * Reindirizza a fine operazione.
	 * Dati passati via POST:
	 * userid		
	 * passwd		
	 * URLOK		URL di redirect in caso positivo
	 * URLERR		URL di redirect in caso negativo
	 *
	 */
   function login() {
		$userid 	= (isset($this->data["login"]["userid"])) ? $this->data["login"]["userid"] : "" ;
		$password 	= (isset($this->data["login"]["passwd"])) ? $this->data["login"]["passwd"] : "" ;
		
		$URLOK 		= (isset($this->data["login"]["URLOK"])) ? $this->data["login"]["URLOK"] : $this->webroot ;
		$URLERR		= (isset($this->data["login"]["URLERR"])) ? $this->data["login"]["URLERR"] : "/" ;
		
		if(!$this->BeAuth->login($userid, $password)) {
			$this->Session->setFlash("Username e/o password non corrette");
			$this->redirect($URLERR);
		} else {
			$this->redirect($URLOK);
		}
	}
	
	/**
	 * Comando che esegue il logout.
	 * Reindirizza a fine operazione
	 * Dati passati via POST:
	 * URLOK		URL di redirect
	 *
	 */
	function logout() {
		$URLOK 		= (isset($this->data["URLOK"])) ? $this->data["URLOK"] : "/" ;
		
		$this->BeAuth->logout() ;
		
		$this->redirect($URLOK);
	}
}



?>