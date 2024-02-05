<?php
declare(strict_types=1);

namespace BEdita\Core\Test\TestCase\Command;

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * {@see BEdita\Core\Command\AsyncJobsCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\AsyncJobsCommand
 */
class AsyncJobsCleanCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();
    }

    /**
     * Test buildOptionParser method
     *
     * @return void
     * @covers ::buildOptionParser()
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
     * @covers ::execute()
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
     * @covers ::execute()
     */
    public function testExecuteSinceService(): void
    {
        $this->exec('async_jobs_clean --since 2024-01-01 --service dummy');
        $this->assertOutputContains('Cleaning async jobs, since 2024-01-01, for service dummy');
        $this->assertOutputContains('Deleted');
        $this->assertOutputContains('Done');
        $this->assertExitSuccess();
    }
}
