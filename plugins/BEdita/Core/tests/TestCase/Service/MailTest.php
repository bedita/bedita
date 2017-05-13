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
namespace BEdita\Core\Test\TestCase\Service;

use BEdita\Core\Service\Mail;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Service\Mail} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Service\Mail
 */
class MailTest extends TestCase
{
    protected $testOptions = ['profile' => 'test'];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->mailService = new Mail();
    }

    /**
     * Data provider for `testRun` test case.
     *
     * @return array
     */
    public function runProvider()
    {
        return [
            'simple' => [
                [
                    'to' => 'me@localhost',
                    'subject' => 'something',
                    'message' => 'it works!'
                ],
                true,
            ],
            'missing' => [
                [
                    'to' => 'me@localhost',
                ],
                false,
            ],
            'mailer' => [
                [
                    'to' => 'me@localhost',
                    'mailer' => 'User',
                    'action' => 'welcome',
                ],
                true,
            ],
            'mailerPlugin' => [
                [
                    'to' => 'me@localhost',
                    'mailer' => 'BEdita/Core.User',
                    'action' => 'welcome',
                ],
                true,
            ],
            'noAction' => [
                [
                    'to' => 'me@localhost',
                    'mailer' => 'Gustavo',
                ],
                false,
            ],
            'noMailer' => [
                [
                    'to' => 'me@localhost',
                    'mailer' => 'Gustavo',
                    'action' => 'support',
                ],
                false,
            ],
        ];
    }

    /**
     * Test run method
     *
     * @param array $payload Payload data
     * @param bool $success True on success, false otherwise
     * @return void
     * @covers ::run()
     * @covers ::mailerSend()
     * @dataProvider runProvider
     */
    public function testRun($payload, $success)
    {
        if (!$success) {
            $this->expectException(\LogicException::class);
        }
        $mailService = new Mail();
        $result = $mailService->run($payload, $this->testOptions);
        $this->assertEquals($success, $result);
    }
}
