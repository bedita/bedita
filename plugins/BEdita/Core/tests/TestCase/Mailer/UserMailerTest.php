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
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.external_auth',
        'plugin.BEdita/Core.auth_providers',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->Email = new Email('test');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        $this->Email = null;
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
            'missing userId' => [
                new \LogicException('Parameter "params.userId" missing'),
                [
                    'params' => [
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
     */
    public function testSignup($expected, $options)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
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
                'userId' => 5,
                'activationUrl' => 'http://example.com',
            ],
        ];

        $this->getMailer('BEdita/Core.User', $this->Email)->send('signup', [$options]);
    }
}
