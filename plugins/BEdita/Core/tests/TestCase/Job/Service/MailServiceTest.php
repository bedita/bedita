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
use Cake\Mailer\TransportFactory;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Job\Service\MailService} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Job\Service\MailService
 */
class MailServiceTest extends TestCase
{
    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        TransportFactory::drop('test');
        TransportFactory::setConfig('test', [
            'className' => 'Debug',
        ]);

        parent::setUp();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();

        TransportFactory::drop('test');
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
                    'from' => ['gustavo.supporto@example.org' => 'Gustavo'],
                    'to' => ['evermannella@example.org' => 'Evermannella'],
                    'subject' => 'Re: Have you installed the latest version of Synapse?',
                    'message' => [
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
        $email = Hash::get($result, 'email');
        array_walk($email, function (&$val) {
            $val = explode("\r\n", $val);
        });

        static::assertTrue(Hash::get($result, 'success'));
        static::assertArrayHasKey('headers', $email);
        foreach ($expected['headers'] as $header) {
            static::assertContains($header, $email['headers']);
        }
        static::assertArrayHasKey('message', $email);
        static::assertSame($expected['message'], $email['message']);
    }
}
