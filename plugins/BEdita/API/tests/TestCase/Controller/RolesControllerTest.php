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

use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * @coversDefaultClass \BEdita\API\Controller\RolesController
 */
class RolesControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.roles_users',
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
                'self' => 'http://api.example.com/roles',
                'first' => 'http://api.example.com/roles',
                'last' => 'http://api.example.com/roles',
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
                    'type' => 'roles',
                    'attributes' => [
                        'name' => 'first role',
                        'description' => 'this is the very first role',
                        'unchangeable' => true,
                        'created' => '2016-04-15T09:57:38+00:00',
                        'modified' => '2016-04-15T09:57:38+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/roles/1',
                    ],
                    'relationships' => [
                        'users' => [
                            'links' => [
                                'self' => 'http://api.example.com/roles/1/relationships/users',
                                'related' => 'http://api.example.com/roles/1/users',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'roles',
                    'attributes' => [
                        'name' => 'second role',
                        'description' => 'this is a second role',
                        'unchangeable' => false,
                        'created' => '2016-04-15T11:59:12+00:00',
                        'modified' => '2016-04-15T11:59:13+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/roles/2',
                    ],
                    'relationships' => [
                        'users' => [
                            'links' => [
                                'self' => 'http://api.example.com/roles/2/relationships/users',
                                'related' => 'http://api.example.com/roles/2/users',
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
        $this->get('/roles');
        $result = json_decode($this->_response->body(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test user roles method.
     *
     * @return void
     *
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testUserRoles()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/roles?user_id=1',
                'first' => 'http://api.example.com/roles?user_id=1',
                'last' => 'http://api.example.com/roles?user_id=1',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
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
                    'id' => '1',
                    'type' => 'roles',
                    'attributes' => [
                        'name' => 'first role',
                        'description' => 'this is the very first role',
                        'unchangeable' => true,
                        'created' => '2016-04-15T09:57:38+00:00',
                        'modified' => '2016-04-15T09:57:38+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/roles/1',
                    ],
                    'relationships' => [
                        'users' => [
                            'links' => [
                                'self' => 'http://api.example.com/roles/1/relationships/users',
                                'related' => 'http://api.example.com/roles/1/users',
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
        $this->get('/users/1/roles');
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
                'self' => 'http://api.example.com/roles',
                'first' => 'http://api.example.com/roles',
                'last' => 'http://api.example.com/roles',
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

        TableRegistry::get('Roles')->deleteAll([]);

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/roles');
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
                'self' => 'http://api.example.com/roles/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'roles',
                'attributes' => [
                    'name' => 'first role',
                    'description' => 'this is the very first role',
                    'unchangeable' => true,
                    'created' => '2016-04-15T09:57:38+00:00',
                    'modified' => '2016-04-15T09:57:38+00:00',
                ],
                'relationships' => [
                    'users' => [
                        'links' => [
                            'self' => 'http://api.example.com/roles/1/relationships/users',
                            'related' => 'http://api.example.com/roles/1/users',
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
        $this->get('/roles/1');
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
                'self' => 'http://api.example.com/roles/99',
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
        $this->get('/roles/99');
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
            'type' => 'roles',
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
        $this->post('/roles', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        $this->assertHeader('Location', 'http://api.example.com/roles/3');
        $this->assertTrue(TableRegistry::get('Roles')->exists(['name' => 'head_of_support']));
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
            'type' => 'roles',
            'attributes' => [
                'description' => 'Anonymous role.',
            ],
        ];

        $count = TableRegistry::get('Roles')->find()->count();

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->post('/roles', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($count, TableRegistry::get('Roles')->find()->count());
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
            'type' => 'roles',
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
        $this->patch('/roles/1', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals('new_name', TableRegistry::get('Roles')->get(1)->get('name'));
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
            'type' => 'roles',
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
        $this->patch('/roles/2', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals('first role', TableRegistry::get('Roles')->get(1)->get('name'));
        $this->assertEquals('second role', TableRegistry::get('Roles')->get(2)->get('name'));
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
            'type' => 'roles',
            'attributes' => [
                'name' => 'second role',
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->patch('/roles/1', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals('first role', TableRegistry::get('Roles')->get(1)->get('name'));
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
        $this->delete('/roles/1');

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $this->assertFalse(TableRegistry::get('Roles')->exists(['id' => 1]));
    }
}
