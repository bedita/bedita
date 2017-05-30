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

namespace BEdita\Core\Test\TestCase\Job\Service;

use BEdita\Core\Job\Service\MailService;
use Cake\Mailer\Email;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Job\Service\MailService} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Job\Service\MailService
 */
class MailServiceTest extends TestCase
{

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        Email::dropTransport('test');
        Email::setConfigTransport('test', [
            'className' => 'Debug',
        ]);

        parent::setUp();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        Email::dropTransport('test');
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
                    'headers' => [
                        'From: Gustavo <gustavo.supporto@example.org>',
                        'To: Evermannella <evermannella@example.org>',
                        'Subject: Re: Have you installed the latest version of Synapse?',
                    ],
                    'message' => [
                        'Not yet. Please write a story on our Scrum board.',
                        '',
                        'Regards,',
                        'Evermannella @ ChiaLab srl',
                    ],
                ],
                [
                    '_from' => ['gustavo.supporto@example.org' => 'Gustavo'],
                    '_to' => ['evermannella@example.org' => 'Evermannella'],
                    '_subject' => 'Re: Have you installed the latest version of Synapse?',
                    '_message' => [
                        'Not yet. Please write a story on our Scrum board.',
                        '',
                        'Regards,',
                        'Evermannella @ ChiaLab srl',
                    ],
                ],
            ],
            'missing' => [
                new \LogicException('You need specify one destination on to, cc or bcc.'),
                [],
            ],
        ];
    }

    /**
     * Test run method
     *
     * @param mixed $expected Expected result.
     * @param array $payload Payload data.
     * @return void
     *
     * @covers ::run()
     * @dataProvider runProvider()
     */
    public function testRun($expected, array $payload)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $mailService = new MailService();
        $result = $mailService->run($payload, ['transport' => 'test']);
        array_walk($result, function (&$val) {
            $val = explode("\r\n", $val);
        });

        static::assertArrayHasKey('headers', $result);
        foreach ($expected['headers'] as $header) {
            static::assertContains($header, $result['headers']);
        }
        static::assertArrayHasKey('message', $result);
        static::assertSame($expected['message'], $result['message']);
    }
}
