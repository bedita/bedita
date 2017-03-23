<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\TestSuite;

use BEdita\Core\State\CurrentApplication;
use BEdita\Core\Utility\LoggedUser;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Network\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestCase as CakeIntegrationTestCase;

/**
 * Base class for API integration tests.
 *
 * @since 4.0.0
 */
abstract class ApiIntegrationTestCase extends CakeIntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [];

    /**
     * The required fixtures for authentication.
     * They are added to fixtures present in test case class
     *
     * @var array
     */
    protected $authFixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.roles_users',
        'plugin.BEdita/Core.endpoints',
        'plugin.BEdita/Core.applications',
        'plugin.BEdita/Core.endpoint_permissions',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
    ];

    /**
     * Default user used for authentication
     *
     * @var array
     */
    protected $defaultUser = [
        'username' => 'first user',
        'password' => 'password1'
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->addAuthFixtures();
        parent::__construct($name, $data, $dataName);
    }

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        LoggedUser::resetUser();
        CurrentApplication::setFromApiKey(API_KEY);

        EventManager::instance()->on('Auth.afterIdentify', function (Event $event, array $user) {
            LoggedUser::setUser($user);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        LoggedUser::resetUser();
        CurrentApplication::getInstance()->set(null);
    }

    /**
     * Add fixtures required for authentication
     *
     * @return void
     */
    protected function addAuthFixtures()
    {
        $this->fixtures = array_unique(array_merge($this->authFixtures, $this->fixtures));
    }

    /**
     * Return the Authorization header starting from a username and a password.
     *
     * @param string $username The user username
     * @param string $password The user password
     * @return array
     */
    public function getUserAuthHeader($username = null, $password = null)
    {
        $tokens = $this->authUser($username, $password);

        return ['Authorization' => 'Bearer ' . $tokens['jwt']];
    }

    /**
     * Authenticate an user and return the JWT access and renewal tokens obtained.
     *
     * This method is also useful to set user in `LoggedUser` singleton.
     *
     * It calls `POST /auth` to receive a valid jwt access token.
     * If `$username` and `$password` are empty then a default user is used.
     * To avoid conflicts with another request potentially prepared, for example calling `self::configRequest()`,
     * the current request conf is saved and restored at the end of the /auth call.
     *
     * @param string $username The user username
     * @param string $password The user password
     * @return array
     */
    public function authUser($username = null, $password = null)
    {
        $fullBaseUrl = Router::fullBaseUrl();
        $prevRequest = $this->_request;

        $this->_request = [];
        $this->configRequestHeaders('POST', [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);
        $username = $username ?: $this->defaultUser['username'];
        $password = $password ?: $this->defaultUser['password'];
        $this->post('/auth', [
            'username' => $username,
            'password' => $password,
        ]);

        Router::fullBaseUrl($fullBaseUrl);

        if ($this->_response->getStatusCode() !== 200) {
            $msg = 'User is not authorized.';
            $body = json_decode((string)$this->_response->getBody(), true);
            if (!empty($body['error'])) {
                $msg .= sprintf(' Status: %s. Error: %s', $body['error']['status'], $body['error']['title']);
            }
            throw new UnauthorizedException($msg);
        }

        $body = json_decode((string)$this->_response->getBody(), true);

        $this->_request = $prevRequest;
        $this->_response = null;

        return $body['meta'];
    }

    /**
     * Setup request headers.
     *
     * Defaults:
     *   'Host' => 'api.example.com',
     *   'Accept' => 'application/vnd.api+json',
     *   'Content-Type' => 'application/vnd.api+json' (POST, PATCH, DELETE methods)
     *
     * @param string $method HTTP method
     * @param array $options Header content options
     * @return void
     */
    public function configRequestHeaders($method = 'GET', array $options = [])
    {
        $headers = [
            'Host' => 'api.example.com',
            'Accept' => 'application/vnd.api+json',
        ];

        if (in_array($method, ['POST', 'PATCH', 'DELETE'])) {
            $headers['Content-Type'] = 'application/vnd.api+json';
        }

        $headers = array_merge($headers, $options);
        $this->configRequest(compact('headers'));
    }

    /**
     * Return last Object ID
     *
     * @return int
     * @codeCoverageIgnore
     */
    public function lastObjectId()
    {
        return TableRegistry::get('Objects')
            ->find()
            ->select('id')
            ->order(['id' => 'DESC'])
            ->first()
            ->id;
    }
}
