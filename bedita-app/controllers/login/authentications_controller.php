<?php
/*-----8<--------------------------------------------------------------------
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2008 ChannelWeb Srl, Chialab Srl
 * 
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published 
 * by the Free Software Foundation, either version 3 of the License, or 
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License 
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
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

		if (!empty($this->data["login"])) {
		
			$userid 	= (isset($this->data["login"]["userid"])) ? $this->data["login"]["userid"] : "" ;
			$password 	= (isset($this->data["login"]["passwd"])) ? $this->data["login"]["passwd"] : "" ;
			$authType 	= (isset($this->data["login"]["auth_type"])) ? $this->data["login"]["auth_type"] : "bedita" ;
			
			if(!$this->BeAuth->login($userid, $password, null, array(), $authType)) {
				$this->loginEvent('warn', $userid, "login not authorized");
				if ($authType=='bedita') {
					$this->userErrorMessage(__("Wrong username/password or session expired", true));
				}
				$this->logged = false;
			} else {
				$this->eventInfo("logged in");
			}

			if (isset($this->data["login"]["URLOK"])) {
				$this->redirect($this->data["login"]["URLOK"]);
			}
			return true;
		}
	}

	function logout() {
		$this->eventInfo("logged out");
		$this->BeAuth->logout() ;
	}

	/**
	 * Recovery user password system
	 *
	 * @param string $service_type the service type used by BeHashComponent
	 * @param string $hash hashed string used to handle the recovery
	 */
	public function recoverUserPassword($service_type = 'recover_password', $hash = null) {
		$this->setupLocale();
		if (!empty($service_type) || !empty($hash)) {
			try {
				$this->Transaction->begin();
				if (!$this->BeHash->handleHash($service_type, $hash)) {
					$this->redirect('/');
				}
				$this->Transaction->commit();
				if (empty($hash) || !$this->Session->check('userToChangePwd')) {
					$this->redirect('/');
				}
			} catch (BeditaException $ex) {
				$this->Transaction->rollback();
				$this->userErrorMessage($ex->getMessage());
				$this->eventError($ex->getDetails());
				if (empty($hash)) {
					$this->redirect('/');
				}
			}
			$this->render(null, null, VIEWS."pages/change_password.tpl");
		}
	}

    protected function forward($action, $result) {
        $redirect = array(
            'logout' => array(
                'OK' => '/',
                'ERROR' => '/authentications/logout'
            ),
            'changePasswd' => array(
                'OK' => '/',
                'ERROR' => '/authentications/logout'
            ),
            'login' => array(
                'OK' => '/',
                'ERROR' => '/'
            )
        );
        if (isset($redirect[$action][$result])) {
            return $redirect[$action][$result];
        };
        return false;
    }

	private function loginEvent($level, $user, $msg) {
		$event = array('EventLog'=>array("log_level"=>$level, 
			"userid"=>$user,"msg"=>$msg, "context"=>strtolower($this->name)));
		$this->EventLog->save($event);
	}
	 

}
