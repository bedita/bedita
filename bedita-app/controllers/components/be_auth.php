<?php
/***************************************************************************
 * 
 * BEdita - a semantic content management framework
 * 
 * Copyright 2009-2014 ChannelWeb Srl, Chialab Srl
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
 ***************************************************************************/

/**
 * User/group/authorization component:
 *  - login, session start
 *  - user/group creation/handling
 */
class BeAuthComponent extends Object {
    var $controller = null;
    var $extAuthComponents = array();
    var $Session    = null ;
    var $user       = null ;
    var $isValid    = true;
    var $changePasswd   = false;
    var $sessionKey = "BEAuthUser" ;
    const SESSION_INFO_KEY = "BESession" ;
    var $authResult = 'OK';
    var $userAuth = 'bedita';

    /**
     * Set current user, if already logged in and/or valid
     * 
     * @param object $controller
     */
    function initialize(&$controller) {
        $conf = Configure::getInstance() ;      
        $this->sessionKey = $conf->session['sessionUserKey'];
        
        $this->controller = $controller;
        $this->Session = &$controller->Session;

        $this->initExternalServices();
        // autostart session
        if (Configure::read('Session.start') === true) {
            $this->startSession();
        }
        $this->controller->set($this->sessionKey, $this->user);
    }

    /**
     * Start session if it isn't already started
     * else check if it's valid
     *
     * @param string $sessionId the session id with which the session try to start (if not already started)
     * @return boolean true if session started and valid
     */
    public function startSession($sessionId = null) {
        if (!$this->Session->started()) {
            $this->Session->activate();
            if ($sessionId) {
                $this->Session->id($sessionId);
            }
            $this->Session->startup($this->controller);

            if (!$this->Session->valid()) {
                $this->log('Started session is expired ' . $this->Session->id 
                    . AppController::usedUrl() . ' - ' . $this->Session->error(), 'debug');
                return false;
            }
            $this->startupExternalServices();
            if ($this->checkSessionKey()) {
                $this->user = $this->Session->read($this->sessionKey);
            }
        } elseif (!$this->Session->valid()) {
            $this->log('Session already started is expired ' . $this->Session->id 
                . AppController::usedUrl() . ' - ' . $this->Session->error(), 'debug');
            return false;
        }

        return true;
    }

    /**
     * Init external auth services components, reading 'extAuthParams' config
     * Create components without components startup
     */
    protected function initExternalServices() {
        $serviceNames = array();
        $extAutParams = Configure::read('extAuthParams');
        if (!empty($extAutParams)) {
            $serviceNames = array_keys($extAutParams);
        }

//         $defaultComponentPath = BEDITA_CORE_PATH . DS . "controllers" . DS . 'components';
//         if ($handle = opendir($defaultComponentPath)) {
//             while (false !== ($entry = readdir($handle))) {
//                 if (strpos($entry, 'be_auth_') === 0) {
//                     $name = str_replace('be_auth_', '', $entry);
//                     $name = str_replace('.php', '', $name);
//                     array_push($components, Inflector::camelize($name));
//                 }
//             }

//             closedir($handle);
//         }
//         $addons = ClassRegistry::init("Addon")->getAddons();
//         if (!empty($addons['components']) && !empty($addons['components']['on'])) {
//             $addonComponents = $addons['components']['on'];
//             foreach ($addonComponents as $key => $component) {
//                 if (strpos($component['name'], 'BeAuth') === 0) {
//                     array_push($components, str_replace('BeAuth', '', $component['name']));
//                 }
//             }
//         }
        if (!empty($serviceNames)) {
            foreach ($serviceNames as $service) {
                //load component dynamically
                $componentClass = 'BeAuth' . Inflector::camelize($service);
                if (!App::import('Component', $componentClass)) {
                    throw new BeditaException(__('External auth component not found: ', true) . $componentClass);
                } else {
                    $componentClass .= 'Component';
                    $componentObject = new $componentClass();
                    $this->extAuthComponents[$service] = $componentObject;
                }
            }
        }
    }

