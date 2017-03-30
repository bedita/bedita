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
namespace BEdita\API\Test\TestCase\Controller;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;

/**
 * @coversDefaultClass \BEdita\API\Controller\ObjectTypesController
 */
class ObjectTypesControllerTest extends IntegrationTestCase
{
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
                'self' => 'http://api.example.com/object_types',
                'first' => 'http://api.example.com/object_types',
                'last' => 'http://api.example.com/object_types',
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
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'document',
                        'name' => 'documents',
                        'alias' => 'Documents',
                        'description' => null,
                        'plugin' => 'BEdita/Core',
                        'model' => 'Objects',
                        'table' => 'BEdita/Core.Objects',
                        'associations' => null,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/object_types/1',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'self' => 'http://api.example.com/object_types/1/relationships/properties',
                                'related' => 'http://api.example.com/object_types/1/properties',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'profile',
                        'name' => 'profiles',
                        'alias' => 'Profiles',
                        'description' => null,
                        'plugin' => 'BEdita/Core',
                        'model' => 'Profiles',
                        'table' => 'BEdita/Core.Profiles',
                        'associations' => null,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/object_types/2',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'self' => 'http://api.example.com/object_types/2/relationships/properties',
                                'related' => 'http://api.example.com/object_types/2/properties',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'user',
                        'name' => 'users',
                        'alias' => 'Users',
                        'description' => null,
                        'plugin' => 'BEdita/Core',
                        'model' => 'Users',
                        'table' => 'BEdita/Core.Users',
                        'associations' => null,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/object_types/3',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'self' => 'http://api.example.com/object_types/3/relationships/properties',
                                'related' => 'http://api.example.com/object_types/3/properties',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '4',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'news',
                        'name' => 'news',
                        'alias' => 'News',
                        'description' => null,
                        'plugin' => 'BEdita/Core',
                        'model' => 'Objects',
                        'table' => 'BEdita/Core.Objects',
                        'associations' => null,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/object_types/4',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'self' => 'http://api.example.com/object_types/4/relationships/properties',
                                'related' => 'http://api.example.com/object_types/4/properties',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '5',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'location',
                        'name' => 'locations',
                        'alias' => 'Locations',
                        'description' => null,
                        'plugin' => 'BEdita/Core',
                        'model' => 'Locations',
                        'table' => 'BEdita/Core.Locations',
                        'associations' => null,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/object_types/5',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'self' => 'http://api.example.com/object_types/5/relationships/properties',
                                'related' => 'http://api.example.com/object_types/5/properties',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '6',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'event',
                        'name' => 'events',
                        'alias' => 'Events',
                        'description' => null,
                        'plugin' => 'BEdita/Core',
                        'model' => 'Objects',
                        'table' => 'BEdita/Core.Objects',
                        'associations' => ['DateRanges'],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/object_types/6',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'self' => 'http://api.example.com/object_types/6/relationships/properties',
                                'related' => 'http://api.example.com/object_types/6/properties',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/object_types');
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
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEmpty()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/object_types',
                'first' => 'http://api.example.com/object_types',
                'last' => 'http://api.example.com/object_types',
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

        TableRegistry::get('ObjectTypes')->deleteAll([]);

        $this->configRequestHeaders();
        $this->get('/object_types');
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
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testSingle()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/object_types/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'object_types',
                'attributes' => [
                    'singular' => 'document',
                    'name' => 'documents',
                    'alias' => 'Documents',
                    'description' => null,
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                    'table' => 'BEdita/Core.Objects',
                    'associations' => null,
                ],
                'relationships' => [
                    'properties' => [
                        'links' => [
                            'self' => 'http://api.example.com/object_types/1/relationships/properties',
                            'related' => 'http://api.example.com/object_types/1/properties',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/object_types/1');
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
     * @covers ::resource()
     * @covers ::initialize()
     * @covers \BEdita\API\Error\ExceptionRenderer
     */
    public function testMissing()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/object_types/99',
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '404',
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/object_types/99');
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
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testAdd()
    {
        $data = [
            'type' => 'object_types',
            'attributes' => [
                'singular' => 'my_object_type',
                'name' => 'my_object_types',
                'alias' => 'My Object Type',
                'description' => null,
                'plugin' => 'BEdita/Core',
                'model' => 'Objects',
                'table' => 'BEdita/Core.Objects'
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/object_types', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        $this->assertHeader('Location', 'http://api.example.com/object_types/7');
        $this->assertTrue(TableRegistry::get('ObjectTypes')->exists(['singular' => 'my_object_type']));
    }

    /**
     * Test add method with invalid data.
     *
     * @return void
     *
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testAddInvalid()
    {
        $data = [
            'type' => 'object_types',
            'attributes' => [
                'description' => 'Anonymous object_type.',
            ],
        ];

        $count = TableRegistry::get('ObjectTypes')->find()->count();

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/object_types', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($count, TableRegistry::get('ObjectTypes')->find()->count());
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
            'type' => 'object_types',
            'attributes' => [
                'singular' => 'document new',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/object_types/1', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $ObjectTypes = TableRegistry::get('ObjectTypes');
        $entity = $ObjectTypes->get(1);
        $this->assertEquals('document new', $entity->get('singular'));

        // restore previous values
        $entity = $ObjectTypes->patchEntity($entity, ['singular' => 'document']);
        $success = $ObjectTypes->save($entity);
        $this->assertTrue((bool)$success);
    }

    /**
     * Test edit method with ID conflict.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEditConflict()
    {
        $data = [
            'id' => '1',
            'type' => 'object_types',
            'attributes' => [
                'singular' => 'profile new',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/object_types/2', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals('document', TableRegistry::get('ObjectTypes')->get(1)->get('singular'));
        $this->assertEquals('profile', TableRegistry::get('ObjectTypes')->get(2)->get('singular'));
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
        $this->delete('/object_types/1');

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $this->assertFalse(TableRegistry::get('ObjectTypes')->exists(['id' => 1]));
    }
}
