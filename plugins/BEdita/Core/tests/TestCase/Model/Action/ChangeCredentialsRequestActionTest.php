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

use BEdita\Core\Model\Action\ChangeCredentialsRequestAction;
use BEdita\Core\Model\Entity\AsyncJob;
use BEdita\Core\Model\Entity\User;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Mailer\Email;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Action\ChangeCredentialsRequestAction} Test Case
 *
 * @covers \BEdita\Core\Model\Action\ChangeCredentialsRequestAction
 */
class ChangeCredentialsRequestActionTest extends TestCase
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
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        Email::dropTransport('default');
        Email::setConfigTransport('default', [
            'className' => 'Debug'
        ]);
    }

    /**
     * Test invocation of command.
     *
     * @return void
     */
    public function testExecute()
    {
        $data = [
            'contact' => 'first.user@example.com',
            'change_url' => 'http://users.example.com',
        ];

        $eventDispatched = 0;
        EventManager::instance()->on('Auth.credentialsChangeRequest', function (...$arguments) use (&$eventDispatched) {
            $eventDispatched++;

            static::assertCount(4, $arguments);
            static::assertInstanceOf(Event::class, $arguments[0]);
            static::assertInstanceOf(User::class, $arguments[1]);
            static::assertInstanceOf(AsyncJob::class, $arguments[2]);
            static::assertTrue(is_string($arguments[3]));
        });

        $action = new ChangeCredentialsRequestAction();
        $res = $action($data);

        static::assertTrue($res);
        static::assertSame(1, $eventDispatched, 'Event not dispatched');
    }

    /**
     * Test validate failure.
     *
     * @return void
     *
     * @expectedException \Cake\Network\Exception\BadRequestException
     */
    public function testValidationFail()
    {
        $data = [
            'contact' => 'ask gustavo',
        ];

        EventManager::instance()->on('Auth.credentialsChangeRequest', function () {
            static::fail('Wrong event triggered');
        });

        $action = new ChangeCredentialsRequestAction();
        $action($data);
    }

    /**
     * Test find contact failure.
     *
     * @return void
     *
     * @expectedException \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function testExecuteFail()
    {
        $data = [
            'contact' => 'mr.gustavo@example.com',
            'change_url' => 'http://users.example.com',
        ];

        EventManager::instance()->on('Auth.credentialsChangeRequest', function () {
            static::fail('Wrong event triggered');
        });

        $action = new ChangeCredentialsRequestAction();
        $action($data);
    }
}
