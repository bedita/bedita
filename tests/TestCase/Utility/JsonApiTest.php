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

namespace BEdita\API\Test\TestCase\Utility;

use BEdita\API\Utility\JsonApi;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

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
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.roles_users',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Roles = TableRegistry::get('Roles');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
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
                        ],
                        'relationships' => [
                            'users' => [
                                'links' => [
                                    'self' => '/roles/1/relationships/users',
                                    'related' => '/roles/1/users',
                                    'available' => '/users',
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
                        ],
                        'relationships' => [
                            'users' => [
                                'links' => [
                                    'self' => '/roles/2/relationships/users',
                                    'related' => '/roles/2/users',
                                    'available' => '/users',
                                ],
                            ],
                        ],
                        'links' => [
                            'self' => '/roles/2',
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
                        ],
                        'relationships' => [
                            'users' => [
                                'links' => [
                                    'self' => '/roles/1/relationships/users',
                                    'related' => '/roles/1/users',
                                    'available' => '/users',
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
                        ],
                        'relationships' => [
                            'users' => [
                                'links' => [
                                    'self' => '/roles/2/relationships/users',
                                    'related' => '/roles/2/users',
                                    'available' => '/users',
                                ],
                            ],
                        ],
                        'links' => [
                            'self' => '/roles/2',
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
                        ],
                        'relationships' => [
                            'users' => [
                                'links' => [
                                    'self' => '/roles/1/relationships/users',
                                    'related' => '/roles/1/users',
                                    'available' => '/users',
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
                        ],
                        'relationships' => [
                            'users' => [
                                'links' => [
                                    'self' => '/roles/2/relationships/users',
                                    'related' => '/roles/2/users',
                                    'available' => '/users',
                                ],
                            ],
                        ],
                        'links' => [
                            'self' => '/roles/2',
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
                    ],
                    'relationships' => [
                        'users' => [
                            'links' => [
                                'self' => '/roles/1/relationships/users',
                                'related' => '/roles/1/users',
                                'available' => '/users',
                            ],
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
                    ],
                    'relationships' => [
                        'users' => [
                            'links' => [
                                'self' => '/roles/1/relationships/users',
                                'related' => '/roles/1/users',
                                'available' => '/users',
                            ],
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
        ];
    }

    /**
     * Test {@see \BEdita\Core\Utility\JsonApi::formatData()} and
     * {@see \BEdita\Core\Utility\JsonApi::formatItem()} methods.
     *
     * @param array|bool $expected Expected result. If `false`, an exception is expected.
     * @param callable $items A callable that returns the items to be converted.
     * @return void
     *
     * @dataProvider formatDataProvider
     * @covers ::formatData
     */
    public function testFormatData($expected, callable $items)
    {
        if ($expected === false) {
            $this->expectException('\InvalidArgumentException');
        }

        $result = JsonApi::formatData($items($this->Roles));
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
     *
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
     *
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
                'lang' => 'eng',
                'publish_start' => '2016-05-13T07:09:23+00:00',
                'publish_end' => '2016-05-13T07:09:23+00:00',
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
                        'available' => sprintf(
                            '/objects?%s',
                            http_build_query(['filter' => ['type' => ['documents', 'profiles']]])
                        ),
                    ],
                ],
                'inverse_test' => [
                    'links' => [
                        'related' => '/documents/2/inverse_test',
                        'self' => '/documents/2/relationships/inverse_test',
                        'available' => sprintf(
                            '/objects?%s',
                            http_build_query(['filter' => ['type' => ['documents']]])
                        ),
                    ],
                ],
            ],
        ];

        $result = JsonApi::formatData(TableRegistry::get('Documents')->get(2));
        $result = json_decode(json_encode($result), true);

        static::assertEquals($expected, $result);
    }
}
