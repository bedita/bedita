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
     * @var \BEdita\Core\Model\Table\UsersTable
     */
    public $Users;

    /**
     * Fixtures.
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.users',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Users = TableRegistry::get('Users');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Users);

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
                        'type' => 'users',
                        'attributes' => [
                            'username' => 'first user',
                            'blocked' => false,
                            'last_login' => null,
                            'last_login_err' => null,
                            'num_login_err' => 1,
                            'created' => '2016-03-15T09:57:38+00:00',
                            'modified' => '2016-03-15T09:57:38+00:00',
                        ],
                        'links' => [
                            'self' => '/users/1',
                        ],
                    ],
                    [
                        'id' => '2',
                        'type' => 'users',
                        'attributes' => [
                            'username' => 'second user',
                            'blocked' => false,
                            'last_login' => '2016-03-15T09:57:38+00:00',
                            'last_login_err' => '2016-03-15T09:57:38+00:00',
                            'num_login_err' => 0,
                            'created' => '2016-03-15T09:57:38+00:00',
                            'modified' => '2016-03-15T09:57:38+00:00',
                        ],
                        'links' => [
                            'self' => '/users/2',
                        ],
                    ],
                ],
                function (Table $Table) {
                    return $Table->find('all');
                },
                'users',
            ],
            'multipleResultSetItems' => [
                [
                    [
                        'id' => '1',
                        'type' => 'users',
                        'attributes' => [
                            'username' => 'first user',
                            'blocked' => false,
                            'last_login' => null,
                            'last_login_err' => null,
                            'num_login_err' => 1,
                            'created' => '2016-03-15T09:57:38+00:00',
                            'modified' => '2016-03-15T09:57:38+00:00',
                        ],
                        'links' => [
                            'self' => '/users/1',
                        ],
                    ],
                    [
                        'id' => '2',
                        'type' => 'users',
                        'attributes' => [
                            'username' => 'second user',
                            'blocked' => false,
                            'last_login' => '2016-03-15T09:57:38+00:00',
                            'last_login_err' => '2016-03-15T09:57:38+00:00',
                            'num_login_err' => 0,
                            'created' => '2016-03-15T09:57:38+00:00',
                            'modified' => '2016-03-15T09:57:38+00:00',
                        ],
                        'links' => [
                            'self' => '/users/2',
                        ],
                    ],
                ],
                function (Table $Table) {
                    return $Table->find('all')->all();
                },
                'users',
            ],
            'multipleArrayItems' => [
                [
                    [
                        'id' => '1',
                        'type' => 'users',
                        'attributes' => [
                            'username' => 'first user',
                            'blocked' => false,
                            'last_login' => null,
                            'last_login_err' => null,
                            'num_login_err' => 1,
                            'created' => '2016-03-15T09:57:38+00:00',
                            'modified' => '2016-03-15T09:57:38+00:00',
                        ],
                        'links' => [
                            'self' => '/users/1',
                        ],
                    ],
                    [
                        'id' => '2',
                        'type' => 'users',
                        'attributes' => [
                            'username' => 'second user',
                            'blocked' => false,
                            'last_login' => '2016-03-15T09:57:38+00:00',
                            'last_login_err' => '2016-03-15T09:57:38+00:00',
                            'num_login_err' => 0,
                            'created' => '2016-03-15T09:57:38+00:00',
                            'modified' => '2016-03-15T09:57:38+00:00',
                        ],
                        'links' => [
                            'self' => '/users/2',
                        ],
                    ],
                ],
                function (Table $Table) {
                    return $Table->find('all')->toArray();
                },
                'users',
            ],
            'singleEntityItem' => [
                [
                    'id' => '1',
                    'type' => 'users',
                    'attributes' => [
                        'username' => 'first user',
                        'blocked' => false,
                        'last_login' => null,
                        'last_login_err' => null,
                        'num_login_err' => 1,
                        'created' => '2016-03-15T09:57:38+00:00',
                        'modified' => '2016-03-15T09:57:38+00:00',
                    ],
                ],
                function (Table $Table) {
                    return $Table->get(1);
                },
                'users',
            ],
            'singleArrayItem' => [
                [
                    'id' => '1',
                    'type' => 'users',
                    'attributes' => [
                        'username' => 'first user',
                        'blocked' => false,
                        'last_login' => null,
                        'last_login_err' => null,
                        'num_login_err' => 1,
                        'created' => '2016-03-15T09:57:38+00:00',
                        'modified' => '2016-03-15T09:57:38+00:00',
                    ],
                ],
                function (Table $Table) {
                    return $Table->get(1)->toArray();
                },
                'users',
            ],
            'getTypeFromItem' => [
                [
                    'id' => '17',
                    'type' => 'customType',
                    'attributes' => [
                        'someAttribute' => 'someValue',
                    ],
                ],
                function () {
                    return [
                        'id' => 17,
                        'type' => 'customType',
                        'someAttribute' => 'someValue',
                    ];
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
        ];
    }

    /**
     * Test {@see \BEdita\Core\Utility\JsonApi::formatData()} and
     * {@see \BEdita\Core\Utility\JsonApi::formatItem()} methods.
     *
     * @param array|bool $expected Expected result. If `false`, an exception is expected.
     * @param callable $items A callable that returns the items to be converted.
     * @param string|null $type Type of items.
     * @return void
     *
     * @dataProvider formatDataProvider
     * @covers ::formatData
     * @covers ::formatItem
     */
    public function testFormatData($expected, callable $items, $type = null)
    {
        if ($expected === false) {
            $this->setExpectedException('\InvalidArgumentException');
        }

        $result = JsonApi::formatData($items($this->Users), $type);

        $this->assertEquals($expected, $result);
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
            $this->setExpectedException('\InvalidArgumentException');
        }

        $result = JsonApi::parseData($items);

        $this->assertEquals($expected, $result);
    }
}
