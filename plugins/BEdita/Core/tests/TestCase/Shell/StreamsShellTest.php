<?php
namespace BEdita\Core\Test\TestCase\Shell;

use BEdita\Core\Test\Utility\TestFilesystemTrait;
use Cake\Console\Shell;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\ConsoleIntegrationTestCase;

/**
 * \BEdita\Core\Shell\StreamsShell Test Case
 *
 * @property \BEdita\Core\Model\Table\StreamsTable $Streams
 * @coversDefaultClass \BEdita\Core\Shell\StreamsShell
 */
class StreamsShellTest extends ConsoleIntegrationTestCase
{
    use TestFilesystemTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->filesystemSetup(true, true);
        $this->Streams = TableRegistry::getTableLocator()->get('Streams');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        $this->filesystemRestore();
        unset($this->Streams);
        parent::tearDown();
    }

    /**
     * Data provider for `testRemoveOrphans` test case.
     *
     * @return array
     */
    public function removeOrphansProvider()
    {
        return [
            'basic test' => [
                1,
                10,
            ],
        ];
    }

    /**
     * Test `removeOrphans` method
     *
     * @param int $expected Expected number of removed streams
     * @param int $days The days.
     * @return void
     * @dataProvider removeOrphansProvider()
     * @covers ::removeOrphans()
     */
    public function testRemoveOrphans($expected, $days)
    {
        $count = TableRegistry::getTableLocator()->get('Streams')->find()->count();
        $this->exec(sprintf('streams removeOrphans --days %d', $days));

        $this->assertExitCode(Shell::CODE_SUCCESS);
        $this->assertErrorEmpty();

        $count -= TableRegistry::getTableLocator()->get('Streams')->find()->count();
        static::assertEquals($expected, $count);
    }
}
