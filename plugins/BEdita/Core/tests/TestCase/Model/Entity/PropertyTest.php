<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Model\Entity\Property;
use BEdita\Core\Model\Entity\PropertyType;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\Property} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\Property
 */
class PropertyTest extends TestCase
{

    /**
     * Test subject's table
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
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.media',
        'plugin.BEdita/Core.locations',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
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
     * Test accessible properties.
     *
     * @return void
     * @coversNothing
     */
    public function testAccessible()
    {
        $property = $this->Properties->get(1);

        $created = $property->created;
        $modified = $property->modified;

        $data = [
            'id' => 42,
            'enabled' => false,
            'created' => '2016-01-01 12:00:00',
            'modified' => '2016-01-01 12:00:00',
            'published' => '2016-01-01 12:00:00',
            'description' => 'another description'
        ];
        $property = $this->Properties->patchEntity($property, $data);
        if (!($property instanceof Property)) {
            throw new \InvalidArgumentException();
        }

        static::assertEquals(1, $property->id);
        static::assertTrue($property->enabled);
        static::assertEquals($created, $property->created);
        static::assertEquals($modified, $property->modified);
        static::assertEquals($data['description'], $property->description);
    }

    /**
     * Data provider for `testGetObjectTypeName` test case.
     *
     * @return array
     */
    public function getObjectTypeNameProvider()
    {
        return [
            'document' => [
                'documents',
                2,
            ],
            'non existent' => [
                null,
                -1,
            ],
            'invalid' => [
                null,
                null,
            ],
        ];
    }

    /**
     * Test magic getter for object type name property.
     *
     * @param string|null $expected Expected object type name.
     * @param mixed $objectTypeId Object type ID.
     * @return void
     *
     * @covers ::_getObjectTypeName()
     * @dataProvider getObjectTypeNameProvider()
     */
    public function testGetObjectTypeName($expected, $objectTypeId)
    {
        $entity = new Property();
        $entity->object_type_id = $objectTypeId;

        $objectTypeName = $entity->object_type_name;

        static::assertSame($expected, $objectTypeName);
    }

    /**
     * Data provider for `testSetObjectTypeName` test case.
     *
     * @return array
     */
    public function setObjectTypeNameProvider()
    {
        return [
            'document' => [
                2,
                'documents',
            ],
            'non existent' => [
                null,
                'this type does not exist',
            ],
        ];
    }

    /**
     * Test magic setter for object type name property.
     *
     * @param string|null $expected Expected object type ID.
     * @param mixed $objectTypeName Object type name.
     * @return void
     *
     * @covers ::_setObjectTypeName()
     * @dataProvider setObjectTypeNameProvider()
     */
    public function testSetObjectTypeName($expected, $objectTypeName)
    {
        $entity = new Property();
        $entity->object_type_name = $objectTypeName;

        $objectTypeId = $entity->object_type_id;

        static::assertSame($expected, $objectTypeId);
    }

    /**
     * Data provider for `testGetPropertyTypeName` test case.
     *
     * @return array
     */
    public function getPropertyTypeNameProvider()
    {
        return [
            'document' => [
                'string',
                1,
            ],
            'non existent' => [
                null,
                -1,
            ],
            'invalid' => [
                null,
                null,
            ],
        ];
    }

    /**
     * Test magic getter for property type.
     *
     * @param string|null $expected Expected property type name.
     * @param mixed $propertyTypeId Property type ID.
     * @return void
     *
     * @covers ::_getPropertyType()
     * @dataProvider getPropertyTypeNameProvider()
     */
    public function testGetPropertyType($expected, $propertyTypeId)
    {
        $entity = new Property();
        $entity->property_type_id = $propertyTypeId;

        $propertyType = $entity->property_type;

        if ($expected === null) {
            static::assertNull($propertyType);
        } else {
            static::assertInstanceOf(PropertyType::class, $propertyType);
            static::assertSame($expected, $propertyType->name);

            $secondRound = $entity->property_type;

            static::assertSame($propertyType, $secondRound);
        }
    }

