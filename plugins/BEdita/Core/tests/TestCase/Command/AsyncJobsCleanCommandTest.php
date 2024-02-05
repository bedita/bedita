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
     * Test `execute` method
     *
     * @return void
     * @covers ::execute()
     */
    public function testExecute(): void
    {
        $this->exec('async_jobs_clean --help');
        $this->assertOutputContains('Cleaning async jobs older than 1 month');
        $this->assertOutputContains('Deleted');
        $this->assertOutputContains('Done');
    }
}
