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
App::import('Vendor', 'twitteroauth', array('file' => 'twitteroauth' . DS . 'twitteroauth.php'));

/**
 * Twitter User auth component
 */
class BeAuthTwitterComponent extends BeAuthComponent{

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
    public $userAuth = 'twitter';

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
    protected $userIdPrefix = 'twitter-';

    /**
     * Related by.
     *
     * @var string
     * @deprecated
     */
    public $relatedBy = 'nickname';

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

        /** Init vendor controller. */
        $this->vendorController = new TwitterOAuth(
            $this->params['keys']['appId'],
            $this->params['keys']['secret']
        );

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
        if (!isset($this->vendorController)) {
            return false;
        }

        if (!empty($_REQUEST['oauth_verifier']) && $this->Session->check('twitterOauthTokens')) {
            $oauthTokens = $this->Session->read('twitterOauthTokens');
            if (!isset($oauthTokens['oauth_token'])) {
                $this->Session->delete('twitterOauthTokens');
                return false;
            }

            $this->vendorController = new TwitterOAuth(
                $this->params['keys']['appId'],
                $this->params['keys']['secret'],
                $oauthTokens['oauth_token'],
                $oauthTokens['oauth_token_secret']
            );

            $accessTokens = @$this->vendorController->getAccessToken($_REQUEST['oauth_verifier']);
            $this->Session->write('twitterAccessTokens', $accessTokens);
        }

        $profile = $this->loadProfile();
        if (!$profile) {
            return false;
        }

        if (!empty($this->params['createUser'])) {
            $this->log("Creating user [{$profile->screen_name}]...", 'info');
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
        $userid = null;

        /** Get user profile, or redirect to OAuth login. */
        if (!$this->Session->check('twitterProfile') || !($profile = $this->loadProfile())) {
            $this->loginUrl();
            return null;
        }

        /** Get user data. */
        $this->User->containLevel('default');
        $userData = $this->User->find('first', array(
            'conditions' => array(
                'auth_params' => $profile->screen_name,
                'auth_type' => $this->userAuth,
            ),
        ));

        if (empty($userData['User'])) {
            $this->Session->write('externalLoginRequestFailed', $profile->screen_name);
            return false;
        }

        return $this->loginPolicy($userData['User']['userid'], $userData, $policy, $authGroupName);
    }

    /**
     * Redirect to Twitter login URL.
     */
    protected function loginUrl() {
        /** Get redirect URL. */
        $redirectUrl = $this->getCurrentUrl();
        if (!empty($this->params['redirectUrl'])) {
            $redirectUrl = $this->params['redirectUrl'];
        }

        $requestToken = $this->vendorController->getRequestToken($redirectUrl);
        $this->Session->write('twitterOauthTokens', $requestToken);
        $url = $this->vendorController->getAuthorizeURL($requestToken);
        $this->controller->redirect($url);
    }

    /**
     * Logout.
     */
    public function logout() {
        $this->Session->delete('twitterAccessTokens');
        $this->Session->delete('twitterOauthTokens');
        $this->Session->delete('twitterProfile');
    }

    /**
     * Load a profile.
     *
     * @return array|null
     */
    public function loadProfile() {
        if ($this->Session->check('twitterProfile')) {
            return $this->Session->read('twitterProfile');
        }
        if (!$this->Session->check('twitterAccessTokens')) {
            return null;
        }

        $accessTokens = $this->Session->read('twitterAccessTokens');
        if (empty($accessTokens['oauth_token'])) {
            return null;
        }

        $this->vendorController = new TwitterOAuth(
            $this->params['keys']['appId'],
            $this->params['keys']['secret'],
            $accessTokens['oauth_token'],
            $accessTokens['oauth_token_secret']
        );

        $profile = $this->vendorController->get('account/verify_credentials');
        if (empty($profile->id)) {
            return null;
        }

        $this->Session->write('twitterProfile', $profile);

        return $profile;
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
                'auth_params' => $profile->screen_name,
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
                'realname' => $profile->name,
                'auth_type' => $this->userAuth,
                'auth_params' => $profile->screen_name,
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
            'email' => '',
            'name' => '',
            'surname' => '',
            'birthdate' => '',
            'gender' => '',
            'person_title' => '',
            'avatar' => $profile->profile_image_url,
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
