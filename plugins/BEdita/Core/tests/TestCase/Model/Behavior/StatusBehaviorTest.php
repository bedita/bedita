<?php
namespace BEdita\Core\Test\TestCase\Model\Behavior;

use BEdita\Core\Exception\BadFilterException;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Behavior\StatusBehavior} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Behavior\StatusBehavior
 */
class StatusBehaviorTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\ObjectsTable
     */
    public $Objects;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.DateRanges',
        'plugin.BEdita/Core.Translations',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
        'plugin.BEdita/Core.Tags',
        'plugin.BEdita/Core.ObjectTags',
        'plugin.BEdita/Core.History',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Objects = TableRegistry::getTableLocator()->get('Objects');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Objects);

        parent::tearDown();
    }

    /**
     * Data provider for `checkStatus`.
     *
     * @return array
     */
    public function checkStatusProvider(): array
    {
        return [
            'no conf' => [
                'draft',
                [
                    'status' => 'draft',
                ],
                '',
            ],
            'error' => [
                new BadRequestException('Status "draft" is not consistent with configured Status.level "on"'),
                [
                    'status' => 'draft',
                ],
                'on',
            ],
            'ok' => [
                'draft',
                [
                    'status' => 'draft',
                ],
                'draft',
            ],
        ];
    }

    /**
     * Test `checkStatus()`.
     *
     * @param string|\Exception $expected Status value or Exception.
     * @param string $config Status level config.
     * @param array $data Save input data.
     * @return void
     * @dataProvider checkStatusProvider()
     * @covers ::checkStatus()
     */
    public function testCheckStatus($expected, array $data, string $config = ''): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        if (!empty($config)) {
            Configure::write('Status.level', $config);
        }

        $object = $this->Objects->get(4);
        $object = $this->Objects->patchEntity($object, $data);
        $object = $this->Objects->save($object);

        static::assertSame($expected, $object->get('status'));
    }

    /**
     * Data provider for `testFindStatus`.
     *
     * @return array
     */
    public function findStatusLevelProvider()
    {
        return [
            'too many options' => [
                new BadFilterException('Invalid options for finder "status"'),
                [1, 2, 3],
            ],
            'invalid array' => [
                new BadFilterException('Invalid options for finder "status"'),
                ['gustavo' => 'on'],
            ],
            'on' => [
                ['on'],
                ['on'],
            ],
            'draft' => [
                ['on', 'draft'],
                ['draft'],
            ],
            'off' => [
                ['on', 'draft', 'off'],
                ['off'],
            ],
            'all' => [
                ['on', 'draft', 'off'],
                ['all'],
            ],
            'invalid level' => [
                new BadFilterException('Invalid options for finder "status"'),
                ['invalid level'],
            ],
        ];
    }

    /**
     * Test `findStatusLevel()`.
     *
     * @param array $expected Expected result.
     * @param array $options Finder options.
     * @return void
     * @dataProvider findStatusLevelProvider()
     * @covers ::findStatusLevel()
     */
    public function testFindStatus($expected, array $options)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        } else {
            $expected = $this->Objects->find('list')
                ->where(['status IN' => $expected])
                ->toArray();
            ksort($expected);
        }

        $actual = $this->Objects->find('list')
            ->find('statusLevel', $options)
            ->toArray();
        ksort($actual);

        static::assertSame($expected, $actual);
    }
}
