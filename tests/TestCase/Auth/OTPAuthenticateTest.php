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

use BEdita\API\Auth\OTPAuthenticate;
use BEdita\API\Auth\OTPdAuthenticate;
use BEdita\Core\Model\Entity\AuthProvider;
use BEdita\Core\Model\Entity\UserToken;
use BEdita\Core\State\CurrentApplication;
use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\API\Auth\OTPdAuthenticate
 */
class OTPAuthenticateTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.config',
        'plugin.BEdita/Core.applications',
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.properties',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.roles_users',
        'plugin.BEdita/Core.auth_providers',
        'plugin.BEdita/Core.external_auth',
        'plugin.BEdita/Core.user_tokens',
    ];

    /**
     * Data provider for `testAuthenticate` test case.
     *
     * @return array
     */
    public function authenticateProvider()
    {
        return [
            'request ok' => [
                [
                    'authorization_code',
                ],
                new ServerRequest([
                    'post' => [
                        'username' => 'first user',
                        'grant_type' => 'otp_request',
                    ],
                ]),
            ],
            'request fail' => [
                false,
                new ServerRequest([
                    'post' => [
                        'username' => 'first user',
                        'grant_type' => 'unknown',
                    ],
                ]),
            ],
            'request no user' => [
                false,
                new ServerRequest([
                    'post' => [
                        'grant_type' => 'otp_request',
                    ],
                ]),
            ],
            'request wrong user' => [
                false,
                new ServerRequest([
                    'post' => [
                        'username' => 'somebody',
                        'grant_type' => 'otp_request',
                    ],
                ]),
            ],
            'access wrong user' => [
                false,
                new ServerRequest([
                    'post' => [
                        'username' => 'somebody',
                        'authorization_code' => 'toktoktoktoktok',
                        'token' => 'secretsecretsecret',
                        'grant_type' => 'otp',
                    ],
                ]),
            ],
            'access ok' => [
                [
                    'id',
                    'username',
                ],
                new ServerRequest([
                    'post' => [
                        'username' => 'second user',
                        'authorization_code' => 'toktoktoktoktok',
                        'token' => 'secretsecretsecret',
                        'grant_type' => 'otp',
                    ],
                ]),
            ],
            'access fail' => [
                false,
                new ServerRequest([
                    'post' => [
                        'username' => 'second user',
                        'authorization_code' => '123123123123',
                        'token' => '123123123123',
                        'grant_type' => 'otp',
                    ],
                ]),
            ],
            'access fail no code' => [
                false,
                new ServerRequest([
                    'post' => [
                        'username' => 'second user',
                        'token' => '123123123123',
                        'grant_type' => 'otp',
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
     * @return void
     *
     * @dataProvider authenticateProvider
     * @covers ::authenticate()
     * @covers ::otpAccess()
     * @covers ::otpARequest()
     */
    public function testAuthenticate($expected, ServerRequest $request)
    {
        $dispatchedEvent = 0;
        TableRegistry::get('Users')->getEventManager()->on('Auth.userToken', function () use (&$dispatchedEvent) {
            $dispatchedEvent++;
            static::assertInstanceOf(UserToken::class, func_get_arg(1));
        });

        CurrentApplication::setApplication(TableRegistry::get('Applications')->get(1));

        $auth = new OTPAuthenticate(new ComponentRegistry(), []);
        $result = $auth->authenticate($request, new Response());

        if (is_array($expected)) {
            static::assertArraySubset($expected, array_keys($result));
        } else {
            static::assertSame($expected, $result);
        }
    }

    /**
     * Test secret token generation
     *
     * @return void
     * @covers ::generateSecretToken()
     * @covers ::defaultSecretGenerator()
     */
    public function testGenerateSecret()
    {
        $auth = new OTPAuthenticate(new ComponentRegistry(), []);
        $result = $auth->generateSecretToken();
        static::assertNotEmpty($result);
        static::assertSame(6, strlen($result));
    }

    /**
     * Test custom secret token generation
     *
     * @return void
     * @covers ::generateSecretToken()
     */
    public function testGenerateSecretCustom()
    {
        $auth = new OTPAuthenticate(new ComponentRegistry(), [
            'generator' => 'time',
        ]);
        $result = $auth->generateSecretToken();
        static::assertNotEmpty($result);
        static::assertSame(10, strlen($result));
    }

    /**
     * Test custom secret token generation via `auth_provides`
     *
     * @return void
     * @covers ::generateSecretToken()
     * @covers ::__construct()
     */
    public function testGenerateSecretAuthProvider()
    {
        $AuthProviders = TableRegistry::get('AuthProviders');
        $authProvider = $AuthProviders->get(4);
        $authProvider->set('params', ['generator' => 'time']);
        $AuthProviders->saveOrFail($authProvider);

        $authConfig = $AuthProviders->find('authenticate')->toArray();

        $auth = new OTPAuthenticate(new ComponentRegistry(), $authConfig['BEdita/API.OTP']);
        $result = $auth->generateSecretToken();
        static::assertNotEmpty($result);
        static::assertSame(10, strlen($result));
    }
}
