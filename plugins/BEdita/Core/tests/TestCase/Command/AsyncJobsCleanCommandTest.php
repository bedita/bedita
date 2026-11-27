<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 Channelweb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Command;

use BEdita\Core\Command\AsyncJobsCleanCommand;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see BEdita\Core\Command\AsyncJobsCleanCommand} Test Case
 */
#[CoversClass(AsyncJobsCleanCommand::class)]
class AsyncJobsCleanCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @inheritDoc
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.AsyncJobs',
    ];

    /**
     * Test buildOptionParser method
     *
     * @return void
     */
    public function testBuildOptionParser()
    {
        $this->exec('async_jobs_clean --help');
        $this->assertOutputContains('Delete async jobs older than this date');
        $this->assertOutputContains('Delete async jobs for this service');
    }

    /**
     * Test `execute` method
     *
     * @return void
     */
    public function testExecute(): void
    {
        $this->exec('async_jobs_clean');
        $this->assertOutputContains('Cleaning async jobs, since -1 month');
        $this->assertOutputContains('Deleted');
        $this->assertOutputContains('Done');
        $this->assertExitSuccess();
    }

    /**
     * Test `execute` method with `--since` option and `--service` option
     *
     * @return void
     */
    public function testExecuteSinceService(): void
    {
        $this->exec('async_jobs_clean --since 2024-01-01 --service dummy');
        $this->assertOutputContains('Cleaning async jobs, since 2024-01-01, for service dummy');
        $this->assertOutputContains('Deleted');
        $this->assertOutputContains('Done');
        $this->assertExitSuccess();
    }

    /**
     * Test `execute` method with multiple `--service` options
     *
     * @return void
     */
    public function testExecuteMultipleServices(): void
    {
        $this->exec('async_jobs_clean --since 2018-01-01 --service example2 --service signup');
        $this->assertOutputContains('Cleaning async jobs, since 2018-01-01, for service example2, signup');
        $this->assertOutputContains('Deleted 2 async jobs');
        $this->assertExitSuccess();
        $services = $this->fetchTable('AsyncJobs')->find()->all()->extract('service')->toList();
        static::assertSame(['example'], array_values(array_unique($services)));
    }
}
