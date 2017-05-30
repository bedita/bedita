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

namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Job\JobService;
use BEdita\Core\Job\ServiceRegistry;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Model\Entity\AsyncJob
 */
class AsyncJobTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\AsyncJobsTable
     */
    public $AsyncJobs;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.async_jobs',
    ];

    /**
     * Async job connection config.
     *
     * @var array
     */
    protected $connection;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->AsyncJobs = TableRegistry::get('AsyncJobs');

        if (in_array('async_jobs', ConnectionManager::configured())) {
            $this->connection = ConnectionManager::getConfig('async_jobs');
            ConnectionManager::drop('async_jobs');
        }
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->AsyncJobs);

        if (in_array('async_jobs', ConnectionManager::configured())) {
            ConnectionManager::drop('async_jobs');
        }
        if (!empty($this->connection)) {
            ConnectionManager::setConfig('async_jobs', $this->connection);
        }

        parent::tearDown();
    }

    /**
     * Data provider for `testGetStatus` test case.
     *
     * @return array
     */
    public function getStatusProvider()
    {
        return [
            'pending' => [
                'pending',
                'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c',
            ],
            'planned' => [
                'planned',
                '66594f3c-995f-49d2-9192-382baf1a12b3',
            ],
            'completed' => [
                'completed',
                '1e2d1c66-c0bb-47d7-be5a-5bc92202333e',
            ],
            'locked' => [
                'locked',
                '6407afa6-96a3-4aeb-90c1-1541756efdef',
            ],
            'no more attempts' => [
                'failed',
                '40e22034-213f-4028-9930-81c0ed79c5a6',
            ],
            'expired' => [
                'failed',
                '0c833458-dff1-4fbb-bbf6-a30818b60616',
            ],
        ];
    }

    /**
     * Test magic getter for status.
     *
     * @param string $expected Expected status.
     * @param string $uuid UUID of
     * @return void
     *
     * @dataProvider getStatusProvider()
     * @covers ::_getStatus()
     */
    public function testGetStatus($expected, $uuid)
    {
        $entity = $this->AsyncJobs->get($uuid);
        $status = $entity->status;

        static::assertSame($expected, $status);
    }

    /**
     * Test running a non-locked asynchronous job.
     *
     * @return void
     *
     * @expectedException \BadMethodCallException
     * @expectedExceptionMessage Only locked jobs can be run
     * @covers ::run()
     */
    public function testRunNotLocked()
    {
        $this->AsyncJobs->get('1e2d1c66-c0bb-47d7-be5a-5bc92202333e')->run();
    }

    /**
     * Test running an asynchronous job.
     *
     * @return void
     *
     * @covers ::run()
     */
    public function testRun()
    {
        $service = $this->getMockBuilder(JobService::class)->getMock();
        $service->method('run')->will(static::returnValue(true));
        ServiceRegistry::set('example', $service);

        $result = $this->AsyncJobs->lock('d6bb8c84-6b29-432e-bb84-c3c4b2c1b99c')->run();

        static::assertTrue($result);
    }
}