    /**
     * Get external auth services list
     */
    public function getExternalServices() {
        $services = array();
        if (!empty($this->extAuthComponents)) {
            foreach ($this->extAuthComponents as $service => $componentObject) {
                $services[] = array(
                    'name' => $service,
                    'relatedBy' => $componentObject->relatedBy
                );
            }
        }
        return $services;
    }

    /**
     * Startup external auth service components
     */
    protected function startupExternalServices() {
        if (!empty($this->extAuthComponents)) {
            foreach ($this->extAuthComponents as $service => $componentObject) {
                if (!$componentObject->startup($this->controller)) {
                    $this->log('External service startup failed: ' . $service);
                }
            }
        }
    }
    
    /**
     * Check whether session key is valid
     */
    protected function checkSessionKey() {
        $res = true;
        $extRes = false;

        if (!isset($this->Session)) {
            $res = false;
            $this->log('Session component not set!');
        } elseif (!$this->Session->valid()) {
            $res = false;
            if ($this->Session->started()) {
                $this->log('checkSessionKey: session not valid! ' . $this->Session->id . AppController::usedUrl());
            }
        } 

        if ($res) {

            if (!$this->Session->check($this->sessionKey)) {
                $res = false;
            }

            if (!$res) {
                foreach ($this->extAuthComponents as $key => $component) {
                    $extRes = $component->checkSessionKey();
                    if ($extRes) {
                        break;
                    }
                }
            }
        }

        return $res || $extRes;
    }
    
    
    /**
     * User authentication on external service (OpenID. LDAP, Shibbolet...)
     *
     * @param string $userid
     * @param string $extAuthType
     * @param array $extAuthOptions
     * @return boolean
     * @throws BeditaException
     */
    public function externalLogin($extAuthType, $extAuthOptions = array()) {
        // load authType component
        if (!empty($this->extAuthComponents[$extAuthType])) {
            $extAuthComponent = $this->extAuthComponents[$extAuthType];
            if ($extAuthComponent->login($extAuthOptions)) {
                $this->userAuth = $extAuthType;
                $this->user = $extAuthComponent->getUser();
                return true;
            } else {
                return false;
            }
        }
    }
    
    /**
     * User authentication
     *
     * @param string $userid
     * @param string $password
     * @param array $policy (could contain parameters like maxLoginAttempts,maxNumDaysInactivity,maxNumDaysValidity)
     * @param array $auth_group_name
     * @param string $authType
     * @param array $extAuthOptions
     * @return boolean 
     */
    public function login($userid, $password, $policy = null, $auth_group_name = array(), $authType = 'bedita', $extAuthOptions = array()) {
        if ($authType == $this->userAuth) {
            $userModel = ClassRegistry::init('User');
            $conditions = array(
                "User.userid"   => $userid,
                "User.passwd"   => md5($password),
            );
            $userModel->containLevel("default");
            $u = $userModel->find($conditions);

            if (!$this->loginPolicy($userid, $u, $policy, $auth_group_name)) {
                return false;
            }
        } else {
            $this->startSession();
            $this->Session->write($this->sessionKey . 'Policy', $policy);
            $this->Session->write($this->sessionKey . 'AuthGroupName', $auth_group_name);
            return $this->externalLogin($authType);
        }

        return true;
    }

