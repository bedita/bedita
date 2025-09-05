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
use BEdita\Core\Model\Table\AsyncJobsTable;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Event\EventInterface;
use Cake\Event\EventManager;
use Cake\Queue\Job\Message;
use Cake\TestSuite\TestCase;
use Enqueue\Null\NullConnectionFactory;
use Enqueue\Null\NullMessage;
use Exception;
use Interop\Queue\Processor;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;

/**
 * {@see \BEdita\Core\Job\QueueJob} Test Case
 */
#[CoversClass(QueueJob::class)]
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
    protected array $fixtures = [
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
        if ($return instanceof Exception) {
            $method->willThrowException($return);
        } else {
            $method->willReturn($return);
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
    public static function executeProvider(): array
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
            'requeue' => [
                Processor::REQUEUE,
                false,
                'e533e1cf-b12c-4dbe-8fb7-b25fafbd2f76',
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
     */
    #[DataProvider('executeProvider')]
    public function testExecute(string $expected, $return, string $uuid = self::TEST_UUID): void
    {
        ServiceRegistry::set('example', $this->getMockService($return));

        $message = $this->createMessage(['data' => compact('uuid')]);
        $job = new QueueJob();
        $result = $job->execute($message);

        static::assertSame($expected, $result);
    }

    /**
     * Test the case when the service fails and then the job is missing.
     *
     * @return void
     */
    public function testServiceFailsThenMissingAsyncJob(): void
    {
        ServiceRegistry::set('example', $this->getMockService(false));
        $message = $this->createMessage(['data' => ['uuid' => self::TEST_UUID]]);

        $count = 0;
        $callback = function (EventInterface $event) use (&$count) {
            if (!$event->getSubject() instanceof AsyncJobsTable) {
                return;
            }

            // first time is `lock` then `unlock` and finally `get` in `QueueJob::execute`
            if ($count >= 2) {
                throw new RecordNotFoundException();
            }
            $count++;
        };
        EventManager::instance()->on('Model.beforeFind', $callback);

        $job = new QueueJob();
        $result = $job->execute($message);
        static::assertEquals(Processor::REJECT, $result);

        EventManager::instance()->off('Model.beforeFind', $callback);
    }
}
