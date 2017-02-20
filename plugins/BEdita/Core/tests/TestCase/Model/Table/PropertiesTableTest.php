<?php
namespace BEdita\Core\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Table\PropertiesTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\PropertiesTable
 */
class PropertiesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\PropertiesTable
     */
    public $Properties;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.properties',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Properties = TableRegistry::get('Properties');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Properties);

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
        $this->Properties->initialize([]);
        $this->assertEquals('properties', $this->Properties->getTable());
        $this->assertEquals('id', $this->Properties->getPrimaryKey());
        $this->assertEquals('name', $this->Properties->getDisplayField());

        $this->assertInstanceOf('\Cake\ORM\Association\BelongsTo', $this->Properties->ObjectTypes);
        $this->assertInstanceOf('\Cake\ORM\Association\BelongsTo', $this->Properties->PropertyTypes);
        $this->assertInstanceOf('\Cake\ORM\Behavior\TimestampBehavior', $this->Properties->behaviors()->get('Timestamp'));
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
                    'name' => 'body',
                    'description' => 'long text of a document',
                ],
            ],
            'emptyName' => [
                false,
                [
                    'name' => '',
                    'description' => 'another description',
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
     */
    public function testValidation($expected, array $data)
    {
        $property = $this->Properties->newEntity();
        $this->Properties->patchEntity($property, $data);
        $property->object_type_id = 1;
        $property->property_type_id = 1;
        $property->property = 'string';

        $error = (bool)$property->errors();
        $this->assertEquals($expected, !$error);

        if ($expected) {
            $success = $this->Properties->save($property);
            $this->assertTrue((bool)$success);
        }
    }
}
