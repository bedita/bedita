<?php
namespace BEdita\Core\Test\TestCase\Shell;

use BEdita\Core\Job\JobService;
use BEdita\Core\Job\ServiceRunner;
use BEdita\Core\Shell\JobsShell;
use BEdita\Core\TestSuite\ShellTestCase;
use Cake\ORM\TableRegistry;

class Example implements JobService
{
    public function run($payload, $options = [])
    {
        return true;
    }
}
/**
 * \BEdita\Core\Shell\JobsShell Test Case
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
     * Test run method
     *
     * @return void
     * @covers ::run()
     */
    public function testRun()
    {
        ServiceRunner::register('example', new Example());
        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();

        // invoke without limit
        $this->invoke(['jobs', 'run'], [], $io);

        $pending = TableRegistry::get('AsyncJobs')->find('pending')->toArray();
        $this->assertEmpty($pending);

        // invoke with limit
        $this->invoke(['jobs', 'run', '--limit', '10'], [], $io);

        $pending = TableRegistry::get('AsyncJobs')->find('pending')->toArray();
        $this->assertEmpty($pending);
    }
}
