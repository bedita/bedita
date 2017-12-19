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

/**
 * @coversDefaultClass \BEdita\API\Controller\Model\PropertyTypesController
 */
class PropertyTypesControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.properties',
    ];

    /**
     * Test index method.
     *
     * @return void
     *
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
                    'count' => 10,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 10,
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
                            'format' => 'date-time',
                        ],
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
                        'name' => 'number',
                        'params' => [
                            'type' => 'number',
                        ],
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
                        'name' => 'boolean',
                        'params' => [
                            'type' => 'boolean',
                        ],
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
                        'name' => 'json',
                        'params' => [
                            'type' => 'object',
                        ],
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
     *
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

        TableRegistry::get('PropertyTypes')->deleteAll([]);

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
     *
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
     * Test view method.
     *
     * @return void
     *
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
     *
     * @covers ::index()
     * @covers ::initialize()
     * @covers ::resourceUrl()
     */
    public function testAdd()
    {
        $data = [
            'type' => 'property_types',
            'attributes' => [
                'name' => 'gustavo',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/model/property_types', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        $this->assertHeader('Location', 'http://api.example.com/model/property_types/11');
        static::assertTrue(TableRegistry::get('PropertyTypes')->exists(['name' => 'gustavo']));
    }

    /**
     * Test add method with invalid data.
     *
     * @return void
     *
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

        $count = TableRegistry::get('PropertyTypes')->find()->count();

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/model/property_types', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($count, TableRegistry::get('PropertyTypes')->find()->count());
    }

    /**
     * Test edit method.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEdit()
    {
        $data = [
            'id' => '1',
            'type' => 'property_types',
            'attributes' => [
                'name' => 'string',
                'params' => [
                    'type' => 'string',
                    'format' => 'my whatever format',
                ],
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/model/property_types/1', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($data['attributes']['params'], TableRegistry::get('PropertyTypes')->get(1)->get('params'));
    }

    /**
     * Test edit method with ID conflict.
     *
     * @return void
     *
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
        static::assertEquals('string', TableRegistry::get('PropertyTypes')->get(1)->get('name'));
        static::assertEquals('text', TableRegistry::get('PropertyTypes')->get(2)->get('name'));
    }

    /**
     * Test delete method.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testDelete()
    {
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete('/model/property_types/10');

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        static::assertFalse(TableRegistry::get('PropertyTypes')->exists(['id' => 10]));
    }
}
