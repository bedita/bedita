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
			$this->loginEvent('warn', $userid, "login blocked");
			$this->userErrorMessage(__("User login temporary blocked", true));
			$this->result=self::ERROR;
		}
		
		if($this->result === self::OK) {
			$this->eventInfo("logged in");
		}
		
		// redirect setup
		if(isset($this->data["login"]["URLOK"])) {
			$this->data['OK'] = $this->data["login"]["URLOK"];
		}
	}

	function logout() {
		$this->eventInfo("logged out");
		$this->BeAuth->logout() ;
	}

	public function recoverPassword($service_type="recover_password", $hash=null) {
		$this->setupLocale();
		if (!empty($service_type) || !empty($hash)) {
			try {
				$this->Transaction->begin();
				if (!$this->BeHash->handleHash($service_type, $hash)) {
					$this->redirect("/");
				}
				$this->Transaction->commit();
				if (empty($hash)) {
					$this->redirect("/");
				}
			} catch (BeditaHashException $ex) {
				$this->Transaction->rollback();
				$this->userErrorMessage($ex->getMessage());
				$this->eventError($ex->getDetails());
			} catch (BeditaException $ex) {
				$this->Transaction->rollback();
				$this->userErrorMessage($ex->getMessage());
				$this->eventError($ex->getDetails());
				$this->redirect("/");
			}
			$this->render(null, null, VIEWS."pages/change_password.tpl");
		}
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
	 									"ERROR"	=> "/" 
	 								)
	 	);
	 	
	 	if(isset($REDIRECT[$action][$esito])) return $REDIRECT[$action][$esito] ;
	 	
	 	return false;
	 }
	 
	
	 private function loginEvent($level, $user, $msg) {
		$event = array('EventLog'=>array("log_level"=>$level, 
			"userid"=>$user,"msg"=>$msg, "context"=>strtolower($this->name)));
		$this->EventLog->save($event);
	}
	 

}

?>