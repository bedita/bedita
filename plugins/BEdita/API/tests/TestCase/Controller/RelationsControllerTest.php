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
 * @coversDefaultClass \BEdita\API\Controller\RelationsController
 */
class RelationsControllerTest extends IntegrationTestCase
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
                'self' => 'http://api.example.com/model/relations',
                'first' => 'http://api.example.com/model/relations',
                'last' => 'http://api.example.com/model/relations',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'pagination' => [
                    'count' => 2,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 2,
                    'page_size' => 20,
                ],
            ],
            'data' => [
                [
                    'id' => '1',
                    'type' => 'relations',
                    'attributes' => [
                        'name' => 'test',
                        'label' => 'Test relation',
                        'inverse_name' => 'inverse_test',
                        'inverse_label' => 'Inverse test relation',
                        'description' => 'Sample description.',
                        'params' => null,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/relations/1',
                    ],
                    'relationships' => [
                        'left_object_types' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/relations/1/relationships/left_object_types',
                                'related' => 'http://api.example.com/model/relations/1/left_object_types',
                            ],
                        ],
                        'right_object_types' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/relations/1/relationships/right_object_types',
                                'related' => 'http://api.example.com/model/relations/1/right_object_types',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'relations',
                    'attributes' => [
                        'name' => 'another_test',
                        'label' => 'Another test relation',
                        'inverse_name' => 'inverse_another_test',
                        'inverse_label' => 'Another inverse test relation',
                        'description' => 'Sample description /2.',
                        'params' => [
                            'type' => 'object',
                            'properties' => [
                                'name' => [
                                    'type' => 'string',
                                ],
                                'age' => [
                                    'type' => 'integer',
                                    'minimum' => 0,
                                ],
                            ],
                            'required' => ['name'],
                        ],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/relations/2',
                    ],
                    'relationships' => [
                        'left_object_types' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/relations/2/relationships/left_object_types',
                                'related' => 'http://api.example.com/model/relations/2/left_object_types',
                            ],
                        ],
                        'right_object_types' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/relations/2/relationships/right_object_types',
                                'related' => 'http://api.example.com/model/relations/2/right_object_types',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/model/relations');
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
                'self' => 'http://api.example.com/model/relations',
                'first' => 'http://api.example.com/model/relations',
                'last' => 'http://api.example.com/model/relations',
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

        TableRegistry::get('Relations')->deleteAll([]);

        $this->configRequestHeaders();
        $this->get('/model/relations');
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
                'self' => 'http://api.example.com/model/relations/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'relations',
                'attributes' => [
                    'name' => 'test',
                    'label' => 'Test relation',
                    'inverse_name' => 'inverse_test',
                    'inverse_label' => 'Inverse test relation',
                    'description' => 'Sample description.',
                    'params' => null,
                ],
                'relationships' => [
                    'left_object_types' => [
                        'links' => [
                            'self' => 'http://api.example.com/model/relations/1/relationships/left_object_types',
                            'related' => 'http://api.example.com/model/relations/1/left_object_types',
                        ],
                    ],
                    'right_object_types' => [
                        'links' => [
                            'self' => 'http://api.example.com/model/relations/1/relationships/right_object_types',
                            'related' => 'http://api.example.com/model/relations/1/right_object_types',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/model/relations/1');
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
                'self' => 'http://api.example.com/model/relations/99',
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '404',
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/model/relations/99');
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
            'type' => 'relations',
            'attributes' => [
                'name' => 'shared',
                'label' => 'Shared',
                'inverse_name' => 'shared_by',
                'inverse_label' => 'Shared by',
                'description' => 'Shared relation',
                'params' => [
                    'test' => 'ok'
                ]
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/model/relations', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $relation = TableRegistry::get('Relations')
            ->find()
            ->order(['id' => 'DESC'])
            ->first();

        $this->assertHeader('Location', 'http://api.example.com/model/relations/' . $relation->id);

        $expected = array_merge(['id' => $relation->id], $data['attributes']);
        static::assertEquals($expected, $relation->toArray());
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
            'type' => 'relations',
            'attributes' => [
                'description' => 'Anonymous relation.',
            ],
        ];

        $Relations = TableRegistry::get('Relations');
        $count = $Relations->find()->count();

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/model/relations', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($count, $Relations->find()->count());
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
            'type' => 'relations',
            'attributes' => [
                'label' => 'new label',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/model/relations/1', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $Relations = TableRegistry::get('Relations');
        $entity = $Relations->get(1);
        $this->assertEquals('new label', $entity->get('label'));
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
            'type' => 'relations',
            'attributes' => [
                'label' => 'new label',
            ],
        ];

        $Relations = TableRegistry::get('Relations');
        $expected = $Relations->get(1)->get('label');

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/model/relations/2', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $Relations->get(1)->get('label'));
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
        $this->delete('/model/relations/1');

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $this->assertFalse(TableRegistry::get('Relations')->exists(['id' => 1]));
    }

    /**
     * Test related method to list related object types.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::related()
     * @covers ::findAssociation()
     */
    public function testRelated()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/model/relations/1/left_object_types',
                'first' => 'http://api.example.com/model/relations/1/left_object_types',
                'last' => 'http://api.example.com/model/relations/1/left_object_types',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
                'available' => 'http://api.example.com/model/object_types',
            ],
            'meta' => [
                'pagination' => [
                    'count' => 1,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 1,
                    'page_size' => 20,
                ],
            ],
            'data' => [
                [
                    'id' => '2',
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
                        'is_abstract' => false,
                    ],
                    'meta' => [
                        'alias' => 'Documents',
                        'relations' => [
                            'test',
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
                            ],
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/2/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/2/right_relations',
                            ],
                        ],
                        'parent' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/2/relationships/parent',
                                'related' => 'http://api.example.com/model/object_types/2/parent',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/model/relations/1/left_object_types');
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
}
