<?php

/**
 *
 * @copyright		
 * @link			
 * @package			
 * @subpackage		
 * @since			
 * @version			
 * @modifiedby		
 * @lastmodified	
 * @license			
 * @author 			giangi@qwerg.com, ste@channelweb.it
 */

/**
 * First Controller login & auth
 * 
 */
class AuthenticationsController extends AppController {
	var $name = 'Authentications';

	var $helpers = array();
	var $components = array('Session');
	var $uses = array();

	/**
	 *  login through POST with redirection 
	 * 
	 */
   function login() {

		$userid 	= (isset($this->data["login"]["userid"])) ? $this->data["login"]["userid"] : "" ;
		$password 	= (isset($this->data["login"]["passwd"])) ? $this->data["login"]["passwd"] : "" ;
		
		if(!$this->BeAuth->login($userid, $password)) {
			$this-> loginEvent('warn', $userid, "login not authorized");
			$this->Session->setFlash(__("Wrong username/password or no authorization", true));
			$this->esito='ERROR';
		}

		if(!$this->BeAuth->isValid) {
			$this-> loginEvent('warn', $userid, "login blocked");
			$this->Session->setFlash(__("User login temporary blocked", true));
			$this->esito='ERROR';
		}
		
		if($this->BeAuth->changePasswd) {
			$this-> loginEvent('info', $userid, "change password");
			$this->set("user", $this->BeAuth->user);
			$this->esito='PWD';
			
			return ;
		}
		
		if($this->esito == "OK")
			$this->eventInfo("logged in");
		
		// Setup del redirect
		$this->data['OK'] 		= (isset($this->data["login"]["URLOK"])) ? $this->data["login"]["URLOK"] : "/" ; 
		$this->data['ERROR'] 	= (isset($this->data["login"]["URLERR"])) ? $this->data["login"]["URLERR"] : "/" ; 
   }

   function changePasswd() {

		$userid 	= (isset($this->data["User"]["userid"])) ? $this->data["User"]["userid"] : "" ;
		$password 	= (isset($this->data["login"]["passwd"])) ? $this->data["login"]["passwd"] : "" ;
		
		if(!$this->BeAuth->changePasswd($userid, $password)) {
			$this->Session->setFlash(__("Error changing password", true));
			$this->esito='ERROR';
		}
   }
   
	/**
	 * logout
	 */
	function logout() {
		$this->eventInfo("logged out");
		$this->BeAuth->logout() ;
	}

	
	 function _REDIRECT($action, $esito) {
	 	$REDIRECT = array(
	 			"logout"	=> 	array(
	 									"OK"	=> "/",
	 									"ERROR"	=> "/logout" 
	 								),
	 			"changePasswd"	=> 	array(
	 									"OK"	=> "/",
	 									"ERROR"	=> "/logout" 
	 								),
	 			"login"	=> 	array(
	 									"OK"	=> "/",
	 									"PWD"	=> "/pages/changePasswd",
	 									"ERROR"	=> "/logout" 
	 								)
	 	);
	 	
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	
	 	return false;
	 }
	
	 private function loginEvent($level, $user, $msg) {
		$event = array('EventLog'=>array("level"=>$level, 
			"user"=>$user,"msg"=>$msg));
		$this->EventLog->save($event);
	}
	 

}

?>