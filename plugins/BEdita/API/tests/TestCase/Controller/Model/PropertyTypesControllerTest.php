<?php
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

namespace BEdita\API\Test\TestCase\Controller\Model;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

/**
 * @coversDefaultClass \BEdita\API\Controller\Model\PropertyTypesController
 */
class PropertyTypesControllerTest extends IntegrationTestCase
{
    use ArraySubsetAsserts;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
    ];

    /**
     * Test index method.
     *
     * @return void
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testIndex()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/model/property_types',
                'first' => 'http://api.example.com/model/property_types',
                'last' => 'http://api.example.com/model/property_types',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'pagination' => [
                    'count' => 13,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 13,
                    'page_size' => 20,
                ],
            ],
            'data' => [
                [
                    'id' => '1',
                    'type' => 'property_types',
                    'attributes' => [
                        'name' => 'string',
                        'params' => [
                            'type' => 'string',
                        ],
                    ],
                    'meta' => [
                        'created' => '2019-11-01T09:23:43+00:00',
                        'modified' => '2019-11-01T09:23:43+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/property_types/1',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'related' => 'http://api.example.com/model/property_types/1/properties',
                                'self' => 'http://api.example.com/model/property_types/1/relationships/properties',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'property_types',
                    'attributes' => [
                        'name' => 'text',
                        'params' => [
                            'type' => 'string',
                            'contentMediaType' => 'text/html',
                        ],
                    ],
                    'meta' => [
                        'created' => '2019-11-01T09:23:43+00:00',
                        'modified' => '2019-11-01T09:23:43+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/property_types/2',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'related' => 'http://api.example.com/model/property_types/2/properties',
                                'self' => 'http://api.example.com/model/property_types/2/relationships/properties',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'property_types',
                    'attributes' => [
                        'name' => 'status',
                        'params' => [
                            'type' => 'string',
                            'enum' => ['on', 'off', 'draft'],
                        ],
                    ],
                    'meta' => [
                        'created' => '2019-11-01T09:23:43+00:00',
                        'modified' => '2019-11-01T09:23:43+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/property_types/3',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'related' => 'http://api.example.com/model/property_types/3/properties',
                                'self' => 'http://api.example.com/model/property_types/3/relationships/properties',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '4',
                    'type' => 'property_types',
                    'attributes' => [
                        'name' => 'email',
                        'params' => [
                            'type' => 'string',
                            'format' => 'email',
                        ],
                    ],
                    'meta' => [
                        'created' => '2019-11-01T09:23:43+00:00',
                        'modified' => '2019-11-01T09:23:43+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/property_types/4',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'related' => 'http://api.example.com/model/property_types/4/properties',
                                'self' => 'http://api.example.com/model/property_types/4/relationships/properties',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '5',
                    'type' => 'property_types',
                    'attributes' => [
                        'name' => 'url',
                        'params' => [
                            'type' => 'string',
                            'format' => 'uri',
                        ],
                    ],
                    'meta' => [
                        'created' => '2019-11-01T09:23:43+00:00',
                        'modified' => '2019-11-01T09:23:43+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/property_types/5',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'related' => 'http://api.example.com/model/property_types/5/properties',
                                'self' => 'http://api.example.com/model/property_types/5/relationships/properties',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '6',
                    'type' => 'property_types',
                    'attributes' => [
                        'name' => 'date',
                        'params' => [
                            'type' => 'string',
                            'format' => 'date',
                        ],
                    ],
                    'meta' => [
                        'created' => '2019-11-01T09:23:43+00:00',
                        'modified' => '2019-11-01T09:23:43+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/property_types/6',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'related' => 'http://api.example.com/model/property_types/6/properties',
                                'self' => 'http://api.example.com/model/property_types/6/relationships/properties',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '7',
                    'type' => 'property_types',
                    'attributes' => [
                        'name' => 'datetime',
                        'params' => [
                            'type' => 'string',
                            'format' => 'date-time',
                        ],
                    ],
                    'meta' => [
                        'created' => '2019-11-01T09:23:43+00:00',
                        'modified' => '2019-11-01T09:23:43+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/property_types/7',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'related' => 'http://api.example.com/model/property_types/7/properties',
                                'self' => 'http://api.example.com/model/property_types/7/relationships/properties',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '8',
                    'type' => 'property_types',
                    'attributes' => [
                        'name' => 'number',
                        'params' => [
                            'type' => 'number',
                        ],
                    ],
                    'meta' => [
                        'created' => '2019-11-01T09:23:43+00:00',
                        'modified' => '2019-11-01T09:23:43+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/property_types/8',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'related' => 'http://api.example.com/model/property_types/8/properties',
                                'self' => 'http://api.example.com/model/property_types/8/relationships/properties',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '9',
                    'type' => 'property_types',
                    'attributes' => [
                        'name' => 'integer',
                        'params' => [
                            'type' => 'integer',
                        ],
                    ],
                    'meta' => [
                        'created' => '2019-11-01T09:23:43+00:00',
                        'modified' => '2019-11-01T09:23:43+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/property_types/9',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'related' => 'http://api.example.com/model/property_types/9/properties',
                                'self' => 'http://api.example.com/model/property_types/9/relationships/properties',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '10',
                    'type' => 'property_types',
                    'attributes' => [
                        'name' => 'boolean',
                        'params' => [
                            'type' => 'boolean',
                        ],
                    ],
                    'meta' => [
                        'created' => '2019-11-01T09:23:43+00:00',
                        'modified' => '2019-11-01T09:23:43+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/property_types/10',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'related' => 'http://api.example.com/model/property_types/10/properties',
                                'self' => 'http://api.example.com/model/property_types/10/relationships/properties',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '11',
                    'type' => 'property_types',
                    'attributes' => [
                        'name' => 'json',
                        'params' => [
                        ],
                    ],
                    'meta' => [
                        'created' => '2019-11-01T09:23:43+00:00',
                        'modified' => '2019-11-01T09:23:43+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/property_types/11',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'related' => 'http://api.example.com/model/property_types/11/properties',
                                'self' => 'http://api.example.com/model/property_types/11/relationships/properties',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '12',
                    'type' => 'property_types',
                    'attributes' => [
                        'name' => 'unused property type',
                        'params' => [
                            'type' => 'object',
                            'properties' => [
                                'gustavo' => [
                                    'const' => 'supporto',
                                ],
                            ],
                            'required' => ['gustavo'],
                        ],
                    ],
                    'meta' => [
                        'created' => '2019-11-02T09:23:43+00:00',
                        'modified' => '2019-11-02T09:23:43+00:00',
                        'core_type' => false,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/property_types/12',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'related' => 'http://api.example.com/model/property_types/12/properties',
                                'self' => 'http://api.example.com/model/property_types/12/relationships/properties',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '13',
                    'type' => 'property_types',
                    'attributes' => [
                        'name' => 'children_order',
                        'params' => [
                            'type' => 'string',
                            'enum' => [
                                'position',
                                '-position',
                                'title',
                                '-title',
                                'created',
                                '-created',
                                'modified',
                                '-modified',
                                'publish_start',
                                '-publish_start',
                            ],
                        ],
                    ],
                    'meta' => [
                        'created' => '2022-12-01T15:35:21+00:00',
                        'modified' => '2022-12-01T15:35:21+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/property_types/13',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'related' => 'http://api.example.com/model/property_types/13/properties',
                                'self' => 'http://api.example.com/model/property_types/13/relationships/properties',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/model/property_types');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test index method.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEmpty()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/model/property_types',
                'first' => 'http://api.example.com/model/property_types',
                'last' => 'http://api.example.com/model/property_types',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'pagination' => [
                    'count' => 0,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 0,
                    'page_size' => 20,
                ],
            ],
            'data' => [],
        ];

        TableRegistry::getTableLocator()->get('PropertyTypes')->deleteAll([]);

        $this->configRequestHeaders();
        $this->get('/model/property_types');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test view method.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testSingle()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/model/property_types/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'property_types',
                'attributes' => [
                    'name' => 'string',
                    'params' => [
                        'type' => 'string',
                    ],
                ],
                'meta' => [
                    'created' => '2019-11-01T09:23:43+00:00',
                    'modified' => '2019-11-01T09:23:43+00:00',
                    'core_type' => true,
                ],
                'relationships' => [
                    'properties' => [
                        'links' => [
                            'related' => 'http://api.example.com/model/property_types/1/properties',
                            'self' => 'http://api.example.com/model/property_types/1/relationships/properties',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/model/property_types/1');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test view method with name as argument.
     *
     * @return void
     * @covers ::getResourceId()
     */
    public function testSingleName()
    {
        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/model/property_types/string');
        $this->assertResponseCode(200);
    }

    /**
     * Test view method.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testMissing()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/model/property_types/999999',
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '404',
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/model/property_types/999999');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayNotHasKey('data', $result);
        static::assertArrayHasKey('links', $result);
        static::assertArrayHasKey('error', $result);
        static::assertEquals($expected['links'], $result['links']);
        static::assertArraySubset($expected['error'], $result['error']);
        static::assertArrayHasKey('title', $result['error']);
        static::assertNotEmpty($result['error']['title']);
    }

    /**
     * Test add method.
     *
     * @return void
     * @covers ::index()
     * @covers ::initialize()
     * @covers ::resourceUrl()
     */
    public function testAdd()
    {
        $data = [
            'type' => 'property_types',
            'attributes' => [
                'name' => 'gustavo_type',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/model/property_types', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        $this->assertHeader('Location', 'http://api.example.com/model/property_types/14');
        static::assertTrue(TableRegistry::getTableLocator()->get('PropertyTypes')->exists(['name' => 'gustavo_type']));
        static::assertFalse(TableRegistry::getTableLocator()->get('PropertyTypes')->get(14)->get('core_type'));
    }

    /**
     * Test add method with invalid data.
     *
     * @return void
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testInvalidAdd()
    {
        $data = [
            'type' => 'property_types',
            'attributes' => [
                'some_property' => 'Some value',
            ],
        ];

        $count = TableRegistry::getTableLocator()->get('PropertyTypes')->find()->count();

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/model/property_types', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($count, TableRegistry::getTableLocator()->get('PropertyTypes')->find()->count());
    }

    /**
     * Test edit method.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEdit()
    {
        $data = [
            'id' => '12',
            'type' => 'property_types',
            'attributes' => [
                'name' => 'gustavo',
                'params' => [
                    'type' => 'string',
                    'format' => 'my whatever format',
                ],
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/model/property_types/12', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        unset($result['data']['relationships'], $result['data']['meta']);
        static::assertEquals($data, $result['data']);
        static::assertEquals($data['attributes']['params'], TableRegistry::getTableLocator()->get('PropertyTypes')->get(12)->get('params'));
    }

    /**
     * Test edit method with ID conflict.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testConflictEdit()
    {
        $data = [
            'id' => '1',
            'type' => 'property_types',
            'attributes' => [
                'name' => 'strong',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/model/property_types/2', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals('string', TableRegistry::getTableLocator()->get('PropertyTypes')->get(1)->get('name'));
        static::assertEquals('text', TableRegistry::getTableLocator()->get('PropertyTypes')->get(2)->get('name'));
    }

    /**
     * Test edit failure on `core` type.
     *
     * @return void
     * @coversNothing
     */
    public function testEditFailure()
    {
        $data = [
            'id' => '1',
            'type' => 'property_types',
            'attributes' => [
                'name' => 'gustavo',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/model/property_types/1', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(403);
        $expected = [
            'error' => [
                'status' => '403',
                'title' => 'Could not modify core property',
            ],
        ];
        unset($result['error']['meta'], $result['links']);
        static::assertEquals($expected, $result);
        static::assertEquals('string', TableRegistry::getTableLocator()->get('PropertyTypes')->get(1)->get('name'));
    }

    /**
     * Test delete method.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testDelete()
    {
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete('/model/property_types/12');

        $this->assertResponseCode(204);
        $this->assertResponseEmpty();
        static::assertFalse(TableRegistry::getTableLocator()->get('PropertyTypes')->exists(['id' => 12]));
    }
}
