<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Test\TestCase\Auth;

use BEdita\API\Auth\JwtAuthenticate;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Network\Request;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Security;
use Firebase\JWT\JWT;

/**
 * @coversDefaultClass \BEdita\API\Auth\JwtAuthenticate
 */
class JwtAuthenticateTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.roles_users',
    ];

    /**
     * Data provider for `testGetToken` test case.
     *
     * @return array
     */
    public function getTokenProvider()
    {
        return [
            'header' => [
                'myToken',
                [],
                new Request([
                    'environment' => ['HTTP_AUTHORIZATION' => 'Bearer myToken'],
                ]),
            ],
            'headerCustom' => [
                'myToken',
                [
                    'header' => 'X-Api-Jwt',
                ],
                new Request([
                    'environment' => ['HTTP_X_API_JWT' => 'Bearer myToken'],
                ]),
            ],
            'headerCustomPrefix' => [
                'myToken',
                [
                    'headerPrefix' => 'MyBearer',
                ],
                new Request([
                    'environment' => ['HTTP_AUTHORIZATION' => 'MyBearer myToken'],
                ]),
            ],
            'headerWrongPrefix' => [
                null,
                [],
                new Request([
                    'environment' => ['HTTP_AUTHORIZATION' => 'WrongBearer myToken'],
                ]),
            ],
            'query' => [
                'myToken',
                [],
                new Request([
                    'query' => ['token' => 'myToken'],
                ]),
            ],
            'queryCustom' => [
                'myToken',
                [
                    'queryParam' => 'token_jwt',
                ],
                new Request([
                    'query' => ['token_jwt' => 'myToken'],
                ]),
            ],
            'queryDisallowed' => [
                null,
                [
                    'queryParam' => null,
                ],
                new Request([
                    'query' => ['token' => 'myToken'],
                ]),
            ],
            'both' => [
                'myToken',
                [],
                new Request([
                    'environment' => ['HTTP_AUTHORIZATION' => 'Bearer myToken'],
                    'query' => ['token' => 'myOtherToken'],
                ]),
            ],
            'missing' => [
                null,
                [],
                new Request(),
            ],
        ];
    }

    /**
     * Test `getToken` method.
     *
     * @param string|null $expected Expected result.
     * @param array $config Configuration.
     * @param \Cake\Network\Request $request Request.
     * @return void
     *
     * @dataProvider getTokenProvider
     * @covers ::getToken()
     */
    public function testGetToken($expected, array $config, Request $request)
    {
        $auth = new JwtAuthenticate($this->getMock('Cake\Controller\ComponentRegistry'), $config);

        $result = $auth->getToken($request);

        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for `testGetUser` test case.
     *
     * @return array
     */
    public function getUserProvider()
    {
        $payload = ['someData' => 'someValue'];

        $token = JWT::encode($payload, Security::salt());
        $renewToken = JWT::encode(['sub' => 1], Security::salt());

        $invalidToken = JWT::encode(['aud' => 'http://example.org'], Security::salt());

        return [
            'default' => [
                $payload,
                [],
                new Request([
                    'environment' => ['HTTP_AUTHORIZATION' => 'Bearer ' . $token],
                ]),
            ],
            'queryDatasource' => [
                TableRegistry::get('BEdita/API.Users')->get(1)->toArray(),
                [
                    'userModel' => 'BEdita/API.Users',
                    'queryDatasource' => true,
                ],
                new Request([
                    'environment' => ['HTTP_AUTHORIZATION' => 'Bearer ' . $renewToken],
                ]),
            ],
            'queryDatasourceNoSub' => [
                false,
                [
                    'userModel' => 'BEdita/API.Users',
                    'queryDatasource' => true,
                ],
                new Request([
                    'environment' => ['HTTP_AUTHORIZATION' => 'Bearer ' . $token],
                ]),
            ],
            'missingToken' => [
                false,
                [],
                new Request(),
            ],
            'invalidToken' => [
                false,
                [],
                new Request([
                    'params' => [
                        'plugin' => 'BEdita/API',
                        'controller' => 'Login',
                        'action' => 'login',
                    ],
                    'environment' => [
                        'HTTP_AUTHORIZATION' => 'Bearer ' . $invalidToken,
                        'HTTP_HOST' => 'api.example.com',
                    ],
                ]),
            ],
        ];
    }

    /**
     * Test `getUser` method.
     *
     * @param array|false $expected Expected result.
     * @param array $config Configuration.
     * @param \Cake\Network\Request $request Request.
     * @return void
     *
     * @dataProvider getUserProvider
     * @covers ::getUser()
     * @covers ::getPayload()
     * @covers ::decode()
     */
    public function testGetUser($expected, array $config, Request $request)
    {
        $debug = Configure::read('debug');
        Configure::write('debug', false);

        $auth = new JwtAuthenticate($this->getMock('Cake\Controller\ComponentRegistry'), $config);

        $result = $auth->getUser($request);

        $this->assertEquals($expected, $result);

        Configure::write('debug', $debug);
    }

    /**
     * Test `unauthenticated` method.
     *
     * @return void
     *
     * @expectedException \Cake\Network\Exception\UnauthorizedException
     * @expectedExceptionMessage MyExceptionMessage
     * @covers ::unauthenticated()
     */
    public function testUnauthenticated()
    {
        $controller = new Controller();
        $controller->loadComponent('Auth', [
            'authError' => 'MyExceptionMessage',
        ]);

        $auth = new JwtAuthenticate($controller->components(), []);

        $auth->unauthenticated($controller->request, $controller->response);
    }
}