    /**
     * Http code response for login attempt
     * 
     * @param string $userid
     * @param string $password
     * @param array $policy (could contain parameters like maxLoginAttempts,maxNumDaysInactivity,maxNumDaysValidity)
     * @param array $auth_group_name
     * @return number (can be 200 'ok' 401 'unauthorized' 403 'forbidden'
     */
    public function responseLogin($userid, $password, $policy = null, $auth_group_name = array()) {
        $userModel = ClassRegistry::init('User');
        $conditions = array(
            "User.userid"   => $userid,
            "User.passwd"   => md5($password),
        );
        $userModel->containLevel("default");
        $u = $userModel->find($conditions);
        if(empty($u["User"])) {
            return 401; // unauthorized
        }
        if($policy == null) {
            $policy = array(); // load backend defaults
            $config = Configure::getInstance() ;
            $policy['maxLoginAttempts'] = $config->loginPolicy['maxLoginAttempts'];
            $policy['maxNumDaysInactivity'] = $config->loginPolicy['maxNumDaysInactivity'];
            $policy['maxNumDaysValidity'] = $config->loginPolicy['maxNumDaysValidity'];
        }
        if(!isset($u["User"]["last_login"])) {
            $u["User"]["last_login"] = date('Y-m-d H:i:s');
        }
        $daysFromLastLogin = (time() - strtotime($u["User"]["last_login"]))/(86400000);
        if($u["User"]["num_login_err"] >= $policy['maxLoginAttempts']) {
            return 401; // unauthorized
        } else if($daysFromLastLogin > $policy['maxNumDaysInactivity']) {
            return 401; // unauthorized
        }
        // check group auth
        $groups = array();
        $authorized = false;
        foreach ($u['Group'] as $g) {
            array_push($groups, $g['name']) ;
            if( $g['backend_auth'] == 1 || in_array($g['name'], $auth_group_name) ) {
                $authorized = true;
            }
        }
        if($authorized === false) {
            return 403; // forbidden
        }
        return 200; // ok
    }

    /**
     * Check policy using $policy array or config if null
     * 
     * @param string $userid
     * @param array $u
     * @param array $policy (could contain parameters like maxLoginAttempts,maxNumDaysInactivity,maxNumDaysValidity)
     * @param array $auth_group_name
     * @return boolean
     */
    protected function loginPolicy($userid, $u, $policy = null, $auth_group_name = array()) {
        $userModel = ClassRegistry::init("User");
        // If fails, exit
        if(empty($u["User"])) {
            // look for existing user
            $userModel->containLevel("minimum");
            $u = $userModel->find(array("User.userid" => $userid));
            if(!empty($u["User"])) {
                $u["User"]["last_login_err"]= date('Y-m-d H:i:s');
                $u["User"]["num_login_err"]=$u["User"]["num_login_err"]+1;
                $userModel->save($u);
            }
            $this->logout();
            if ($this->userAuth != 'bedita') {
                $this->startSession();
                $this->Session->write('externalLoginRequestFailed', true);
            }
            return false;
        }

        if($policy == null) {
            $policy = array(); // load backend defaults
            $config = Configure::getInstance() ;
            $policy['maxLoginAttempts'] = ( !empty($config->loginPolicy['maxLoginAttempts']) ) ? $config->loginPolicy['maxLoginAttempts'] : -1;
            $policy['maxNumDaysInactivity'] = $config->loginPolicy['maxNumDaysInactivity'];
            $policy['maxNumDaysValidity'] = $config->loginPolicy['maxNumDaysValidity'];
        }

        // check activity & validity
        if(!isset($u["User"]["last_login"])) {
            $u["User"]["last_login"] = date('Y-m-d H:i:s');
        }
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
        $authorized = false;
        foreach ($u['Group'] as $g) {
            array_push($groups, $g['name']) ;
            if( $g['backend_auth'] == 1 || in_array($g['name'], $auth_group_name) ) {
                $authorized = true;
            }
        }

        if ($authorized === false) {
            $this->log("User login not authorized: ".$userid);
            if ($this->userAuth != 'bedita') {
                $this->startSession();
                $this->Session->write('externalLoginRequestFailed', $userid);
            }
            // TODO: special message?? or not for security???
            return false;
        }

        $u['User']['valid'] = $this->isValid; // validity may have changed
        
        if($this->isValid) {
            $u["User"]["num_login_err"] = 0;
            $u["User"]["last_login"] = date('Y-m-d H:i:s');
        }
        
        $data["User"] = $u["User"];
        $userModel->save($data); //, true, array('num_login_err','last_login_err','valid','last_login'));
        
        if(!$this->isValid) {
            $this->logout();
            if ($this->userAuth != 'bedita') {
                $this->startSession();
                $this->Session->write('externalLoginRequestFailed', $userid);
            }
            return false;
        }

        $userModel->compact($u) ;
        $this->user = $u;
        $this->setSessionVars();
        
        return true;
    }

