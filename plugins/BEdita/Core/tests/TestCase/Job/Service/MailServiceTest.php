<?php
declare(strict_types=1);

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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \BEdita\Core\Job\Service\MailService} Test Case
 */
#[CoversClass(MailService::class)]
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
    public static function runProvider(): array
    {
        return [
            'simple' => [
                true,
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
                false,
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
     */
    #[DataProvider('runProvider')]
    public function testRun($expected, array $payload)
    {
        $mailService = new MailService();
        $result = $mailService->run($payload, ['transport' => 'test']);

        static::assertEquals($expected, $result);
    }
}
