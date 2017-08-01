<?php
/*-----8<--------------------------------------------------------------------
 *
* BEdita - a semantic content management framework
*
* Copyright 2013 ChannelWeb Srl, Chialab Srl
*
*------------------------------------------------------------------->8-----
*/

App::import('Component', 'BeAuth');
App::import('Vendor', 'facebook', array('file' => 'facebook' . DS . 'facebook.php'));

/**
 * Facebook User auth component
 */
class BeAuthFacebookComponent extends BeAuthComponent {

    /**
     * Components used by this component.
     *
     * @var array
     */
    public $components = array('Transaction');

    /**
     * Models used by this component.
     *
     * @var array
     */
    public $uses = array('Card', 'Image', 'User');

    /**
     * Code name of this authentication method.
     *
     * @var string
     */
    public $userAuth = 'facebook';

    /**
     * Authentication parameters.
     *
     * @var array
     */
    protected $params = null;

    /**
     * Facebook API controller.
     *
     * @var ???
     */
    protected $vendorController = null;

    /**
     * User ID prefix.
     *
     * @var string
     * @deprecated
     */
    protected $userIdPrefix = 'facebook-';

    /**
     * Related by.
     *
     * @var string
     * @deprecated
     */
    public $relatedBy = 'e-mail';

    /**
     * Array of permissions requested by the app.
     *
     * @var array
     */
    protected $permissions = array('email', 'user_birthday');

    /**
     * Component startup.
     *
     * @param Controller $controller
     * @return bool
     */
    public function startup($controller = null) {
        $this->controller = $controller;
        $this->Session = $controller->Session;

        /** Read external auth parameters. */
        $params = Configure::read('extAuthParams') ?: array();
        if (!isset($params[$this->userAuth]) || !isset($params[$this->userAuth]['keys'])) {
            return false;
        }

        /** Prepare params for this auth method. */
        $this->params = $params[$this->userAuth];
        if (isset($this->params['extraUserData'])) {
            $this->permissions = array_unique(array_merge($this->permissions, $this->params['extraUserData']));
        }

        /** Init vendor controller. */
        $this->vendorController = new Facebook(array(
            'appId'  => $this->params['keys']['appId'],
            'secret' => $this->params['keys']['secret'],
            'cookie' => true,
        ));

        /** Init models. */
        foreach ($this->uses as $model) {
            $this->$model = ClassRegistry::init($model);
        }

        return true;
    }

    /**
     * Check session key.
     *
     * @return bool
     */
    protected function checkSessionKey() {
        /** Load current profile. */
        $profile = $this->loadProfile();
        if (!$profile || !isset($profile['email'])) {
            return false;
        }

        /** Create user. */
        if (!empty($this->params['createUser'])) {
            $this->log("Creating user [{$profile['email']}]...", 'info');
            $this->createUser($profile, !empty($this->params['groups']) ? $this->params['groups'] : array());
        }

        return $this->login();
    }

    /**
     * Perform login action.
     *
     * @return bool
     */
    public function login() {
        if (!isset($this->vendorController)) {
            return;
        }

        $policy = $this->Session->read($this->sessionKey . 'Policy');
        $authGroupName = $this->Session->read($this->sessionKey . 'AuthGroupName');

        /** Get user profile, or redirect to OAuth login. */
        $profile = $this->loadProfile();
        if (!$profile) {
            $this->loginUrl();
            return;
        }

        /** Get user data. */
        $this->User->containLevel('default');
        $userData = $this->User->find('first', array(
            'conditions' => array(
                'auth_params' => $profile['id'],
                'auth_type' => $this->userAuth,
            ),
        ));
        if (empty($userData['User']) && !$this->checkSessionKey()) {
            $this->Session->write('externalLoginRequestFailed', $profile['email']);
            return false;
        }

        return $this->loginPolicy($userData['User']['id'], $userData, $policy, $authGroupName);
    }

    /**
     * Redirect to Facebook login URL.
     */
    protected function loginUrl() {
        $params = array(
            'scope' => $this->permissions,
        );
        if (!empty($this->params['redirectUrl'])) {
            $params['redirect_uri'] = $this->params['redirectUrl'];
        }

        $url = $this->vendorController->getLoginUrl($params);
        $this->controller->redirect($url);
    }