    /**
     * Change password for user and set num_login_err to 0
     * 
     * @param string $userid
     * @param string $password
     * @return boolean
     */
    public function changePassword($userid, $password) {
        if (empty($userid) || empty($password)) {
            return false;
        }
        $userModel = ClassRegistry::init('User');
        $u = $userModel->find("first", array(
            "conditions" => array(
                "User.userid" => $userid
            ),
            "contain" => array()
        ));
        $u["User"]["passwd"] = md5($password);
        $u["User"]["num_login_err"]=0;
        if (!$userModel->save($u)) {
            return false;
        }
        return true;
    }
    
    /**
     * User logout: remove session data for the user
     *
     * @return boolean
     */
    public function logout() {
        if ($this->userAuth != 'bedita' && !empty($this->extAuthComponents[$this->userAuth])) {
            if (method_exists($this->extAuthComponents[$this->userAuth], 'logout')) {
                $this->extAuthComponents[$this->userAuth]->logout();
            }
        }

        $this->user = null ;
        
        if(isset($this->Session)) {
            $this->Session->destroy();
        }
        
        if(isset($this->controller)) {
            $this->controller->set($this->sessionKey, null);
        }

        return true ;
    }

    /**
     * Check whether current user is logged in
     * 
     * @return boolean
     */
    public function isLogged() {
        if (Configure::read('Session.start') === false && isset($_COOKIE[Configure::read('Session.cookie')])) {
            $this->startSession();
        }
        if ($this->Session->started() && $this->checkSessionKey()) {
            if (empty($this->user)) {
                $this->user = $this->Session->read($this->sessionKey);
            }
            $this->controller->set($this->sessionKey, $this->user);
            // update session info
            $this->Session->write(self::SESSION_INFO_KEY, array(
                'userAgent' => $_SERVER['HTTP_USER_AGENT'],
                'ipNumber' => $_SERVER['REMOTE_ADDR'],
                'time' => time()
            ));
            return true;
        }
        $this->user = null;
        if (!isset($this->controller)) {
            return false;
        }
        $this->controller->set($this->sessionKey, $this->user);
        return false;
    }

    /**
     * Get the current used component for the authentication
     */
    public function getAuthComponent() {
        if ($this->userAuth != 'bedita' && !empty($this->extAuthComponents[$this->userAuth])) {
            return $this->extAuthComponents[$this->userAuth];
        } else {
            return $this;
        }
    }

    /**
     * Get the current logged user
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * Get the current logged userid or empty string if no user is logged
     */
    public function userid() {
        return !empty($this->user['userid']) ? $this->user['userid'] : '';
    }
    
    /**
     * Check whether user group is authorized
     * 
     * @param array $groups
     * @return boolean
     */
    public function isUserGroupAuthorized($groups=array()) {
        if (empty($this->user)) {
            if (!$this->isLogged())
                return false;
        }
        $backendGroups = ClassRegistry::init('Group')->find("list", array(
                "fields" => "name",
                "conditions" => array("backend_auth" => 1)
                )
            );
        $groups = array_merge($backendGroups,$groups);
        $groupsIntersect = array_intersect($this->user["groups"], $groups);
        if (empty($groupsIntersect)) {
            return false;
        }
        return true;
    }

    /**
     * Create new user
     *
     * @param array $userData
     * @param array $groups (contains groups names)
     * @param boolean $notify (send newUser backend notify)
     *
     * @return int id of user created
     * @throws BeditaException
     */
    public function createUser($userData, $groups=NULL, $notify=true) {
        $user = ClassRegistry::init('User');
        $user->containLevel("minimum");

        if (isset($userData['User']['auth_type']) && $userData['User']['auth_type'] != 'bedita') {
            $user->validate = $user->externalServiceValidate;
        }

        $u = $user->findByUserid($userData['User']['userid']);
        if(!empty($u["User"])) {
            $this->log("User ".$userData['User']['userid']." already created");
            throw new BeditaException(__("User already created",true));
        }
        
        $this->userGroupModel($userData, $groups);
        if ($notify) {
            $user->Behaviors->attach('Notify');
        }
        
        if (!isset($userData['User']['auth_type']) || $userData['User']['auth_type'] == 'bedita') {
            if(!$user->passwordValidation($userData['User'])) {
                throw new BeditaException(__("Password not valid",true) . " - " . Configure::read("loginPolicy.passwordErrorMessage"));
            }
            if (!empty($userData['User']['passwd'])) {
                $userData['User']['passwd'] = md5($userData['User']['passwd']);
            }
        }

        $user->create();
        if(!$user->save($userData)) {
            throw new BeditaException(__("Error saving user",true), $user->validationErrors);
        }
        if ($notify) {
            $user->Behaviors->detach('Notify');
        }
        return $user->getLastInsertID();
    }

