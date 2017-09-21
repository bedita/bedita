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
use Cake\Utility\Hash;

/**
 * @coversDefaultClass \BEdita\API\Controller\ObjectTypesController
 */
class ObjectTypesControllerTest extends IntegrationTestCase
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
                'self' => 'http://api.example.com/model/object_types',
                'first' => 'http://api.example.com/model/object_types',
                'last' => 'http://api.example.com/model/object_types',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'pagination' => [
                    'count' => 7,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 7,
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
                        'description' => null,
                        'plugin' => 'BEdita/Core',
                        'model' => 'Objects',
                        'table' => 'BEdita/Core.Objects',
                        'associations' => null,
                        'hidden' => null,
                    ],
                    'meta' => [
                        'alias' => 'Documents',
                        'relations' => [
                            'test',
                            'inverse_test',
                        ],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/object_types/1',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/1/relationships/properties',
                                'related' => 'http://api.example.com/model/object_types/1/properties',
                            ],
                        ],
                        'left_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/1/relationships/left_relations',
                                'related' => 'http://api.example.com/model/object_types/1/left_relations',
                            ]
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/1/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/1/right_relations',
                            ]
                        ],
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'profile',
                        'name' => 'profiles',
                        'description' => null,
                        'plugin' => 'BEdita/Core',
                        'model' => 'Profiles',
                        'table' => 'BEdita/Core.Profiles',
                        'associations' => null,
                        'hidden' => null,
                    ],
                    'meta' => [
                        'alias' => 'Profiles',
                        'relations' => [
                            'inverse_test',
                        ],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/object_types/2',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/2/relationships/properties',
                                'related' => 'http://api.example.com/model/object_types/2/properties',
                            ],
                        ],
                        'left_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/2/relationships/left_relations',
                                'related' => 'http://api.example.com/model/object_types/2/left_relations',
                            ]
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/2/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/2/right_relations',
                            ]
                        ],
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'user',
                        'name' => 'users',
                        'description' => null,
                        'plugin' => 'BEdita/Core',
                        'model' => 'Users',
                        'table' => 'BEdita/Core.Users',
                        'associations' => null,
                        'hidden' => null,
                    ],
                    'meta' => [
                        'alias' => 'Users',
                        'relations' => [],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/object_types/3',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/3/relationships/properties',
                                'related' => 'http://api.example.com/model/object_types/3/properties',
                            ],
                        ],
                        'left_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/3/relationships/left_relations',
                                'related' => 'http://api.example.com/model/object_types/3/left_relations',
                            ]
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/3/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/3/right_relations',
                            ]
                        ],
                    ],
                ],
                [
                    'id' => '4',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'news',
                        'name' => 'news',
                        'description' => null,
                        'plugin' => 'BEdita/Core',
                        'model' => 'Objects',
                        'table' => 'BEdita/Core.Objects',
                        'associations' => null,
                        'hidden' => ['body'],
                    ],
                    'meta' => [
                        'alias' => 'News',
                        'relations' => [],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/object_types/4',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/4/relationships/properties',
                                'related' => 'http://api.example.com/model/object_types/4/properties',
                            ],
                        ],
                        'left_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/4/relationships/left_relations',
                                'related' => 'http://api.example.com/model/object_types/4/left_relations',
                            ]
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/4/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/4/right_relations',
                            ]
                        ],
                    ],
                ],
                [
                    'id' => '5',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'location',
                        'name' => 'locations',
                        'description' => null,
                        'plugin' => 'BEdita/Core',
                        'model' => 'Locations',
                        'table' => 'BEdita/Core.Locations',
                        'associations' => null,
                        'hidden' => null,
                    ],
                    'meta' => [
                        'alias' => 'Locations',
                        'relations' => [
                            'another_test',
                            'inverse_another_test',
                        ],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/object_types/5',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/5/relationships/properties',
                                'related' => 'http://api.example.com/model/object_types/5/properties',
                            ],
                        ],
                        'left_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/5/relationships/left_relations',
                                'related' => 'http://api.example.com/model/object_types/5/left_relations',
                            ]
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/5/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/5/right_relations',
                            ]
                        ],
                    ],
                ],
                [
                    'id' => '6',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'event',
                        'name' => 'events',
                        'description' => null,
                        'plugin' => 'BEdita/Core',
                        'model' => 'Objects',
                        'table' => 'BEdita/Core.Objects',
                        'associations' => ['DateRanges'],
                        'hidden' => null,
                    ],
                    'meta' => [
                        'alias' => 'Events',
                        'relations' => [],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/object_types/6',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/6/relationships/properties',
                                'related' => 'http://api.example.com/model/object_types/6/properties',
                            ],
                        ],
                        'left_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/6/relationships/left_relations',
                                'related' => 'http://api.example.com/model/object_types/6/left_relations',
                            ]
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/6/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/6/right_relations',
                            ]
                        ],
                    ],
                ],
                [
                    'id' => '7',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'media',
                        'name' => 'media',
                        'description' => null,
                        'plugin' => 'BEdita/Core',
                        'model' => 'Media',
                        'table' => 'BEdita/Core.Media',
                        'associations' => ['Streams'],
                        'hidden' => null,
                    ],
                    'meta' => [
                        'alias' => 'Media',
                        'relations' => [],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/object_types/7',
                    ],
                    'relationships' => [
                        'properties' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/7/relationships/properties',
                                'related' => 'http://api.example.com/model/object_types/7/properties',
                            ],
                        ],
                        'left_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/7/relationships/left_relations',
                                'related' => 'http://api.example.com/model/object_types/7/left_relations',
                            ]
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/7/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/7/right_relations',
                            ]
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/model/object_types');
        $result = json_decode((string)$this->_response->getBody(), true);

        /*
         * @todo To remove when fquffio :) resolves the inconsistency response.
         *       According with other endpoint responses "included" and "data" of "relationships"
         *       should be present only when the query string "include" is present
         */
        $result = Hash::remove($result, 'included');
        $result = Hash::remove($result, 'data.{n}.relationships.left_relations.data');
        $result = Hash::remove($result, 'data.{n}.relationships.right_relations.data');

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
                'self' => 'http://api.example.com/model/object_types',
                'first' => 'http://api.example.com/model/object_types',
                'last' => 'http://api.example.com/model/object_types',
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

        TableRegistry::get('Properties')->deleteAll([]);
        TableRegistry::get('ObjectTypes')->deleteAll([]);

        $this->configRequestHeaders();
        $this->get('/model/object_types');
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
                'self' => 'http://api.example.com/model/object_types/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'object_types',
                'attributes' => [
                    'singular' => 'document',
                    'name' => 'documents',
                    'description' => null,
                    'plugin' => 'BEdita/Core',
                    'model' => 'Objects',
                    'table' => 'BEdita/Core.Objects',
                    'associations' => null,
                    'hidden' => null,
                ],
                'meta' => [
                    'alias' => 'Documents',
                    'relations' => [
                        'test',
                        'inverse_test',
                    ],
                ],
                'relationships' => [
                    'properties' => [
                        'links' => [
                            'self' => 'http://api.example.com/model/object_types/1/relationships/properties',
                            'related' => 'http://api.example.com/model/object_types/1/properties',
                        ],
                    ],
                    'left_relations' => [
                        'links' => [
                            'self' => 'http://api.example.com/model/object_types/1/relationships/left_relations',
                            'related' => 'http://api.example.com/model/object_types/1/left_relations',
                        ],
                    ],
                    'right_relations' => [
                        'links' => [
                            'self' => 'http://api.example.com/model/object_types/1/relationships/right_relations',
                            'related' => 'http://api.example.com/model/object_types/1/right_relations',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/model/object_types/1');
        $result = json_decode((string)$this->_response->getBody(), true);

        /*
         * @todo To remove when fquffio :) resolves the inconsistency response.
         *       According with other endpoint responses "included" and "data" of "relationships"
         *       should be present only when the query string "include" is present
         */
        $result = Hash::remove($result, 'included');
        $result = Hash::remove($result, 'data.relationships.left_relations.data');
        $result = Hash::remove($result, 'data.relationships.right_relations.data');

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
    public function testMissing()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/model/object_types/99',
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '404',
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/model/object_types/99');
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
     * @covers ::resourceUrl()
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
        $this->post('/model/object_types', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        $this->assertHeader('Location', 'http://api.example.com/model/object_types/8');
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
        $this->post('/model/object_types', json_encode(compact('data')));

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
        $this->patch('/model/object_types/1', json_encode(compact('data')));

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
        $this->patch('/model/object_types/2', json_encode(compact('data')));

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
        $this->delete('/model/object_types/1');

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $this->assertFalse(TableRegistry::get('ObjectTypes')->exists(['id' => 1]));
    }
}
