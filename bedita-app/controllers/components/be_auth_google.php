<?php
/*-----8<--------------------------------------------------------------------
 *
* BEdita - a semantic content management framework
*
* Copyright 2013 ChannelWeb Srl, Chialab Srl
*
*------------------------------------------------------------------->8-----
*/

set_include_path(BEDITA_CORE_PATH . DS . "vendors" . DS . 'google' . DS . 'src' . PATH_SEPARATOR . get_include_path());
App::import('component', 'be_auth');
App::import('Vendor', 'google', array('file' => 'google' . DS . 'src' . DS . 'Google' . DS . 'Client.php'));
App::import('Vendor', 'google', array('file' => 'google' . DS . 'src' . DS . 'Google' . DS . 'OAuth2.php'));

/**
 * Google User auth component
*/
class BeAuthGoogleComponent extends BeAuthComponent{
    var $components = array('Transaction');
    var $uses = array('Image', 'Card');

    public $userAuth = 'google';

    protected $params = null;
    protected $vendorController = null;
    protected $oauthTokens = null;
    protected $accessTokens = null;
    protected $userIdPrefix = 'google-';
    public $disabled = false;

    public function startup($controller=null) {
        $this->controller = $controller;
        $this->Session = $controller->Session;

        $this->params = Configure::read("extAuthParams");
        if (isset( $this->params[$this->userAuth] ) && isset( $this->params[$this->userAuth]['keys'] )) {
            $this->vendorController = new Google_Client();
            $this->vendorController->setClientId($this->params[$this->userAuth]['keys']['clientId']);
            $this->vendorController->setClientSecret($this->params[$this->userAuth]['keys']['clientSecret']);
            $this->vendorController->setRedirectUri($this->getCurrentUrl());
            foreach ($this->params[$this->userAuth]['scopes'] as $scope) {
               $this->vendorController->addScope($scope);
            }

            if (isset($_GET['code']) && !$this->Session->check('googleAccessToken') && $this->Session->check('googleRequestedToken')) {
                $this->vendorController->authenticate($_GET['code']);
                $this->Session->write('googleAccessToken', $this->vendorController->getAccessToken());
            }

            if ($this->Session->check('googleAccessToken')) {
                $this->vendorController->setAccessToken($this->Session->read('googleAccessToken'));
            } else {
                $this->vendorController->setRedirectUri($this->getCurrentUrl());
            }
            return true;
        } else {
            return false;
        }
    }

    protected function checkSessionKey() {
        if (isset( $this->vendorController )) {
            $profile = $this->loadProfile();
            if ($profile) {
                if (isset($this->params[$this->userAuth]['createUser']) && $this->params[$this->userAuth]['createUser']) {
                    $this->createUser($profile);
                }
                if ($this->login()) {
                    return true;
                }
            }
            return false;
        } else {
            return false;
        }
    }

    public function login() {
        $policy = $this->Session->read($this->sessionKey . 'Policy');
        $authGroupName = $this->Session->read($this->sessionKey . 'AuthGroupName');
        $userid = null;

        if (!isset( $this->vendorController )) {
            return;
        }

        if ($this->Session->check('googleAccessToken')) {
            $profile = $this->loadProfile();
            $userid = $profile->id;
        }

        //get the user
        if ($userid) {
            //BE user
            $user = ClassRegistry::init('User');
            $user->containLevel("default");
            $u = $user->find('first', array(
                    'conditions' => array(
                        'auth_params' => $profile->id,
                        'auth_type' => $this->userAuth
                    )
                )
            );

            if (empty($u['User'])) {
                return false;
            }

            if(!$this->loginPolicy($userid, $u, $policy, $authGroupName)) {
                return false;
            }
            return true;

        } else { 
            //get tokens
            $this->loginUrl();
        }
    }

    public function logout() {
        $this->vendorController->revokeToken();
        $this->Session->delete('googleAccessToken');
    }

    protected function loginUrl() {
        $this->Session->write('googleRequestedToken', true);
        $url = $this->vendorController->createAuthUrl();
        $this->controller->redirect($url);
    }

    public function getUser() {
        return $this->user;
    }

    public function loadProfile() {
        if ($this->Session->check('googleAccessToken')) {
            $oauth2 = new Google_Service_Oauth2($this->vendorController);
            try {
                $profile = $oauth2->userinfo->get();
                if (property_exists($profile, 'id')) {
                    return $profile;
                } else {
                    return null;
                }
            } catch(Exception $ex) {
                return null;
            }
        } else {
            return null;
        }
    }

