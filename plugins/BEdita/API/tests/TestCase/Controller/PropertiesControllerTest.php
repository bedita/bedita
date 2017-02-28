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
namespace BEdita\API\Test\TestCase\Controller;

use BEdita\Core\State\CurrentApplication;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * @coversDefaultClass \BEdita\API\Controller\PropertiesController
 */
class PropertiesControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.endpoints',
        'plugin.BEdita/Core.applications',
        'plugin.BEdita/Core.endpoint_permissions',
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.properties',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        CurrentApplication::setFromApiKey(API_KEY);
    }

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
                'self' => 'http://api.example.com/properties',
                'first' => 'http://api.example.com/properties',
                'last' => 'http://api.example.com/properties',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'pagination' => [
                    'count' => 6,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 6,
                    'page_size' => 20,
                ],
            ],
            'data' => [
                [
                    'id' => '1',
                    'type' => 'properties',
                    'attributes' => [
                        'name' => 'title',
                        'multiple' => false,
                        'options_list' => null,
                        'created' => '2016-12-31T23:09:23+00:00',
                        'modified' => '2016-12-31T23:09:23+00:00',
                        'description' => null,
                        'property_type_name' => 'string',
                        'object_type_name' => 'documents',
                        'label' => null,
                        'list_view' => true
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/properties/1',
                    ],
                    'relationships' => [
                        'object_types' => [
                            'links' => [
                                'self' => 'http://api.example.com/properties/1/relationships/object_types',
                                'related' => 'http://api.example.com/properties/1/object_types',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'properties',
                    'attributes' => [
                        'name' => 'description',
                        'multiple' => false,
                        'options_list' => null,
                        'created' => '2016-12-31T23:09:23+00:00',
                        'modified' => '2016-12-31T23:09:23+00:00',
                        'description' => null,
                        'property_type_name' => 'string',
                        'object_type_name' => 'documents',
                        'label' => 'Brief description',
                        'list_view' => false
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/properties/2',
                    ],
                    'relationships' => [
                        'object_types' => [
                            'links' => [
                                'self' => 'http://api.example.com/properties/2/relationships/object_types',
                                'related' => 'http://api.example.com/properties/2/object_types',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'properties',
                    'attributes' => [
                        'name' => 'username',
                        'multiple' => false,
                        'options_list' => null,
                        'created' => '2016-12-31T23:09:23+00:00',
                        'modified' => '2016-12-31T23:09:23+00:00',
                        'description' => 'Username, unique string',
                        'property_type_name' => 'string',
                        'object_type_name' => 'users',
                        'label' => null,
                        'list_view' => true
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/properties/3',
                    ],
                    'relationships' => [
                        'object_types' => [
                            'links' => [
                                'self' => 'http://api.example.com/properties/3/relationships/object_types',
                                'related' => 'http://api.example.com/properties/3/object_types',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '4',
                    'type' => 'properties',
                    'attributes' => [
                        'name' => 'email',
                        'multiple' => false,
                        'options_list' => null,
                        'created' => '2016-12-31T23:09:23+00:00',
                        'modified' => '2016-12-31T23:09:23+00:00',
                        'description' => 'User email',
                        'property_type_name' => 'string',
                        'object_type_name' => 'users',
                        'label' => null,
                        'list_view' => true
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/properties/4',
                    ],
                    'relationships' => [
                        'object_types' => [
                            'links' => [
                                'self' => 'http://api.example.com/properties/4/relationships/object_types',
                                'related' => 'http://api.example.com/properties/4/object_types',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '5',
                    'type' => 'properties',
                    'attributes' => [
                        'name' => 'birthdate',
                        'multiple' => false,
                        'options_list' => null,
                        'created' => '2016-12-31T23:09:23+00:00',
                        'modified' => '2016-12-31T23:09:23+00:00',
                        'description' => null,
                        'property_type_name' => 'date',
                        'object_type_name' => 'profiles',
                        'label' => 'Date of birth',
                        'list_view' => false
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/properties/5',
                    ],
                    'relationships' => [
                        'object_types' => [
                            'links' => [
                                'self' => 'http://api.example.com/properties/5/relationships/object_types',
                                'related' => 'http://api.example.com/properties/5/object_types',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '6',
                    'type' => 'properties',
                    'attributes' => [
                        'name' => 'surname',
                        'multiple' => false,
                        'options_list' => null,
                        'created' => '2016-12-31T23:09:23+00:00',
                        'modified' => '2016-12-31T23:09:23+00:00',
                        'description' => null,
                        'property_type_name' => 'string',
                        'object_type_name' => 'profiles',
                        'label' => null,
                        'list_view' => true
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/properties/6',
                    ],
                    'relationships' => [
                        'object_types' => [
                            'links' => [
                                'self' => 'http://api.example.com/properties/6/relationships/object_types',
                                'related' => 'http://api.example.com/properties/6/object_types',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/properties');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test index method.
     *
     * @return void
     *
     * @covers ::view()
     * @covers ::initialize()
     */
    public function testEmpty()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/properties',
                'first' => 'http://api.example.com/properties',
                'last' => 'http://api.example.com/properties',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'pagination' => [
                    'count' => 0,
                    'page' => 1,
                    'page_count' => 0,
                    'page_items' => 0,
                    'page_size' => 20,
                ],
            ],
            'data' => [],
        ];

        TableRegistry::get('Properties')->deleteAll([]);

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/properties');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test view method.
     *
     * @return void
     *
     * @covers ::view()
     * @covers ::initialize()
     */
    public function testSingle()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/properties/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'properties',
                'attributes' => [
                    'name' => 'title',
                    'multiple' => false,
                    'options_list' => null,
                    'created' => '2016-12-31T23:09:23+00:00',
                    'modified' => '2016-12-31T23:09:23+00:00',
                    'description' => null,
                    'property_type_name' => 'string',
                    'object_type_name' => 'documents',
                    'label' => null,
                    'list_view' => true
                ],
                'relationships' => [
                    'object_types' => [
                        'links' => [
                            'self' => 'http://api.example.com/properties/1/relationships/object_types',
                            'related' => 'http://api.example.com/properties/1/object_types',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/properties/1');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test view method.
     *
     * @return void
     *
     * @covers ::view()
     * @covers ::initialize()
     * @covers \BEdita\API\Error\ExceptionRenderer
     */
    public function testMissing()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/properties/999999',
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '404',
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/properties/999999');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
        $this->assertArrayNotHasKey('data', $result);
        $this->assertArrayHasKey('links', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals($expected['links'], $result['links']);
        $this->assertArraySubset($expected['error'], $result['error']);
        $this->assertArrayHasKey('title', $result['error']);
        $this->assertNotEmpty($result['error']['title']);
    }

    /**
     * Test add method.
     *
     * @return void
     *
     * @covers ::add()
     * @covers ::initialize()
     */
    public function testAdd()
    {
        $data = [
            'type' => 'properties',
            'attributes' => [
                'name' => 'body',
                'object_type_name' => 'documents',
                'property_type_name' => 'string',
                'multiple' => 0,
                'options_list' => null,
                'description' => null,
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->post('/properties', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        $this->assertHeader('Location', 'http://api.example.com/properties/7');
        $this->assertTrue(TableRegistry::get('Properties')->exists(['name' => 'body']));
    }

    /**
     * Test add method with invalid data.
     *
     * @return void
     *
     * @covers ::add()
     * @covers ::initialize()
     */
    public function testInvalidAdd()
    {
        $data = [
            'type' => 'properties',
            'attributes' => [
                'description' => 'Undefined property',
            ],
        ];

        $count = TableRegistry::get('Properties')->find()->count();

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->post('/properties', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($count, TableRegistry::get('Properties')->find()->count());
    }

    /**
     * Test edit method.
     *
     * @return void
     *
     * @covers ::edit()
     * @covers ::initialize()
     */
    public function testEdit()
    {
        $data = [
            'id' => '1',
            'type' => 'properties',
            'attributes' => [
                'name' => 'title',
                'description' => 'nice description',
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->patch('/properties/1', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals('nice description', TableRegistry::get('Properties')->get(1)->get('description'));
    }

    /**
     * Test edit method with ID conflict.
     *
     * @return void
     *
     * @covers ::edit()
     * @covers ::initialize()
     */
    public function testConflictEdit()
    {
        $data = [
            'id' => '1',
            'type' => 'properties',
            'attributes' => [
                'name' => 'profile new',
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->patch('/properties/2', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals('title', TableRegistry::get('Properties')->get(1)->get('name'));
        $this->assertEquals('description', TableRegistry::get('Properties')->get(2)->get('name'));
    }

    /**
     * Test delete method.
     *
     * @return void
     *
     * @covers ::delete()
     * @covers ::initialize()
     */
    public function testDelete()
    {
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->delete('/properties/1');

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $this->assertFalse(TableRegistry::get('Properties')->exists(['id' => 1]));
    }
}
