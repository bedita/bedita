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

namespace BEdita\Core\Test\TestCase\Shell;

use BEdita\Core\Job\JobService;
use BEdita\Core\Job\ServiceRegistry;
use BEdita\Core\TestSuite\ShellTestCase;

/**
 * {@see \BEdita\Core\Shell\JobsShell} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Shell\JobsShell
 */
class JobsShellTest extends ShellTestCase
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
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        ServiceRegistry::reset();
    }

    /**
     * Get mock service.
     *
     * @param bool|\Exception $return Return value for `run()` method.
     * @return \BEdita\Core\Job\JobService
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
     * Test run method.
     *
     * @return void
     * @covers ::run()
     */
    public function testRun()
    {
        $uuid = 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c';
        ServiceRegistry::set('example', $this->getMockService());

        $result = $this->invoke(['jobs', 'run', $uuid]);

        $this->assertNotAborted();
        $this->assertOutputContains('completed successfully');
        static::assertNull($result);
    }

    /**
     * Test run method with invalid job.
     *
     * @return void
     * @covers ::run()
     */
    public function testRunInvalid()
    {
        $uuid = 'gustavo';
        ServiceRegistry::set('example', $this->getMockService());

        $result = $this->invoke(['jobs', 'run', $uuid]);

        $this->assertNotAborted();
        $this->assertOutputContains('Could not obtain lock');
        static::assertNull($result);
    }

    /**
     * Test run method with smooth failure.
     *
     * @return void
     * @covers ::run()
     */
    public function testRunFailException()
    {
        $exception = new \BadMethodCallException('example');
        $uuid = 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c';
        ServiceRegistry::set('example', $this->getMockService($exception));

        $result = $this->invoke(['jobs', 'run', $uuid]);

        $this->assertNotAborted();
        $this->assertErrorContains('BadMethodCallException with message "example"');
        $this->assertErrorContains('failed');
        static::assertNull($result);
    }

    /**
     * Test run method with smooth failure.
     *
     * @return void
     * @covers ::run()
     */
    public function testRunFailSmooth()
    {
        $uuid = 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c';
        ServiceRegistry::set('example', $this->getMockService(false));

        $result = $this->invoke(['jobs', 'run', $uuid]);

        $this->assertNotAborted();
        $this->assertErrorContains('failed');
        static::assertNull($result);
    }

    /**
     * Test run method with fail hard mode.
     *
     * @return void
     * @covers ::run()
     */
    public function testRunFailHard()
    {
        $uuid = 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c';
        ServiceRegistry::set('example', $this->getMockService(false));

        $result = $this->invoke(['jobs', 'run', '-F', $uuid]);

        $this->assertAborted();
        $this->assertErrorContains('failed');
        static::assertSame(1, $result);
    }

    /**
     * Test run pending jobs.
     *
     * @return void
     * @covers ::pending()
     */
    public function testPending()
    {
        ServiceRegistry::set('example', $this->getMockService());

        $result = $this->invoke(['jobs', 'pending']);

        $this->assertNotAborted();
        $this->assertOutputContains('completed successfully');
        $this->assertOutputContains('Operation complete');
        static::assertNull($result);
    }

    /**
     * Test run pending jobs with no pending jobs to run.
     *
     * @return void
     * @covers ::pending()
     */
    public function testPendingEmpty()
    {
        ServiceRegistry::set('example', $this->getMockService());

        $result = $this->invoke(['jobs', 'pending', '--limit', '0']);

        $this->assertNotAborted();
        $this->assertOutputContains('Nothing to do');
        static::assertNull($result);
    }

    /**
     * Test run pending jobs with fail hard mode.
     *
     * @return void
     * @covers ::pending()
     */
    public function testPendingFailHard()
    {
        ServiceRegistry::set('example', $this->getMockService(false));

        $result = $this->invoke(['jobs', 'pending', '-F']);

        $this->assertAborted();
        $this->assertErrorContains('failed');
        static::assertSame(1, $result);
    }
}
