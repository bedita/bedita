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

namespace BEdita\API\Test\TestCase\Controller\Admin;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;

/**
 * @coversDefaultClass \BEdita\API\Controller\Admin\EndpointsController
 */
class EndpointsControllerTest extends IntegrationTestCase
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
                'self' => 'http://api.example.com/admin/endpoints',
                'first' => 'http://api.example.com/admin/endpoints',
                'last' => 'http://api.example.com/admin/endpoints',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'pagination' => [
                    'count' => 3,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 3,
                    'page_size' => 20,
                ],
            ],
            'data' => [
                [
                    'id' => '1',
                    'type' => 'endpoints',
                    'attributes' => [
                        'name' => 'auth',
                        'description' => '/auth endpoint',
                        'enabled' => true,
                        'object_type_id' => null,
                    ],
                    'meta' => [
                        'created' => '2016-11-07T13:32:25+00:00',
                        'modified' => '2016-11-07T13:32:25+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/endpoints/1',
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'endpoints',
                    'attributes' => [
                        'name' => 'home',
                        'description' => '/home endpoint',
                        'enabled' => true,
                        'object_type_id' => null,
                    ],
                    'meta' => [
                        'created' => '2016-11-07T13:32:26+00:00',
                        'modified' => '2016-11-07T13:32:26+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/endpoints/2',
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'endpoints',
                    'attributes' => [
                        'name' => 'disabled',
                        'description' => '/disabled endpoint',
                        'enabled' => false,
                        'object_type_id' => null,
                    ],
                    'meta' => [
                        'created' => '2017-05-03T07:12:26+00:00',
                        'modified' => '2017-05-03T07:12:26+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/admin/endpoints/3',
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/endpoints');
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
                'self' => 'http://api.example.com/admin/endpoints',
                'first' => 'http://api.example.com/admin/endpoints',
                'last' => 'http://api.example.com/admin/endpoints',
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

        TableRegistry::get('EndpointPermissions')->deleteAll([]);
        TableRegistry::get('Endpoints')->deleteAll([]);

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/endpoints');
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
                'self' => 'http://api.example.com/admin/endpoints/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'endpoints',
                'attributes' => [
                    'name' => 'auth',
                    'description' => '/auth endpoint',
                    'enabled' => true,
                    'object_type_id' => null,
                ],
                'meta' => [
                    'created' => '2016-11-07T13:32:25+00:00',
                    'modified' => '2016-11-07T13:32:25+00:00',
                ],
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/endpoints/1');
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
     * @covers \BEdita\API\Error\ExceptionRenderer
     */
    public function testMissing()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/admin/endpoints/99',
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '404',
            ],
        ];

        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/admin/endpoints/99');
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
            'type' => 'endpoints',
            'attributes' => [
                'name' => 'magic',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/admin/endpoints', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $endpoint = TableRegistry::get('Endpoints')
            ->find()
            ->order(['id' => 'DESC'])
            ->first();

        $this->assertHeader('Location', 'http://api.example.com/admin/endpoints/' . $endpoint->id);

        $expected = array_merge(['id' => $endpoint->id], $data['attributes']);
        static::assertArraySubset($expected, $endpoint->toArray());
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
            'type' => 'endpoints',
            'attributes' => [
                'description' => 'Where the routes have no name.',
            ],
        ];

        $Endpoints = TableRegistry::get('Endpoints');
        $count = $Endpoints->find()->count();

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/admin/endpoints', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($count, $Endpoints->find()->count());
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
            'type' => 'endpoints',
            'attributes' => [
                'name' => 'magic',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/admin/endpoints/1', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $Endpoints = TableRegistry::get('Endpoints');
        $entity = $Endpoints->get(1);
        static::assertEquals('magic', $entity->get('name'));
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
            'type' => 'endpoints',
            'attributes' => [
                'name' => 'magic',
            ],
        ];

        $Endpoints = TableRegistry::get('Endpoints');
        $expected = $Endpoints->get(1)->get('name');

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/admin/endpoints/2', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $Endpoints->get(1)->get('name'));
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
        $this->delete('/admin/endpoints/2');

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        static::assertFalse(TableRegistry::get('Endpoints')->exists(['id' => 2]));
    }
}
