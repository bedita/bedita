<?php
/*-----8<--------------------------------------------------------------------
 *
* BEdita - a semantic content management framework
*
* Copyright 2013 ChannelWeb Srl, Chialab Srl
*
*------------------------------------------------------------------->8-----
*/

App::import('component', 'be_auth');
App::import('Vendor', 'facebook', array('file' => 'facebook' . DS . 'facebook.php'));

/**
 * Facebook User auth component
*/
class BeAuthFacebookComponent extends BeAuthComponent{
    var $components = array('Transaction');
    var $uses = array('Image', 'Card');

    public $userAuth = 'facebook';

    protected $params = null;
    protected $vendorController = null;
    protected $userIdPrefix = 'facebook-';
    public $disabled = false;

    public function startup($controller=null) {
        $this->controller = $controller;
        $this->Session = $controller->Session;

        $this->params = Configure::read("extAuthParams");

        if (isset( $this->params[$this->userAuth] ) && isset( $this->params[$this->userAuth]['keys'] )) {
            $this->vendorController = new Facebook(array(
                'appId'  => $this->params[$this->userAuth]['keys']['appId'],
                'secret' => $this->params[$this->userAuth]['keys']['secret'],
                'cookie' => true
            ));
            return true;
        } else {
            return false;
        }
    }

    protected function checkSessionKey() {
        $profile = $this->loadProfile();
        if ($profile) {
            if (isset($profile['email'])) {
                if (isset($this->params[$this->userAuth]['createUser']) && $this->params[$this->userAuth]['createUser']) {
                    $this->createUser($profile);
                }
                return $this->login();
            }
        }
        return false;
    }

    public function login() {
        $policy = $this->Session->read($this->sessionKey . 'Policy');
        $authGroupName = $this->Session->read($this->sessionKey . 'AuthGroupName');

        if (!isset( $this->vendorController )) {
            return;
        }

        //get the user
        $profile = $this->loadProfile();
        if ($profile) {
            $user = ClassRegistry::init('User');
            $user->containLevel("default");
            $u = $user->find('first', array(
                    'conditions' => array(
                        'auth_params' => $profile['id'],
                        'auth_type' => $this->userAuth
                    )
                )
            );

            if (empty($u['User'])) {
                return false;
            }

            $userid = $u['User']['id'];
            if(!$this->loginPolicy($userid, $u, $policy, $authGroupName)) {
                return false ;
            }
            return true;
        } else {            
            $this->loginUrl();
        }
    }

    protected function loginUrl() {
        $params = array(
            'scope' => $this->params[$this->userAuth]['permissions']
        );
        $url = $this->vendorController->getLoginUrl($params);
        $this->controller->redirect($url);
    }

    public function loadProfile() {
         if (isset( $this->vendorController )) {
            $vendorId = $this->vendorController->getUser();
            if ($vendorId) {
                try {
                    $profile = $this->vendorController->api('/me');
                    $photo = $this->vendorController->api(
                        '/me/picture',
                        "GET",
                        array(
                            'redirect' => false,
                            'height' => '200',
                            'type' => 'normal',
                            'width' => '200',
                        )
                    );
                    $profile['avatar'] = $photo;
                    return $profile;
                } catch (FacebookApiException $e) {
                    return null;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }   
    }

    public function createUser($profile) {
        $user = ClassRegistry::init('User');
        $user->containLevel("default");

        $u = $user->find('first', array(
                'conditions' => array(
                    'auth_params' => $profile['id'],
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
            'userid' => $profile['id'],
            'email' => $profile['email'],
            'realname' => $profile['name'],
            'auth_type' => $this->userAuth,
            'auth_params' => $profile['id']
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
                $this->createCard($u);
            }
            return $u;
        } else {
            return null;
        }
    }

    public function createCard($u) {
        $res = array();
        $profile = $this->loadProfile();
       
        $res = array(
            'title' => $profile['name'],
            'email' => $profile['email'],
            'name' => $profile['first_name'],
            'surname' => $profile['last_name'],
            'birthdate' => $profile['birthday'],
            'gender' => $profile['gender'],
            'avatar' => $profile['avatar']['data']['url']
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

        $this->Transaction->begin();

        $this->data = $data;

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