    /**
     * Check confirm password
     * 
     * @param string $password
     * @param string $confirmedPassword
     * @return boolean
     */
    public function checkConfirmPassword($password, $confirmedPassword) {
        if (trim($password) !== trim($confirmedPassword)) {
            return false;
        }
        return true;
    }

    /**
     * Fill group data for user (set group data in $userData)
     * 
     * @param array $userData
     * @param array $groups
     */
    protected function userGroupModel(&$userData, $groups) {
        if(isset($groups)) {
            $userData['Group']= array();
            $userData['Group']['Group']= array();
            $groupModel = ClassRegistry::init('Group');
            $group_ids = $groupModel->find("list", array(
                "fields" => "id",
                "conditions" => array("name" => $groups),
                "order" => "id ASC"
            ));
            if (!empty($group_ids)) {
                $userData['Group']['Group'] = array_values($group_ids);
            }
        }
    }

    /**
     * Update user data
     * 
     * @param array $userData
     * @param array $groups
     * @return boolean
     * @throws BeditaException
     */
    public function updateUser($userData, $groups=NULL) {
        $this->userGroupModel($userData, $groups);
        $user = ClassRegistry::init('User');
        if($userData['User']['valid'] == '1') { // reset number of login error, if user is valid
            $userData['User']['num_login_err'] = '0';
        }
        
        $user->Behaviors->attach('Notify');
        if(!$user->passwordValidation($userData['User'])) {
            throw new BeditaException(__("Password not valid",true). " - " . Configure::read("loginPolicy.passwordErrorMessage"));
        }
        if (!empty($userData['User']['passwd'])) {
            $userData['User']['passwd'] = md5($userData['User']['passwd']);
        }
        if(!$user->save($userData))
            throw new BeditaException(__("Error updating user",true), $user->validationErrors);
        return true;
    }

    /**
     * Remove group
     * 
     * @param string $groupName
     * @return boolean
     * @throws BeditaException
     */
    public function removeGroup($groupName) {
        $groupModel = ClassRegistry::init('Group');
        $g =  $groupModel->find("first", array(
                "conditions" => array("name" => $groupName),
                "contain" => array()
        ));
        if ($g['Group']['immutable'] == 1) {
            throw new BeditaException(__("Immutable group", true) . " " . $groupName);
        }
        if(!$groupModel->delete($g['Group']['id'])) {
            throw new BeditaException(__("Error removing group",true) . " " . $groupName);
        }
        return true;
    }

    /**
     * Save group
     * 
     * @param array $groupData
     * @return int group id
     * @throws BeditaException
     */
    public function saveGroup($groupData) {
        $group = ClassRegistry::init('Group');
        $group->create();
        if(isset($groupData['Group']['id'])) {
            $immutable = $group->field('immutable', array('id' => $groupData['Group']['id']));
            if($immutable == 1) {
                throw new BeditaException(__("Immutable group",true));
            }
        } else { // check existing group
            $id = $group->field('id', array('name' => $groupData['Group']['name']));
            if(!empty($id)) {
                throw new BeditaException(__("Existing group",true));
            }
        }
        if(!$group->save($groupData))
            throw new BeditaException(__("Error saving group",true), $group->validationErrors);
        if(!isset($groupData['Group']['id']))
            return $group->getLastInsertID();
        return $group->getID();
    }

    /**
     * Remove user
     * 
     * @param string $userId
     * @throws BeditaException
     * @return boolean
     */
    public function removeUser($userId) {
        // TODO: how can we do with related objects??? default removal??
        $user = ClassRegistry::init('User');
        $user->containLevel("minimum");
        $u = $user->findByUserid($userId);
        if(empty($u["User"])) {
            throw new BeditaException(__("User not present",true));
        }
        return $user->delete($u["User"]['id']);
    }   

