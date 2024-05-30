<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Job;

use BEdita\Core\Job\JobService;
use BEdita\Core\Job\QueueJob;
use BEdita\Core\Job\ServiceRegistry;
use Cake\Queue\Job\Message;
use Cake\TestSuite\TestCase;
use Enqueue\Null\NullConnectionFactory;
use Enqueue\Null\NullMessage;
use Interop\Queue\Processor;
use RuntimeException;

/**
 * {@see \BEdita\Core\Job\QueueJob} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Job\QueueJob
 */
class QueueJobTest extends TestCase
{
    /**
     * Test UUID
     *
     * @var string
     */
    public const TEST_UUID = 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c';

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.AsyncJobs',
    ];

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();
        ServiceRegistry::reset();
    }

    /**
     * Get mock service.
     *
     * @param bool|\Exception $return Return value for `run()` method.
     * @return \BEdita\Core\Job\JobService|\PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMockService($return = true)
    {
        $service = $this->getMockBuilder(JobService::class)
            ->getMock();

        $method = $service->method('run');
        $method->will(static::returnValue($return));
        if ($return instanceof \Exception) {
            $method->willThrowException($return);
        }

        return $service;
    }

    /**
     * Create queue message
     *
     * @param array $body Message body
     * @return \Cake\Queue\Job\Message
     */
    protected function createMessage(array $body): Message
    {
        $messageBody = json_encode($body);
        $context = (new NullConnectionFactory())->createContext();
        $originalMessage = new NullMessage($messageBody);

        return new Message($originalMessage, $context);
    }

    /**
     * Data provider for `testExecute`
     *
     * @return array
     */
    public function executeProvider(): array
    {
        return [
            'ok' => [
                Processor::ACK,
                true,
            ],
            'not ok' => [
                Processor::REJECT,
                false,
            ],
            'non existing uuid' => [
                Processor::REJECT,
                true,
                '1e1e1e1e-c0c0-4747-bebe-5b5b5b5b5b5b',
            ],
            'not pending uuid' => [
                Processor::REJECT,
                true,
                '1e2d1c66-c0bb-47d7-be5a-5bc92202333e',
            ],
            'exception' => [
                Processor::REJECT,
                new RuntimeException('Big big error'),
            ],
        ];
    }

    /**
     * Test `execute` method
     *
     * @param string $expected Expected result
     * @param bool|\Exception $return Service return value
     * @param string $uuid Job UUID
     * @return void
     * @dataProvider executeProvider
     * @covers ::execute()
     * @covers ::run()
     */
    public function testExecute(string $expected, $return, string $uuid = self::TEST_UUID): void
    {
        ServiceRegistry::set('example', $this->getMockService($return));

        $message = $this->createMessage(['data' => compact('uuid')]);
        $job = new QueueJob();
        $result = $job->execute($message);

        static::assertSame($expected, $result);
    }
}
