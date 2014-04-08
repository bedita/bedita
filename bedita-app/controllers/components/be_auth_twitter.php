<?php
/*-----8<--------------------------------------------------------------------
 *
* BEdita - a semantic content management framework
*
* Copyright 2013 ChannelWeb Srl, Chialab Srl
*
*------------------------------------------------------------------->8-----
*/

if(class_exists('BeAuthComponent') != true) {
    require(BEDITA_CORE_PATH . DS . "controllers" . DS . 'components' . DS . 'be_auth.php');
}

if(class_exists('tmhOAuth') != true) {
    require(BEDITA_CORE_PATH . DS . "vendors" . DS . 'twitteroauth' . DS . 'twitteroauth.php');
}

/**
 * Facebook User auth component
*/
class BeAuthTwitterComponent extends BeAuthComponent{
    var $components = array('Transaction');
    var $uses = array('Image', 'Card');

    public $userAuth = 'twitter';

    protected $params = null;
    protected $vendorController = null;
    protected $oauthTokens = null;
    protected $accessTokens = null;
    protected $userIdPrefix = 'twitter-';

    function __construct(&$controller=null) {
        $this->loadComponents();
        $this->controller = &$controller;
        $this->Session = &$controller->Session;

        $this->params = Configure::read("extAuthParams");

        if ($this->Session->check('twitterOauthTokens')) {
            $this->oauthTokens = $this->Session->read('twitterOauthTokens');
        }
        if ($this->Session->check('twitterAccessTokens')) {
            $this->accessTokens = $this->Session->read('twitterAccessTokens');
        }

        if (isset( $this->params['twitter'] ) && isset( $this->params['twitter']['kies'] )) {
            $this->vendorController = new TwitterOAuth(
                    $this->params['twitter']['kies']['consumerKey'],
                    $this->params['twitter']['kies']['consumerSecret']
                );
        }

        if($this->checkSessionKey()) {
            $this->user = $this->Session->read($this->sessionKey);
        }
        
        $this->controller->set($this->sessionKey, $this->user);
    }

    protected function checkSessionKey() {
        if (isset( $this->vendorController )) {
            if(!empty($_REQUEST['oauth_verifier'])) {

                $this->vendorController = new TwitterOAuth(
                        $this->params['twitter']['kies']['consumerKey'],
                        $this->params['twitter']['kies']['consumerSecret'],
                        $this->oauthTokens['oauth_token'],
                        $this->oauthTokens['oauth_token_secret']
                    );

                $this->accessTokens = $this->vendorController->getAccessToken($_REQUEST['oauth_verifier']);
                $this->Session->write('twitterAccessTokens', $this->accessTokens);
            }

            $profile = $this->loadProfile();
            if ($profile) {
                $be_user_object = $this->createUser($profile);
                if ($this->login()) {
                    return true;
                }
            } else {
                $this->log("Twitter login failed");
                return false;
            }
            return false;
        } else {
            return false;
        }
    }

    public function login() {
        $policy = $this->Session->read($this->sessionKey . 'Policy');
        $auth_group_name = $this->Session->read($this->sessionKey . 'AuthGroupName');
        $userid = null;

        if (!isset( $this->vendorController )) {
            return;
        }

        if ($this->accessTokens) {
            $profile = $this->loadProfile();
            $userid = $this->userIdPrefix . $profile->id;
        }

        //get the user
        if ($userid) {
            //BE user
            $user = ClassRegistry::init('User');
            $user->containLevel("default");
            $u = $user->findByUserid($userid);
            if(!$this->loginPolicy($userid, $u, $policy, $auth_group_name)) {
                return false;
            }
            return true;

        } else { 
            //get tokens
            $this->loginUrl();
        }
    }

    public function logout() {
        $this->Session->write('twitterAccessTokens', null);
        $this->Session->write('twitterOauthTokens', null);
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

        $query = '';
        if (!empty($parts['query'])) {
                // drop known fb params
                $params = explode('&', $parts['query']);
                $retained_params = array();
                foreach ($params as $param) {
                if ($this->shouldRetainParam($param)) {
                  $retained_params[] = $param;
                }
            }

            if (!empty($retained_params)) {
                $query = '?'.implode($retained_params, '&');
            }
        }

        // use port if non default
        $port = isset($parts['port']) && (($protocol === 'http://' && $parts['port'] !== 80) || ($protocol === 'https://' && $parts['port'] !== 443)) ? ':' . $parts['port'] : '';

        // rebuild
        return $protocol . $parts['host'] . $port . $parts['path'] . $query;
  }

    protected function loginUrl() {
        $request_token = $this->vendorController->getRequestToken($this->getCurrentUrl());
        $this->oauthTokens = $request_token;
        $this->Session->write('twitterOauthTokens', $this->oauthTokens);
        $url = $this->vendorController->getAuthorizeURL($request_token);
        $this->controller->redirect($url);
    }

    public function getUser() {
        return $this->user;
    }

    public function loadProfile() {
        if (!empty($this->accessTokens['oauth_token'])) {
            $this->vendorController = new TwitterOAuth(
                    $this->params['twitter']['kies']['consumerKey'],
                    $this->params['twitter']['kies']['consumerSecret'],
                    $this->accessTokens['oauth_token'],
                    $this->accessTokens['oauth_token_secret']
                );
            $profile = $this->vendorController->get('account/verify_credentials');
            if (property_exists($profile, 'id')) {
                return $profile;
            } else {
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

        //create the data array
        $res = array();
        $res['User'] = array(
            'userid' => $this->userIdPrefix . $profile->id,
            'realname' => $profile->name,
            'auth_type' => 'twitter',
            'auth_params' => array(
                'userid' => $profile->id
            )
        );

        $groups = array();
        if (!empty($this->params['twitter']['groups'])) {
            foreach ($this->params['twitter']['groups'] as $key => $value) {
                array_push($groups, $value);
            }
        }

        $res['Groups'] = $groups;

        //create the BE user
        $user = ClassRegistry::init('User');
        $user->containLevel("minimum");
        $u = $user->findByUserid($res['User']['userid']);
        if(!empty($u["User"])) {
            return $u;
        }

        $this->userGroupModel($res, $groups);
        
        $user->create();
        if(!$user->save($res)) {
            throw new BeditaException(__("Error saving user", true), $user->validationErrors);
        }
 
        $u = $user->findByUserid($res['User']['userid']);
        if(!empty($u["User"])) {
            if (!empty($this->params['twitter']['createCard']) && $this->params['twitter']['createCard']) {
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
            'avatar' => $profile->profile_image_url
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