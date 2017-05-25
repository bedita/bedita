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

use BEdita\Core\Model\Action\ChangeCredentialsAction;
use BEdita\Core\Model\Action\SaveEntityAction;
use Cake\I18n\Time;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 *  {@see \BEdita\Core\Model\Action\ChangeCredentialsAction} Test Case
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
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.roles_users',
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
        Email::dropTransport('default');
        Email::setConfigTransport('default', [
            'className' => 'Debug'
        ]);
    }

    /**
     * Create job for test
     *
     * @return void
     */
    protected function createTestJob()
    {
        $action = new SaveEntityAction(['table' => TableRegistry::get('AsyncJobs')]);

        return $action([
            'entity' => TableRegistry::get('AsyncJobs')->newEntity(),
            'data' => [
                'service' => 'credentials_change',
                'payload' => [
                    'user_id' => 1,
                ],
                'scheduled_from' => new Time('1 day'),
                'priority' => 1,
            ]
        ]);
    }

    /**
     * Test invocation of command.
     *
     * @return void
     *
     * @covers ::execute()
     * @covers ::validate()
     */
    public function testExecute()
    {
        $job = $this->createTestJob();
        $data = [
            'token' => $job->uuid,
            'password' => 'gustavoforpresident',
        ];

        $action = new ChangeCredentialsAction();
        $res = $action($data);

        $user = TableRegistry::get('Users')->get(1, ['contain' => ['Roles']]);
        $this->assertEquals($res->toArray(), $user->toArray());
    }

    /**
     * Test validate failure.
     *
     * @return void
     *
     * @covers ::execute()
     * @covers ::validate()
     */
    public function testValidationFail()
    {
        $this->expectException('\Cake\Network\Exception\BadRequestException');

        $data = [
            'token' => 'whatatoken!',
        ];

        $action = new ChangeCredentialsAction();
        $res = $action($data);
    }

    /**
     * Test find job failure.
     *
     * @return void
     *
     * @covers ::execute()
     * @covers ::validate()
     */
    public function testExecuteFail()
    {
        $this->expectException('\Cake\Datasource\Exception\RecordNotFoundException');
        $data = [
            'token' => '112312312312312312',
            'password' => 'unbreakablepassword',
        ];

        $action = new ChangeCredentialsAction();
        $res = $action($data);
    }

    /**
     * Test payload failure.
     *
     * @return void
     *
     * @covers ::execute()
     * @covers ::validate()
     */
    public function testPayloadFail()
    {
        $this->expectException('\LogicException');
        $data = [
            'token' => '66594f3c-995f-49d2-9192-382baf1a12b3',
            'password' => 'unbreakablepassword',
        ];

        $action = new ChangeCredentialsAction();
        $res = $action($data);
    }

}
