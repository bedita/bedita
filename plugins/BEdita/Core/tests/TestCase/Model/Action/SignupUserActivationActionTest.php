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
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Mailer\Email;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\ConflictException;
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

        $this->AsyncJobs = TableRegistry::get('AsyncJobs');
        $this->Users = TableRegistry::get('Users');

        Email::dropTransport('default');
        Email::setConfigTransport('default', [
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
        $Users = TableRegistry::get('Users');
        $Users->save($user);

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

        $signupActivationAction = new SignupUserActivationAction();
        $signupActivationAction(['uuid' => $asyncJob->uuid]);

        $user = $this->Users->get($user->id);

        static::assertEquals($user->id, $user->created_by);
        static::assertEquals($user->id, $user->modified_by);
        static::assertEquals('on', $user->status);
        static::assertTrue($user->verified);

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
                'password_hash' => 'testsignup',
                'email' => 'test.signup@example.com',
            ],
            'urlOptions' => [
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
