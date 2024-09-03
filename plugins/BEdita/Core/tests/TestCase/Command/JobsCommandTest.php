<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Command;

use BEdita\Core\Job\JobService;
use BEdita\Core\Job\ServiceRegistry;
use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use Cake\Utility\Text;

/**
 * {@see BEdita\Core\Command\JobsCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\JobsCommand
 */
class JobsCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

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
    protected function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();
    }

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
     * Test buildOptionParser method
     *
     * @return void
     * @covers ::buildOptionParser()
     */
    public function testBuildOptionParser()
    {
        $this->exec('jobs --help');
        $this->assertOutputContains('Action to perform: process, pending, run');
        $this->assertOutputContains('UUID of job to be processed');
        $this->assertOutputContains('Enable "fail hard" mode');
        $this->assertOutputContains('Limit number of jobs being run');
        $this->assertOutputContains('Run only pending jobs with a priority higher than');
        $this->assertOutputContains('Run only pending jobs for the supplied service');
    }

    /**
     * Test `process` method via `jobs run <uuid>`.
     *
     * @return void
     * @covers ::execute()
     * @covers ::process()
     */
    public function testRetrocompatibility(): void
    {
        $uuid = 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c';
        ServiceRegistry::set('example', $this->getMockService());
        $this->exec(sprintf('jobs run %s', $uuid));
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('completed successfully');
        $this->assertErrorEmpty();
    }

    /**
     * Test `process` method.
     *
     * @return void
     * @covers ::execute()
     * @covers ::process()
     */
    public function testProcess()
    {
        $uuid = 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c';
        ServiceRegistry::set('example', $this->getMockService());
        $this->exec(sprintf('jobs process %s', $uuid));
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('completed successfully');
        $this->assertErrorEmpty();
    }

    /**
     * Test `process` method with invalid job.
     *
     * @return void
     * @covers ::execute()
     * @covers ::process()
     */
    public function testProcessInvalid()
    {
        $uuid = Text::uuid(); // This UUID hopefully does not exist. :)
        ServiceRegistry::set('example', $this->getMockService());
        $this->exec(sprintf('jobs process %s', $uuid));
        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertOutputContains('Could not obtain lock');
        $this->assertErrorEmpty();
    }

    /**
     * Test `process` method with smooth failure.
     *
     * @return void
     * @covers ::execute()
     * @covers ::process()
     */
    public function testProcessFailException()
    {
        $exception = new \BadMethodCallException('example');
        $uuid = 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c';
        ServiceRegistry::set('example', $this->getMockService($exception));
        $this->exec(sprintf('jobs process %s', $uuid));
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertErrorContains('BadMethodCallException with message "example"');
        $this->assertErrorContains('failed');
    }

    /**
     * Test `process` method with smooth failure.
     *
     * @return void
     * @covers ::execute()
     * @covers ::process()
     */
    public function testProcessFailSmooth()
    {
        $uuid = 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c';
        ServiceRegistry::set('example', $this->getMockService(false));
        $this->exec(sprintf('jobs process %s', $uuid));
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertErrorContains('failed');
    }

    /**
     * Test `process` method with fail hard mode.
     *
     * @return void
     * @covers ::execute()
     * @covers ::process()
     */
    public function testProcessFailHard()
    {
        $uuid = 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c';
        ServiceRegistry::set('example', $this->getMockService(false));
        $this->exec(sprintf('jobs process -F %s', $uuid));
        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains('failed');
    }

    /**
     * Test run pending jobs.
     *
     * @return void
     * @covers ::execute()
     * @covers ::pending()
     */
    public function testPending()
    {
        ServiceRegistry::set('example', $this->getMockService());
        ServiceRegistry::set('example2', $this->getMockService());
        ServiceRegistry::set('signup', $this->getMockService());
        $this->exec('jobs pending');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('completed successfully');
        $this->assertOutputContains('Operation complete');
        $this->assertErrorEmpty();
    }

    /**
     * Test run pending jobs with no pending jobs to run.
     *
     * @return void
     * @covers ::execute()
     * @covers ::pending()
     */
    public function testPendingEmpty()
    {
        ServiceRegistry::set('example', $this->getMockService());
        $this->exec('jobs pending --limit 0');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('Nothing to do');
        $this->assertErrorEmpty();
    }

    /**
     * Test run pending jobs with fail hard mode.
     *
     * @return void
     * @covers ::execute()
     * @covers ::pending()
     */
    public function testPendingFailHard()
    {
        ServiceRegistry::set('example', $this->getMockService(false));
        $this->exec('jobs pending -F');
        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains('failed');
    }
}
