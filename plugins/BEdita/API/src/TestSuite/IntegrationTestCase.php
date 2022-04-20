<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\TestSuite;

use BEdita\API\Event\CommonEventHandler;
use BEdita\Core\State\CurrentApplication;
use BEdita\Core\Utility\LoggedUser;
use Cake\Event\EventManager;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Mailer\TransportFactory;
use Cake\ORM\TableRegistry;
use Cake\Routing\RouteCollection;
use Cake\Routing\Router;
use Cake\TestSuite\IntegrationTestCase as CakeIntegrationTestCase;
use Cake\TestSuite\MiddlewareDispatcher;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

/**
 * Base class for API integration tests.
 *
 * @since 4.0.0
 */
abstract class IntegrationTestCase extends CakeIntegrationTestCase
{
    use ArraySubsetAsserts;

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
        'plugin.BEdita/Core.Config',
        'plugin.BEdita/Core.AsyncJobs',
        'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.ExternalAuth',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.Endpoints',
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.EndpointPermissions',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Translations',
        'plugin.BEdita/Core.UserTokens',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
        'plugin.BEdita/Core.Tags',
        'plugin.BEdita/Core.ObjectTags',
        'plugin.BEdita/Core.History',
    ];

    /**
     * Default user used for authentication
     *
     * @var array
     */
    protected $defaultUser = [
        'username' => 'first user',
        'password' => 'password1',
    ];

    /**
     * @inheritDoc
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->addAuthFixtures();
        parent::__construct($name, $data, $dataName);
    }

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        LoggedUser::resetUser();
        CurrentApplication::setFromApiKey(API_KEY);

        TransportFactory::drop('default');
        TransportFactory::setConfig('default', [
            'className' => 'Debug',
        ]);

        EventManager::instance()->on(new CommonEventHandler());
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
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
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _makeDispatcher(): MiddlewareDispatcher
    {
        Router::setRouteCollection(new RouteCollection());

        return parent::_makeDispatcher();
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
            'X-Api-Key' => API_KEY,
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
        return TableRegistry::getTableLocator()->get('Objects')
            ->find()
            ->select('id')
            ->order(['id' => 'DESC'])
            ->first()
            ->id;
    }
}
