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
use Cake\Mailer\Email;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Action\ChangeCredentialsRequestAction} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Action\ChangeCredentialsRequestAction
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
        Email::dropTransport('default');
        Email::setConfigTransport('default', [
            'className' => 'Debug'
        ]);
    }

    /**
     * Test invocation of command.
     *
     * @return void
     *
     * @covers ::execute()
     * @covers ::validate()
     * @covers ::createJob()
     * @covers ::sendMail()
     * @covers ::getChangeUrl()
     */
    public function testExecute()
    {
        $data = [
            'contact' => 'first.user@example.com',
            'change_url' => 'http://users.example.com',
        ];

        $action = new ChangeCredentialsRequestAction();
        $res = $action($data);

        $this->assertTrue($res);
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
            'contact' => 'ask gustavo',
        ];

        $action = new ChangeCredentialsRequestAction();
        $res = $action($data);
    }

    /**
     * Test find contact failure.
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
            'contact' => 'mr.gustavo@example.com',
            'change_url' => 'http://users.example.com',
        ];

        $action = new ChangeCredentialsRequestAction();
        $res = $action($data);
    }
}
