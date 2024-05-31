<?php
declare(strict_types=1);

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

use BEdita\Core\Model\Entity\ObjectType;
use BEdita\Core\Model\Table\ObjectTypesTable;
use Cake\Event\Event;
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
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->ObjectTypes = TableRegistry::getTableLocator()->get('ObjectTypes');

        $this->loadPlugins(['BEdita/API' => ['routes' => true]]);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
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
        $objectType = $this->ObjectTypes->newEmptyEntity();
        $objectType->set('name', 'FooBar');

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
                ['inverse_test_abstract'],
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
     * @dataProvider getRelationsByNameProvider()
     * @covers ::getRelations()
     */
    public function testGetRelationsByName($expected, $name, $side = 'both')
    {
        $objectType = $this->ObjectTypes->get($name);
        $relations = array_keys($objectType->getRelations($side));

        static::assertEquals($expected, $relations, '');
        static::assertEqualsCanonicalizing($expected, $relations, '');
        static::assertEqualsWithDelta($expected, $relations, 0, '');
    }

    /**
     * Test getter for relations.
     *
     * @return void
     * @covers ::_getRelations()
     */
    public function testGetRelations()
    {
        $expected = [
            'inverse_test',
        ];
        $objectType = $this->ObjectTypes->get(3);

        static::assertEquals($expected, $objectType->relations, '');
        static::assertEqualsCanonicalizing($expected, $objectType->relations, '');
        static::assertEqualsWithDelta($expected, $objectType->relations, 0, '');
    }

    /**
     * Test getter for relations when associations haven't been loaded.
     *
     * @return void
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
     * Data provider for {@see ObjectTypeTest::testGetParent()} test case.
     *
     * @return array[]
     */
    public function getParentProvider(): array
    {
        return [
            'no parent' => [
                null,
                function (ObjectTypesTable $table): ObjectType {
                    return $table->get('objects');
                },
            ],
            'parent needs to be loaded' => [
                'objects',
                function (ObjectTypesTable $table): ObjectType {
                    return $table->get('documents');
                },
            ],
            'parent, preloaded' => [
                'objects',
                function (ObjectTypesTable $table): ObjectType {
                    return $table->get('locations', ['contain' => ['Parent']]);
                },
            ],
        ];
    }

    /**
     * Test {@see ObjectType::getParent()}.
     *
     * @param string|null $expected Expected parent.
     * @param callable $subject Function that is expected to return an {@see ObjectType} entity.
     * @return void
     * @dataProvider getParentProvider()
     * @covers ::getParent()
     */
    public function testGetParent(?string $expected, callable $subject): void
    {
        /** @var ObjectType $objectType */
        $objectType = $subject($this->ObjectTypes);

        $actual = $objectType->getParent();
        if ($expected === null) {
            static::assertNull($actual);
        } else {
            static::assertInstanceOf(ObjectType::class, $actual);
            static::assertSame($expected, $actual->name);
        }
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
            'objects null' => [
                'objects',
                null,
                null,
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
        $objectType = $this->ObjectTypes->newEntity([]);
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
            'files' => [
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
                                new \stdClass(),
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
                        'media_property' => [
                            'type' => 'boolean',
                            '$id' => '/properties/media_property',
                            'title' => 'Media Property',
                        ],
                        'disabled_property' => [
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                [
                                    'type' => 'string',
                                ],
                            ],
                            '$id' => '/properties/disabled_property',
                            'title' => 'Disabled Property',
                        ],
                        'files_property' => [
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                new \stdClass(),
                            ],
                            '$id' => '/properties/files_property',
                            'title' => 'Files Property',
                        ],
                        'name' => [
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                [
                                    'type' => 'string',
                                    'contentMediaType' => 'text/html',
                                ],
                            ],
                            '$id' => '/properties/name',
                            'title' => 'Name',
                        ],
                        'provider' => [
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                [
                                    'type' => 'string',
                                ],
                            ],
                            '$id' => '/properties/provider',
                            'title' => 'Provider',
                        ],
                        'provider_uid' => [
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                [
                                    'type' => 'string',
                                ],
                            ],
                            '$id' => '/properties/provider_uid',
                            'title' => 'Provider Uid',
                        ],
                        'provider_url' => [
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                [
                                    'type' => 'string',
                                    'format' => 'uri',
                                ],
                            ],
                            '$id' => '/properties/provider_url',
                            'title' => 'Provider Url',
                        ],
                        'provider_thumbnail' => [
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                [
                                    'type' => 'string',
                                    'format' => 'uri',
                                ],
                            ],
                            '$id' => '/properties/provider_thumbnail',
                            'title' => 'Provider Thumbnail',
                        ],
                        'provider_extra' => [
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                new \stdClass(),
                            ],
                            '$id' => '/properties/provider_extra',
                            'title' => 'Provider Extra',
                        ],
                    ],
                    'required' => [
                        'media_property',
                    ],
                    'associations' => [
                        'Streams',
                    ],
                    'relations' => [
                        'inverse_test_abstract' => [
                            'label' => 'Inverse test relation involving abstract types',
                            'params' => null,
                            'types' => ['events'],
                        ],
                    ],
                    'translatable' => [
                        'body',
                        'description',
                        'name',
                        'title',
                    ],
                ],
                'files',
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
                                new \stdClass(),
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
                        'categories' => [
                            '$id' => '/properties/categories',
                            'title' => 'Categories',
                            'oneOf' => [
                                [
                                    'type' => 'null',
                                ],
                                [
                                    'type' => 'array',
                                    'uniqueItems' => true,
                                    'items' => [
                                        'type' => 'object',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'required' => [],
                    'associations' => [
                        'Categories',
                    ],
                    'relations' => [
                        'inverse_test' => [
                            'label' => 'Inverse test relation',
                            'params' => null,
                            'types' => ['documents'],
                        ],
                        'test' => [
                            'label' => 'Test relation',
                            'params' => null,
                            'types' => ['documents', 'profiles'],
                        ],
                    ],
                    'translatable' => [
                        'body',
                        'description',
                        'title',
                    ],
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
     * @dataProvider getSchemaProvider()
     * @covers ::_getSchema()
     * @covers ::objectTypeRelations()
     * @covers ::translatableProperty()
     * @covers ::accessMode()
     * @covers ::associationProperties()
     */
    public function testGetSchema($expected, $name): void
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
     * Test `readOnly` property in schema.
     *
     * @covers ::accessMode()
     * @return void
     */
    public function testReadOnlyProp(): void
    {
        $objectType = $this->ObjectTypes->get('profiles');
        $schema = $objectType->schema;
        static::assertTrue(Hash::get($schema, 'properties.another_birthdate.readOnly'));
    }

    /**
     * Test getter for `schema` with an event listener which modifies the schema.
     *
     * @param mixed $expected Expected result.
     * @param string $name Object type name.
     * @return void
     * @dataProvider getSchemaProvider()
     * @covers ::_getSchema()
     */
    public function testGetSchemaModified($expected, string $name): void
    {
        $objectType = $this->ObjectTypes->get($name);

        $called = 0;
        $objectType->getEventManager()->on(
            'ObjectType.getSchema',
            function (Event $event, array $schema, ObjectType $ot) use ($expected, $objectType, &$called): array {
                $called++;

                static::assertSame($objectType, $event->getSubject());
                static::assertSame($objectType, $ot);
                static::assertEquals($expected, Hash::remove($schema, 'properties.{*}.description'));

                return ['foo'];
            }
        );

        $schema = $objectType->schema;
        if ($expected !== false) {
            static::assertSame(1, $called);
            static::assertSame(['foo'], $schema);
        } else {
            static::assertSame(0, $called);
            static::assertSame(false, $schema);
        }
    }

    /**
     * Test getter for `schema` with an event listener which does NOT modify the schema.
     *
     * @param mixed $expected Expected result.
     * @param string $name Object type name.
     * @return void
     * @dataProvider getSchemaProvider()
     * @covers ::_getSchema()
     */
    public function testGetSchemaNotModified($expected, string $name): void
    {
        $objectType = $this->ObjectTypes->get($name);

        $called = 0;
        $objectType->getEventManager()->on(
            'ObjectType.getSchema',
            function (Event $event, array $schema, ObjectType $ot) use ($expected, $objectType, &$called): void {
                $called++;

                static::assertSame($objectType, $event->getSubject());
                static::assertSame($objectType, $ot);
                static::assertEquals($expected, Hash::remove($schema, 'properties.{*}.description'));
            }
        );

        $schema = $objectType->schema;
        if ($expected !== false) {
            static::assertSame(1, $called);
            static::assertEquals($expected, Hash::remove($schema, 'properties.{*}.description'));
        } else {
            static::assertSame(0, $called);
            static::assertSame(false, $schema);
        }
    }

    /**
     * Test getter for `schema` with an event listener which aborts execution.
     *
     * @param mixed $expected Expected result.
     * @param string $name Object type name.
     * @return void
     * @dataProvider getSchemaProvider()
     * @covers ::_getSchema()
     */
    public function testGetSchemaStopped($expected, string $name): void
    {
        $objectType = $this->ObjectTypes->get($name);

        $called = 0;
        $objectType->getEventManager()->on(
            'ObjectType.getSchema',
            function (Event $event, array $schema, ObjectType $ot) use ($expected, $objectType, &$called): void {
                $called++;

                static::assertSame($objectType, $event->getSubject());
                static::assertSame($objectType, $ot);
                static::assertEquals($expected, Hash::remove($schema, 'properties.{*}.description'));

                $event->stopPropagation();
            }
        );

        $schema = $objectType->schema;
        static::assertSame($expected !== false ? 1 : 0, $called);
        static::assertSame(false, $schema);
    }

    /**
     * Test getter for `schema` when properties have not been loaded.
     *
     * @return void
     * @covers ::_getSchema()
     */
    public function testGetSchemaNoProperties()
    {
        $objectType = $this->ObjectTypes->newEntity([]);
        $objectType->is_abstract = false;

        $schema = $objectType->schema;

        static::assertFalse($schema);
    }

    /**
     * Test getter for disabled `schema`.
     *
     * @return void
     * @covers ::_getSchema()
     */
    public function testGetSchemaDisabled()
    {
        $objectType = $this->ObjectTypes->get('news');
        $schema = $objectType->schema;
        static::assertFalse($schema);
    }

    /**
     * Test getter for `schema` whith hidden properties.
     *
     * @return void
     * @covers ::_getSchema()
     * @covers ::objectTypeProperties()
     */
    public function testGetSchemaHiddenProperties(): void
    {
        // enable type `news`
        $objectType = $this->ObjectTypes->get('news');
        $objectType->enabled = true;
        $success = $this->ObjectTypes->save($objectType);
        static::assertTrue((bool)$success);

        // `body` property is hidden
        $schema = $objectType->schema;
        $properties = Hash::extract($schema, 'properties');
        static::assertArrayNotHasKey('body', $properties);
    }

    /**
     * Test `objectTypeProperties` method whith required properties.
     *
     * @return void
     * @covers ::objectTypeProperties()
     */
    public function testGetSchemaRequired(): void
    {
        $objectType = $this->ObjectTypes->get('users');
        $schema = $objectType->schema;
        $required = Hash::extract($schema, 'required');
        static::assertEquals(['username'], $required);
    }

    /** Test static properties override in `schema`.
     *
     * @return void
     * @covers ::_getSchema()
     */
    public function testSchemaOverride()
    {
        $objectType = $this->ObjectTypes->get('profiles');
        $schema = $objectType->schema;
        $oneOf = Hash::extract($schema, 'properties.street_address.oneOf');
        $expected = [
            [
                'type' => 'null',
            ],
            [
                'type' => 'string',
            ],
        ];
        static::assertEquals($expected, $oneOf);

        // remove override property type of `street_address`
        $Properties = TableRegistry::getTableLocator()->get('Properties');
        $entity = $Properties->get(10);
        $Properties->delete($entity);

        $schema = $objectType->schema;
        $oneOf = Hash::extract($schema, 'properties.street_address.oneOf');

        $expected[1]['contentMediaType'] = 'text/html';
        static::assertEquals($expected, $oneOf);
    }

    /**
     * Data provider for {@see self::testGetFullInheritanceChain()} test case.
     *
     * @return array[]
     */
    public function getFullInheritanceChainProvider(): array
    {
        return [
            'objects' => [
                ['objects'],
                'objects',
            ],
            'locations' => [
                ['locations', 'objects'],
                'locations',
            ],
            'media' => [
                ['media', 'objects'],
                'media',
            ],
            'files' => [
                ['files', 'media', 'objects'],
                'files',
            ],
        ];
    }

    /**
     * Test {@see ObjectType::getFullInheritanceChain()}.
     *
     * @param string[] $expected Expected chain.
     * @param string $name Test subject.
     * @return void
     * @dataProvider getFullInheritanceChainProvider()
     * @covers ::getFullInheritanceChain
     */
    public function testGetFullInheritanceChain(array $expected, string $name): void
    {
        $objectType = $this->ObjectTypes->get($name);

        $fullChain = $objectType->getFullInheritanceChain();
        static::assertInstanceOf(\Iterator::class, $fullChain);

        $fullChain = iterator_to_array($fullChain);
        foreach ($fullChain as $ancestor) {
            static::assertInstanceOf(ObjectType::class, $ancestor);
        }

        $actual = Hash::extract($fullChain, '{*}.name');
        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for {@see ObjectTypeTest::testIsDescendantOf()} test case.
     *
     * @return array[]
     */
    public function isDescendantOfProvider(): array
    {
        return [
            'media descendant of objects' => [true, 'media', 'objects'],
            'files descendant of objects' => [true, 'files', 'objects'],
            'files descendant of media' => [true, 'files', 'media'],
            'users descendant of objects' => [true, 'users', 'objects'],
            'users NOT descendant of media' => [false, 'users', 'media'],
            'objects NOT descendant of media' => [false, 'objects', 'media'],
            'objects NOT descendant of files' => [false, 'objects', 'files'],
            'media NOT descendant of files' => [false, 'media', 'files'],
            'objects NOT descendant of users' => [false, 'objects', 'users'],
            'media NOT descendant of users' => [false, 'media', 'users'],
        ];
    }

    /**
     * Test {@see ObjectType::isDescendantOf()}.
     *
     * @param bool $expected Expected result.
     * @param string $descendantName Descendant object type name.
     * @param string $ancestorName Ancestor object type name.
     * @return void
     * @dataProvider isDescendantOfProvider()
     * @covers ::isDescendantOf()
     */
    public function testIsDescendantOf(bool $expected, string $descendantName, string $ancestorName): void
    {
        $descendant = $this->ObjectTypes->get($descendantName);
        $ancestor = $this->ObjectTypes->get($ancestorName);

        $actual = $descendant->isDescendantOf($ancestor);
        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for {@see ObjectTypeTest::testGetClosestCommonAncestor()} test case.
     *
     * @return array[]
     */
    public function getClosestCommonAncestorProvider(): array
    {
        return [
            'Ø = null' => [null, []],
            'profiles = profiles' => ['profiles', ['profiles']],
            'files|media = media' => ['media', ['files', 'media']],
            'media|files = media' => ['media', ['media', 'files']],
            'locations|users|files = objects' => ['objects', ['locations', 'users', 'files']],
        ];
    }

    /**
     * Test {@see ObjectType::getClosestCommonAncestor()}.
     *
     * @param string|null $expected Expected result.
     * @param ObjectType[]|string[] $names Object type names.
     * @return void
     * @dataProvider getClosestCommonAncestorProvider()
     * @covers ::getClosestCommonAncestor()
     */
    public function testGetClosestCommonAncestor(?string $expected, array $names): void
    {
        $objectTypes = array_map(
            function ($name): ObjectType {
                if (is_string($name)) {
                    return $this->ObjectTypes->get($name);
                }

                return $name;
            },
            $names
        );

        $actual = ObjectType::getClosestCommonAncestor(...$objectTypes);
        if ($expected === null) {
            static::assertNull($actual);
        } else {
            static::assertInstanceOf(ObjectType::class, $actual);
            static::assertSame($expected, $actual->name);
        }
    }

    /**
     * Test `addAssoc()`
     *
     * @return void
     * @covers ::addAssoc()
     */
    public function testAddAssoc(): void
    {
        /** @var \BEdita\Core\Model\Entity\ObjectType $entity */
        $entity = $this->ObjectTypes->newEmptyEntity();
        $entity->associations = ['Categories'];
        $entity->addAssoc('Permissions');
        static::assertEquals(['Categories', 'Permissions'], $entity->associations);
    }

    /**
     * Test `hasAssoc()`
     *
     * @return void
     * @covers ::hasAssoc()
     */
    public function testHasAssoc(): void
    {
        /** @var \BEdita\Core\Model\Entity\ObjectType $entity */
        $entity = $this->ObjectTypes->newEmptyEntity();
        static::assertFalse($entity->hasAssoc('Permissions'));
        $entity->associations = ['Permissions'];
        static::assertTrue($entity->hasAssoc('Permissions'));
    }

    /**
     * Test `removeAssoc()`
     *
     * @return void
     * @covers ::removeAssoc()
     */
    public function testRemoveAssoc(): void
    {
        /** @var \BEdita\Core\Model\Entity\ObjectType $entity */
        $entity = $this->ObjectTypes->newEmptyEntity();
        $entity->associations = null;
        $entity->removeAssoc('Permissions');
        static::assertEquals(null, $entity->associations);

        $entity->associations = ['Categories', 'Permissions'];
        $entity->removeAssoc('Permissions');
        static::assertEquals(['Categories'], $entity->associations);
    }
}
