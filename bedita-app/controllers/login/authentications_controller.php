<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the Affero GNU General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the Affero GNU General Public License for more details.
 * You should have received a copy of the Affero GNU General Public License 
 * version 3 along with BEdita (see LICENSE.AGPL).
 * If not, see <http://gnu.org/licenses/agpl-3.0.html>.
 * 
 *------------------------------------------------------------------->8-----
 */

/**
 * Authentication controller
 * 
 *
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class AuthenticationsController extends AppController {

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