    public function createUser($profile = null) {
        if ($profile == null) {
            $profile = $this->loadProfile();
        }

        $user = ClassRegistry::init('User');
        $user->containLevel("minimum");

        $u = $user->find('first', array(
                'conditions' => array(
                    'auth_params' => $profile->id,
                    'auth_type' => $this->userAuth
                )
            )
        );

        if(!empty($u["User"])) {
            return $u;
        }

        //create the data array
        $res = array();
        $res['User'] = array(
            'userid' => $profile->id,
            'realname' => $profile->name,
            'email' => $profile->email,
            'auth_type' => $this->userAuth,
            'auth_params' => $profile->id
        );

        $groups = array();
        if (!empty($this->params[$this->userAuth]['groups'])) {
            foreach ($this->params[$this->userAuth]['groups'] as $key => $value) {
                array_push($groups, $value);
            }
        }

        $res['Groups'] = $groups;

        //create the BE user
        $this->userGroupModel($res, $groups);
        
        $user->create();
        if(!$user->save($res)) {
            throw new BeditaException(__("Error saving user", true), $user->validationErrors);
        }
 
        $u = $user->findByUserid($res['User']['userid']);
        if(!empty($u["User"])) {
            if (!empty($this->params[$this->userAuth]['createCard']) && $this->params[$this->userAuth]['createCard']) {
                $this->createCard($u, $profile);
            }
            return $u;
        } else {
            return null;
        }
    }

    public function createCard($u, $profile = null) {
        $res = array();
        
        if ($profile == null) {
            $profile = $this->loadProfile();
            if ($profile == null) {
                return false;
            }
        }

        $res = array(
            'title' => $profile->name,
            'name' => $profile->givenName,
            'surname' => $profile->familyName,
            'email' => $profile->email,
            'gender' => $profile->gender,
            'avatar' => $profile->picture
        );

        $card = ClassRegistry::init("ObjectUser")->find("first", array(
            "conditions" => array("user_id" => $u['User']['id'], "switch" => "card" )
        ));

        $data = array(
            "title" => "",
            "name" => "",
            "surname" => "",
            "birthdate" => "",
            "person_title" => "",
            "gender" => "",
            "status" => "on",
            "ObjectUser" =>  array(
                    "card" => array(
                        0 => array(
                            "user_id" => $u['User']['id']
                        )
                    )
                )
        );

        $data = array_merge($data, $res);

        $avatarId = null;
        if (!empty($data['avatar'])) {
            $avatar = $this->uploadAvatarByUrl($data);
            $avatarId = $avatar->id;
            if ($avatarId) {
                $data['RelatedObject'] = array(
                    'attach' => array()
                );

                $data['RelatedObject']['attach'][$avatarId] = array(
                    'id' => $avatarId
                );
            }
        }

        $this->data = $data;

        $this->Transaction->begin();

        $cardModel = ClassRegistry::init("Card");
        if (!$cardModel->save($this->data)) {
            throw new BeditaRuntimeException(__("Error saving user data", true), $cardModel->validationErrors);
        }

        $this->Transaction->commit();
 
        return $cardModel;
    }

    protected function getCurrentUrl() {
        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1)
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $protocol = 'https://';
        } else {
            $protocol = 'http://';
        }
        $currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $parts = parse_url($currentUrl);

        // use port if non default
        $port = isset($parts['port']) && (($protocol === 'http://' && $parts['port'] !== 80) || ($protocol === 'https://' && $parts['port'] !== 443)) ? ':' . $parts['port'] : '';

        // rebuild
        return $protocol . $parts['host'] . $port . $parts['path'];
    }

    protected function uploadAvatarByUrl($userData) {
        $this->data = array(
            'title' => $userData['title'] . '\'s avatar',
            'uri' => $userData['avatar'],
            'status' => 'on'
        );
        $this->Transaction->begin();

        $mediaModel = ClassRegistry::init("Image");
        if (!$mediaModel->save($this->data)) {
            throw new BeditaRuntimeException(__("Error saving avatar data", true), $mediaModel->validationErrors);
        }
        $this->Transaction->commit();
        return $mediaModel;
    }
}
?>