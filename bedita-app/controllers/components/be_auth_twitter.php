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
        foreach ($this->components as $component) {
            if(isset($this->{$component})) continue;
            $className = $component . 'Component' ;
            if(!class_exists($className))
                App::import('Component', $component);
            $this->{$component} = new $className() ;
        }

        $this->controller = &$controller;
        $this->Session = &$controller->Session;
        if ($this->Session->check('twitter.oauthTokens')) {
            $this->oauthTokens = $this->Session->read('twitter.oauthTokens');
        }
        if ($this->Session->check('twitter.accessTokens')) {
            $this->accessTokens = $this->Session->read('twitter.accessTokens');
        }

        $this->params = Configure::read("extAuthParams");

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
                $this->Session->write('twitter.accessTokens', $this->accessTokens);
            }

            $profile = $this->loadProfile();
            if ($profile) {
                $be_user_object = $this->createUser($profile);
                if ($this->login( $be_user_object['User']['id'] )) {
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

    public function login($userid = null, $policy = null, $auth_group_name = array()) {        
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
            $this->loginUrl($url);
        }
    }

    protected function loginUrl() {
        $request_token = $this->vendorController->getRequestToken('http://www.beexample.lcl');
        $this->oauthTokens = $request_token;
        $this->Session->write('twitter.oauthTokens', $this->oauthTokens);
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
            "email" => $u['User']['userid'],
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

        print_r($data);
        exit();

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