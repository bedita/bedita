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
 * @coversDefaultClass \BEdita\API\Controller\UsersController
 */
class UsersControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
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
                'self' => 'http://api.example.com/users',
                'first' => 'http://api.example.com/users',
                'last' => 'http://api.example.com/users',
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
                    'type' => 'users',
                    'attributes' => [
                        'username' => 'first user',
                        'blocked' => false,
                        'last_login' => null,
                        'last_login_err' => null,
                        'num_login_err' => 1,
                        'created' => '2016-03-15T09:57:38+0000',
                        'modified' => '2016-03-15T09:57:38+0000',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/users/1',
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'users',
                    'attributes' => [
                        'username' => 'second user',
                        'blocked' => false,
                        'last_login' => '2016-03-15T09:57:38+0000',
                        'last_login_err' => '2016-03-15T09:57:38+0000',
                        'num_login_err' => 0,
                        'created' => '2016-03-15T09:57:38+0000',
                        'modified' => '2016-03-15T09:57:38+0000',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/users/2',
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
        $this->get('/users');
        $result = json_decode($this->_response->body(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test index method filtering by role.
     *
     * @return void
     *
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testIndexRoles()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/roles/1/users',
                'first' => 'http://api.example.com/roles/1/users',
                'last' => 'http://api.example.com/roles/1/users',
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
                    'type' => 'users',
                    'attributes' => [
                        'username' => 'first user',
                        'blocked' => false,
                        'last_login' => null,
                        'last_login_err' => null,
                        'num_login_err' => 1,
                        'created' => '2016-03-15T09:57:38+0000',
                        'modified' => '2016-03-15T09:57:38+0000',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/users/1',
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
        $this->get('/roles/1/users');
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
                'self' => 'http://api.example.com/users',
                'first' => 'http://api.example.com/users',
                'last' => 'http://api.example.com/users',
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

        TableRegistry::get('Users')->deleteAll([]);

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/users');
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
                'self' => 'http://api.example.com/users/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'users',
                'attributes' => [
                    'username' => 'first user',
                    'blocked' => false,
                    'last_login' => null,
                    'last_login_err' => null,
                    'num_login_err' => 1,
                    'created' => '2016-03-15T09:57:38+0000',
                    'modified' => '2016-03-15T09:57:38+0000',
                ],
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/users/1');
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
                'self' => 'http://api.example.com/users/99',
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
        $this->get('/users/99');
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
            'type' => 'users',
            'attributes' => [
                'username' => 'gustavo_supporto',
                'password_hash' => 'aiuto',
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->post('/users', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        $this->assertHeader('Location', 'http://api.example.com/users/3');
        $this->assertTrue(TableRegistry::get('Users')->exists(['username' => 'gustavo_supporto']));
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
            'type' => 'users',
            'attributes' => [
                'password_hash' => 'aiuto',
            ],
        ];

        $count = TableRegistry::get('Users')->find()->count();

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->post('/users', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($count, TableRegistry::get('Users')->find()->count());
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
            'type' => 'users',
            'attributes' => [
                'username' => 'new_username',
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->patch('/users/1', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals('new_username', TableRegistry::get('Users')->get(1)->get('username'));
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
            'type' => 'users',
            'attributes' => [
                'username' => 'new_username',
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->patch('/users/2', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals('first user', TableRegistry::get('Users')->get(1)->get('username'));
        $this->assertEquals('second user', TableRegistry::get('Users')->get(2)->get('username'));
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
            'type' => 'users',
            'attributes' => [
                'username' => 'second user',
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->patch('/users/1', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals('first user', TableRegistry::get('Users')->get(1)->get('username'));
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
        $this->delete('/users/1');

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $this->assertFalse(TableRegistry::get('Users')->exists(['id' => 1]));
    }
}
