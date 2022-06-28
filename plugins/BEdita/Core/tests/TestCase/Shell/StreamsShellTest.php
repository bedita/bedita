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
     * Test `refreshMetadata` method
     *
     * @return void
     * @covers ::refreshMetadata()
     * @covers ::updateStreamMetadata()
     * @covers ::streamsGenerator()
     */
    public function testRefreshMetadata(): void
    {
        // check width population if initial width is not available
        $this->Streams->updateAll(['width' => null], []);
        $this->exec('streams refreshMetadata');

        $results = $this->Streams->find('all')->all();
        $data = $results->toList();

        foreach ($data as $entry) {
            $entry['original_width'] = $entry['width'];

            if (preg_match('/image\//', $entry['mime_type'])) {
                $this->assertNotNull($entry['width']);
            }
        }

        // check width population with force option
        $this->Streams->updateAll(['width' => 800], []);
        $this->exec('streams refreshMetadata --force');

        $results = $this->Streams->find('all')->all();
        $lastData = $results->toList();

        foreach ($lastData as $entry) {
            if (preg_match('/image\//', $entry['mime_type'])) {
                $originalEntry = current(array_filter($data, function ($e) use ($entry) {
                    return $e['uuid'] === $entry['uuid'];
                }));

                $this->assertEquals($originalEntry['original_width'], $entry['width']);
            }
        }
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
