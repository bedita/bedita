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

use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

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
        'plugin.BEdita/Core.object_types'
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
                'self' => 'http://api.example.com/object_types',
                'first' => 'http://api.example.com/object_types',
                'last' => 'http://api.example.com/object_types',
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
                    'type' => 'object_types',
                    'attributes' => [
                        'name' => 'first object_type',
                        'pluralized' => 'pluralized sample 1',
                        'description' => 'this is the very first object_type',
                        'plugin' => 'plugin sample 1',
                        'model' => 'model sample 1'
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/object_types/1',
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'object_types',
                    'attributes' => [
                        'name' => 'second object_type',
                        'pluralized' => 'pluralized sample 2',
                        'description' => 'this is the second object_type',
                        'plugin' => 'plugin sample 2',
                        'model' => 'model sample 2'
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/object_types/2',
                    ]
                ],
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/object_types');
        $result = json_decode($this->_response->body(), true);

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

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/object_types');
        $result = json_decode($this->_response->body(), true);

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
                'self' => 'http://api.example.com/object_types/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'object_types',
                'attributes' => [
                    'name' => 'first object_type',
                    'pluralized' => 'pluralized sample 1',
                    'description' => 'this is the very first object_type',
                    'plugin' => 'plugin sample 1',
                    'model' => 'model sample 1'
                ],
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/object_types/1');
        $result = json_decode($this->_response->body(), true);

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
                'self' => 'http://api.example.com/object_types/99',
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
        $this->get('/object_types/99');
        $result = json_decode($this->_response->body(), true);

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
            'type' => 'object_types',
            'attributes' => [
                'name' => 'head_of_support',
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->post('/object_types', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        $this->assertHeader('Location', 'http://api.example.com/object_types/3');
        $this->assertTrue(TableRegistry::get('ObjectTypes')->exists(['name' => 'head_of_support']));
    }

    /**
     * Test add method with invalid data.
     *
     * @return void
     *
     * @covers ::add()
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

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
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
     * @covers ::edit()
     * @covers ::initialize()
     */

    public function testEdit()
    {
        $data = [
            'id' => '1',
            'type' => 'object_types',
            'attributes' => [
                'name' => 'new_name',
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->patch('/object_types/1', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals('new_name', TableRegistry::get('ObjectTypes')->get(1)->get('name'));
    }

    /**
     * Test edit method with ID conflict.
     *
     * @return void
     *
     * @covers ::edit()
     * @covers ::initialize()
     */

    public function testEditConflict()
    {
        $data = [
            'id' => '1',
            'type' => 'object_types',
            'attributes' => [
                'name' => 'new_name',
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->patch('/object_types/2', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals('first object_type', TableRegistry::get('ObjectTypes')->get(1)->get('name'));
        $this->assertEquals('second object_type', TableRegistry::get('ObjectTypes')->get(2)->get('name'));
    }

    /**
     * Test edit method with invalid data.
     *
     * @return void
     *
     * @covers ::edit()
     * @covers ::initialize()
     */

    public function testEditInvalid()
    {
        $data = [
            'id' => '1',
            'type' => 'object_types',
            'attributes' => [
                'description' => 'second object_type',
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->patch('/object_types/1', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals('first object_type', TableRegistry::get('ObjectTypes')->get(1)->get('name'));
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
            ],
        ]);
        $this->delete('/object_types/1');

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $this->assertFalse(TableRegistry::get('ObjectTypes')->exists(['id' => 1]));
    }

}
