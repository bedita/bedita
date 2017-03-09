<?php
namespace BEdita\Core\Test\TestCase\Model\Table;

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
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.objects',
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
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Objects);

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

        $error = (bool)$object->errors();
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
                [1],
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
     * Test object date range finder.
     * {@see \BEdita\Core\Model\Table\DateRangesTable} for a more detailed test case
     *
     * @return void
     *
     * @covers ::findDate()
     */
    public function testFindDate()
    {
        $result = $this->Objects->find()->find('date', ['startAfter' => '2017-12-31'])->toArray();
        $this->assertEmpty($result);
    }
}
