<?php
/***************************************************************************
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
 ***************************************************************************/

/**
 * User/group/authorization component:
 * 	- login, session start
 * 	- user/group creation/handling
 * 
 * @link			http://www.bedita.com
 * @version			$Revision$
 * @modifiedby 		$LastChangedBy$
 * @lastmodified	$LastChangedDate$
 * 
 * $Id$
 */
class BeAuthComponent extends Object {
	var $controller	;
	var $Session	= null ;
	var $user		= null ;
	var $allow		= null;
	var $isValid	= true;
	var $changePasswd	= false;
	var $sessionKey = "BEAuthUser" ;
	var $allowKey 	= "BEAuthAllow" ;
	var $authResult	= 'OK';
	
	function __construct() {
		if(!class_exists('User')) {
			App::import('Model', 'User') ;
		}
		if(!class_exists('Group')) {
			App::import('Model', 'Group') ;
		}
		parent::__construct() ;
	} 

	/**
	 * Definisce l'utente corrente se gia' loggato e/o valido
	 * altrimenti setta a null.
	 * 
	 * @param object $controller
	 */
	function startup(&$controller)
	{
		$conf = Configure::getInstance() ;		
		$this->sessionKey = $conf->session["sessionUserKey"] ;
		
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
	function login ($userid, $password, $policy=null) {
		if(!isset($this->User)) {
			$this->User = new User() ;
		}
		
		$conditions = array(
			"User.userid" 	=> $userid,
			"User.passwd" 	=> md5($password),
		);
		
		$this->User->recursive = 1;
		$this->User->unbindModel(array('hasMany' => array('Permission')));
		$u = $this->User->find($conditions);
		
		if(!$this->loginPolicy($userid, $u, $policy))
			return false ;
			
		$this->allow = $u['User']['valid'];
		$this->User->compact($u) ;
		$this->user = $u;
		
		if(isset($this->Session)) {
			$this->Session->write($this->sessionKey, $this->user);
			$this->Session->write($this->allowKey, $this->allow);
		}

		if(isset($this->controller)) {
			$this->controller->set($this->sessionKey, $this->user);
			$this->controller->set($this->allowKey, $this->allow);
		}
		return true ;
	}
	
	/**
	 * Check policy using $policy array or config if null
	 * @return boolean
	 */
	function loginPolicy($userid, $u, $policy) {
		// Se fallisce esce
		if(empty($u["User"])) {
			// look for existing user
			
			$this->User->recursive = 1;
			$this->User->unbindModel(array('hasMany' => array('Permission')));
			$u2 = $this->User->find(array("User.userid" => $userid));
			if(!empty($u2["User"])) {
				$u2["User"]["last_login_err"]= date('Y-m-d H:i:s');
				$u2["User"]["num_login_err"]=$u2["User"]["num_login_err"]+1;
                $this->User->unbindGroups();
				$this->User->save($u2);
                $this->User->rebindGroups();
			}
			$this->logout() ;
			return false ;
		}

		if($policy == null) {
			$policy = array(); // load backend defaults
			$config = Configure::getInstance() ;
			$policy['maxLoginAttempts'] = $config->maxLoginAttempts;
			$policy['maxNumDaysInactivity'] = $config->maxNumDaysInactivity;
			$policy['maxNumDaysValidity'] = $config->maxNumDaysValidity;
			$policy['authorizedGroups'] = $config->authorizedGroups;
		}

		// check activity & validity
		if(!isset($u["User"]["last_login"])) 
			$u["User"]["last_login"] = date('Y-m-d H:i:s');
		$daysFromLastLogin = (time() - strtotime($u["User"]["last_login"]))/(86400000);
		$this->isValid = $u['User']['valid'];
		
		if($u["User"]["num_login_err"] >= $policy['maxLoginAttempts']) {
			$this->isValid = false;
			$this->log("Max login attempts error, user: ".$userid);

		} else if($daysFromLastLogin > $policy['maxNumDaysInactivity']) {
			$this->isValid = false;
			$this->log("Max num days inactivity: user: ".$userid." days: ".$daysFromLastLogin);
			
		} else if($daysFromLastLogin > $policy['maxNumDaysValidity']) {
			$this->changePasswd = true;
		}
		
		// check group auth
		$groups = array();
		foreach ($u['Group'] as $g)
			array_push($groups, $g['name']) ;

		if(count(array_intersect($groups, $policy['authorizedGroups'])) === 0) {
			$this->log("User login not authorized: ".$userid);
			// TODO: special message?? or not for security???
			return false;
		}

		$u['User']['valid'] = $this->isValid; // validity may have changed
		
		if($this->isValid) {
				$u["User"]["num_login_err"]=0;
				$u["User"]["last_login"]=date('Y-m-d H:i:s');
		}		
        
        $this->User->unbindGroups();
        $this->User->save($u); //, true, array('num_login_err','last_login_err','valid','last_login'));
        $this->User->rebindGroups();
        
		if(!$this->isValid) {
			$this->logout();
		}
		
		return true;
	}

	function changePassword($userid, $password) {
		if(!isset($this->User)) {
			$this->User = new User() ;
		}
		
		$this->User->recursive = 1;
		$this->User->unbindModel(array('hasMany' => array('Permission')));
		$u = $this->User->find(array("User.userid" => $userid));
		$u["User"]["passwd"] = md5($password);
		$u["User"]["num_login_err"]=0;
		$u["User"]["last_login"]=date('Y-m-d H:i:s');
		$this->User->save($u);
		$this->user = $u;
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
		
		if(isset($this->Session)) {
			$this->Session->delete($this->sessionKey);
			$this->Session->delete($this->allowKey);
		}
		
		if(isset($this->controller)) {
			$this->controller->set($this->sessionKey, null);
			$this->controller->set($this->allowKey, null);
		}		
		return true ;
	}
	
	/**
	 * Torna true se l'utente e' riconosciuto e la sessione valida.
	 *
	 * @return unknown
	 */
	public function isLogged() {
		
		if (isset($this->Session) && $this->Session->valid() &&  $this->Session->check($this->sessionKey)) {
			if(@empty($this->user)) $this->user 	= $this->Session->read($this->sessionKey);
			$this->controller->set($this->sessionKey, $this->user);
			
			return true ;
		} else {
			$this->user 	= null ;
			$this->allow	= false ;
		}
		
		if(!isset($this->controller)) return false ;
		
		$this->controller->set($this->sessionKey, $this->user);
		$this->controller->set($this->allowKey, $this->allow);
		
		return false ;
	}
	
	/**
	 * Create new user
	 *
	 * @param unknown_type $userData
	 */
	public function createUser($userData, $groups=NULL) {
		$user = new User() ;
		$user->recursive = 1;
		$user->unbindModel(array('hasMany' => array('Permission')));
		$u = $user->findByUserid($userData['User']['userid']);
		if(!empty($u["User"])) {
			$this->log("User ".$userData['User']['userid']." already created");
			throw new BeditaException(__("User already created",true));
		}
		$userData['User']['passwd'] = md5($userData['User']['passwd']);
		$this->userGroupModel($userData, $groups);
		if(!$user->save($userData))
			throw new BeditaException(__("Error saving user",true), $user->validationErrors);
		return true;
	}

	private function userGroupModel(&$userData, $groups) {
		if(isset($groups)) {
			$userData['Group']= array();
			$userData['Group']['Group']= array();
			$groupModel = new Group() ;
			foreach ($groups as $g) {
				$group =  $groupModel->findByName($g);
				array_push($userData['Group']['Group'], $group['Group']['id']) ;
			}
		}
	}
	
	public function updateUser($userData, $groups=NULL)	{
		if(isset($userData['User']['passwd']))
			$userData['User']['passwd'] = md5($userData['User']['passwd']);
		$this->userGroupModel($userData, $groups);
		$user = new User() ;
		if($userData['User']['valid'] == '1') { // reset number of login error, if user is valid
			$userData['User']['num_login_err'] = '0';
		}
		if(!$user->save($userData))
			throw new BeditaException(__("Error updating user",true), $user->validationErrors);
		return true;
	}
	
	public function removeGroup($groupName) {
		$config =& Configure::getInstance();
		if (in_array($groupName, $config->basicGroups)) {
			throw new BeditaException(sprintf(__("Immutable group %s", true),$groupName));
		}
		$groupModel = new Group();
		$g =  $groupModel->findByName($groupName);
		if(!$groupModel->del($g['Group']['id'])) {
			throw new BeditaException(__("Error removing group",true));
		}
		return true;
	}
	
	public function saveGroup($groupData) {
		$config =& Configure::getInstance();
		if (in_array($groupData['Group']['name'], $config->basicGroups)) {
			throw new BeditaException(__("Immutable group",true));
		}
		$group = new Group();
		if(!$group->save($groupData))
			throw new BeditaException(__("Error saving group",true), $group->validationErrors);
		if(!isset($groupData['Group']['id']))
			return $group->getLastInsertID();
		return $group->getID();
	}
	
	public function removeUser($userId) {
		// TODO: come fare con oggetti associati??? sono cancellati di default??
		$user = new User();
		$user->unbindModel(array('hasMany' => array('Permission')));
		$u = $user->findByUserid($userId);
		if(empty($u["User"])) {
			throw new BeditaException(__("User not present",true));
		}
		return $user->delete($u["User"]['id']);
	}	
	
	public function connectedUser() {
		$connectedUser = array();
		$sessionDb = Configure::read('Session.database');
		if ( (Configure::read('Session.save') == "database") && !empty($sessionDb) ) {
			$db =& ConnectionManager::getDataSource($sessionDb);
			$table = $db->fullTableName(Configure::read('Session.table'), false);
			$res = $db->query("SELECT " . $db->name($table.'.data') . " FROM " . $db->name($table) . " WHERE " . $db->name($table.'.expires') . " >= " . time(), false);
			if (empty($res)) {
				return $connectedUser;
			}
			foreach($res as $key => $val) {
				$unserialized_data = $this->unserializesession($val[$table]['data']);
				if(!empty($unserialized_data) && !empty($unserialized_data['BEAuthUser']) && !empty($unserialized_data['BEAuthUser']['userid'])) {
					$connectedUser[]=$unserialized_data['BEAuthUser']['userid'];
				}
			}
		}
		return $connectedUser;
	}
	
	function unserializesession($data) {
		$result = array();
		$vars=preg_split('/([a-zA-Z0-9]+)\|/',$data,-1,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		for($i=0; @$vars[$i]; $i++) {
			$result[$vars[$i++]]=unserialize($vars[$i]);
		}
		return $result;
	}
}
?>