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

	public $components = array('Session');
	public $uses = array();
	
	/**
	 *  login through POST with redirection 
	 */
   function login() {

		$userid 	= (isset($this->data["login"]["userid"])) ? $this->data["login"]["userid"] : "" ;
		$password 	= (isset($this->data["login"]["passwd"])) ? $this->data["login"]["passwd"] : "" ;
	
		if(!$this->BeAuth->login($userid, $password)) {
			$this->loginEvent('warn', $userid, "login not authorized");
			$this->userErrorMessage(__("Wrong username/password or no authorization", true));
			$this->result=self::ERROR;
		}

		if(!$this->BeAuth->isValid) {
			$this-> loginEvent('warn', $userid, "login blocked");
			$this->userErrorMessage(__("User login temporary blocked", true));
			$this->result=self::ERROR;
		}
		
		if($this->BeAuth->changePasswd) {
			$this-> loginEvent('info', $userid, "change password");
			$this->set("user", $this->BeAuth->user);
			$this->result='PWD';
			
			return ;
		}
		
		if($this->result === self::OK)
			$this->eventInfo("logged in");
		
		// redirect setup
		if(isset($this->data["login"]["URLOK"])) 
		 		$this->data['OK'] = $this->data["login"]["URLOK"];
   }

   function changePasswd() {

		$userid 	= (isset($this->data["User"]["userid"])) ? $this->data["User"]["userid"] : "" ;
		$password 	= (isset($this->data["login"]["passwd"])) ? $this->data["login"]["passwd"] : "" ;
		
		if(!$this->BeAuth->changePasswd($userid, $password)) {
			$this->userErrorMessage(__("Error changing password", true));
			$this->result=self::ERROR;
		}
   }
   
	/**
	 * logout
	 */
	function logout() {
		$this->eventInfo("logged out");
		$this->BeAuth->logout() ;
	}
	
	 protected function forward($action, $esito) {
	 	$REDIRECT = array(
	 			"logout"	=> 	array(
	 									"OK"	=> "/",
	 									"ERROR"	=> "/authentications/logout" 
	 								),
	 			"changePasswd"	=> 	array(
	 									"OK"	=> "/",
	 									"ERROR"	=> "/authentications/logout" 
	 								),
	 			"login"	=> 	array(
	 									"OK"	=> "/",
	 									"PWD"	=> "/pages/changePasswd",
	 									"ERROR"	=> "/authentications/logout" 
	 								),
	 			"switchlang"	=> 	array(
	 									"OK"	=> "/",
	 									"ERROR"	=> "/authentications/logout" 
	 								)
	 	);
	 	
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	
	 	return false;
	 }
	 
	
	 private function loginEvent($level, $user, $msg) {
		$event = array('EventLog'=>array("level"=>$level, 
			"user"=>$user,"msg"=>$msg, "context"=>strtolower($this->name)));
		$this->EventLog->save($event);
	}
	 

}

?>