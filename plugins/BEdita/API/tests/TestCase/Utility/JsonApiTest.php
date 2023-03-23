<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Test\TestCase\Utility;

use BEdita\API\Test\TestConstants;
use BEdita\API\Utility\JsonApi;
use BEdita\Core\Utility\JsonApiSerializable;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * @coversDefaultClass \BEdita\API\Utility\JsonApi
 */
class JsonApiTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\RolesTable
     */
    public $Roles;

    /**
     * Fixtures.
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Roles = TableRegistry::getTableLocator()->get('Roles');

        $this->loadPlugins(['BEdita/API' => ['routes' => true]]);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Roles);

        parent::tearDown();
    }

    /**
     * Data provider for `testFormatData` test case.
     *
     * @return array
     */
    public function formatDataProvider()
    {
        return [
            'multipleQueryItems' => [
                [
                    [
                        'id' => '1',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'first role',
                            'description' => 'this is the very first role',
                        ],
                        'meta' => [
                            'unchangeable' => true,
                            'created' => '2016-04-15T09:57:38+00:00',
                            'modified' => '2016-04-15T09:57:38+00:00',
                            'priority' => 0,
                        ],
                        'relationships' => [
                            'users' => [
                                'links' => [
                                    'self' => '/roles/1/relationships/users',
                                    'related' => '/roles/1/users',
                                ],
                            ],
                        ],
                        'links' => [
                            'self' => '/roles/1',
                        ],
                    ],
                    [
                        'id' => '2',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'second role',
                            'description' => 'this is a second role',
                        ],
                        'meta' => [
                            'unchangeable' => false,
                            'created' => '2016-04-15T11:59:12+00:00',
                            'modified' => '2016-04-15T11:59:13+00:00',
                            'priority' => 100,
                        ],
                        'relationships' => [
                            'users' => [
                                'links' => [
                                    'self' => '/roles/2/relationships/users',
                                    'related' => '/roles/2/users',
                                ],
                            ],
                        ],
                        'links' => [
                            'self' => '/roles/2',
                        ],
                    ],
                    '_schema' => [
                        'roles' => [
                            '$id' => '/model/schema/roles',
                            'revision' => TestConstants::SCHEMA_REVISIONS['roles'],
                        ],
                    ],
                ],
                function (Table $Table) {
                    return $Table->find('all');
                },
            ],
            'multipleResultSetItems' => [
                [
                    [
                        'id' => '1',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'first role',
                            'description' => 'this is the very first role',
                        ],
                        'meta' => [
                            'unchangeable' => true,
                            'created' => '2016-04-15T09:57:38+00:00',
                            'modified' => '2016-04-15T09:57:38+00:00',
                            'priority' => 0,
                        ],
                        'relationships' => [
                            'users' => [
                                'links' => [
                                    'self' => '/roles/1/relationships/users',
                                    'related' => '/roles/1/users',
                                ],
                            ],
                        ],
                        'links' => [
                            'self' => '/roles/1',
                        ],
                    ],
                    [
                        'id' => '2',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'second role',
                            'description' => 'this is a second role',
                        ],
                        'meta' => [
                            'unchangeable' => false,
                            'created' => '2016-04-15T11:59:12+00:00',
                            'modified' => '2016-04-15T11:59:13+00:00',
                            'priority' => 100,
                        ],
                        'relationships' => [
                            'users' => [
                                'links' => [
                                    'self' => '/roles/2/relationships/users',
                                    'related' => '/roles/2/users',
                                ],
                            ],
                        ],
                        'links' => [
                            'self' => '/roles/2',
                        ],
                    ],
                    '_schema' => [
                        'roles' => [
                            '$id' => '/model/schema/roles',
                            'revision' => TestConstants::SCHEMA_REVISIONS['roles'],
                        ],
                    ],
                ],
                function (Table $Table) {
                    return $Table->find('all')->all();
                },
            ],
            'multipleArrayItems' => [
                [
                    [
                        'id' => '1',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'first role',
                            'description' => 'this is the very first role',
                        ],
                        'meta' => [
                            'unchangeable' => true,
                            'created' => '2016-04-15T09:57:38+00:00',
                            'modified' => '2016-04-15T09:57:38+00:00',
                            'priority' => 0,
                        ],
                        'relationships' => [
                            'users' => [
                                'links' => [
                                    'self' => '/roles/1/relationships/users',
                                    'related' => '/roles/1/users',
                                ],
                            ],
                        ],
                        'links' => [
                            'self' => '/roles/1',
                        ],
                    ],
                    [
                        'id' => '2',
                        'type' => 'roles',
                        'attributes' => [
                            'name' => 'second role',
                            'description' => 'this is a second role',
                        ],
                        'meta' => [
                            'unchangeable' => false,
                            'created' => '2016-04-15T11:59:12+00:00',
                            'modified' => '2016-04-15T11:59:13+00:00',
                            'priority' => 100,
                        ],
                        'relationships' => [
                            'users' => [
                                'links' => [
                                    'self' => '/roles/2/relationships/users',
                                    'related' => '/roles/2/users',
                                ],
                            ],
                        ],
                        'links' => [
                            'self' => '/roles/2',
                        ],
                    ],
                    '_schema' => [
                        'roles' => [
                            '$id' => '/model/schema/roles',
                            'revision' => TestConstants::SCHEMA_REVISIONS['roles'],
                        ],
                    ],
                ],
                function (Table $Table) {
                    return $Table->find('all')->toArray();
                },
            ],
            'singleEntityItem' => [
                [
                    'id' => '1',
                    'type' => 'roles',
                    'attributes' => [
                        'name' => 'first role',
                        'description' => 'this is the very first role',
                    ],
                    'meta' => [
                        'unchangeable' => true,
                        'created' => '2016-04-15T09:57:38+00:00',
                        'modified' => '2016-04-15T09:57:38+00:00',
                        'priority' => 0,
                    ],
                    'relationships' => [
                        'users' => [
                            'links' => [
                                'self' => '/roles/1/relationships/users',
                                'related' => '/roles/1/users',
                            ],
                        ],
                    ],
                    '_schema' => [
                        'roles' => [
                            '$id' => '/model/schema/roles',
                            'revision' => TestConstants::SCHEMA_REVISIONS['roles'],
                        ],
                    ],
                ],
                function (Table $Table) {
                    return $Table->get(1);
                },
            ],
            'singleEntityItemAutomaticType' => [
                [
                    'id' => '1',
                    'type' => 'roles',
                    'attributes' => [
                        'name' => 'first role',
                        'description' => 'this is the very first role',
                    ],
                    'meta' => [
                        'unchangeable' => true,
                        'created' => '2016-04-15T09:57:38+00:00',
                        'modified' => '2016-04-15T09:57:38+00:00',
                        'priority' => 0,
                    ],
                    'relationships' => [
                        'users' => [
                            'links' => [
                                'self' => '/roles/1/relationships/users',
                                'related' => '/roles/1/users',
                            ],
                        ],
                    ],
                    '_schema' => [
                        'roles' => [
                            '$id' => '/model/schema/roles',
                            'revision' => TestConstants::SCHEMA_REVISIONS['roles'],
                        ],
                    ],
                ],
                function (Table $Table) {
                    return $Table->get(1);
                },
            ],
            'emptyArray' => [
                [],
                function () {
                    return [];
                },
            ],
            'nullItem' => [
                null,
                function () {
                    return null;
                },
            ],
            'unsupportedType' => [
                false,
                function () {
                    return [
                        'unsupported format',
                    ];
                },
            ],
            'missingId' => [
                false,
                function () {
                    return [
                        'type' => 'test',
                        'name' => 'Paolo',
                        'blocked' => true,
                    ];
                },
            ],
            'excludeMetaAndAttributes' => [
                [
                    'id' => '1',
                    'type' => 'roles',
                    'relationships' => [
                        'users' => [
                            'links' => [
                                'self' => '/roles/1/relationships/users',
                                'related' => '/roles/1/users',
                            ],
                        ],
                    ],
                ],
                function (Table $Table) {
                    return $Table->get(1);
                },
                JsonApiSerializable::JSONAPIOPT_EXCLUDE_ATTRIBUTES | JsonApiSerializable::JSONAPIOPT_EXCLUDE_META,
            ],
            'included' => [
                [
                    'id' => '2',
                    'type' => 'documents',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'title-one',
                        'title' => 'title one',
                        'description' => 'description here',
                        'body' => 'body here',
                        'extra' => [
                            'abstract' => 'abstract here',
                            'list' => ['one', 'two', 'three'],
                        ],
                        'lang' => 'en',
                        'publish_start' => '2016-05-13T07:09:23+00:00',
                        'publish_end' => '2016-05-13T07:09:23+00:00',
                        'another_title' => null,
                        'another_description' => null,
                    ],
                    'meta' => [
                        'locked' => true,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => '2016-05-13T07:09:23+00:00',
                        'created_by' => 1,
                        'modified_by' => 1,
                    ],
                    'relationships' => [
                        'test' => [
                            'links' => [
                                'related' => '/documents/2/test',
                                'self' => '/documents/2/relationships/test',
                            ],
                            'data' => [
                                [
                                    'id' => '4',
                                    'type' => 'profiles',
                                ],
                                [
                                    'id' => '3',
                                    'type' => 'documents',
                                ],
                            ],
                        ],
                        'inverse_test' => [
                            'links' => [
                                'related' => '/documents/2/inverse_test',
                                'self' => '/documents/2/relationships/inverse_test',
                            ],
                        ],
                        'parents' => [
                            'links' => [
                                'related' => '/documents/2/parents',
                                'self' => '/documents/2/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => '/documents/2/translations',
                                'self' => '/documents/2/relationships/translations',
                            ],
                        ],
                    ],
                    '_schema' => [
                        'profiles' => [
                            '$id' => '/model/schema/profiles',
                            'revision' => TestConstants::SCHEMA_REVISIONS['profiles'],
                        ],
                        'documents' => [
                            '$id' => '/model/schema/documents',
                            'revision' => TestConstants::SCHEMA_REVISIONS['documents'],
                        ],
                    ],
                ],
                function () {
                    return TableRegistry::getTableLocator()->get('Documents')->get(2, ['contain' => ['Test']]);
                },
            ],
        ];
    }

    /**
     * Test {@see \BEdita\Core\Utility\JsonApi::formatData()} and
     * {@see \BEdita\Core\Utility\JsonApi::formatItem()} methods.
     *
     * @param array|bool $expected Expected result. If `false`, an exception is expected.
     * @param callable $items A callable that returns the items to be converted.
     * @param int $options Format data options
     * @return void
     * @dataProvider formatDataProvider
     * @covers ::formatData()
     * @covers ::metaSchema()
     * @covers ::dispatchEvent()
     */
    public function testFormatData($expected, callable $items, $options = 0)
    {
        if ($expected === false) {
            $this->expectException('\InvalidArgumentException');
        }

        $result = JsonApi::formatData($items($this->Roles), $options);
        $result = json_decode(json_encode($result), true);

        static::assertEquals($expected, $result);
    }

    /**
     * Data provider for `testParseData` test case.
     *
     * @return array
     */
    public function parseDataProvider()
    {
        return [
            'singleItem' => [
                [
                    'id' => '1',
                    'type' => 'customType',
                    'key' => 'value',
                    'otherKey' => 'otherValue',
                ],
                [
                    'id' => '1',
                    'type' => 'customType',
                    'attributes' => [
                        'key' => 'value',
                        'otherKey' => 'otherValue',
                    ],
                ],
            ],
            'multipleItems' => [
                [
                    [
                        'id' => '1',
                        'type' => 'customType',
                    ],
                    [

                        'id' => '2',
                        'type' => 'otherType',
                        'numeric' => 2,
                        'boolean' => false,
                        'array' => [1, 2, 3],
                    ],
                ],
                [
                    [
                        'id' => '1',
                        'type' => 'customType',
                        'attributes' => [],
                    ],
                    [
                        'id' => '2',
                        'type' => 'otherType',
                        'attributes' => [
                            'numeric' => 2,
                            'boolean' => false,
                            'array' => [1, 2, 3],
                        ],
                    ],
                ],
            ],
            'missingId' => [
                [
                    'type' => 'customType',
                    'name' => 'Gustavo',
                ],
                [
                    'type' => 'customType',
                    'attributes' => [
                        'name' => 'Gustavo',
                    ],
                ],
            ],
            'missingType' => [
                false,
                [
                    'id' => '17',
                    'attributes' => [
                        'description' => 'Seventeen comes just after sixteen',
                    ],
                ],
            ],
            'meta' => [
                [
                    'type' => 'customType',
                    'name' => 'Gustavo',
                    '_meta' => [
                        'complex' => ['meta', 'data'],
                        'number' => 1,
                    ],
                ],
                [
                    'type' => 'customType',
                    'attributes' => [
                        'name' => 'Gustavo',
                    ],
                    'meta' => [
                        'complex' => ['meta', 'data'],
                        'number' => 1,
                    ],
                ],
            ],
            'empty' => [
                [],
                [],
            ],
        ];
    }

    /**
     * Test {@see \BEdita\Core\Utility\JsonApi::parseData()} and
     * {@see \BEdita\Core\Utility\JsonApi::parseItem()} methods.
     *
     * @param array|bool $expected Expected result. If `false`, an exception is expected.
     * @param array $items Items to be parsed.
     * @return void
     * @dataProvider parseDataProvider
     * @covers ::parseData
     * @covers ::parseItem
     */
    public function testParseData($expected, array $items)
    {
        if ($expected === false) {
            $this->expectException('\InvalidArgumentException');
        }

        $result = JsonApi::parseData($items);

        static::assertEquals($expected, $result);
    }

    /**
     * Test generation of relationships links.
     *
     * @return void
     * @covers ::formatData
     */
    public function testFallbackLinks()
    {
        $expected = [
            'id' => '2',
            'type' => 'documents',
            'attributes' => [
                'status' => 'on',
                'uname' => 'title-one',
                'title' => 'title one',
                'description' => 'description here',
                'body' => 'body here',
                'extra' => [
                    'abstract' => 'abstract here',
                    'list' => ['one', 'two', 'three'],
                ],
                'lang' => 'en',
                'publish_start' => '2016-05-13T07:09:23+00:00',
                'publish_end' => '2016-05-13T07:09:23+00:00',
                'another_title' => null,
                'another_description' => null,
            ],
            'meta' => [
                'locked' => true,
                'created' => '2016-05-13T07:09:23+00:00',
                'modified' => '2016-05-13T07:09:23+00:00',
                'published' => '2016-05-13T07:09:23+00:00',
                'created_by' => 1,
                'modified_by' => 1,
            ],
            'relationships' => [
                'test' => [
                    'links' => [
                        'related' => '/documents/2/test',
                        'self' => '/documents/2/relationships/test',
                    ],
                ],
                'inverse_test' => [
                    'links' => [
                        'related' => '/documents/2/inverse_test',
                        'self' => '/documents/2/relationships/inverse_test',
                    ],
                ],
                'parents' => [
                    'links' => [
                        'related' => '/documents/2/parents',
                        'self' => '/documents/2/relationships/parents',
                    ],
                ],
                'translations' => [
                    'links' => [
                        'related' => '/documents/2/translations',
                        'self' => '/documents/2/relationships/translations',
                    ],
                ],
            ],
            '_schema' => [
                'documents' => [
                    '$id' => '/model/schema/documents',
                    'revision' => TestConstants::SCHEMA_REVISIONS['documents'],
                ],
            ],
        ];

        $result = JsonApi::formatData(TableRegistry::getTableLocator()->get('Documents')->get(2));
        $result = json_decode(json_encode($result), true);

        static::assertEquals($expected, $result);
    }

    /**
     * Data provider for `testSchemaInfo` test case.
     *
     * @return array
     */
    public function schemaInfoProvider()
    {
        return [
            'roles' => [
                'roles',
                [
                    '$id' => '/model/schema/roles',
                    'revision' => TestConstants::SCHEMA_REVISIONS['roles'],
                ],
            ],
            'objects' => [
                'object_types',
                null,
            ],
            'properties' => [
                'properties',
                null,
            ],
        ];
    }

    /**
     * Test `schemaInfo` method
     *
     * @return void
     * @covers ::schemaInfo
     * @dataProvider schemaInfoProvider
     */
    public function testSchemaInfo($type, $expected)
    {
        $result = JsonApi::schemaInfo($type);
        static::assertEquals($expected, $result);
    }

    /**
     * Test `JsonApi.beforeFormatData` event.
     *
     * @return void
     * @covers ::formatData()
     * @covers ::dispatchEvent()
     */
    public function testBeforeFormatEvent(): void
    {
        $dispatchedEvent = 0;
        EventManager::instance()->on('JsonApi.beforeFormatData', function (Event $event, $items) use (&$dispatchedEvent) {
            EventManager::instance()->off('JsonApi.beforeFormatData');
            $dispatchedEvent++;
            $item = $items[0];

            static::assertInstanceOf(EntityInterface::class, $item);
            static::assertEquals('on', $item->get('status'));

            $item->set('status', 'off');

            return $items;
        });

        $document = TableRegistry::getTableLocator()->get('Documents')->get(2);
        $result = JsonApi::formatData($document);

        static::assertEquals(1, $dispatchedEvent);
        static::assertEquals('off', Hash::get($result, 'attributes.status'));
    }

    /**
     * Test `JsonApi.afterFormatData` event.
     *
     * @return void
     * @covers ::formatData()
     * @covers ::dispatchEvent()
     */
    public function testAfterFormatDataEvent(): void
    {
        $dispatchedEvent = 0;
        EventManager::instance()->on('JsonApi.afterFormatData', function (Event $event, $data) use (&$dispatchedEvent) {
            EventManager::instance()->off('JsonApi.afterFormatData');
            $dispatchedEvent++;

            foreach ($data as $d) {
                static::assertEquals('documents', Hash::get($d, 'type'));
            }

            $data = Hash::insert($data, '{n}.meta.after_format', true);

            return $data;
        });

        $document = TableRegistry::getTableLocator()->get('Documents')
            ->find('type', ['documents'])
            ->limit(2)
            ->all();

        $result = JsonApi::formatData($document);
        $expected = [true, true];
        static::assertEquals(1, $dispatchedEvent);
        static::assertEquals($expected, Hash::extract($result, '{n}.meta.after_format'));
    }

    /**
     * Test that an exception was raised if some item was not serializable.
     *
     * @return void
     * @covers ::formatData()
     */
    public function testNotJsonSerializable(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('Objects must implement "%s", got "array" instead', JsonApiSerializable::class));

        JsonApi::formatData(['name' => 'Gustavo']);
    }
}
