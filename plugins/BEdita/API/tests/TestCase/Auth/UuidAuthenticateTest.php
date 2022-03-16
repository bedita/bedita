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

namespace BEdita\API\Test\TestCase\Auth;

use BEdita\API\Auth\UuidAuthenticate;
use BEdita\Core\Model\Entity\AuthProvider;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\API\Auth\UuidAuthenticate
 */
class UuidAuthenticateTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.ExternalAuth',
        'plugin.BEdita/Core.History',
    ];

    /**
     * Data provider for `testGetToken` test case.
     *
     * @return array
     */
    public function getTokenProvider()
    {
        static $uuid = 'fd0f68af-3706-441d-ba71-7b1c88d36571';

        return [
            'header' => [
                $uuid,
                [],
                new ServerRequest([
                    'environment' => ['HTTP_AUTHORIZATION' => 'UUID ' . $uuid],
                ]),
            ],
            'custom header' => [
                $uuid,
                [
                    'header' => 'X-Api-Uuid',
                ],
                new ServerRequest([
                    'environment' => ['HTTP_X_API_UUID' => 'UUID ' . $uuid],
                ]),
            ],
            'custom header prefix' => [
                $uuid,
                [
                    'headerPrefix' => 'GUID',
                ],
                new ServerRequest([
                    'environment' => ['HTTP_AUTHORIZATION' => 'GUID ' . $uuid],
                ]),
            ],
            'wrong header prefix' => [
                null,
                [],
                new ServerRequest([
                    'environment' => ['HTTP_AUTHORIZATION' => 'DIUU ' . $uuid],
                ]),
            ],
            'invalid UUID' => [
                null,
                [],
                new ServerRequest([
                    'environment' => ['HTTP_AUTHORIZATION' => 'UUID not-a-uuid'],
                ]),
            ],
            'missing' => [
                null,
                [],
                new ServerRequest(),
            ],
        ];
    }

    /**
     * Test `getToken` method.
     *
     * @param string|null $expected Expected result.
     * @param array $config Configuration.
     * @param \Cake\Http\ServerRequest $request Request.
     * @return void
     * @dataProvider getTokenProvider
     * @covers ::getToken()
     */
    public function testGetToken($expected, array $config, ServerRequest $request)
    {
        $auth = new UuidAuthenticate(new ComponentRegistry(), $config);

        $result = $auth->getToken($request);

        static::assertEquals($expected, $result);
    }

    /**
     * Data provider for `testAuthenticate` test case.
     *
     * @return array
     */
    public function authenticateProvider()
    {
        static $uuid = 'fd0f68af-3706-441d-ba71-7b1c88d36571';

        return [
            'new' => [
                [
                    'username' => 'uuid-' . $uuid,
                ],
                true,
                new ServerRequest([
                    'environment' => ['HTTP_AUTHORIZATION' => 'UUID ' . $uuid],
                ]),
            ],
            'existing' => [
                [
                    'id' => 5,
                ],
                false,
                new ServerRequest([
                    'environment' => ['HTTP_AUTHORIZATION' => 'UUID 17fec0fa-068a-4d7c-8283-da91d47cef7d'],
                ]),
            ],
            'missing token' => [
                false,
                false,
                new ServerRequest(),
            ],
            'invalid token' => [
                false,
                false,
                new ServerRequest([
                    'environment' => [
                        'HTTP_AUTHORIZATION' => 'UUID not-a-uuid',
                    ],
                ]),
            ],
        ];
    }

    /**
     * Test `getUser` method.
     *
     * @param array|false $expected Expected result.
     * @param bool $newUser Should this request trigger a new user creation?
     * @param \Cake\Http\ServerRequest $request Request.
     * @return void
     * @dataProvider authenticateProvider
     * @covers ::authenticate()
     * @covers ::getUser()
     * @covers ::_findUser()
     */
    public function testAuthenticate($expected, $newUser, ServerRequest $request)
    {
        $Users = TableRegistry::getTableLocator()->get('Users');
        $count = $Users->find()->count();

        $dispatchedEvent = 0;
        $Users->getEventManager()->on('Auth.externalAuth', function () use (&$dispatchedEvent) {
            $dispatchedEvent++;

            static::assertInstanceOf(AuthProvider::class, func_get_arg(1));
            static::assertTrue(is_string(func_get_arg(2)));
        });

        $authConfig = TableRegistry::getTableLocator()->get('AuthProviders')
            ->find('authenticate')
            ->toArray();

        $auth = new UuidAuthenticate(new ComponentRegistry(), $authConfig['BEdita/API.Uuid']);

        $result = $auth->authenticate($request, new Response());
        $countAfter = $Users->find()->count();

        if (is_array($expected)) {
            static::assertArraySubset($expected, $result);
        } else {
            static::assertSame($expected, $result);
        }
        static::assertSame((int)$newUser, $dispatchedEvent);
        static::assertSame($count + (int)$newUser, $countAfter);
    }

    /**
     * Test `unauthenticated` method.
     *
     * @return void
     * @covers ::unauthenticated()
     */
    public function testUnauthenticated()
    {
        $this->expectException(\Cake\Http\Exception\UnauthorizedException::class);
        $this->expectExceptionMessage('MyExceptionMessage');
        $controller = new Controller();
        $controller->loadComponent('Auth', [
            'authError' => 'MyExceptionMessage',
        ]);

        $auth = new UuidAuthenticate($controller->components(), []);

        $auth->unauthenticated($controller->getRequest(), $controller->getResponse());
    }
}
