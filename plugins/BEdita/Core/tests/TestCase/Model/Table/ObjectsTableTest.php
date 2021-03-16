<?php
namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Model\Entity\ObjectEntity;
use BEdita\Core\Utility\Database;
use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Model\Table\ObjectsTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\ObjectsTable
 */
class ObjectsTableTest extends TestCase
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
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.ObjectTypes',
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
        'plugin.BEdita/Core.History',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Objects = TableRegistry::getTableLocator()->get('Objects');
        LoggedUser::setUser(['id' => 1]);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Objects);
        LoggedUser::resetUser();

        parent::tearDown();
    }

    /**
     * Test initialization.
     *
     * @return void
     * @coversNothing
     */
    public function testInitialization()
    {
        $this->Objects->initialize([]);
        $this->assertEquals('objects', $this->Objects->getTable());
        $this->assertEquals('id', $this->Objects->getPrimaryKey());
        $this->assertEquals('title', $this->Objects->getDisplayField());

        $this->assertInstanceOf('\Cake\ORM\Association\BelongsTo', $this->Objects->ObjectTypes);
        $this->assertInstanceOf('\Cake\ORM\Behavior\TimestampBehavior', $this->Objects->behaviors()->get('Timestamp'));
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'valid' => [
                true,
                [
                    'title' => 'title three',
                    'description' => 'another description',
                    'uname' => 'title-three',
                ],
            ],
            'notUniqueUname' => [
                false,
                [
                    'title' => 'title four',
                    'description' => 'another description',
                    'status' => 'on',
                    'uname' => 'title-one',
                    'lang' => 'en',
                ],
            ],
            'titleOnly' => [
                true,
                [
                    'title' => 'title only',
                ],
            ],
            'emptyForm' => [
                true,
                [
                    'title' => 'just another title',
                    'uname' => null,
                    'status' => null,
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     *
     * @return void
     * @dataProvider validationProvider
     * @coversNothing
     */
    public function testValidation($expected, array $data)
    {
        $object = $this->Objects->newEntity($data);
        $object->type = 'documents';

        $error = (bool)$object->getErrors();
        $this->assertEquals($expected, !$error);

        if ($expected) {
            $success = $this->Objects->save($object);
            $this->assertTrue((bool)$success);
        }
    }

    /**
     * Data provider for `testFindType` test case.
     *
     * @return array
     */
    public function findTypeProvider()
    {
        return [
            'documents' => [
                [
                    2 => 'title one',
                    3 => 'title two',
                    6 => 'title one deleted',
                    7 => 'title two deleted',
                    15 => null,
                ],
                [2],
            ],
            'multiple' => [
                [
                    2 => 'title one',
                    3 => 'title two',
                    4 => 'Gustavo Supporto profile',
                    6 => 'title one deleted',
                    7 => 'title two deleted',
                    15 => null,
                ],
                ['document', 'profiles'],
            ],
            'exclude' => [
                [
                    1 => 'Mr. First User',
                    4 => 'Gustavo Supporto profile',
                    5 => 'Miss Second User',
                    8 => 'The Two Towers',
                    9 => 'first event',
                    10 => 'first media',
                    11 => 'Root Folder',
                    12 => 'Sub Folder',
                    13 => 'Another Root Folder',
                    14 => 'second media',
                ],
                ['ne' => 'documents'],
            ],
            'missing' => [
                new RecordNotFoundException('Record not found in table "object_types"'),
                ['document', 'profiles', 0],
            ],
            'abstract' => [
                [
                    1 => 'Mr. First User',
                    2 => 'title one',
                    3 => 'title two',
                    4 => 'Gustavo Supporto profile',
                    5 => 'Miss Second User',
                    6 => 'title one deleted',
                    7 => 'title two deleted',
                    8 => 'The Two Towers',
                    9 => 'first event',
                    10 => 'first media',
                    11 => 'Root Folder',
                    12 => 'Sub Folder',
                    13 => 'Another Root Folder',
                    14 => 'second media',
                    15 => null,
                ],
                ['objects'],
            ],
            'polluted array' => [
                [
                    4 => 'Gustavo Supporto profile',
                ],
                [
                    'profiles',
                    'banana' => 33,
                ],
            ],
        ];
    }

    /**
     * Test object types finder.
     *
     * @param array|\Exception $expected Expected results.
     * @param array $types Array of object types to filter for.
     * @return void
     *
     * @dataProvider findTypeProvider
     * @covers ::findType()
     */
    public function testFindType($expected, array $types)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $result = $this->Objects->find('list')->find('type', $types)->toArray();

        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for `testFindDateRanges` test case.
     *
     * @return array
     */
    public function findDateRangesProvider()
    {
        return [
            'simple' => [
                [9],
                [
                    'start_date' => ['gt' => '2017-01-01'],
                ],
            ],
            'sub1' => [
                [],
                [
                    'date_ranges_min_start_date' => true,
                    'from_date' => '2019-01-01',
                ],
            ],
            'sub2' => [
                [9],
                [
                    'date_ranges_max_start_date' => true,
                ],
            ],
        ];
    }

    /**
     * Test object date ranges finder.
     * {@see \BEdita\Core\Model\Table\DateRangesTable} for a more detailed test case
     *
     * @param array $expected Expected results.
     * @param array $options Finder options.
     * @return void
     *
     * @dataProvider findDateRangesProvider
     * @covers ::findDateRanges()
     * @covers ::dateRangesSubQueryJoin()
     */
    public function testFindDateRanges(array $expected, array $options)
    {
        $result = $this->Objects->find('dateRanges', $options)->toArray();
        $this->assertEquals($expected, Hash::extract($result, '{n}.id'));
    }

    /**
     * Test save of date ranges using 'replace' save strategy ({@see https://github.com/bedita/bedita/issues/1152}).
     *
     * @return void
     *
     * @coversNothing
     */
    public function testSaveDateRanges()
    {
        $object = $this->Objects->newEntity();
        $object->type = 'events';

        $data = [
            'date_ranges' => [
                [
                    'start_date' => '1992-08-17',
                    'params' => [
                        'k' => 'v',
                    ],
                ],
            ],
        ];
        $object = $this->Objects->patchEntity($object, $data);
        $object = $this->Objects->save($object);
        if (!$object) {
            static::fail('Unable to save object');
        }
        $object = $this->Objects->get($object->id, ['contain' => ['DateRanges']]);
        static::assertCount(1, $object->date_ranges);
        static::assertEquals(['k' => 'v'], $object->date_ranges[0]['params']);

        $data['date_ranges'][0]['start_date'] = date('Y-m-d');
        $object = $this->Objects->patchEntity($object, $data);
        $object = $this->Objects->save($object);
        if (!$object) {
            static::fail('Unable to save object');
        }

        $object = $this->Objects->get($object->id, ['contain' => ['DateRanges']]);

        static::assertCount(1, $object->date_ranges);
        static::assertSame(1, $this->Objects->DateRanges->find()->where(['object_id' => $object->id])->count());
        static::assertSame(0, $this->Objects->DateRanges->find()->where(['object_id IS' => null])->count());
    }

    /**
     * Test finder for my objects.
     *
     * @return void
     *
     * @covers ::findMine()
     */
    public function testFindMine()
    {
        $expected = $this->Objects->find()
            ->find('list', ['keyField' => 'id', 'valueField' => 'id'])
            ->where(['created_by' => 1])
            ->toArray();

        $result = $this->Objects->find('mine')
            ->find('list', ['keyField' => 'id', 'valueField' => 'id'])
            ->toArray();

        static::assertEquals($expected, $result);
    }

    /**
     * Test save emojis in text fields.
     *
     * @return void
     * @coversNothing
     */
    public function testEmoji()
    {
        $object = $this->Objects->get(1);
        $expected = "🙈 😂 😱";
        $info = Database::basicInfo();
        if ($info['vendor'] == 'mysql' && (empty($info['encoding']) || $info['encoding'] != 'utf8mb4')) {
            $expected = "";
        }
        $object['description'] = $expected;
        $this->Objects->save($object);
        $object = $this->Objects->get(1);
        static::assertEquals($object['description'], $expected);
    }

    /**
     * Data provider for `testSaveAbstractDisabledTypes` test case.
     *
     * @return array
     */
    public function saveAbstractDisabledTypes()
    {
        return [
            'objects' => [
                true,
                true,
                'objects',
            ],
            'media' => [
                true,
                true,
                'media',
            ],
            'documents' => [
                false,
                true,
                'documents',
            ],
            'news' => [
                true,
                false,
                'news',
            ],
        ];
    }

    /**
     * Test that save of abstract or not enabled types fails as expected.
     *
     * @param bool $abstract Is the type abstract?
     * @param bool $enabled Is the type enabled?
     * @param string $type Type being saved.
     * @return void
     *
     * @covers ::beforeSave()
     * @dataProvider saveAbstractDisabledTypes()
     */
    public function testSaveAbstractDisabledTypes($abstract, $enabled, $type)
    {
        if ($abstract || !$enabled) {
            $this->expectException(PersistenceFailedException::class);
        }

        $object = $this->Objects->newEntity();
        $object->type = $type;

        $result = $this->Objects->saveOrFail($object);

        static::assertInstanceOf(ObjectEntity::class, $result);
    }

    /**
     * Data provider for `testGetId()`
     *
     * @return array
     */
    public function getIdProvider()
    {
        return [
            'id' => [1, 1],
            'idString' => [1, '1'],
            'uname' => [1, 'first-user'],
            'notFound' => [
                new RecordNotFoundException('Record not found in table "objects"'),
                'this-uname-doesnt-exist',
            ],
            'null' => [
                new RecordNotFoundException('Record not found in table "objects"'),
                null,
            ],
            'emptyString' => [
                new RecordNotFoundException('Record not found in table "objects"'),
                '',
            ],
        ];
    }

    /**
     * Test `getId()`
     *
     * @param mixed $expected The expected result.
     * @param int|string $uname The unique object identifier.
     * @return void
     *
     * @dataProvider getIdProvider
     * @covers ::getId()
     */
    public function testGetId($expected, $uname)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $id = $this->Objects->getId($uname);
        static::assertEquals($expected, $id);
    }

    /**
     * Test `findAncestor()`
     *
     * @return void
     *
     * @covers ::findAncestor()
     */
    public function testFindAncestor()
    {
        $objects = $this->Objects->find('ancestor', [11])->toArray();
        static::assertNotEmpty($objects);
        $ids = Hash::extract($objects, '{n}.id');
        static::assertEquals([12, 4, 2], $ids);
    }

    /**
     * Test `findParent()`
     *
     * @return void
     *
     * @covers ::findParent()
     */
    public function testFindParent()
    {
        $objects = $this->Objects->find('parent', [12])->toArray();
        static::assertNotEmpty($objects);
        $ids = Hash::extract($objects, '{n}.id');
        static::assertEquals([4], $ids);
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
     * @param array|\Exception $expected Expected result.
     * @param array $options Finder options.
     * @return void
     *
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

    /**
     * Data provider for `checkLangTag`.
     *
     * @return array
     */
    public function checkLangTagProvider()
    {
        return [
            'any lang' => [
                'en-US',
                [
                    'default' => null,
                ],
                [
                    'lang' => 'en-US',
                ],
            ],
            'use default' => [
                'en',
                [
                    'default' => 'en',
                ],
                [
                    'lang' => '',
                ],
            ],
        ];
    }

    /**
     * Test `checkLangTag()`.
     *
     * @param string $expected Expected result.
     * @param array $config I18n config.
     * @param array $data Save input data.
     * @return void
     *
     * @dataProvider checkLangTagProvider()
     * @covers ::checkLangTag()
     */
    public function testCheckLangTag($expected, array $config, array $data)
    {
        Configure::write('I18n', $config);

        $object = $this->Objects->get(3);
        $object = $this->Objects->patchEntity($object, $data);
        $object = $this->Objects->save($object);

        static::assertSame($expected, $object->get('lang'));
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
     *
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

        $id = Hash::get($data, 'id', 2);
        $object = $this->Objects->get($id);
        unset($data['id']);
        $object = $this->Objects->patchEntity($object, $data);
        $object = $this->Objects->save($object);

        static::assertSame($expected, $object->get('status'));
    }

    /**
     * Test `findTranslations()`.
     *
     * @return void
     *
     * @covers ::findTranslations()
     */
    public function testFindTranslations()
    {
        $result = $this->Objects->find('translations', ['lang' => 'fr'])
            ->where(['Objects.id' => 2])
            ->toArray();

        static::assertNotEmpty($result);
        static::assertSame(1, count($result));
        static::assertSame(2, $result[0]['id']);
    }

    /**
     * Data provider for `testFindAvailable`.
     *
     * @return array
     */
    public function findAvailableProvider()
    {
        return [
            'no status' => [
                12,
                ['id > 0']
            ],
            'status on' => [
                7,
                ['id > 5'],
                'on',
            ],
        ];
    }

    /**
     * Test `findAvailable()`.
     *
     * @return void
     *
     * @dataProvider findAvailableProvider()
     * @covers ::findAvailable()
     */
    public function testFindAvailable(int $expected, array $condition, string $statusLevel = null)
    {
        $result = $this->Objects->find('available')
            ->where($condition)
            ->toArray();
        if (!empty($statusLevel)) {
            Configure::write('Status.level', $statusLevel);
        }
        static::assertSame($expected, count($result));
    }

    /**
     * Test `findCategories` method.
     *
     * @return void
     *
     * @covers ::findCategories()
     * @covers ::categoriesQuery()
     */
    public function testFindCategories()
    {
        $result = TableRegistry::getTableLocator()
            ->get('Documents')
            ->find('categories', ['first-cat,second-cat'])
            ->toArray();
        static::assertSame(1, count($result));
    }

    /**
     * Test `findTags` method.
     *
     * @return void
     *
     * @covers ::findTags()
     * @covers ::categoriesQuery()
     */
    public function testFindTags()
    {
        $result = TableRegistry::getTableLocator()
            ->get('Profiles')
            ->find('tags', ['first-tag'])
            ->toArray();
        static::assertSame(1, count($result));
    }

    /**
     * Test `findUnameId` method.
     *
     * @return void
     *
     * @covers ::findUnameId()
     */
    public function testFindUnameID()
    {
        $result = TableRegistry::getTableLocator()
            ->get('Profiles')
            ->find('unameId', ['gustavo-supporto'])
            ->firstOrFail();
        static::assertSame(4, $result->get('id'));

        $result = TableRegistry::getTableLocator()
            ->get('Profiles')
            ->find('unameId', [4])
            ->firstOrFail();
        static::assertSame('gustavo-supporto', $result->get('uname'));
    }
}
