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
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Mailer\Email;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\InternalErrorException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

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
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.async_jobs',
        'plugin.BEdita/Core.roles',
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
                        'password_hash' => 'testsignup',
                        'email' => 'test.signup@example.com',
                    ],
                    'urlOptions' => [
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
                        'password_hash' => 'testsignup',
                        'email' => 'test.signup@example.com',
                    ],
                    'urlOptions' => [
                        'activation_url' => 'myapp://activate',
                        'redirect_url' => 'myapp://',
                    ],
                ]
            ],
            'missing activation_url' => [
                new BadRequestException(),
                [
                    'data' => [
                        'username' => 'testsignup',
                        'password_hash' => 'testsignup',
                        'email' => 'test.signup@example.com',
                    ],
                ]
            ],
            'activation url invalid' => [
                new BadRequestException(),
                [
                    'data' => [
                        'username' => 'testsignup',
                        'password_hash' => 'testsignup',
                        'email' => 'test.signup@example.com',
                    ],
                    'urlOptions' => [
                        'activation_url' => '/activate',
                    ],
                ]
            ],
            'activation url invalid 2' => [
                new BadRequestException(),
                [
                    'data' => [
                        'username' => 'testsignup',
                        'password_hash' => 'testsignup',
                        'email' => 'test.signup@example.com',
                    ],
                    'urlOptions' => [
                        'activation_url' => 'https://activate',
                    ],
                ]
            ],
        ];
    }

    /**
     * Test command execution.
     *
     * @param array|\Exception $expected Expected result.
     * @param array $data Action data.
     * @return void
     *
     * @dataProvider executeProvider
     */
    public function testExecute($expected, array $data)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
        }

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

        static::assertTrue((bool)$result);
        static::assertInstanceOf(User::class, $result);
        static::assertSame('draft', $result->status);
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
                'password_hash' => 'testsignup',
                'email' => 'test.signup@example.com',
            ],
            'urlOptions' => [
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
                'password_hash' => 'testsignup',
                'email' => 'test.signup@example.com',
            ],
            'urlOptions' => [
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
}
