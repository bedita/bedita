<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2015 ChannelWeb Srl, Chialab Srl
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
 *------------------------------------------------------------------->8-----
 */

App::import('Vendor', 'BeforeValidException', array('file' => 'php-jwt' . DS . 'Exceptions' . DS . 'BeforeValidException.php'));
App::import('Vendor', 'ExpiredException', array('file' => 'php-jwt' . DS . 'Exceptions' . DS . 'ExpiredException.php'));
App::import('Vendor', 'SignatureInvalidException', array('file' => 'php-jwt' . DS . 'Exceptions' . DS . 'SignatureInvalidException.php'));
App::import('Vendor', 'JWT', array('file' => 'php-jwt' . DS . 'Authentication' . DS . 'JWT.php'));


/**
 * JSON Web Token (JWT) auth component
*/
class BeAuthJwtComponent extends Object {

    /**
     * The Controller
     *
     * @var Controller
     */
    protected $controller;

    /**
     * The authenticate user
     * It's false if user is not authenticated
     *
     * @var array|bool
     */
    private $user = false;

    /**
     * The JWT generated or read from request
     *
     * @var string
     */
    private $token = null;

    /**
     * The payload used token generation
     *
     * @var array
     */
    private $payload = array();

    /**
     * Configuration used to customize token generation
     *
     * @var array
     */
    public $config = array(
        'expiresIn' => 600, // in seconds (10 minutes)
        'alg' => 'HS256'
    );

    /**
     * Initialize component callback
     *
     * @param Controller $controller the controller
     * @param array $settings component configuration
     * @return void
     */
    public function initialize(Controller $controller, $settings = array()) {
        Configure::write('Session.start', false);
        $this->controller = $controller;
        $this->token = null;
        $jwtConf = Configure::read('api.JWT');
        if ($jwtConf) {
            $this->config = $jwtConf + $this->config;
        }
    }

    /**
     * Startup component callback
     * Set up JWT 'iss' to public_url if it's not set
     *
     * @param Controller $controller the controller
     * @param array $settings component configuration
     * @return void
     */
    public function startup($controller , $settings = array()) {
        if (empty($this->config['iss'])) {
            $this->config['iss'] = $controller->viewVars['publication']['public_url'];
        }
    }

    /**
     * Return true if user is authenticated through JWT
     * Used from FrontendController, it replaces BeAuthComponent::isLogged() in api context
     *
     * @return bool
     */
    public function isLogged() {
        return $this->identify();
    }

    /**
     * Identify and return an user starting from JWT
     * If user was already identified return it immediately
     * Return false if no token exists or no user found
     *
     * @return array|bool
     */
    public function identify() {
        if (!empty($this->user)) {
            return $this->user;
        }
        $this->user = false;
        $token = $this->getToken();
        if ($token) {
            $this->user = $this->findUser($token);
        }
        return $this->user;
    }

    /**
     * Login user, it replaces BeAuthComponent::login() in api context
     *
     * @param string $userid the username
     * @param string $password the user password
     * @param array $policy unused in api context
     * @param array $authGroupName an array of groups authorized to login
     * @return bool
     */
    public function login($userid, $password, $policy = null, array $authGroupName = array()) {
        $userModel = ClassRegistry::init('User');
        $conditions = array(
            'User.userid' => $userid,
            'User.passwd' => md5($password),
            'User.valid' => 1
        );
        $userModel->containLevel('default');
        $u = $userModel->find($conditions);
        if (!$u) {
            return false;
        }

        // check group auth
        $authorized = false;
        foreach ($u['Group'] as $g) {
            // check backend_auth???
            if ($g['backend_auth'] == 1 || in_array($g['name'], $authGroupName)) {
                $authorized = true;
                break;
            }
        }

        if ($authorized === false) {
            $this->log('User login not authorized: ' . $userid);
            return false;
        }

        $userModel->compact($u);

        $this->user = $u;
        return true;
    }

    /**
     * Generate and return a new JWT
     * If user is not identified in return null
     *
     * @return string|null
     */
    public function generateToken() {
        $this->token = null;
        if (!empty($this->user)) {
            $iat = time();
            $this->payload = array(
                'iss' => $this->config['iss'],
                'iat' => $iat,
                //'jti' => uniqid(), // a unique id for this token (for revocation purposes, it should be blacklisted)
                'exp' => $iat + $this->config['expiresIn'],
                'id' => $this->user['id']
            );

            $this->token = JWT::encode($this->payload, Configure::read('Security.salt'), $this->config['alg']);
        }
        return $this->token;
    }

    /**
     * Renew a JWT using a refresh token
     * If it fails to renew JWT then return false
     *
     * @param string $refreshToken the refresh token
     * @return string|bool
     */
    public function renewToken($refreshToken) {
        $token = false;
        // @todo missing to check against groups permissions to verify nothing was changed
        $this->user = $this->findUser($refreshToken, 'refresh');
        if ($this->user) {
            $token = $this->generateToken();
        }
        return $token;
    }

