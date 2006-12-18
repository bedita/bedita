<?php
/**
 * Modulo superuser.
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
class UsersController extends AppController {
	var $components = array('BeAuth');
	var $helpers 	= array('Bevalidation');
	
	var $name = 'Users';
	
	function __construct() {
		parent::__construct() ;
		$this->view 	= 'Smarty';
	}
	
	/**
	 * Visualizza una porzione degli utenti redattori/gestori presenti nel sistema.
	 *
	 * @param integer $page		pagina dell'elenco richiesta
	 * @param integer $dim		dimensione della pagina
	 * @param string $order		nome campo su cui ordinare la lista. Aggiungere "desc" per invertire l'ordine
	 */
	function index($page = 1, $dim = 10, $order = null) {
		// Verifica i permessi d'accesso
		if(!$this->checkLogin()) return ;

		// Preleva l'elenco degli utenti richiesto
		if(!$this->User->listUser($users, $page, $dim , $order)) {
			$this->Session->setFlash("Errore nel prelievo della lista degli utenti");
			return ;
		}
		
		$this->set('Users', $users);
	}

	/**
	 * Visualizza il form per la modifica dei dati utente
	 *
	 * @param integer $id
	 */
	function view($id = null) {
		// Verifica i permessi d'accesso
		if(!$this->checkLogin()) return ;
		
		// Preleva l'utente
		if(!$this->User->view($user, $id)) {
			$this->Session->setFlash("Errore nel prelievo dell'utente '$id'");
			return ;
		}
     	
	   	$this->set('User', $user);
		
		// Preleva lista dei moduli
        $this->set('moduleList', $this->requestAction('/modules/getModuleList'));
   }
   
   ////////////////////////////////////////////////////////////
   ////////////////////////////////////////////////////////////
   /**
    * modifica i dati dell'utente. 
    * I dati sono passati via POST.
    *
    */
	function edit() {
		if(empty($this->data)) {
			$this->Session->setFlash("Nessun utente da salvare");
			return ;
		}
		
		// salva i dati
		if(!$this->User->save($this->data)) {
			$this->Session->setFlash("Errore nella modifica dei dati utente");
			$this->redirect($this->data["back"]["ERROR"]) ;
			
			return ;
		}
		
		$this->redirect($this->data["back"]["OK"]) ;
   }

	
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
		
		$URLOK 		= (isset($this->data["login"]["URLOK"])) ? $this->data["login"]["URLOK"] : "/" ;
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