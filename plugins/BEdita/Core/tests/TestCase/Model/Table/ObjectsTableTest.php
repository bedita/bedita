<?php
namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Table\ObjectsTable;
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
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.object_types'
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
        $this->assertInstanceOf('\Cake\ORM\Behavior\TimestampBehavior', $this->Objects->Behaviors()->get('Timestamp'));
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
        $user = $this->Objects->newEntity();
        $this->Objects->patchEntity($user, $data);

        $error = (bool)$user->errors();
        $this->assertEquals($expected, !$error, print_r($user->errors(), true));

        if ($expected) {
            $success = $this->Objects->save($user);
            $this->assertTrue((bool)$success);
        }
    }
}
