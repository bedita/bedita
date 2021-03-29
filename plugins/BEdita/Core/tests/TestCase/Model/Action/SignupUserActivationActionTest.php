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
use BEdita\Core\Model\Action\SignupUserActivationAction;
use BEdita\Core\Model\Entity\AsyncJob;
use BEdita\Core\Model\Entity\User;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ConflictException;
use Cake\I18n\Time;
use Cake\Mailer\Email;
use Cake\Mailer\TransportFactory;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @covers \BEdita\Core\Model\Action\SignupUserActivationAction
 */
class SignupUserActivationActionTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.AsyncJobs',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.ExternalAuth',
        'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.History',
    ];

    /**
     * The UsersTable table
     *
     * @var \BEdita\Core\Model\Table\UsersTable
     */
    protected $Users = null;

    /**
     * The AsyncJobs table
     *
     * @var \BEdita\Core\Model\Table\AsyncJobsTable
     */
    protected $AsyncJobs = null;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->AsyncJobs = TableRegistry::getTableLocator()->get('AsyncJobs');
        $this->Users = TableRegistry::getTableLocator()->get('Users');

        Configure::write('Signup', []);
        TransportFactory::drop('default');
        TransportFactory::setConfig('default', [
            'className' => 'Debug'
        ]);
    }

    /**
     * Provider for `testExecuteFailure()`
     *
     * @return array
     */
    public function executeFailureProvider()
    {
        return [
            'missing uuid' => [
                new BadRequestException('Parameter "uuid" missing'),
                [],
            ],
            'async job completed' => [
                new RecordNotFoundException('Record not found in table "async_jobs"'),
                [
                    'uuid' => '1e2d1c66-c0bb-47d7-be5a-5bc92202333e'
                ],
            ],
            'async job missing user_id' => [
                new BadRequestException('Invalid async job, missing user_id'),
                [
                    'uuid' => 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c'
                ]
            ],
            'async job not valid user_id' => [
                new RecordNotFoundException('Record not found in table "users"'),
                [
                    'uuid' => '427ece75-71fb-4aca-bfab-1214cd98495a'
                ]
            ],
        ];
    }

    /**
     * Test command execution failure.
     *
     * @param \Exception $expected The exception expected
     * @param array $data The data given to action
     * @return void
     *
     * @dataProvider executeFailureProvider
     */
    public function testExecuteFailure($expected, $data)
    {
        $this->expectException(get_class($expected));
        $this->expectExceptionMessage($expected->getMessage());

        EventManager::instance()->on('Auth.signupActivation', function () {
            static::fail('Wrong event triggered');
        });

        $action = new SignupUserActivationAction();
        $action($data);
    }

    /**
     * Test failure "409 Conflict" if user exists but is already activated
     *
     * @return void
     */
    public function testExecuteUserAlreadyActived()
    {
        $this->expectException(ConflictException::class);
        $this->expectExceptionMessage('User already active');

        list($user, $asyncJob) = $this->signup();

        $user->status = 'on';
        $user->verified = new Time();
        $Users = TableRegistry::getTableLocator()->get('Users');
        $Users->save($user);

        EventManager::instance()->on('Auth.signupActivation', function () {
            static::fail('Wrong event triggered');
        });

        $signupActivationAction = new SignupUserActivationAction();
        $signupActivationAction(['uuid' => $asyncJob->uuid]);
    }

    /**
     * Test activation action ok
     *
     * @return void
     */
    public function testExecuteOk()
    {
        list($user, $asyncJob) = $this->signup();

        static::assertEquals(1, $user->created_by);
        static::assertEquals(1, $user->modified_by);
        static::assertEquals('draft', $user->status);

        $eventDispatched = 0;
        EventManager::instance()->on('Auth.signupActivation', function (...$arguments) use (&$eventDispatched) {
            $eventDispatched++;

            static::assertCount(3, $arguments);
            static::assertInstanceOf(Event::class, $arguments[0]);
            static::assertInstanceOf(User::class, $arguments[1]);
            static::assertInstanceOf(AsyncJob::class, $arguments[2]);
        });

        $signupActivationAction = new SignupUserActivationAction();
        $signupActivationAction(['uuid' => $asyncJob->uuid]);

        $user = $this->Users->get($user->id);

        static::assertEquals($user->id, $user->created_by);
        static::assertEquals($user->id, $user->modified_by);
        static::assertEquals('on', $user->status);
        static::assertNotNull($user->verified);
        static::assertSame(1, $eventDispatched, 'Event not dispatched');

        $count = $this->AsyncJobs
            ->find('incomplete')
            ->where(['uuid' => $asyncJob->uuid])
            ->count();

        static::assertEquals(0, $count);
    }

    /**
     * Execute a signup action returning the user and relative async job entities
     *
     * @return array
     */
    protected function signup()
    {
        $data = [
            'data' => [
                'username' => 'testsignup',
                'password' => 'testsignup',
                'email' => 'test.signup@example.com',
                'activation_url' => 'http://sample.com?confirm=true',
            ],
        ];

        $signupAction = new SignupUserAction();
        $signupAction($data);

        /* @var \BEdita\Core\Model\Entity\AsyncJob $asyncJob */
        $asyncJob = $this->AsyncJobs->find()
            ->order(['AsyncJobs.created' => 'DESC'])
            ->first();

        $user = $this->Users->get($asyncJob->payload['user_id']);

        return [$user, $asyncJob];
    }
}
