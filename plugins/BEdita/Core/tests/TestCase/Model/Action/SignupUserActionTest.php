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

namespace BEdita\Core\Test\TestCase\Model\Action;

use BEdita\Core\Model\Action\SignupUserAction;
use BEdita\Core\Model\Entity\AsyncJob;
use BEdita\Core\Model\Entity\User;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception as CakeException;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Mailer\Email;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\InternalErrorException;
use Cake\Network\Exception\UnauthorizedException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * @covers \BEdita\Core\Model\Action\SignupUserAction
 */
class SignupUserActionTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.properties',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.async_jobs',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.trees',
        'plugin.BEdita/Core.roles_users',
        'plugin.BEdita/Core.external_auth',
        'plugin.BEdita/Core.auth_providers',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.object_relations',
    ];

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        Email::dropTransport('default');
        Email::setConfigTransport('default', [
            'className' => 'Debug',
        ]);
    }

    /**
     * Provider for `testExecute()`
     *
     * @return array
     */
    public function executeProvider()
    {
        return [
            'ok' => [
                true,
                [
                    'data' => [
                        'username' => 'testsignup',
                        'password' => 'testsignup',
                        'email' => 'test.signup@example.com',
                        'activation_url' => 'http://sample.com?confirm=true',
                        'redirect_url' => 'http://sample.com/ok',
                    ],
                ]
            ],
            'ok custom url' => [
                true,
                [
                    'data' => [
                        'username' => 'testsignup',
                        'password' => 'testsignup',
                        'email' => 'test.signup@example.com',
                        'activation_url' => 'myapp://activate',
                        'redirect_url' => 'myapp://',
                    ],
                ]
            ],
            'ok json api' => [
                true,
                [
                    'data' => [
                        'data' => [
                            'attributes' => [
                                'username' => 'testsignup',
                                'password' => 'testsignup',
                                'email' => 'test.signup@example.com',
                            ],
                            'meta' => [
                                'activation_url' => 'myapp://activate',
                                'redirect_url' => 'myapp://',
                            ]
                        ]
                    ],
                ]
            ],

            'existing user' => [
                new BadRequestException([
                    'title' => 'User "second user" already registered',
                    'code' => 'be_user_exists',
                ]),
                [
                    'data' => [
                        'username' => 'second user',
                        'password' => 'somepassword',
                        'email' => 'test.signup@example.com',
                        'activation_url' => 'myapp://activate',
                    ],
                ]
            ],

            'missing activation_url' => [
                new BadRequestException([
                    'title' => 'Invalid data',
                    'detail' => ['activation_url' => ['_required' => 'This field is required']],
                ]),
                [
                    'data' => [
                        'username' => 'testsignup',
                        'password' => 'testsignup',
                        'email' => 'test.signup@example.com',
                    ],
                ]
            ],
            'activation url invalid' => [
                new BadRequestException([
                    'title' => 'Invalid data',
                    'detail' => ['activation_url' => ['customUrl' => 'The provided value is invalid']],
                ]),
                [
                    'data' => [
                        'username' => 'testsignup',
                        'password' => 'testsignup',
                        'email' => 'test.signup@example.com',
                        'activation_url' => '/activate',
                    ],
                ]
            ],
            'activation url invalid 2' => [
                new BadRequestException([
                    'title' => 'Invalid data',
                    'detail' => ['activation_url' => ['customUrl' => 'The provided value is invalid']],
                ]),
                [
                    'data' => [
                        'username' => 'testsignup',
                        'password' => 'testsignup',
                        'email' => 'test.signup@example.com',
                        'activation_url' => 'https://activate',
                    ],
                ]
            ],
        ];
    }

    /**
     * Test command execution.
     *
     * @param bool|\Exception $expected Expected result.
     * @param array $data Action data.
     * @return void
     *
     * @dataProvider executeProvider
     */
    public function testExecute($expected, array $data)
    {
        $eventDispatched = 0;
        EventManager::instance()->on('Auth.signup', function (...$arguments) use (&$eventDispatched) {
            $eventDispatched++;

            static::assertCount(4, $arguments);
            static::assertInstanceOf(Event::class, $arguments[0]);
            static::assertInstanceOf(User::class, $arguments[1]);
            static::assertInstanceOf(AsyncJob::class, $arguments[2]);
            static::assertTrue(is_string($arguments[3]));
        });

        if ($expected instanceof \Exception) {
            static::expectException(get_class($expected));
        }

        try {
            $action = new SignupUserAction();
            $result = $action($data);
        } catch (CakeException $e) {
            static::assertInstanceOf(CakeException::class, $expected); // Ensure we're expecting an exception of the same kind.
            static::assertEquals($expected->getAttributes(), $e->getAttributes());
            static::assertEquals($expected->getCode(), $e->getCode());

            throw $e; // Re-throw exception.
        }

        static::assertTrue((bool)$result);
        static::assertInstanceOf(User::class, $result);
        static::assertSame('draft', $result->status);
        static::assertSame(1, $eventDispatched, 'Event not dispatched');
    }

    /**
     * Provider for `testExecuteExtAuth()`
     *
     * @return array
     */
    public function executeExtAuthProvider()
    {
        return [
            'ok' => [
                true,
                [
                    'data' => [
                        'username' => 'testsignup',
                        'email' => 'testsignup@example.com',
                        'auth_provider' => 'example',
                        'provider_username' => 'test',
                        'provider_userdata' => [
                            'lot of data',
                        ],
                        'access_token' => 'incredibly-long-string',
                    ],
                ],
                [
                    'owner_id' => 'test',
                ]
            ],
            'bad provider' => [
                new UnauthorizedException('External auth provider not found'),
                [
                    'data' => [
                        'username' => 'testsignup',
                        'email' => 'testsignup@example.com',
                        'auth_provider' => 'someprovider',
                        'provider_username' => 'test',
                        'access_token' => 'incredibly-long-string',
                    ],
                ],
                [
                    'owner_id' => 'test',
                ]
            ],
            'oauth2 fail' => [
                new UnauthorizedException('External auth failed'),
                [
                    'data' => [
                        'username' => 'testsignup',
                        'email' => 'testsignup@example.com',
                        'auth_provider' => 'example',
                        'provider_username' => 'not-found',
                        'provider_userdata' => [],
                        'access_token' => 'incredibly-long-string',
                    ],
                ],
                [
                    'owner_id' => 'test',
                ],
            ],
        ];
    }

    /**
     * Test command execution with external auth
     *
     * @param array|\Exception $expected Expected result.
     * @param array $data Action data.
     * @return void
     *
     * @dataProvider executeExtAuthProvider
     */
    public function testExecuteExtAuth($expected, array $data, array $oauthResponse)
    {
        if ($expected instanceof \Exception) {
            static::expectException(get_class($expected));
            static::expectExceptionMessage($expected->getMessage());
        }

        $eventDispatched = 0;
        EventManager::instance()->on('Auth.signupActivation', function (...$arguments) use (&$eventDispatched) {
            $eventDispatched++;

            static::assertCount(2, $arguments);
            static::assertInstanceOf(Event::class, $arguments[0]);
            static::assertInstanceOf(User::class, $arguments[1]);
        });

        $action = $this->getMockBuilder(SignupUserAction::class)
            ->setMethods(['getOAuth2Response'])
            ->getMock();

        $action
            ->method('getOAuth2Response')
            ->willReturn($oauthResponse);

        $result = $action($data);

        static::assertTrue((bool)$result);
        static::assertInstanceOf(User::class, $result);
        static::assertSame('on', $result->status);
        static::assertSame(1, $eventDispatched, 'Event not dispatched');
    }

    /**
     * Test signup action when activation is not required.
     *
     * @return void
     */
    public function testExecuteActivationNotRequired()
    {
        $data = [
            'data' => [
                'username' => 'testsignup',
                'password' => 'testsignup',
                'email' => 'test.signup@example.com',
                'activation_url' => 'http://sample.com?confirm=true',
                'redirect_url' => 'http://sample.com/ok',
            ],
        ];

        Configure::write('Signup.requireActivation', false);

        $eventDispatched = 0;
        EventManager::instance()->on('Auth.signup', function (...$arguments) use (&$eventDispatched) {
            $eventDispatched++;

            static::assertCount(4, $arguments);
            static::assertInstanceOf(Event::class, $arguments[0]);
            static::assertInstanceOf(User::class, $arguments[1]);
            static::assertInstanceOf(AsyncJob::class, $arguments[2]);
            static::assertTrue(is_string($arguments[3]));
        });

        $action = new SignupUserAction();
        $result = $action($data);

        static::assertInstanceOf(User::class, $result);
        static::assertSame('on', $result->status);
        static::assertSame(1, $eventDispatched, 'Event not dispatched');
    }

    /**
     * Test execute when exception was raised sending email
     *
     * @return void
     *
     * @expectedException \Cake\Network\Exception\InternalErrorException
     */
    public function testExceptionSendMail()
    {
        $data = [
            'data' => [
                'username' => 'testsignup',
                'password' => 'testsignup',
                'email' => 'test.signup@example.com',
                'activation_url' => 'http://sample.com?confirm=true',
                'redirect_url' => 'http://sample.com/ok',
            ],
        ];

        $eventDispatched = 0;
        EventManager::instance()->on('Auth.signup', function () use (&$eventDispatched) {
            $eventDispatched++;

            throw new InternalErrorException;
        });

        $action = new SignupUserAction();

        try {
            $action($data);
        } finally {
            static::assertSame(1, $eventDispatched, 'Event not dispatched');
            static::assertFalse(TableRegistry::get('Users')->exists(['username' => 'testsignup']));
        }
    }

    /**
     * Data provider for `testRoles()`
     *
     * @return array
     */
    public function rolesProvider()
    {
        return [
            'admin' => [
                true,
                [
                    'data' => [
                        'username' => 'testsignup',
                        'password' => 'testsignup',
                        'email' => 'test.signup@example.com',
                        'roles' => ['second role'],
                        'activation_url' => 'http://sample.com?confirm=true',
                        'redirect_url' => 'http://sample.com/ok',
                    ],
                ],
                ['second role'],
            ],
            'failNoRoles' => [
                new BadRequestException('Role "second role" not allowed on signup'),
                [
                    'data' => [
                        'username' => 'testsignup',
                        'password' => 'testsignup',
                        'email' => 'test.signup@example.com',
                        'roles' => ['second role'],
                        'activation_url' => 'http://sample.com?confirm=true',
                        'redirect_url' => 'http://sample.com/ok',
                    ],
                ],
                [],
            ],
            'failAdminRole' => [
                new BadRequestException('Role "first role" not allowed on signup'),
                [
                    'data' => [
                        'username' => 'testsignup',
                        'password' => 'testsignup',
                        'email' => 'test.signup@example.com',
                        'roles' => ['first role'],
                        'activation_url' => 'http://sample.com?confirm=true',
                        'redirect_url' => 'http://sample.com/ok',
                    ],
                ],
                ['first role'],
            ],
        ];
    }

    /**
     * Test `addRoles` and `loadRoles` methods
     *
     * @param bool|\Exception $expected Expected result.
     * @param array $data Action data.
     * @param array $allowed Allowe roles to set in configuration.
     *
     * @dataProvider rolesProvider
     * @return void
     */
    public function testRoles($expected, array $data, array $allowed)
    {
        Configure::write('Signup.roles', $allowed);
        if ($expected instanceof \Exception) {
            static::expectException(get_class($expected));
            static::expectExceptionMessage($expected->getMessage());
        }

        $action = new SignupUserAction();
        $result = $action($data);

        static::assertTrue((bool)$result);
        static::assertSame($data['data']['roles'], Hash::extract($result->get('roles'), '{n}.name'));
    }
}
