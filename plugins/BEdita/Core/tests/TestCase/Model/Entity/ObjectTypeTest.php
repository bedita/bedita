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

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Model\Entity\ObjectType} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\ObjectType
 */
class ObjectTypeTest extends TestCase
{

    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\ObjectTypesTable
     */
    public $ObjectTypes;

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

        $this->ObjectTypes = TableRegistry::get('ObjectTypes');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->ObjectTypes);

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
        $objectType = $this->ObjectTypes->get(1);

        $data = [
            'id' => 42,
            'name' => 'patched_name',
        ];
        $objectType = $this->ObjectTypes->patchEntity($objectType, $data);

        static::assertEquals(1, $objectType->id);
        static::assertEquals('patched_name', $objectType->name);
    }

    /**
     * Test virtual properties.
     *
     * @return void
     * @coversNothing
     */
    public function testVirtual()
    {
        $expected = [
            'alias' => 'Documents',
            'table' => 'BEdita/Core.Objects',
            'relations' => [
                'test',
                'inverse_test',
            ],
            'parent_name' => 'objects',
        ];

        $objectType = $this->ObjectTypes->get(2);

        static::assertEquals($expected, $objectType->extract($objectType->getVirtual()));
    }

    /**
     * Test setter method for `name`.
     *
     * @return void
     * @covers ::_setName()
     */
    public function testSetName()
    {
        $data = [
            'name' => 'FooBar',
        ];
        $objectType = $this->ObjectTypes->newEntity($data);

        static::assertEquals('foo_bar', $objectType->name);
    }

    /**
     * Data provider for `testGetSetSingular` test case.
     *
     * @return array
     */
    public function getSetSingularProvider()
    {
        return [
            'default' => ['foo_bar', 'foo_bars', 'FooBar'],
            'missing' => ['foo_bar', 'foo_bars', null],
        ];
    }

    /**
     * Test getter/setter method for `singular`.
     *
     * @param string $expected Expected result.
     * @param string $name Object type name.
     * @param string|null $singular Object type singular name.
     * @return void
     *
     * @dataProvider getSetSingularProvider
     * @covers ::_getSingular()
     * @covers ::_setSingular()
     */
    public function testGetSetSingular($expected, $name, $singular)
    {
        $data = compact('name', 'singular');
        $objectType = $this->ObjectTypes->newEntity($data);

        static::assertEquals($expected, $objectType->singular);
    }

    /**
     * Test getter method for `alias`.
     *
     * @return void
     * @covers ::_getAlias()
     */
    public function testGetAlias()
    {
        $data = [
            'name' => 'foo_bars',
        ];
        $objectType = $this->ObjectTypes->newEntity($data);

        static::assertEquals('FooBars', $objectType->alias);
    }

    /**
     * Data provider for `testGetSetTable` test case.
     *
     * @return array
     */
    public function getSetTableProvider()
    {
        return [
            'pluginSyntax' => ['BEdita/Core.Objects', 'BEdita/Core', 'Objects', 'BEdita/Core.Objects'],
            'noPlugin' => ['Objects', null, 'Objects', 'Objects'],
        ];
    }

    /**
     * Test getter/setter method for `table`.
     *
     * @param string $expected Expected result.
     * @param string|null $expectedPlugin Expected plugin.
     * @param string $expectedModel Expected model.
     * @param string|null $table Object type table.
     * @return void
     *
     * @dataProvider getSetTableProvider
     * @covers ::_getTable()
     * @covers ::_setTable()
     */
    public function testGetSetTable($expected, $expectedPlugin, $expectedModel, $table)
    {
        $data = compact('table');
        $objectType = $this->ObjectTypes->newEntity($data);

        static::assertEquals($expectedPlugin, $objectType->plugin);
        static::assertEquals($expectedModel, $objectType->model);
        static::assertEquals($expected, $objectType->table);
    }

    /**
     * Data provider for `testGetRelationsByName` test case.
     *
     * @return array
     */
    public function getRelationsByNameProvider()
    {
        return [
            'empty' => [
                [],
                'objects',
            ],
            'both' => [
                ['test', 'inverse_test'],
                'documents',
                'both',
            ],
            'left' => [
                ['test'],
                'documents',
                'left',
            ],
            'right' => [
                ['inverse_test'],
                'documents',
                'right',
            ],
            'inherited' => [
                ['test_abstract', 'inverse_test_abstract'],
                'files',
            ],
        ];
    }

    /**
     * Test `getRelations()` method.
     *
     * @param string[] $expected List of expected relations.
     * @param string $name Object type name to get relations for.
     * @param string $side Side to get relations for.
     * @return void
     *
     * @dataProvider getRelationsByNameProvider()
     * @covers ::getRelations()
     */
    public function testGetRelationsByName($expected, $name, $side = 'both')
    {
        $objectType = $this->ObjectTypes->get($name);
        $relations = array_keys($objectType->getRelations($side));

        static::assertEquals($expected, $relations, '', 0, 10, true);
    }

    /**
     * Test getter for relations.
     *
     * @return void
     *
     * @covers ::_getRelations()
     */
    public function testGetRelations()
    {
        $expected = [
            'inverse_test',
        ];
        $objectType = $this->ObjectTypes->get(3);

        static::assertEquals($expected, $objectType->relations, '', 0, 10, true);
    }

    /**
     * Test getter for relations when associations haven't been loaded.
     *
     * @return void
     *
     * @covers ::_getRelations()
     */
    public function testGetRelationsAssociationsNotLoaded()
    {
        /** @var \BEdita\Core\Model\Entity\ObjectType $objectType */
        $objectType = $this->ObjectTypes->find()
            ->contain(['LeftRelations'], true)
            ->firstOrFail();

        static::assertInstanceOf($this->ObjectTypes->getEntityClass(), $objectType);
        static::assertNull($objectType->relations);
    }

    /**
     * Test that `relations` association was removed serializing entity
     *
     * @return void
     *
     * @covers ::listAssociations()
     */
    public function testListAssociations()
    {
        $objectType = $this->ObjectTypes->get(1);

        $result = $objectType->jsonApiSerialize();

        static::assertArrayHasKey('relationships', $result);
        static::assertArrayHasKey('left_relations', $result['relationships']);
        static::assertArrayHasKey('right_relations', $result['relationships']);
        static::assertArrayNotHasKey('relations', $result['relationships']);
    }

    /**
     * Data provider for `testGetSetParentName` test case.
     *
     * @return array
     */
    public function getSetParentNameProvider()
    {
        return [
            'objects' => [
                'objects',
                null,
                'not_found',
                null,
            ],
            'documents' => [
                'documents',
                'objects',
                'objects',
                'objects',
            ],
            'profilesBadParent' => [
                'profiles',
                'objects',
                'documents',
                'objects',
            ],
            'changeParent' => [
                'profiles',
                'objects',
                'media',
                'media',
            ],
        ];
    }

    /**
     * Test getter/setter method for `parent_name`.
     *
     * @param string $name Object type name.
     * @param string|null $getExpected Expected parent name result.
     * @param string $newParent New parent name to set.
     * @param string|null $setExpected Parent name set expected result.
     * @return void
     *
     * @dataProvider getSetParentNameProvider
     * @covers ::_getParentName()
     * @covers ::_setParentName()
     */
    public function testGetSetParentName($name, $getExpected, $newParent, $setExpected)
    {
        $objectType = $this->ObjectTypes->get($name);

        static::assertEquals($getExpected, $objectType->parent_name);
        $objectType->set('parent_name', $newParent);
        static::assertEquals($setExpected, $objectType->parent_name);
    }

    /**
     * Test set failure if `parent_name` is not `enabled`.
     *
     * @return void
     *
     * @covers ::_setParentName()
     */
    public function testSetParentNameDisabled()
    {
        $data = [
            'singular' => 'foo',
            'name' => 'foos',
            'is_abstract' => true,
            'enabled' => false,
        ];
        $objectType = $this->ObjectTypes->newEntity();
        $this->ObjectTypes->patchEntity($objectType, $data);
        $success = $this->ObjectTypes->save($objectType);
        static::assertTrue((bool)$success);

        $objectType = $this->ObjectTypes->get('news');
        $objectType->set('parent_name', 'foos');
        static::assertEquals('objects', $objectType->parent_name);
    }

    /**
     * Data provider for `testGetSchema`.
     *
     * @return array
     */
    public function getSchemaProvider()
    {
        return [
            'objects' => [
                false,
                'objects',
            ],
            'media' => [
                false,
                'media',
            ],
            'documents' => [
                [
                    'properties' => [
                        'id' => [
                            '$id' => '/properties/id',
                            'title' => 'Id',
                            'type' => 'integer',
                            'readOnly' => true,
                        ],
                        'title' => [
                            '$id' => '/properties/title',
                            'title' => 'Title',
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                [
                                    'type' => 'string',
                                    'contentMediaType' => 'text/html',
                                ],
                            ],
                        ],
                        'description' => [
                            '$id' => '/properties/description',
                            'title' => 'Description',
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                [
                                    'type' => 'string',
                                    'contentMediaType' => 'text/html',
                                ],
                            ],
                        ],
                        'body' => [
                            '$id' => '/properties/body',
                            'title' => 'Body',
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                [
                                    'type' => 'string',
                                    'contentMediaType' => 'text/html',
                                ],
                            ],
                        ],
                        'uname' => [
                            '$id' => '/properties/uname',
                            'title' => 'Uname',
                            'type' => 'string',
                            'maxLength' => 255,
                        ],
                        'status' => [
                            '$id' => '/properties/status',
                            'title' => 'Status',
                            'type' => 'string',
                            'enum' => ['on', 'off', 'draft'],
                            'default' => 'draft',
                        ],
                        'lang' => [
                            '$id' => '/properties/lang',
                            'title' => 'Lang',
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                [
                                    'type' => 'string',
                                ],
                            ],
                        ],
                        'locked' => [
                            '$id' => '/properties/locked',
                            'title' => 'Locked',
                            'type' => 'boolean',
                            'readOnly' => true,
                            'default' => false,
                        ],
                        'extra' => [
                            '$id' => '/properties/extra',
                            'title' => 'Extra',
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                [
                                    'type' => 'object',
                                ],
                            ],
                        ],
                        'created' => [
                            '$id' => '/properties/created',
                            'title' => 'Created',
                            'type' => 'string',
                            'format' => 'date-time',
                            'readOnly' => true,
                        ],
                        'modified' => [
                            '$id' => '/properties/modified',
                            'title' => 'Modified',
                            'type' => 'string',
                            'format' => 'date-time',
                            'readOnly' => true,
                        ],
                        'created_by' => [
                            '$id' => '/properties/created_by',
                            'title' => 'Created By',
                            'type' => 'integer',
                            'readOnly' => true,
                        ],
                        'modified_by' => [
                            '$id' => '/properties/modified_by',
                            'title' => 'Modified By',
                            'type' => 'integer',
                            'readOnly' => true,
                        ],
                        'published' => [
                            '$id' => '/properties/published',
                            'title' => 'Published',
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                [
                                    'type' => 'string',
                                    'format' => 'date-time',
                                ],
                            ],
                            'readOnly' => true,
                        ],
                        'publish_start' => [
                            '$id' => '/properties/publish_start',
                            'title' => 'Publish Start',
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                [
                                    'type' => 'string',
                                    'format' => 'date-time',
                                ],
                            ],
                        ],
                        'publish_end' => [
                            '$id' => '/properties/publish_end',
                            'title' => 'Publish End',
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                [
                                    'type' => 'string',
                                    'format' => 'date-time',
                                ],
                            ],
                        ],
                        'another_title' => [
                            '$id' => '/properties/another_title',
                            'title' => 'Another Title',
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                [
                                    'type' => 'string',
                                ],
                            ],
                        ],
                        'another_description' => [
                            '$id' => '/properties/another_description',
                            'title' => 'Another Description',
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                [
                                    'type' => 'string',
                                ],
                            ],
                        ],
                    ],
                    'required' => [],
                ],
                'documents',
            ],
        ];
    }

    /**
     * Test getter for `schema`.
     *
     * @param mixed $expected Expected result.
     * @param string $name Object type name.
     * @return void
     *
     * @dataProvider getSchemaProvider()
     * @covers ::_getSchema()
     */
    public function testGetSchema($expected, $name)
    {
        $objectType = $this->ObjectTypes->get($name);

        $schema = $objectType->schema;
        if (is_array($schema)) {
            // Ignore description because it is empty on SQLite.
            $schema = Hash::remove($schema, 'properties.{*}.description');
        }

        static::assertEquals($expected, $schema);
    }

    /**
     * Test getter for `schema` when properties have not been loaded.
     *
     * @return void
     *
     * @covers ::_getSchema()
     */
    public function testGetSchemaNoProperties()
    {
        $objectType = $this->ObjectTypes->newEntity();
        $objectType->is_abstract = false;

        $schema = $objectType->schema;

        static::assertFalse($schema);
    }
}
