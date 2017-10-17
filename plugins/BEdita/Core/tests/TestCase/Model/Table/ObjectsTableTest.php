<?php
namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Entity\ObjectEntity;
use BEdita\Core\Utility\Database;
use BEdita\Core\Utility\LoggedUser;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

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
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.properties',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.object_relations',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.date_ranges',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Objects = TableRegistry::get('Objects');
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
                    'lang' => 'eng',
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
                ],
                ['ne' => 'documents'],
            ],
            'missing' => [
                false,
                ['document', 'profiles', 0],
            ],
        ];
    }

    /**
     * Test object types finder.
     *
     * @param array|false $expected Expected results.
     * @param array $types Array of object types to filter for.
     * @return void
     *
     * @dataProvider findTypeProvider
     * @covers ::findType()
     */
    public function testFindType($expected, array $types)
    {
        if (!$expected) {
            $this->expectException('\Cake\Datasource\Exception\RecordNotFoundException');
        }

        $result = $this->Objects->find('list')->find('type', $types)->toArray();

        $this->assertEquals($expected, $result);
    }

    /**
     * Test object date ranges finder.
     * {@see \BEdita\Core\Model\Table\DateRangesTable} for a more detailed test case
     *
     * @return void
     *
     * @covers ::findDateRanges()
     */
    public function testFindDateRanges()
    {
        $result = $this->Objects->find('dateRanges', ['start_date' => ['gt' => '2017-01-01']])->toArray();
        $this->assertNotEmpty($result);
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
     * Data provider for `testSaveAbstractTypes` test case.
     *
     * @return array
     */
    public function saveAbstractTypesProvider()
    {
        return [
            'objects' => [
                true,
                'objects',
            ],
            'media' => [
                true,
                'media',
            ],
            'documents' => [
                false,
                'documents',
            ],
        ];
    }

    /**
     * Test that save of abstract types fails as expected.
     *
     * @param bool $abstract Is the type abstract?
     * @param string $type Type being saved.
     * @return void
     *
     * @covers ::beforeSave()
     * @dataProvider saveAbstractTypesProvider()
     */
    public function testSaveAbstractTypes($abstract, $type)
    {
        if ($abstract) {
            $this->expectException(PersistenceFailedException::class);
        }

        $object = $this->Objects->newEntity();
        $object->type = $type;

        $result = $this->Objects->saveOrFail($object);

        static::assertInstanceOf(ObjectEntity::class, $result);
    }
}
