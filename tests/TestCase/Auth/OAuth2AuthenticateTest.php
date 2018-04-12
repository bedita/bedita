<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Test\TestCase\Auth;

use BEdita\API\Auth\OAuth2Authenticate;
use BEdita\Core\Model\Entity\AuthProvider;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 *  {@see \BEdita\API\Auth\OAuth2Authenticate} Test Case
 *
 * @coversDefaultClass \BEdita\API\Auth\OAuth2Authenticate
 */
class OAuth2AuthenticateTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.properties',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.roles_users',
        'plugin.BEdita/Core.auth_providers',
        'plugin.BEdita/Core.external_auth',
    ];

    /**
     * Data provider for `testAuthenticate` test case.
     *
     * @return array
     */
    public function authenticateProvider()
    {
        return [
            'found' => [
                [
                    'id' => 1,
                ],
                new ServerRequest([
                    'post' => [
                        'auth_provider' => 'example',
                        'provider_username' => 'first_user',
                        'access_token' => 'very-log-string',
                    ],
                ]),
                [
                    'owner_id' => 'first_user',
                    'other' => 'lot of stolen data',
                ]
            ],
            'not found' => [
                false,
                new ServerRequest([
                    'post' => [
                        'auth_provider' => 'example',
                        'provider_username' => 'another_user',
                        'access_token' => 'very-log-string',
                    ],
                ]),
                [
                    'owner_id' => 'another_user',
                    'other' => 'lot of useless data',
                ]
            ],
            'no provider' => [
                false,
                new ServerRequest([
                    'post' => [
                        'auth_provider' => 'linkedout',
                        'provider_username' => 'someone',
                        'access_token' => 'very-log-string',
                    ],
                ]),
            ],
            'no auth' => [
                false,
                new ServerRequest([
                    'post' => [
                        'auth_provider' => 'example',
                        'provider_username' => 'another_user',
                        'access_token' => 'very-log-string',
                    ],
                ]),
                [
                    'owner_id' => 'first_user',
                    'other' => 'lot of useless data',
                ]
            ],
            'missing' => [
                false,
                new ServerRequest([
                    'post' => [
                        'auth_provider' => 'example',
                    ],
                ]),
            ],
        ];
    }

    /**
     * Test `authenticate` method.
     *
     * @param array|false $expected Expected result.
     * @param \Cake\Http\ServerRequest $request Request.
     * @param array $oauthResponse OAuth2 server response.
     * @return void
     *
     * @dataProvider authenticateProvider
     * @covers ::authenticate()
     */
    public function testAuthenticate($expected, ServerRequest $request, array $oauthResponse = [])
    {
        $authConfig = TableRegistry::get('AuthProviders')
            ->find('authenticate')
            ->toArray();

        $auth = $this->getMockBuilder(OAuth2Authenticate::class)
            ->setConstructorArgs([new ComponentRegistry(), $authConfig['BEdita/API.OAuth2']])
            ->setMethods(['getOAuth2Response'])
            ->getMock();

        $auth
            ->method('getOAuth2Response')
            ->willReturn($oauthResponse);

        $result = $auth->authenticate($request, new Response());

        if (is_array($expected)) {
            static::assertArraySubset($expected, $result);
        } else {
            static::assertSame($expected, $result);
        }
    }

    /**
     * Test `getUser` method.
     *
     * @return void
     *
     * @covers ::getUser()
     */
    public function testGetUser()
    {
        $auth = new OAuth2Authenticate(new ComponentRegistry());
        $result = $auth->getUser(new ServerRequest([]));
        static::assertFalse($result);
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

        $auth = new OAuth2Authenticate($controller->components(), []);

        $auth->unauthenticated($controller->request, $controller->response);
    }
}
