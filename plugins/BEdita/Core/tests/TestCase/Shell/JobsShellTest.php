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
use Cake\Console\Shell;
use Cake\TestSuite\ConsoleIntegrationTestCase;
use Cake\Utility\Text;

/**
 * {@see \BEdita\Core\Shell\JobsShell} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Shell\JobsShell
 */
class JobsShellTest extends ConsoleIntegrationTestCase
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
     * @return \BEdita\Core\Job\JobService|\PHPUnit_Framework_MockObject_MockObject
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

        $this->exec(sprintf('jobs run %s', $uuid));

        $this->assertExitCode(Shell::CODE_SUCCESS);
        $this->assertOutputContains('completed successfully');
        $this->assertErrorEmpty();
    }

    /**
     * Test run method with invalid job.
     *
     * @return void
     * @covers ::run()
     */
    public function testRunInvalid()
    {
        $uuid = Text::uuid(); // This UUID hopefully does not exist. :)
        ServiceRegistry::set('example', $this->getMockService());

        $this->exec(sprintf('jobs run %s', $uuid));

        $this->assertExitCode(Shell::CODE_SUCCESS);
        $this->assertOutputContains('Could not obtain lock');
        $this->assertErrorEmpty();
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

        $this->exec(sprintf('jobs run %s', $uuid));

        $this->assertExitCode(Shell::CODE_SUCCESS);
        $this->assertErrorContains('BadMethodCallException with message "example"');
        $this->assertErrorContains('failed');
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

        $this->exec(sprintf('jobs run %s', $uuid));

        $this->assertExitCode(Shell::CODE_SUCCESS);
        $this->assertErrorContains('failed');
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

        $this->exec(sprintf('jobs run -F %s', $uuid));

        $this->assertExitCode(Shell::CODE_ERROR);
        $this->assertErrorContains('failed');
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
        ServiceRegistry::set('example2', $this->getMockService());
        ServiceRegistry::set('signup', $this->getMockService());

        $this->exec('jobs pending');

        $this->assertExitCode(Shell::CODE_SUCCESS);
        $this->assertOutputContains('completed successfully');
        $this->assertOutputContains('Operation complete');
        $this->assertErrorEmpty();
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

        $this->exec('jobs pending --limit 0');

        $this->assertExitCode(Shell::CODE_SUCCESS);
        $this->assertOutputContains('Nothing to do');
        $this->assertErrorEmpty();
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

        $this->exec('jobs pending -F');

        $this->assertExitCode(Shell::CODE_ERROR);
        $this->assertErrorContains('failed');
    }
}