    /**
     * Get users whose sessions are still active (not expired)
     * 
     * @return array
     */
    public function connectedUser() {
        $connectedUser = array();
        $res = array();
        $sessionDb = Configure::read('Session.database');
        if ( (Configure::read('Session.save') == "database") && !empty($sessionDb) ) {
            $db =& ConnectionManager::getDataSource($sessionDb);
            $sessionModelName = Configure::read('Session.model');
            $res = ClassRegistry::init($sessionModelName)->find("all", array(
                "fields" => array("data"),
                "conditions" => array("expires >= " . time())
            ));
        }
        foreach($res as $key => $val) {
            $sessiondata = !empty($val[$sessionModelName]['data']) ? $val[$sessionModelName]['data'] : $val[0]['data'];
            $unserialized_data = $this->unserializesession($sessiondata);
            if(!empty($unserialized_data) && !empty($unserialized_data['BEAuthUser']) && !empty($unserialized_data['BESession'])) {
                $timeout = Configure::read('activityTimeout');
                if((time() - $unserialized_data['BESession']['time']) < ($timeout*60) ) {
                    $usr = $unserialized_data['BEAuthUser']['userid'];
                    $connectedUser[]= array($usr => array_merge($unserialized_data['BEAuthUser'], 
                        $unserialized_data['BESession']));
                }
            }
        }
        return $connectedUser;
    }

    /**
     * Unserialize session data
     * 
     * @param array $data
     * @return array
     */
    public function unserializesession($data) {
        $result = array();
        $vars=preg_split('/([a-zA-Z0-9]+)\|/',$data,-1,PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
        for($i=0; @$vars[$i]; $i++) {
            $result[$vars[$i++]]=unserialize($vars[$i]);
        }
        return $result;
    }

    /**
     * Get user session data if present, false otherwise
     * 
     * @return mixed string|boolean
     */
    public function getUserSession() {
        if (!empty($this->user)) {
            return $this->user;
        }
        return ($this->checkSessionKey())? $this->Session->read($this->sessionKey) : false; 
    } 

    /**
     * Set session variables (i.e. userAgent, ipNumber, time, Config.language)
     */
    public function setSessionVars() {
        if (isset($this->Session)) {
            // assure that session started
            $this->startSession();
            // load history
            $historyConf = Configure::read("history");
            if (!empty($historyConf)) {
                $groupBy = ($historyConf["showDuplicates"] === false)? array("url", "area_id") : array();
                $this->user["History"] = ClassRegistry::init("History")->getUserHistory($this->user["id"], $historyConf["sessionEntry"], $groupBy);
            }
            $this->Session->write($this->sessionKey, $this->user);
            $this->Session->write(self::SESSION_INFO_KEY, array("userAgent" => $_SERVER['HTTP_USER_AGENT'], 
                "ipNumber" => $_SERVER['REMOTE_ADDR'], "time" => time()));
            if (!empty($this->user["lang"])) {
                $this->Session->write('Config.language',$this->user["lang"]);
            }
        }

        if (isset($this->controller)) {
            $this->controller->set($this->sessionKey, $this->user);
        }
    }

    /**
     * Update session history
     * 
     * @param array $historyItem
     * @param array $historyConf
     */
    public function updateSessionHistory($historyItem, $historyConf) {
        if (empty($historyItem) || empty($historyConf) || !$this->Session->started()) {
            return;
        }
        $history = $this->Session->read($this->sessionKey . ".History");
        if (empty($history)) {
            $history[] = $historyItem; 
        } else {
            if ($historyConf["showDuplicates"] === false) {
                foreach ($history as $h) {
                    if ($h["url"] == $historyItem["url"]) {
                        $findPath = true;
                        break;
                    }
                }
            }
            
            if (empty($findPath)) {
                if (count($history) == $historyConf["sessionEntry"]) {
                    array_pop($history);
                }
                array_unshift($history, $historyItem);
            }
        }
        
        $this->Session->write($this->sessionKey . ".History" , $history);
    }
}
?>