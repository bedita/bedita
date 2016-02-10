<?php
/*-----8<--------------------------------------------------------------------
 *
* BEdita - a semantic content management framework
*
* Copyright 2013 ChannelWeb Srl, Chialab Srl
*
*------------------------------------------------------------------->8-----
*/

set_include_path(CAKE_CORE_INCLUDE_PATH . DS . 'vendors' . DS . 'google' . DS . 'src' . PATH_SEPARATOR . get_include_path());
App::import('Component', 'BeAuth');
App::import('Vendor', 'Google_Client', array('file' => 'google' . DS . 'src' . DS . 'Google' . DS . 'Client.php'));
App::import('Vendor', 'Google_Service_Oauth2', array('file' => 'google' . DS . 'src' . DS . 'Google' . DS . 'Service' . DS . 'Oauth2.php'));

/**
 * Google User auth component
 */
class BeAuthGoogleComponent extends BeAuthComponent {

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
    public $userAuth = 'google';

    /**
     * Authentication parameters.
     *
     * @var array
     */
    protected $params = null;

    /**
     * Google API controller.
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
    protected $userIdPrefix = 'google-';

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
    protected $permissions = array('email', 'profile');

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

        /** Get redirect URL. */
        $redirectUrl = $this->getCurrentUrl();
        if (!empty($this->params['redirectUrl'])) {
            $redirectUrl = $this->params['redirectUrl'];
        }

        /** Init vendor controller. */
        $this->vendorController = new Google_Client();
        $this->vendorController->setClientId($this->params['keys']['appId']);
        $this->vendorController->setClientSecret($this->params['keys']['secret']);
        $this->vendorController->setRedirectUri($redirectUrl);

        foreach ($this->permissions as $scope) {
           $this->vendorController->addScope($scope);
        }

        if (isset($_GET['code']) && !$this->Session->check('googleAccessToken') && $this->Session->check('googleRequestedToken')) {
            $this->vendorController->authenticate($_GET['code']);
            $this->Session->write('googleAccessToken', $this->vendorController->getAccessToken());
        }

        if ($this->Session->check('googleAccessToken')) {
            $this->vendorController->setAccessToken($this->Session->read('googleAccessToken'));
        } else {
            $this->vendorController->setRedirectUri($redirectUrl);
        }

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
        if (!isset($this->vendorController) || !($profile = $this->loadProfile())) {
            $this->Session->delete('googleAccessToken');
            $this->Session->delete('googleRequestedToken');
            return false;
        }

        /** Create user. */
        if (!empty($this->params['createUser'])) {
            $this->log("Creating user [{$profile->email}]...", 'info');
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
        if (!$this->Session->check('googleAccessToken') || !($profile = $this->loadProfile()) || !$profile->id) {
            $this->loginUrl();
            return null;
        }

        /** Get user data. */
        $this->User->containLevel('default');
        $userData = $this->User->find('first', array(
            'conditions' => array(
                'auth_params' => $profile->id,
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
     * Redirect to Google login URL.
     */
    protected function loginUrl() {
        $this->Session->write('googleRequestedToken', true);
        $url = $this->vendorController->createAuthUrl();
        $this->controller->redirect($url);
    }

    /**
     * Logout.
     */
    public function logout() {
        $this->vendorController->revokeToken();
        $this->Session->delete('googleAccessToken');
    }

    /**
     * Load a profile.
     *
     * @return array|bool|null
     */
    public function loadProfile() {
        if (!$this->Session->check('googleAccessToken')) {
            return null;
        }

        $oauth2 = new Google_Service_Oauth2($this->vendorController);
        try {
            $profile = $oauth2->userinfo->get();
            return !empty($profile->id) ? $profile : null;
        } catch (Exception $e) {
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
    public function createUser($profile = null, $groups = array(), $createCard = false) {
        $this->User->containLevel('minimum');

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
                'userid' => $profile->id,
                'email' => $profile->email,
                'realname' => $profile->name,
                'auth_type' => $this->userAuth,
                'auth_params' => $profile->id,
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
            'title' => $profile->name,
            'email' => $profile->email,
            'name' => $profile->givenName,
            'surname' => $profile->familyName,
            'birthdate' => '',
            'gender' => $profile->gender,
            'person_title' => '',
            'avatar' => $profile->picture,
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

    /**
     * Get current URL.
     *
     * @return string
     */
    protected function getCurrentUrl() {
        $isSecure = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1);
        $isSecure = $isSecure || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https';
        $protocol = $isSecure ? 'https://' : 'http://';

        $parts = parse_url($protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);

        $isDefaultPort = !isset($parts['port']) || (!$isSecure && $parts['port'] == 80) || ($isSecure && $parts['port'] == 443);
        $port = !$isDefaultPort ? ':' . $parts['port'] : '';

        return $protocol . $parts['host'] . $port . $parts['path'];
    }
}
