<?
/**
 * Short description for file.
 *
 * PHP versions 4 
 *
 * CakePHP :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright (c)	2006, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Gestisce il riconoscimento del gerstore/redattore connesso e i 
 * suoi dati di sessione.
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
class BeAuthComponent extends Object {
	var $controller	;
	var $userModel 	= "User" ;
	
	var $Session	= null ;
	var $user		= null ;
	var $allow		= null ;
	var $sessionKey = "BEAuthUser" ;
	var $allowKey 	= "BEAuthAllow" ;
	
	/**
	 * Defiisce l'utente corrente se gia' loggato e/o valido
	 * altrimenti setta a null.
	 * 
	 * @param object $controller
	 */
	function startup(&$controller)
	{
		$this->controller 	= $controller;
		$this->Session 		= &$controller->Session;
		
		if ($this->Session->valid() &&  $this->Session->check($this->sessionKey)) {
			$this->user 	= $this->Session->read($this->sessionKey);
			$this->allow 	= $this->Session->read($this->allowKey);
		}
		$this->controller->set($this->sessionKey, $this->user);
		$this->controller->set($this->allowKey, $this->allow);
	}
	
	/**
	 * Esegue il riconoscimento dell'utente
	 *
	 * @param string $userid
	 * @param string $password
	 * @return boolean 
	 */
	function login ($userid, $password) {
		$conditions = array(
			$this->userModel.".username" 	=> $userid,
			$this->userModel.".passw" 		=> $password,
		);
		
		$this->controller->{$this->userModel}->recursive = 0 ;
		$user = $this->controller->{$this->userModel}->find($conditions);
		
		// Se fallisce esce
		if(empty($user["User"])) {
			$this->logout() ;
			
			return false ;
		}
		
		$this->user = $user["User"] ;
		$this->allow = true ;
		
		// Inserisce i dati in sessione
		$this->Session->write($this->sessionKey, $this->user);
		$this->Session->write($this->allowKey, $this->allow);
		
		$this->controller->set($this->sessionKey, $this->user);
		$this->controller->set($this->allowKey, $this->allow);
		
		return true ;
	}
	
	/**
	 * Esegue la sconnessione dell'utente e cancella i dati di sessione
	 * connessi all'utente.
	 *
	 * @return boolean
	 */
	
	function logout() {
		$this->user = null ;
		$this->allow = false ;
		
		$this->Session->delete($this->sessionKey);
		$this->Session->delete($this->allowKey);
		
		$this->controller->set($this->sessionKey, null);
		$this->controller->set($this->allowKey, null);
		
		return true ;
	}
	
	/**
	 * Torna true se l'utente e' riconosciuto e la sessione valida.
	 *
	 * @return unknown
	 */
	function isLogged() {
		if (isset($this->Session) && $this->Session->valid() &&  $this->Session->check($this->sessionKey)) {
			if(@empty($this->user)) $this->user 	= $this->Session->read($this->sessionKey);
			$this->controller->set($this->sessionKey, $this->user);
			
			return true ;
		} else {
			$this->user 	= null ;
			$this->allow	= false ;
		}
		
//		if(!isset($this->controller)) return false ;
		
		$this->controller->set($this->sessionKey, $this->user);
		$this->controller->set($this->allowKey, $this->allow);
		
		return false ;
	}
}

?>