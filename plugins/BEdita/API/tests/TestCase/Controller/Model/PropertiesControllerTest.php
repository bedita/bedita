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
 * @coversDefaultClass \BEdita\API\Controller\Model\PropertiesController
 */
class PropertiesControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
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
                'self' => 'http://api.example.com/model/properties?' . http_build_query(['filter' => ['type' => 'dynamic']]),
                'first' => 'http://api.example.com/model/properties?' . http_build_query(['filter' => ['type' => 'dynamic']]),
                'last' => 'http://api.example.com/model/properties?' . http_build_query(['filter' => ['type' => 'dynamic']]),
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
                    'type' => 'properties',
                    'attributes' => [
                        'name' => 'another_title',
                        'description' => null,
                        'property_type_name' => 'string',
                        'object_type_name' => 'documents',
                        'label' => null,
                        'is_nullable' => true,
                        'is_static' => false,
                    ],
                    'meta' => [
                        'created' => '2016-12-31T23:09:23+00:00',
                        'modified' => '2016-12-31T23:09:23+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/properties/1',
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'properties',
                    'attributes' => [
                        'name' => 'another_description',
                        'description' => null,
                        'property_type_name' => 'string',
                        'object_type_name' => 'documents',
                        'label' => 'Brief description',
                        'is_nullable' => true,
                        'is_static' => false,
                    ],
                    'meta' => [
                        'created' => '2016-12-31T23:09:23+00:00',
                        'modified' => '2016-12-31T23:09:23+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/properties/2',
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'properties',
                    'attributes' => [
                        'name' => 'another_username',
                        'description' => 'Username, unique string',
                        'property_type_name' => 'string',
                        'object_type_name' => 'users',
                        'label' => null,
                        'is_nullable' => true,
                        'is_static' => false,
                    ],
                    'meta' => [
                        'created' => '2016-12-31T23:09:23+00:00',
                        'modified' => '2016-12-31T23:09:23+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/properties/3',
                    ],
                ],
                [
                    'id' => '4',
                    'type' => 'properties',
                    'attributes' => [
                        'name' => 'another_email',
                        'description' => 'User email',
                        'property_type_name' => 'string',
                        'object_type_name' => 'users',
                        'label' => null,
                        'is_nullable' => true,
                        'is_static' => false,
                    ],
                    'meta' => [
                        'created' => '2016-12-31T23:09:23+00:00',
                        'modified' => '2016-12-31T23:09:23+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/properties/4',
                    ],
                ],
                [
                    'id' => '5',
                    'type' => 'properties',
                    'attributes' => [
                        'name' => 'another_birthdate',
                        'description' => null,
                        'property_type_name' => 'date',
                        'object_type_name' => 'profiles',
                        'label' => 'Date of birth',
                        'is_nullable' => true,
                        'is_static' => false,
                    ],
                    'meta' => [
                        'created' => '2016-12-31T23:09:23+00:00',
                        'modified' => '2016-12-31T23:09:23+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/properties/5',
                    ],
                ],
                [
                    'id' => '6',
                    'type' => 'properties',
                    'attributes' => [
                        'name' => 'another_surname',
                        'description' => null,
                        'property_type_name' => 'string',
                        'object_type_name' => 'profiles',
                        'label' => null,
                        'is_nullable' => true,
                        'is_static' => false,
                    ],
                    'meta' => [
                        'created' => '2016-12-31T23:09:23+00:00',
                        'modified' => '2016-12-31T23:09:23+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/properties/6',
                    ],
                ],
                [
                    'id' => '7',
                    'type' => 'properties',
                    'attributes' => [
                        'name' => 'disabled_property',
                        'description' => 'Disabled property example',
                        'property_type_name' => 'string',
                        'object_type_name' => 'files',
                        'label' => null,
                        'is_nullable' => true,
                        'is_static' => false,
                    ],
                    'meta' => [
                        'created' => '2017-09-05T11:10:00+00:00',
                        'modified' => '2017-09-05T11:10:00+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/properties/7',
                    ],
                ],
                [
                    'id' => '8',
                    'type' => 'properties',
                    'attributes' => [
                        'name' => 'media_property',
                        'description' => null,
                        'property_type_name' => 'string',
                        'object_type_name' => 'media',
                        'label' => null,
                        'is_nullable' => true,
                        'is_static' => false,
                    ],
                    'meta' => [
                        'created' => '2017-11-07T18:32:00+00:00',
                        'modified' => '2017-11-07T18:32:00+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/properties/8',
                    ],
                ],
                [
                    'id' => '9',
                    'type' => 'properties',
                    'attributes' => [
                        'name' => 'files_property',
                        'description' => null,
                        'property_type_name' => 'string',
                        'object_type_name' => 'files',
                        'label' => null,
                        'is_nullable' => true,
                        'is_static' => false,
                    ],
                    'meta' => [
                        'created' => '2017-11-07T18:32:00+00:00',
                        'modified' => '2017-11-07T18:32:00+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/properties/9',
                    ],
                ],
                [
                    'id' => '10',
                    'type' => 'properties',
                    'attributes' => [
                        'name' => 'street_address',
                        'description' => null,
                        'property_type_name' => 'string',
                        'object_type_name' => 'profiles',
                        'label' => null,
                        'is_nullable' => true,
                        'is_static' => true,
                    ],
                    'meta' => [
                        'created' => '2020-08-07T16:23:00+00:00',
                        'modified' => '2020-08-07T16:23:00+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/properties/10',
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/model/properties?filter[type]=dynamic');
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
                'self' => 'http://api.example.com/model/properties?' . http_build_query(['filter' => ['type' => 'dynamic']]),
                'first' => 'http://api.example.com/model/properties?' . http_build_query(['filter' => ['type' => 'dynamic']]),
                'last' => 'http://api.example.com/model/properties?' . http_build_query(['filter' => ['type' => 'dynamic']]),
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

        TableRegistry::getTableLocator()->get('Properties')->deleteAll([]);

        $this->configRequestHeaders();
        $this->get('/model/properties?filter[type]=dynamic');
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
                'self' => 'http://api.example.com/model/properties/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'properties',
                'attributes' => [
                    'name' => 'another_title',
                    'description' => null,
                    'property_type_name' => 'string',
                    'object_type_name' => 'documents',
                    'label' => null,
                    'is_nullable' => true,
                    'is_static' => false,
                ],
                'meta' => [
                    'created' => '2016-12-31T23:09:23+00:00',
                    'modified' => '2016-12-31T23:09:23+00:00',
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/model/properties/1');
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
                'self' => 'http://api.example.com/model/properties/999999',
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '404',
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/model/properties/999999');
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
            'type' => 'properties',
            'attributes' => [
                'name' => 'yet_another_body',
                'object_type_name' => 'documents',
                'property_type_name' => 'string',
                'description' => null,
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/model/properties', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        $this->assertHeader('Location', 'http://api.example.com/model/properties/11');
        static::assertTrue(TableRegistry::getTableLocator()->get('Properties')->exists(['name' => 'yet_another_body']));
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
            'type' => 'properties',
            'attributes' => [
                'description' => 'Undefined property',
            ],
        ];

        $count = TableRegistry::getTableLocator()->get('Properties')->find()->count();

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/model/properties', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($count, TableRegistry::getTableLocator()->get('Properties')->find()->count());
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
            'type' => 'properties',
            'attributes' => [
                'name' => 'another_title',
                'description' => 'nice description',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/model/properties/1', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        unset($result['data']['meta']);
        $data['attributes'] += [
            'label' => null,
            'is_nullable' => true,
            'property_type_name' => 'string',
            'object_type_name' => 'documents',
            'is_static' => false,
        ];
        static::assertEquals($data, $result['data']);

        static::assertEquals('nice description', TableRegistry::getTableLocator()->get('Properties')->get(1)->get('description'));
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
            'type' => 'properties',
            'attributes' => [
                'name' => 'profile new',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/model/properties/2', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals('another_title', TableRegistry::getTableLocator()->get('Properties')->get(1)->get('name'));
        static::assertEquals('another_description', TableRegistry::getTableLocator()->get('Properties')->get(2)->get('name'));
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
        $this->delete('/model/properties/1');

        $this->assertResponseCode(204);
        $this->assertResponseEmpty();
        static::assertFalse(TableRegistry::getTableLocator()->get('Properties')->exists(['id' => 1]));
    }
}
