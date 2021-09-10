<?php
namespace BEdita\Core\Test\TestCase\Command;

use Cake\TestSuite\ConsoleIntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * {@see BEdita\Core\Command\FixHistoryCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\FixHistoryCommand
 */
class FixHistoryCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Streams',
        'plugin.BEdita/Core.History',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->useCommandRunner();
    }

    /**
     * Test buildOptionParser method
     *
     * @return void
     *
     * @covers ::buildOptionParser()
     */
    public function testBuildOptionParser()
    {
        $this->exec('fix_history --help');
        $this->assertOutputContains('Object ID to check');
        $this->assertOutputContains('Object type name to check');
    }

    /**
     * Test `execute` method
     *
     * @return void
     *
     * @covers ::execute()
     * @covers ::initialize()
     * @covers ::fixHistoryCreate()
     * @covers ::fixHistoryUpdate()
     * @covers ::historyEntity()
     * @covers ::missingHistoryQuery()
     * @covers ::objectsGenerator()
     */
    public function testExecute(): void
    {
        $this->exec('fix_history');
        $this->assertExitSuccess();
        $this->assertOutputContains('History creation items fixed: 14');
        $this->assertOutputContains('History update items fixed: 1');
    }

    /**
     * Test `execute` with `id` and `type` option
     *
     * @return void
     *
     * @covers ::execute()
     * @covers ::missingHistoryQuery()
     */
    public function testOptionsExecute(): void
    {
        $this->exec('fix_history --type users --id 5');
        $this->assertExitSuccess();
        $this->assertOutputContains('History creation items fixed: 1');
        $this->assertOutputContains('History update items fixed: 0');
    }
}
