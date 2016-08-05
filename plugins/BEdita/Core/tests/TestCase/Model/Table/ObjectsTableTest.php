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
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Objects = TableRegistry::get('BEdita/Core.Objects');
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
     * @covers ::initialize()
     */
    public function testInitialization()
    {
        $this->Objects->initialize([]);
        $this->assertEquals('objects', $this->Objects->table());
        $this->assertEquals('id', $this->Objects->primaryKey());
        $this->assertEquals('title', $this->Objects->displayField());

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
                    'object_type_id' => 1,
                    'status' => 'draft',
                    'uname' => 'title-three',
                    'lang' => 'eng',
                    'created_by' => 1,
                    'modified_by' => 1,
                ],
            ],
            'notUniqueUname' => [
                false,
                [
                    'title' => 'title four',
                    'description' => 'another description',
                    'object_type_id' => 1,
                    'status' => 'on',
                    'uname' => 'title-one',
                    'lang' => 'eng',
                    'created_by' => 1,
                    'modified_by' => 1,
                ],
            ],
            'notExistsInObjectTypes' => [
                false,
                [
                    'object_type_id' => 9999,
                    'status' => 'draft',
                    'uname' => 'title-one',
                    'lang' => 'eng',
                    'created_by' => 1,
                    'modified_by' => 1,
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
     * @covers ::validationDefault
     * @covers ::buildRules
     */
    public function testValidation($expected, array $data)
    {
        $object = $this->Objects->newEntity($data, [
            'accessibleFields' => [
                'created_by' => true,
                'modified_by' => true,
            ]
        ]);

        $error = (bool)$object->errors();
        $this->assertEquals($expected, !$error, print_r($object->errors(), true));

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
                ],
                [1],
            ],
            'multiple' => [
                [
                    2 => 'title one',
                    3 => 'title two',
                    4 => 'Gustavo Supporto profile',
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
            $this->setExpectedException('\Cake\Datasource\Exception\RecordNotFoundException');
        }

        $result = $this->Objects->find('list')->find('type', $types)->toArray();

        $this->assertEquals($expected, $result);
    }
}
