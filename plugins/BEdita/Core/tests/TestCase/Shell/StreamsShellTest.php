<?php
namespace BEdita\Core\Test\TestCase\Shell;

use BEdita\Core\Filesystem\FilesystemRegistry;
use BEdita\Core\Model\Entity\EndpointPermission;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\ConsoleIntegrationTestCase;
use Cake\Utility\Inflector;

/**
 * \BEdita\Core\Shell\StreamsShell Test Case
 *
 * @coversDefaultClass \BEdita\Core\Shell\StreamsShell
 */
class StreamsShellTest extends ConsoleIntegrationTestCase
{
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
     * List of files to keep in test filesystem, and their contents.
     *
     * @var \Cake\Collection\Collection
     */
    private $keep = [];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        FilesystemRegistry::setConfig(Configure::read('Filesystem'));
        $this->Streams = TableRegistry::get('Streams');

        $mountManager = FilesystemRegistry::getMountManager();
        $this->keep = collection($mountManager->listContents('default://'))
            ->map(function (array $object) use ($mountManager) {
                $path = sprintf('%s://%s', $object['filesystem'], $object['path']);
                $contents = fopen('php://memory', 'wb+');
                fwrite($contents, $mountManager->read($path));
                fseek($contents, 0);

                return compact('contents', 'path');
            })
            ->compile();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        // Cleanup test filesystem.
        $mountManager = FilesystemRegistry::getMountManager();
        $keep = $this->keep
            ->each(function (array $object) use ($mountManager) {
                $mountManager->putStream($object['path'], $object['contents']);
            })
            ->map(function (array $object) {
                return $object['path'];
            })
            ->toList();
        collection($mountManager->listContents('default://'))
            ->map(function (array $object) {
                return sprintf('%s://%s', $object['filesystem'], $object['path']);
            })
            ->reject(function ($uri) use ($keep) {
                return in_array($uri, $keep);
            })
            ->each([$mountManager, 'delete']);

        unset($this->Streams);
        FilesystemRegistry::dropAll();

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
     *
     * @dataProvider removeOrphansProvider()
     * @covers ::removeOrphans()
     */
    public function testRemoveOrphans($expected, $days)
    {
        $count = TableRegistry::get('Streams')->find()->count();
        $this->exec(sprintf('streams removeOrphans --days %d', $days));

        $this->assertExitCode(Shell::CODE_SUCCESS);
        $this->assertErrorEmpty();

        $count -= TableRegistry::get('Streams')->find()->count();
        static::assertEquals($expected, $count);
    }
}