    /**
     * Test magic getter for property type name property.
     *
     * @param string|null $expected Expected property type name.
     * @param mixed $propertyTypeId Property type ID.
     * @return void
     *
     * @covers ::_getPropertyTypeName()
     * @dataProvider getPropertyTypeNameProvider()
     */
    public function testGetPropertyTypeName($expected, $propertyTypeId)
    {
        $entity = new Property();
        $entity->property_type_id = $propertyTypeId;

        $propertyTypeName = $entity->property_type_name;

        static::assertSame($expected, $propertyTypeName);
    }

    /**
     * Data provider for `testSetPropertyTypeName` test case.
     *
     * @return array
     */
    public function setPropertyTypeNameProvider()
    {
        return [
            'document' => [
                1,
                'string',
            ],
            'non existent' => [
                null,
                'this type does not exist',
            ],
        ];
    }

    /**
     * Test magic setter for property type name property.
     *
     * @param string|null $expected Expected property type ID.
     * @param mixed $propertyTypeName Property type name.
     * @return void
     *
     * @covers ::_setPropertyTypeName()
     * @dataProvider setPropertyTypeNameProvider()
     */
    public function testSetPropertyTypeName($expected, $propertyTypeName)
    {
        $entity = new Property();
        $entity->property_type_name = $propertyTypeName;

        $propertyTypeId = $entity->property_type_id;

        static::assertSame($expected, $propertyTypeId);
    }

    /**
     * Data provider for `testGetRequired` test case.
     *
     * @return array
     */
    public function getRequiredProvider()
    {
        return [
            'true' => [
                true,
                false,
            ],
            'false' => [
                false,
                true,
            ],
        ];
    }

    /**
     * Test getter for `required` virtual property.
     *
     * @param bool $expected Expected result.
     * @param bool $isNullable Is property nullable?
     * @return void
     *
     * @dataProvider getRequiredProvider()
     * @covers ::_getRequired()
     */
    public function testGetRequired($expected, $isNullable)
    {
        $entity = new Property();
        $entity->is_nullable = $isNullable;

        static::assertSame($expected, $entity->required);
    }

    /**
     * Data provider for `testGetSchema` test case.
     *
     * @return array
     */
    public function getSchemaProvider()
    {
        return [
            'email' => [
                [
                    '$id' => '/properties/email',
                    'title' => 'Email',
                    'description' => null,
                    'type' => 'string',
                    'format' => 'email',
                ],
                'email',
                false,
            ],
            'email (nullable)' => [
                [
                    '$id' => '/properties/email',
                    'title' => 'Email',
                    'description' => null,
                    'oneOf' => [
                        [
                            'type' => 'null',
                        ],
                        [
                            'type' => 'string',
                            'format' => 'email',
                        ],
                    ],
                ],
                'email',
                true,
            ],
            'email (read only)' => [
                [
                    '$id' => '/properties/email',
                    'title' => 'Email',
                    'description' => null,
                    'type' => 'string',
                    'format' => 'email',
                    'readOnly' => true,
                ],
                'email',
                false,
                'readOnly',
            ],
            'non-existent' => [
                true,
                'gustavo',
                true,
            ],
        ];
    }

    /**
     * Test generator for property schema.
     *
     * @param mixed $expected Expected result.
     * @param string $propertyTypeName Property type name.
     * @param bool $isNullable Is the property nullable?
     * @param string|null $mode Property access mode.
     * @return void
     *
     * @dataProvider getSchemaProvider()
     * @covers ::getSchema()
     */
    public function testGetSchema($expected, $propertyTypeName, $isNullable, $mode = null)
    {
        $entity = new Property();
        $entity->name = $propertyTypeName;
        $entity->property_type_name = $propertyTypeName;
        $entity->is_nullable = $isNullable;

        $schema = $entity->getSchema($mode);

        static::assertEquals($expected, $schema);
    }
}
