<?php
declare(strict_types=1);

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

use BEdita\Core\Exception\InvalidDataException;
use BEdita\Core\Exception\UserExistsException;
use BEdita\Core\Model\Action\SignupUserAction;
use BEdita\Core\Model\Entity\AsyncJob;
use BEdita\Core\Model\Entity\User;
use Cake\Core\Configure;
use Cake\Core\Exception\CakeException;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\Mailer\TransportFactory;
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
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.AsyncJobs',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.ExternalAuth',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.History',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
        'plugin.BEdita/Core.Tags',
        'plugin.BEdita/Core.ObjectTags',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        Configure::write('Signup', []);
        TransportFactory::drop('default');
        TransportFactory::setConfig('default', [
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
                ],
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
                ],
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
                            ],
                        ],
                    ],
                ],
            ],

            'existing user' => [
                new UserExistsException('User "second user" already registered'),
                [
                    'data' => [
                        'username' => 'second user',
                        'password' => 'somepassword',
                        'email' => 'test.signup@example.com',
                        'activation_url' => 'myapp://activate',
                    ],
                ],
            ],

            'missing activation_url' => [
                new InvalidDataException(
                    'Invalid data',
                    ['activation_url' => ['_required' => 'This field is required']]
                ),
                [
                    'data' => [
                        'username' => 'testsignup',
                        'password' => 'testsignup',
                        'email' => 'test.signup@example.com',
                    ],
                ],
            ],
            'activation url invalid' => [
                new InvalidDataException(
                    'Invalid data',
                    ['activation_url' => ['customUrl' => 'The provided value is invalid']]
                ),
                [
                    'data' => [
                        'username' => 'testsignup',
                        'password' => 'testsignup',
                        'email' => 'test.signup@example.com',
                        'activation_url' => '/activate',
                    ],
                ],
            ],
            'activation url invalid 2' => [
                new InvalidDataException(
                    'Invalid data',
                    ['activation_url' => ['customUrl' => 'The provided value is invalid']]
                ),
                [
                    'data' => [
                        'username' => 'testsignup',
                        'password' => 'testsignup',
                        'email' => 'test.signup@example.com',
                        'activation_url' => 'https://activate',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test command execution.
     *
     * @param bool|\Exception $expected Expected result.
     * @param array $data Action data.
     * @return void
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
            $this->expectException(get_class($expected));
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
                ],
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
                ],
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
     * Test command execution with `Status.level` set to `on`.
     *
     * @return void
     */
    public function testStatusLevelExecute(): void
    {
        Configure::write('Status.level', 'on');
        $data = [
            'username' => 'testsignup',
            'password' => 'testsignup',
            'email' => 'test.signup@example.com',
            'activation_url' => 'http://sample.com?confirm=true',
            'redirect_url' => 'http://sample.com/ok',
        ];

        $action = new SignupUserAction();
        $result = $action(compact('data'));

        static::assertTrue((bool)$result);
        static::assertInstanceOf(User::class, $result);
        static::assertSame('draft', $result->status);
        static::assertSame($data['username'], $result->username);
        Configure::delete('Status.level');
    }

    /**
     * Test command execution with external auth
     *
     * @param array|\Exception $expected Expected result.
     * @param array $data Action data.
     * @return void
     * @dataProvider executeExtAuthProvider
     */
    public function testExecuteExtAuth($expected, array $data, array $oauthResponse)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $eventDispatched = 0;
        EventManager::instance()->on('Auth.signupActivation', function (...$arguments) use (&$eventDispatched) {
            $eventDispatched++;

            static::assertCount(2, $arguments);
            static::assertInstanceOf(Event::class, $arguments[0]);
            static::assertInstanceOf(User::class, $arguments[1]);
        });

        $action = $this->getMockBuilder(SignupUserAction::class)
            ->onlyMethods(['getOAuth2Response'])
            ->getMock();

        $action
            ->method('getOAuth2Response')
            ->willReturn($oauthResponse);

        $result = $action($data);

        static::assertTrue((bool)$result);
        static::assertInstanceOf(User::class, $result);
        static::assertSame('on', $result->status);
        static::assertNotEmpty($result->verified);
        static::assertSame(1, $eventDispatched, 'Event not dispatched');
    }

    /**
     * Test action execution with external auth callback
     *
     * @return void
     */
    public function testExecuteExtAuthCallback(): void
    {
        $authProvider = $this->fetchTable('AuthProviders')->get(1);
        $authProvider->params = [
            'options' => [
                'credentials_callback' => [static::class, 'dummyCallback'],
            ],
        ];
        $this->fetchTable('AuthProviders')->saveOrFail($authProvider);
        $data = [
            'username' => 'testsignup',
            'email' => 'testsignup@example.com',
            'auth_provider' => 'example',
            'provider_username' => 'not-found',
            'provider_userdata' => [],
            'access_token' => 'incredibly-long-string',
        ];
        $action = new SignupUserAction();
        $result = $action(compact('data'));
        static::assertTrue((bool)$result);
    }

    /**
     * Dummy test callback method
     *
     * @return bool
     */
    public static function dummyCallback(): bool
    {
        return true;
    }

    /**
     * Test action failure with external auth callback
     *
     * @return void
     */
    public function testExecuteExtAuthCallbackFail(): void
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('External auth failed');

        $authProvider = $this->fetchTable('AuthProviders')->get(1);
        $authProvider->params = [
            'options' => [
                'credentials_callback' => [static::class, 'dummyCallbackFalse'],
            ],
        ];
        $this->fetchTable('AuthProviders')->saveOrFail($authProvider);
        $data = [
            'username' => 'testsignup',
            'email' => 'testsignup@example.com',
            'auth_provider' => 'example',
            'provider_username' => 'not-found',
            'provider_userdata' => [],
            'access_token' => 'incredibly-long-string',
        ];
        $action = new SignupUserAction();
        $result = $action(compact('data'));
    }

    /**
     * Another dummy test callback method
     *
     * @return bool
     */
    public static function dummyCallbackFalse(): bool
    {
        return false;
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
     */
    public function testExceptionSendMail()
    {
        $this->expectException(\Cake\Http\Exception\InternalErrorException::class);
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

            throw new InternalErrorException();
        });

        $action = new SignupUserAction();

        try {
            $action($data);
        } finally {
            static::assertSame(1, $eventDispatched, 'Event not dispatched');
            static::assertFalse(TableRegistry::getTableLocator()->get('Users')->exists(['username' => 'testsignup']));
        }
    }

    /**
     * Test `Signup.activationUrl` config
     *
     * @return void
     */
    public function testActivationUrl()
    {
        Configure::write('Signup.activationUrl', 'https://my.activation.url');

        $data = [
            'data' => [
                'username' => 'testsignup',
                'password' => 'testsignup',
                'email' => 'test.signup@example.com',
            ],
        ];

        $invoked = 0;
        EventManager::instance()->on('Auth.signup', function (...$arguments) use (&$invoked) {
            $invoked++;
            static::assertCount(4, $arguments);
            $job = $arguments[2];
            $url = sprintf('%s?uuid=%s', Configure::read('Signup.activationUrl'), $job->get('uuid'));
            static::assertEquals($url, $arguments[3]);
        });

        $action = new SignupUserAction();
        $action($data);

        static::assertSame(1, $invoked);
        $user = TableRegistry::getTableLocator()->get('Users')->find()->where(['username' => 'testsignup'])->first();
        static::assertEquals('test.signup@example.com', $user->get('email'));
    }

    /**
     * Test signup with empty email and password case
     *
     * @return void
     */
    public function testEmptyPasswordEmail()
    {
        Configure::write('Signup', [
            'requireEmail' => false,
            'requirePassword' => false,
        ]);

        $data = [
            'data' => [
                'username' => 'testsignup',
                'activation_url' => 'http://sample.com?confirm=true',
            ],
        ];

        $action = new SignupUserAction();
        $action($data);

        $user = TableRegistry::getTableLocator()->get('Users')->find()->where(['username' => 'testsignup'])->first();
        static::assertNull($user->get('email'));
    }

    /**
     * Data provider for `testRoles()`
     *
     * @return array
     */
    public function rolesProvider()
    {
        return [
            'roleAsFromConfig' => [
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
                [
                    'roles' => ['second role'],
                ],
            ],
            // fail when no conf for roles is present and role are passed
            'failNoRoles' => [
                new InvalidDataException(
                    'Invalid data',
                    [
                        'roles' => [
                            'validateRoles' => 'Roles are not allowed on signup',
                        ],
                    ]
                ),
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
            // fail beacause admin role not allowed in signup
            'failAdminRole' => [
                new InvalidDataException(
                    'Invalid data',
                    [
                        'roles' => [
                            'validateRoles' => 'first role not allowed on signup',
                        ],
                    ]
                ),
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
                [
                    'roles' => ['first role'],
                ],
            ],
            'default role' => [
                true,
                [
                    'data' => [
                        'username' => 'testsignup',
                        'password' => 'testsignup',
                        'email' => 'test.signup@example.com',
                        'activation_url' => 'http://sample.com?confirm=true',
                    ],
                ],
                [
                    'roles' => ['first role', 'second role'],
                    'defaultRoles' => ['second role'],
                ],
            ],
            // fail because roles is not set with signup roles conf present
            'failRoleNotSetWithAllowed' => [
                new InvalidDataException(
                    'Invalid data',
                    [
                        'roles' => [
                            '_required' => 'This field is required',
                        ],
                    ]
                ),
                [
                    'data' => [
                        'username' => 'testsignup',
                        'password' => 'testsignup',
                        'email' => 'test.signup@example.com',
                        'activation_url' => 'http://sample.com?confirm=true',
                        'redirect_url' => 'http://sample.com/ok',
                    ],
                ],
                [
                    'roles' => ['second role'],
                ],
            ],
            // fail because roles is an empty array with signup roles conf present
            'failEmptyRoleWithAllowed' => [
                new InvalidDataException(
                    'Invalid data',
                    [
                        'roles' => [
                            '_empty' => 'This field cannot be left empty',
                        ],
                    ]
                ),
                [
                    'data' => [
                        'username' => 'testsignup',
                        'password' => 'testsignup',
                        'email' => 'test.signup@example.com',
                        'activation_url' => 'http://sample.com?confirm=true',
                        'redirect_url' => 'http://sample.com/ok',
                        'roles' => [],
                    ],
                ],
                [
                    'roles' => ['second role'],
                ],
            ],
            // fail two not allowed roles
            'filMultipleRolesNotAllowed' => [
                new InvalidDataException(
                    'Invalid data',
                    [
                        'roles' => [
                            'validateRoles' => 'third_role, fourth_role not allowed on signup',
                        ],
                    ]
                ),
                [
                    'data' => [
                        'username' => 'testsignup',
                        'password' => 'testsignup',
                        'email' => 'test.signup@example.com',
                        'activation_url' => 'http://sample.com?confirm=true',
                        'redirect_url' => 'http://sample.com/ok',
                        'roles' => ['third_role', 'fourth_role'],
                    ],
                ],
                [
                    'roles' => ['second role'],
                ],
            ],
        ];
    }

    /**
     * Test `addRoles` and `loadRoles` methods
     *
     * @param bool|\Exception $expected Expected result.
     * @param array $data Action data.
     * @param array $config Signup configuration.
     * @dataProvider rolesProvider
     * @return void
     */
    public function testRoles($expected, array $data, array $config = [])
    {
        Configure::write('Signup', $config);
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
        }

        try {
            $action = new SignupUserAction();
            $result = $action($data);
        } catch (CakeException $e) {
            static::assertInstanceOf(CakeException::class, $expected); // Ensure we're expecting an exception of the same kind.
            static::assertEquals($expected->getAttributes(), $e->getAttributes());

            throw $e;
        }

        static::assertTrue((bool)$result);
        $roles = Hash::get($data, 'data.roles', Configure::read('Signup.defaultRoles'));
        static::assertSame($roles, Hash::extract($result->get('roles'), '{n}.name'));
    }
}
