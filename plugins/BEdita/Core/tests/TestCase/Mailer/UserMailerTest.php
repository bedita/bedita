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

namespace BEdita\Core\Test\TestCase\Mailer;

use BEdita\Core\Model\Entity\Application;
use BEdita\Core\State\CurrentApplication;
use Cake\Mailer\Email;
use Cake\Mailer\MailerAwareTrait;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Mailer\UserMailer
 */
class UserMailerTest extends TestCase
{
    use MailerAwareTrait;

    /**
     * The Email instance
     *
     * @var \Cake\Mailer\Email
     */
    protected $Email;

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
    public $fixtures = [
        'plugin.BEdita/Core.async_jobs',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.external_auth',
        'plugin.BEdita/Core.auth_providers',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.async_jobs',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        Email::dropTransport('test');
        Email::setConfigTransport('test', [
            'className' => 'BEdita/Core.AsyncJobs',
        ]);

        Email::drop('test');
        Email::setConfig('test', [
            'transport' => 'test',
            'from' => [
                'gustavo.supporto@example.org' => 'Gustavo',
            ],
        ]);

        $this->Email = new Email('test');

        $this->Users = TableRegistry::get('Users');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        $this->Users = null;
        $this->Email = null;

        Email::drop('test');
        Email::dropTransport('test');
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
                    ]
                ],
            ],
            'missing user' => [
                new \LogicException('Parameter "params.user" missing'),
                [
                    'params' => [
                        'activationUrl' => 'http://example.com',
                    ]
                ],
            ],
            'invalid user entity' => [
                new \LogicException('Invalid user, it must be an User Entity'),
                [
                    'params' => [
                        'user' => ['id' => 1],
                        'activationUrl' => 'http://example.com',
                    ]
                ],
            ],
            'missing activationUrl' => [
                new \LogicException('Parameter "params.activationUrl" missing'),
                [
                    'params' => [
                        'userId' => 5,
                    ]
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
     *
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

        $result = $this->getMailer('BEdita/Core.User', $this->Email)->send('signup', [$options]);

        static::assertEquals($expected, (bool)$result);
    }

    /**
     * Test fail sending email to user without email
     *
     * @return void
     *
     * @covers ::signup()
     * @covers ::checkUser()
     */
    public function testSignupMissingUserEmail()
    {
        $Users = TableRegistry::get('Users');
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

        $this->getMailer('BEdita/Core.User', $this->Email)->send('signup', [$options]);
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
                    ]
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
                    ]
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
     *
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

        $result = $this->getMailer('BEdita/Core.User', $this->Email)->send('welcome', [$options]);

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
                    ]
                ],
            ],
            'missing userId' => [
                new \LogicException('Parameter "params.user" missing'),
                [
                    'params' => [
                        'changeUrl' => 'http://example.com',
                    ]
                ],
            ],
            'missing changeUrl' => [
                new \LogicException('Parameter "params.changeUrl" missing'),
                [
                    'params' => [
                        'userId' => 1,
                    ]
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
     *
     * @dataProvider changeRequestProvider
     * @covers ::changeRequest()
     * @covers ::getUser()
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

        $result = $this->getMailer('BEdita/Core.User', $this->Email)->send('changeRequest', [$options]);

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
            ],
            'custom app name' => [
                'Superunknown :(',
                new Application([
                    'name' => 'Superunknown :('
                ])
            ],
        ];
    }

    /**
     * Test `getProjectName()`
     *
     * @param string $expected The project name expected
     * @param \BEdita\Core\Model\Entity\Application $application The application entity
     * @return void
     *
     * @dataProvider getProjectNameProvider
     * @covers ::getProjectName()
     */
    public function testGetProjectName($expected, $application = null)
    {
        if ($application instanceof Application) {
            CurrentApplication::setApplication($application);
        }

        $this->getMailer('BEdita/Core.User', $this->Email)->send('welcome', [
            [
                'params' => [
                    'user' => $this->Users->get(5)
                ]
            ]
        ]);

        $viewVars = $this->Email->getViewVars();
        static::assertArrayHasKey('projectName', $viewVars);
        static::assertEquals($expected, $viewVars['projectName']);
    }
}