    /**
     * Load a profile.
     *
     * @return array|bool|null
     */
    public function loadProfile() {
        if (!isset($this->vendorController)) {
            return false;
        }

        /** Get vendor ID. */
        $vendorId = $this->vendorController->getUser();
        if (!$vendorId) {
            return false;
        }

        /** Query Facebook API. */
        try {
            $profile = $this->vendorController->api('/me?fields=email,name');
            $photo = $this->vendorController->api('/me/picture', 'GET', array(
                'redirect' => false,
                'height' => '200',
                'type' => 'normal',
                'width' => '200',
            ));
            $profile['avatar'] = $photo;

            return $profile;
        } catch (FacebookApiException $e) {
            return null;
        }
    }

    /**
     * Create a user profile.
     *
     * @param array $profile
     * @param array $groups
     * @param bool $createCard
     * @return array|null
     * @throws BeditaException Throws an exception if profile could not be created.
     */
    public function createUser($profile, $groups = array(), $createCard = false) {
        $this->User->containLevel('default');

        /** Check if user already exists. */
        $userData = $this->User->find('first', array(
            'conditions' => array(
                'auth_params' => $profile['id'],
                'auth_type' => $this->userAuth,
            ),
        ));
        if (!empty($userData['User'])) {
            return $userData;
        }

        /** Prepare data. */
        $data = array(
            'User' => array(
                'userid' => $profile['id'],
                'email' => $profile['email'],
                'realname' => $profile['name'],
                'auth_type' => $this->userAuth,
                'auth_params' => $profile['id'],
            ),
        );

        /** Create the BE user. */
        $this->userGroupModel($data, $groups);
        $this->User->create();
        if(!$this->User->save($data)) {
            throw new BeditaException(__('Error saving user', true), $this->User->validationErrors);
        }

        $userData = $this->User->findByUserid($data['User']['userid']);
        if (empty($userData['User'])) {
            return null;
        }
        if ($createCard) {
            $this->createCard($userData, $profile);
        }

        return $userData;
    }

    /**
     * Create a card for the user.
     *
     * @param array $user
     * @param array|null $profile
     * @return Model
     * @throws BeditaRuntimeException Throws an exception if card could not be created.
     */
    public function createCard($user, $profile = null) {
        /** Prepare data. */
        $profile = $profile ?: $this->loadProfile();
        $this->data = array(
            'title' => $profile['name'],
            'email' => $profile['email'],
            'name' => $profile['first_name'],
            'surname' => $profile['last_name'],
            'birthdate' => $profile['birthday'],
            'gender' => $profile['gender'],
            'person_title' => '',
            'avatar' => $profile['avatar']['data']['url'],
            'status' => 'on',
            'ObjectUser' =>  array(
                'card' => array(
                    0 => array(
                        'user_id' => $user['User']['id']
                    ),
                ),
            ),
        );

        /** Download avatar. */
        if (!empty($this->data['avatar'])) {
            $avatar = $this->uploadAvatarByUrl($this->data);
            if ($avatar->id) {
                $this->data['RelatedObject'] = array(
                    'attach' => array(
                        $avatar->id => array(
                            'id' => $avatar->id,
                        ),
                    ),
                );
            }
        }

        /** Save card. */
        $this->Transaction->begin();
        if (!$this->Card->save($this->data)) {
            throw new BeditaRuntimeException(__('Error saving user data', true), $this->Card->validationErrors);
        }
        $this->Transaction->commit();

        return $this->Card;
    }

    /**
     * Upload user's avatar.
     *
     * @param array $userData
     * @return Model
     * @throws BeditaRuntimeException Throws an exception if image could not be saved.
     */
    protected function uploadAvatarByUrl($userData) {
        /** Prepare data. */
        $this->data = array(
            'title' => "{$userData['title']}'s avatar",
            'uri' => $userData['avatar'],
            'status' => 'on'
        );

        /** Save avatar. */
        $this->Transaction->begin();
        if (!$this->Image->save($this->data)) {
            throw new BeditaRuntimeException(__('Error saving avatar data', true), $this->Image->validationErrors);
        }
        $this->Transaction->commit();

        return $this->Image;
    }
}
