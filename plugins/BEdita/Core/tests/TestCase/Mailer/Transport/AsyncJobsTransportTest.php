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

namespace BEdita\Core\Test\TestCase\Mailer\Transport;

use BEdita\Core\Job\Service\MailService;
use Cake\Mailer\Email;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Mailer\Transport\AsyncJobsTransport
 */
class AsyncJobsTransportTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.async_jobs',
    ];

    /**
     * Asynchronous jobs table.
     *
     * @var \BEdita\Core\Model\Table\AsyncJobsTable
     */
    protected $AsyncJobs;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        Email::dropTransport('test');
        Email::setConfigTransport('test', [
            'className' => 'BEdita/Core.AsyncJobs',
        ]);
        Email::dropTransport('debug');
        Email::setConfigTransport('debug', [
            'className' => 'Debug',
        ]);
        Email::drop('test');
        Email::setConfig('test', [
            'transport' => 'test',
            'from' => [
                'gustavo.supporto@example.org' => 'Gustavo',
            ],
        ]);

        $this->AsyncJobs = TableRegistry::get('AsyncJobs');

        parent::setUp();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        Email::drop('test');
        Email::dropTransport('test');
        Email::dropTransport('debug');
    }

    /**
     * Test creation of asynchronous jobs.
     *
     * @return void
     *
     * @covers ::send()
     */
    public function testSend()
    {
        $before = $this->AsyncJobs->find()->count();

        Email::deliver(
            ['evermannella@example.org' => 'Evermannella'],
            'Re Have you installed the latest version of Synapse?',
            [
                'Not yet. Please write a story on our Scrum board.',
                '',
                'Regards,',
                'Evermannella @ ChiaLab srl',
            ],
            'test'
        );

        $after = $this->AsyncJobs->find()->count();
        $mailJobs = $this->AsyncJobs->find()->where(['service' => 'mail'])->count();

        static::assertSame($before + 1, $after);
        static::assertSame(1, $mailJobs);
    }

    /**
     * Test creation of asynchronous jobs.
     *
     * @return void
     *
     * @covers ::send()
     */
    public function testSendPriority()
    {
        Email::dropTransport('test');
        Email::setConfigTransport('test', [
            'className' => 'BEdita/Core.AsyncJobs',
            'priority' => 1000,
        ]);

        Email::deliver(
            ['evermannella@example.org' => 'Evermannella'],
            'Re: Have you installed the latest version of Synapse?',
            "Not yet. Please write a story on our Scrum board.\r\n\r\nRegards,\r\nEvermannella @ ChiaLab srl",
            'test'
        );

        /* @var \BEdita\Core\Model\Entity\AsyncJob $asyncJob */
        $asyncJob = $this->AsyncJobs->find()->where(['service' => 'mail'])->first();

        static::assertInstanceOf($this->AsyncJobs->getEntityClass(), $asyncJob);
        static::assertSame(1000, $asyncJob->priority);
    }

    /**
     * Test creation of asynchronous jobs and later real email sending.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testAsyncSend()
    {
        $expected = [
            'headers' => [
                'From: Gustavo <gustavo.supporto@example.org>',
                'To: Evermannella <evermannella@example.org>',
                'Subject: ' . mb_encode_mimeheader('Re: Have you installed the latest version of Synapse?', mb_internal_encoding(), 'B'),
            ],
            'message' => [
                'Not yet. Please write a story on our Scrum board.',
                '',
                'Regards,',
                'Evermannella @ ChiaLab srl',
            ],
        ];

        Email::deliver(
            ['evermannella@example.org' => 'Evermannella'],
            'Re: Have you installed the latest version of Synapse?',
            "Not yet. Please write a story on our Scrum board.\r\n\r\nRegards,\r\nEvermannella @ ChiaLab srl",
            'test'
        );

        /* @var \BEdita\Core\Model\Entity\AsyncJob $asyncJob */
        $asyncJob = $this->AsyncJobs->find()->where(['service' => 'mail'])->first();

        static::assertInstanceOf($this->AsyncJobs->getEntityClass(), $asyncJob);

        $mailService = new MailService();
        $result = $mailService->run($asyncJob->payload, ['transport' => 'debug']);
        array_walk($result, function (&$val) {
            $val = explode("\r\n", $val);
        });

        static::assertArrayHasKey('headers', $result);
        foreach ($expected['headers'] as $header) {
            static::assertContains($header, $result['headers']);
        }
        static::assertArrayHasKey('message', $result);
        static::assertArraySubset($expected['message'], $result['message']);
    }
}
