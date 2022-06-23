<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
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
use BEdita\Core\Model\Action\ChangeCredentialsAction;
use BEdita\Core\Model\Action\SaveEntityAction;
use BEdita\Core\Model\Entity\AsyncJob;
use BEdita\Core\Model\Entity\User;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\I18n\FrozenTime;
use Cake\Mailer\TransportFactory;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Action\ChangeCredentialsAction} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Action\ChangeCredentialsAction
 */
class ChangeCredentialsActionTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.AsyncJobs',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.PropertyTypes',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        TransportFactory::drop('default');
        TransportFactory::setConfig('default', [
            'className' => 'Debug',
        ]);
    }

    /**
     * Create job for test
     *
     * @return \BEdita\Core\Model\Entity\AsyncJob
     */
    protected function createTestJob()
    {
        $action = new SaveEntityAction(['table' => TableRegistry::getTableLocator()->get('AsyncJobs')]);

        return $action([
            'entity' => TableRegistry::getTableLocator()->get('AsyncJobs')->newEntity([]),
            'data' => [
                'service' => 'credentials_change',
                'payload' => [
                    'user_id' => 1,
                ],
                'scheduled_from' => new FrozenTime('1 day'),
                'priority' => 1,
            ],
        ]);
    }

    /**
     * Test invocation of command.
     *
     * @return void
     * @covers ::execute()
     * @covers ::validate()
     */
    public function testExecute()
    {
        $job = $this->createTestJob();
        $data = [
            'uuid' => $job->uuid,
            'password' => 'gustavoforpresident',
        ];

        $eventDispatched = 0;
        EventManager::instance()->on('Auth.credentialsChange', function (...$arguments) use (&$eventDispatched) {
            $eventDispatched++;

            static::assertCount(3, $arguments);
            static::assertInstanceOf(Event::class, $arguments[0]);
            static::assertInstanceOf(User::class, $arguments[1]);
            static::assertInstanceOf(AsyncJob::class, $arguments[2]);
        });

        $action = new ChangeCredentialsAction();
        $res = $action($data);

        $user = TableRegistry::getTableLocator()->get('Users')->get(1, ['contain' => ['Roles']]);
        static::assertEquals($res->id, $user->id);
        static::assertEquals($res->username, $user->username);
        static::assertSame(1, $eventDispatched, 'Event not dispatched');
    }

    /**
     * Test validate failure.
     *
     * @return void
     * @covers ::execute()
     * @covers ::validate()
     */
    public function testValidationFail()
    {
        $this->expectException(InvalidDataException::class);
        $data = [
            'uuid' => 'whatatoken!',
        ];

        EventManager::instance()->on('Auth.credentialsChange', function () {
            static::fail('Wrong event triggered');
        });

        $action = new ChangeCredentialsAction();
        $action($data);
    }

    /**
     * Test find job failure.
     *
     * @return void
     * @covers ::execute()
     * @covers ::validate()
     */
    public function testExecuteFail()
    {
        $this->expectException(\Cake\Datasource\Exception\RecordNotFoundException::class);
        $data = [
            'uuid' => '66594f3c-8888-49d2-9999-382baf1a12b3',
            'password' => 'unbreakablepassword',
        ];

        EventManager::instance()->on('Auth.credentialsChange', function () {
            static::fail('Wrong event triggered');
        });

        $action = new ChangeCredentialsAction();
        $action($data);
    }

    /**
     * Test payload failure.
     *
     * @return void
     * @covers ::execute()
     * @covers ::validate()
     */
    public function testPayloadFail()
    {
        $this->expectException(\LogicException::class);
        $data = [
            'uuid' => '66594f3c-995f-49d2-9192-382baf1a12b3',
            'password' => 'unbreakablepassword',
        ];

        EventManager::instance()->on('Auth.credentialsChange', function () {
            static::fail('Wrong event triggered');
        });

        $action = new ChangeCredentialsAction();
        $action($data);
    }
}
