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

namespace BEdita\Core\Test\TestCase\Mailer;

use BEdita\Core\Mailer\UserMailer;
use Cake\Core\Configure;
use Cake\Mailer\Mailer;
use Cake\Mailer\MailerAwareTrait;
use Cake\Mailer\TransportFactory;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Mailer\UserMailer
 */
class UserMailerTest extends TestCase
{
    use MailerAwareTrait;

    /**
     * The UsersTable instance
     *
     * @var \BEdita\Core\Model\Table\UsersTable
     */
    protected $Users;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.AsyncJobs',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.ExternalAuth',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        TransportFactory::drop('test');
        TransportFactory::setConfig('test', [
            'className' => 'BEdita/Core.AsyncJobs',
        ]);

        Mailer::drop('test');
        Mailer::setConfig('test', [
            'transport' => 'test',
            'from' => [
                'gustavo.supporto@example.org' => 'Gustavo',
            ],
        ]);

        $this->Users = TableRegistry::getTableLocator()->get('Users');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->Users = null;

        Mailer::drop('test');
        TransportFactory::drop('test');
    }

    /**
     * Provider for `testSignup()`
     *
     * @return array
     */
    public function signupProvider()
    {
        return [
            'ok' => [
                true,
                [
                    'params' => [
                        'userId' => 5,
                        'activationUrl' => 'http://example.com',
                    ],
                ],
            ],
            'missing user' => [
                new \LogicException('Parameter "params.user" missing'),
                [
                    'params' => [
                        'activationUrl' => 'http://example.com',
                    ],
                ],
            ],
            'invalid user entity' => [
                new \LogicException('Invalid user, it must be an User Entity'),
                [
                    'params' => [
                        'user' => ['id' => 1],
                        'activationUrl' => 'http://example.com',
                    ],
                ],
            ],
            'missing activationUrl' => [
                new \LogicException('Parameter "params.activationUrl" missing'),
                [
                    'params' => [
                        'userId' => 5,
                    ],
                ],
            ],
        ];
    }

    /**
     * Test signup
     *
     * @param mixed $expected
     * @param array $options
     * @return void
     * @dataProvider signupProvider
     * @covers ::signup()
     * @covers ::checkUser()
     * @covers ::getProjectName()
     */
    public function testSignup($expected, $options)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        if (!empty($options['params']['userId'])) {
            $options['params']['user'] = $this->Users->get($options['params']['userId']);
        }

        $result = $this->getMailer('BEdita/Core.User', 'test')->send('signup', [$options]);

        static::assertEquals($expected, (bool)$result);
    }

    /**
     * Test fail sending email to user without email
     *
     * @return void
     * @covers ::signup()
     * @covers ::checkUser()
     */
    public function testSignupMissingUserEmail()
    {
        $Users = TableRegistry::getTableLocator()->get('Users');
        $user = $Users->get(5);
        $user->email = null;
        $Users->save($user);

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('User email missing');

        $options = [
            'params' => [
                'user' => $user,
                'activationUrl' => 'http://example.com',
            ],
        ];

        $this->getMailer('BEdita/Core.User', 'test')->send('signup', [$options]);
    }

    /**
     * Provider for `testWelcome()`
     *
     * @return array
     */
    public function welcomeProvider()
    {
        return [
            'ok' => [
                true,
                [
                    'params' => [
                        'userId' => 5,
                    ],
                ],
            ],
            'missing user' => [
                new \LogicException('Parameter "params.user" missing'),
                [],
            ],
            'invalid user entity' => [
                new \LogicException('Invalid user, it must be an User Entity'),
                [
                    'params' => [
                        'user' => ['id' => 1],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test welcome
     *
     * @param mixed $expected
     * @param array $options
     * @return void
     * @dataProvider welcomeProvider
     * @covers ::welcome()
     * @covers ::checkUser()
     */
    public function testWelcome($expected, $options)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        if (!empty($options['params']['userId'])) {
            $options['params']['user'] = $this->Users->get($options['params']['userId']);
        }

        $result = $this->getMailer('BEdita/Core.User', 'test')->send('welcome', [$options]);

        static::assertEquals($expected, (bool)$result);
    }

    /**
     * Provider for `testChangeRequest()`
     *
     * @return array
     */
    public function changeRequestProvider()
    {
        return [
            'ok' => [
                true,
                [
                    'params' => [
                        'userId' => 1,
                        'changeUrl' => 'http://example.com',
                    ],
                ],
            ],
            'missing userId' => [
                new \LogicException('Parameter "params.user" missing'),
                [
                    'params' => [
                        'changeUrl' => 'http://example.com',
                    ],
                ],
            ],
            'missing changeUrl' => [
                new \LogicException('Parameter "params.changeUrl" missing'),
                [
                    'params' => [
                        'userId' => 1,
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `changeRequest`
     *
     * @param mixed $expected
     * @param array $options
     * @return void
     * @dataProvider changeRequestProvider
     * @covers ::changeRequest()
     * @covers ::checkUser()
     */
    public function testChangeRequest($expected, $options)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        if (!empty($options['params']['userId'])) {
            $options['params']['user'] = $this->Users->get($options['params']['userId']);
        }

        $result = $this->getMailer('BEdita/Core.User', 'test')->send('changeRequest', [$options]);

        static::assertEquals($expected, (bool)$result);
    }

    /**
     * Data provider for `testGetProjectName()`
     *
     * @return array
     */
    public function getProjectNameProvider()
    {
        return [
            'default' => [
                'BEdita',
                null,
            ],
            'custom name' => [
                'Superunknown :(',
                'Superunknown :(',
            ],
        ];
    }

    /**
     * Test `getProjectName()`
     *
     * @param string $expected The project name expected
     * @param string $configured The project name to put in configuration
     * @return void
     * @dataProvider getProjectNameProvider
     * @covers ::getProjectName()
     */
    public function testGetProjectName($expected, $configured)
    {
        Configure::write('Project.name', $configured);

        $mailer = new class extends UserMailer {
            // make method public in
            public function getProjectName()
            {
                return parent::getProjectName();
            }
        };
        static::assertEquals($expected, $mailer->getProjectName());
    }
}