    /**
     * Generate a refresh token to use for renew JWT
     * The refresh token is saved in hash_jobs table
     * If user is not identified then return false
     *
     * @return string|bool
     */
    public function generateRefreshToken() {
        if (!$this->user) {
            return false;
        }

        $refreshToken = Security::generateAuthKey();
        $hashJob = ClassRegistry::init('HashJob');
        $data = array(
            'service_type' => 'refresh_token',
            'user_id' => $this->user['id'],
            'hash' => $refreshToken,
            'expired' => '2050-01-01',
            'used_for' => 'JWT'
        );
        if (!$hashJob->save($data)) {
            return false;
        }
        return $refreshToken;
    }

    /**
     * Revoke a refresh token
     *
     * @param string $refreshToken the rfresh token to remove
     * @return bool
     */
    public function revokeRefreshToken($refreshToken) {
        if (!$this->user) {
            return false;
        }

        $hashJob = ClassRegistry::init('HashJob');
        $hashId = $hashJob->field('id', array(
            'service_type' => 'refresh_token',
            'hash' => $refreshToken,
            'user_id' => $this->user['id']
        ));
        if (!$hashId) {
            throw new BeditaNotFoundException('missing token or already removed');
        }

        return $hashJob->delete($hashId);
    }

    /**
     * Return the payload used to generate JWT
     *
     * @return array
     */
    public function getPayload() {
        return $this->payload;
    }

    /**
     * Return the token reading from Authorization header or from query url
     * If token is been already read return it
     * Return false if no token is found
     *
     * @return string|bool
     */
    public function getToken() {
        if (!empty($this->token)) {
            return $this->token;
        }

        $token = env('HTTP_AUTHORIZATION');

        if (!$token && function_exists('getallheaders')) {
            $headers = getallheaders();
            if (isset($headers['Authorization']) &&
                substr($headers['Authorization'], 0, 7) === 'Bearer '
            ) {
                $token = $headers['Authorization'];
            }
        }

        if ($token) {
            $this->token = substr($token, 7);
        } elseif (!empty($this->params['url']['access_token'])) {
            $this->token = $this->params['url']['access_token'];
        }

        return $this->token ? $this->token : false;
    }

    /**
     * Return the updated time to token expiration (in seconds)
     *
     * @return int
     */
    public function expiresIn() {
        return (!empty($this->payload)) ? $this->payload['exp'] - time() : 0;
    }

    /**
     * Find the user starting from a token and a token type:
     *
     * - if $type is 'jwt' try to get user starting from JWT
     * - if $type is 'refresh' try to get user starting from refresh token saved in hash_jobs table
     *
     * If no user was found return false
     *
     * @param string $token the token
     * @param string $type the token type ('jwt' or 'refresh')
     * @return array|bool
     */
    protected function findUser($token, $type = 'jwt') {
        if ($type == 'jwt') {
            try {
                $salt = Configure::read('Security.salt');
                $decodedToken = JWT::decode($token, $salt, array($this->config['alg']));
            } catch (ExpiredException  $e) {
                throw new BeditaUnauthorizedException('token_expired');
            } catch (Exception $e) {
                throw new BeditaUnauthorizedException('token_not_valid');
            }

            // Token missing data
            if (!isset($decodedToken->id)) {
                throw new BeditaUnauthorizedException('token_not_valid');
            }

            $userId = $decodedToken->id;
            $this->payload = (array) $decodedToken;
        // refresh
        } elseif ($type == 'refresh') {
            $hashJob = ClassRegistry::init('HashJob');
            $hash = $hashJob->find('first', array(
                'conditions' => array(
                    'hash' => $token,
                    'NOT' => array(
                        'status' => 'expired'
                    )
                )
            ));

            if (empty($hash)) {
                return false;
            }
            $userId = $hash['HashJob']['user_id'];
        } else {
            return false;
        }

        $userModel = ClassRegistry::init('User');
        $userModel->containLevel('default');
        $user = $userModel->find('first', array(
            'conditions' => array(
                'User.id' => $userId,
                'User.valid' => 1
            )
        ));

        if (empty($user)) {
            return false;
        }

        $userModel->compact($user);
        return $user;
    }

    /**
     * Return the userid
     * It replaces BeAuthComponent::userid() in api context
     *
     * @return string
     */
    public function userid() {
        return !empty($this->user['userid']) ? $this->user['userid'] : '';
    }

    /**
     * Return the user data
     * it replaces BeAuthComponent::getUserSession() in api context
     *
     * @return array
     */
    public function getUserSession() {
        return $this->user ? $this->user : array();
    }

}
