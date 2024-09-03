<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
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
use BEdita\Core\Model\Entity\StaticProperty;
use BEdita\Core\Model\Table\StreamsTable;
use BEdita\Core\Model\Table\UsersTable;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

/**
 * {@see \BEdita\Core\Model\Entity\StaticProperty} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\StaticProperty
 */
class StaticPropertyTest extends TestCase
{
    use ArraySubsetAsserts;

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
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Properties = TableRegistry::getTableLocator()->get('Properties');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Properties);

        parent::tearDown();
    }

    /**
     * Test conversion from a property to a static property.
     *
     * @return void
     * @covers ::fromProperty()
     */
    public function testFromProperty()
    {
        $property = $this->Properties->get(1);
        $staticProperty = StaticProperty::fromProperty($property);

        static::assertInstanceOf(Property::class, $property);
        static::assertInstanceOf(StaticProperty::class, $staticProperty);
        static::assertFalse($staticProperty->isNew());
        static::assertEmpty($staticProperty->getDirty());

        static::assertSame($property->toArray(), $staticProperty->toArray());
    }

    /**
     * Data provider for `testInferFromSchema` test case.
     *
     * @return array
     */
    public function inferFromSchemaProvider()
    {
        return [
            'email' => [
                [
                    'name' => 'email',
                    'property_type_name' => 'email',
                    'is_nullable' => true,
                ],
                'email',
                'Profiles',
            ],
            'mime_type' => [
                [
                    'name' => 'mime_type',
                    'property_type_name' => 'string',
                    'is_nullable' => false,
                ],
                'mime_type',
                'Streams',
            ],
            'created' => [
                [
                    'name' => 'created',
                    'property_type_name' => 'datetime',
                    'is_nullable' => false,
                ],
                'created',
                'Objects',
            ],
            'where the streets have no name' => [
                [],
                null,
                'Objects',
            ],
        ];
    }

    /**
     * Test inference of property metadata from schema.
     *
     * @param array $expected Expected properties.
     * @param string $name Column name.
     * @param string $table Table name.
     * @return void
     * @dataProvider inferFromSchemaProvider()
     * @covers ::_setName()
     * @covers ::_setTable()
     * @covers ::getSchemaColumnDefinition()
     * @covers ::inferFromSchema()
     */
    public function testInferFromSchema(array $expected, $name, $table)
    {
        $entity = new StaticProperty();
        $entity->name = $name;
        $entity->table = $table;

        static::assertInstanceOf(Table::class, $entity->table);

        $result = $entity->toArray();

        static::assertArraySubset($expected, $result);
    }

    /**
     * Data provider for `testGetTable` test case.
     *
     * @return array
     */
    public function getTableProvider()
    {
        return [
            'table' => [
                StreamsTable::class,
                [
                    'table' => 'Streams',
                ],
            ],
            'object type' => [
                UsersTable::class,
                [
                    'object_type_name' => 'users',
                ],
            ],
            'null' => [
                null,
                [],
            ],
        ];
    }

    /**
     * Test getter for `table` property.
     *
     * @param string|null $expected Expected result
     * @param array $data Entity data.
     * @return void
     * @dataProvider getTableProvider()
     * @covers ::_getTable()
     */
    public function testGetTable($expected, array $data)
    {
        $entity = new StaticProperty($data);

        $table = $entity->table;

        if ($expected === null) {
            static::assertNull($table);
        } else {
            static::assertInstanceOf($expected, $table);
        }
    }

    /**
     * Data provider for `testGetDefault` test case.
     *
     * @return array
     */
    public function getDefaultProvider()
    {
        return [
            'empty' => [
                null,
                [],
            ],
            'CURRENT_TIMESTAMP' => [
                null,
                [
                    'name' => 'created',
                    'table' => 'Streams',
                ],
            ],
            'status' => [
                'draft',
                [
                    'name' => 'status',
                    'table' => 'Objects',
                ],
            ],
            'company' => [
                false,
                [
                    'name' => 'company',
                    'table' => 'Profiles',
                ],
            ],
        ];
    }

    /**
     * Test getter for `default` virtual property.
     *
     * @param mixed $expected Expected result.
     * @param array $data Entity data.
     * @return void
     * @dataProvider getDefaultProvider()
     * @covers ::_getDefault()
     */
    public function testGetDefault($expected, array $data)
    {
        $entity = new StaticProperty($data);

        $default = $entity->default;

        static::assertSame($expected, $default);

        // Empty `name`and `table` without invoking setters.
        $entity->set('name', null, ['setter' => false]);
        $entity->set('table', null, ['setter' => false]);

        $default = $entity->default;

        static::assertSame($expected, $default);
    }

    /**
     * Data provider for `testGetRequired` test case.
     *
     * @return array
     */
    public function getRequiredProvider()
    {
        return [
            'no table, nullable' => [
                false,
                [
                    'is_nullable' => true,
                ],
            ],
            'no table, with default' => [
                false,
                [
                    'is_nullable' => false,
                    'default' => 'gustavo',
                ],
            ],
            'no table, not nullable, without default' => [
                true,
                [
                    'is_nullable' => false,
                ],
            ],
            'missing field' => [
                false,
                [
                    'table' => 'Streams',
                    'name' => 'gustavo',
                ],
            ],
            'from validator' => [
                true,
                [
                    'table' => 'Streams',
                    'name' => 'mime_type',
                ],
            ],
        ];
    }

    /**
     * Test getter for `required` virtual property.
     *
     * @param bool $expected Expected result.
     * @param array $data Entity data.
     * @return void
     * @dataProvider getRequiredProvider()
     * @covers ::_getRequired()
     */
    public function testGetRequired($expected, array $data)
    {
        $entity = new StaticProperty($data);

        $required = $entity->required;

        static::assertSame($expected, $required);
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
                'Profiles',
            ],
            'mime_type' => [
                [
                    '$id' => '/properties/mime_type',
                    'title' => 'Mime Type',
                    'type' => 'string',
                    'maxLength' => 255,
                    'default' => 'application/octet-stream',
                ],
                'mime_type',
                'Streams',
            ],
            'created' => [
                [
                    '$id' => '/properties/created',
                    'title' => 'Created',
                    'type' => 'string',
                    'format' => 'date-time',
                    'readOnly' => true,
                ],
                'created',
                'Objects',
                'readOnly',
            ],
            'non-existent' => [
                true,
                'gustavo',
                'Users',
            ],
        ];
    }

    /**
     * Test generator for property schema.
     *
     * @param mixed $expected Expected result.
     * @param string $name Column name.
     * @param string $table Table name.
     * @param string|null $mode Property access mode.
     * @return void
     * @dataProvider getSchemaProvider()
     * @covers ::getSchema()
     */
    public function testGetSchema($expected, $name, $table, $mode = null)
    {
        $entity = new StaticProperty();
        $entity->name = $name;
        $entity->table = $table;

        $schema = $entity->getSchema($mode);
        if (is_array($schema)) {
            // Ignore description because it is empty on SQLite.
            unset($schema['description']);
        }

        static::assertEquals($expected, $schema);
    }

    /**
     * Data provider for `testGetTranslatable` test case.
     *
     * @return array
     */
    public function getTranslatableProvider(): array
    {
        return [
            'missing field' => [
                false,
                [
                    'table' => 'Profiles',
                    'name' => 'gustavo',
                ],
            ],
            'text type' => [
                true,
                [
                    'table' => 'Objects',
                    'name' => 'description',
                ],
            ],
            'date type' => [
                false,
                [
                    'table' => 'Objects',
                    'name' => 'created',
                ],
            ],
            'title' => [
                true,
                [
                    'table' => 'Objects',
                    'name' => 'title',
                ],
            ],
            'string type' => [
                false,
                [
                    'table' => 'Profiles',
                    'name' => 'surname',
                ],
            ],
        ];
    }

    /**
     * Test getter for `translatable` virtual property.
     *
     * @param bool $expected Expected result.
     * @param array $data Entity data.
     * @return void
     * @dataProvider getTranslatableProvider()
     * @covers ::_getTranslatable()
     */
    public function testGetTranslatable($expected, array $data): void
    {
        $entity = new StaticProperty($data);
        $tr = $entity->translatable;

        static::assertSame($expected, $tr);
    }
}
