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
	var $Session	= null ;
	var $user		= null ;
	var $allow		= null;
	var $isValid	= true;
	var $changePasswd	= false;
	var $sessionKey = "BEAuthUser" ;
	var $allowKey 	= "BEAuthAllow" ;
	
	function __construct() {
		if(!class_exists('User')) {
			loadModel('User') ;
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
		$this->User->unbindModel(array('hasMany' => array('Permission', 'ObjectUser')));
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
	function loginPolicy($userid, &$u, $policy) {
		// Se fallisce esce
		if(empty($u["User"])) {
			// look for existing user
			$this->User->recursive = 1;
			$this->User->unbindModel(array('hasMany' => array('Permission', 'ObjectUser')));
			$u2 = $this->User->find(array("User.userid" => $userid));
			if(!empty($u2["User"])) {
				$u2["User"]["last_login_err"]= date('Y-m-d H:i:s');
				$u2["User"]["num_login_err"]=$u2["User"]["num_login_err"]+1;
				$this->User->save($u2);
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
		$this->User->save($u);
		
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
		$this->User->unbindModel(array('hasMany' => array('Permission', 'ObjectUser')));
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
	function isLogged() {
		
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
}

?>