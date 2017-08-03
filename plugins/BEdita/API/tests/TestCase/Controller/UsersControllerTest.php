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
 * @coversDefaultClass \BEdita\API\Controller\UsersController
 */
class UsersControllerTest extends IntegrationTestCase
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
                        'status' => 'on',
                        'uname' => 'first-user',
                        'title' => 'Mr. First User',
                        'description' => null,
                        'body' => null,
                        'extra' => null,
                        'lang' => 'eng',
                        'name' => 'First',
                        'surname' => 'User',
                        'email' => 'first.user@example.com',
                        'person_title' => 'Mr.',
                        'gender' => null,
                        'birthdate' => null,
                        'deathdate' => null,
                        'company' => false,
                        'company_name' => null,
                        'company_kind' => null,
                        'street_address' => null,
                        'city' => null,
                        'zipcode' => null,
                        'country' => null,
                        'state_name' => null,
                        'phone' => null,
                        'website' => null,
                        'national_id_number' => null,
                        'vat_number' => null,
                        'publish_start' => null,
                        'publish_end' => null,
                        'username' => 'first user',
                    ],
                    'meta' => [
                        'locked' => true,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                        'blocked' => false,
                        'last_login' => null,
                        'last_login_err' => null,
                        'num_login_err' => 1,
                        'verified' => '2017-05-29T11:36:00+00:00',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/users/1',
                    ],
                    'relationships' => [
                        'roles' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/1/roles',
                                'self' => 'http://api.example.com/users/1/relationships/roles',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '5',
                    'type' => 'users',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'second-user',
                        'title' => 'Miss Second User',
                        'description' => null,
                        'body' => null,
                        'extra' => null,
                        'lang' => 'eng',
                        'name' => 'Second',
                        'surname' => 'User',
                        'email' => 'second.user@example.com',
                        'person_title' => 'Miss',
                        'gender' => null,
                        'birthdate' => null,
                        'deathdate' => null,
                        'company' => false,
                        'company_name' => null,
                        'company_kind' => null,
                        'street_address' => null,
                        'city' => null,
                        'zipcode' => null,
                        'country' => null,
                        'state_name' => null,
                        'phone' => null,
                        'website' => null,
                        'national_id_number' => null,
                        'vat_number' => null,
                        'publish_start' => null,
                        'publish_end' => null,
                        'username' => 'second user',
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                        'blocked' => false,
                        'last_login' => '2016-03-15T09:57:38+00:00',
                        'last_login_err' => '2016-03-15T09:57:38+00:00',
                        'num_login_err' => 0,
                        'verified' => null,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/users/5',
                    ],
                    'relationships' => [
                        'roles' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/5/roles',
                                'self' => 'http://api.example.com/users/5/relationships/roles',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/users');
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

        $this->configRequestHeaders();
        $this->get('/users');
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
                'self' => 'http://api.example.com/users/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'users',
                'attributes' => [
                    'status' => 'on',
                    'uname' => 'first-user',
                    'title' => 'Mr. First User',
                    'description' => null,
                    'body' => null,
                    'extra' => null,
                    'lang' => 'eng',
                    'name' => 'First',
                    'surname' => 'User',
                    'email' => 'first.user@example.com',
                    'person_title' => 'Mr.',
                    'gender' => null,
                    'birthdate' => null,
                    'deathdate' => null,
                    'company' => false,
                    'company_name' => null,
                    'company_kind' => null,
                    'street_address' => null,
                    'city' => null,
                    'zipcode' => null,
                    'country' => null,
                    'state_name' => null,
                    'phone' => null,
                    'website' => null,
                    'national_id_number' => null,
                    'vat_number' => null,
                    'publish_start' => null,
                    'publish_end' => null,
                    'username' => 'first user',
                ],
                'meta' => [
                    'locked' => true,
                    'created' => '2016-05-13T07:09:23+00:00',
                    'modified' => '2016-05-13T07:09:23+00:00',
                    'published' => null,
                    'created_by' => 1,
                    'modified_by' => 1,
                    'blocked' => false,
                    'last_login' => null,
                    'last_login_err' => null,
                    'num_login_err' => 1,
                    'verified' => '2017-05-29T11:36:00+00:00',
                ],
                'relationships' => [
                    'roles' => [
                        'links' => [
                            'related' => 'http://api.example.com/users/1/roles',
                            'self' => 'http://api.example.com/users/1/relationships/roles',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/users/1');
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
                'self' => 'http://api.example.com/users/99',
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '404',
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/users/99');
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
            'type' => 'users',
            'attributes' => [
                'username' => 'gustavo_supporto',
                'password_hash' => 'aiuto',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/users', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        $this->assertHeader('Location', 'http://api.example.com/users/11');
        $this->assertTrue(TableRegistry::get('Users')->exists(['username' => 'gustavo_supporto']));
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
            'type' => 'users',
            'attributes' => [
                'password_hash' => 'aiuto',
            ],
        ];

        $count = TableRegistry::get('Users')->find()->count();

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
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
     * @covers ::resource()
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

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/users/1', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals('new_username', TableRegistry::get('Users')->get(1)->get('username'));
        static::assertEquals('users', TableRegistry::get('Users')->get(1)->get('type'));

        $result = json_decode((string)$this->_response->getBody(), true);
        static::assertEquals($data['id'], $result['data']['id']);
        static::assertEquals($data['type'], $result['data']['type']);
        static::assertEquals($data['attributes']['username'], $result['data']['attributes']['username']);
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
            'type' => 'users',
            'attributes' => [
                'username' => 'new_username',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/users/5', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals('first user', TableRegistry::get('Users')->get(1)->get('username'));
        $this->assertEquals('second user', TableRegistry::get('Users')->get(5)->get('username'));
    }

    /**
     * Test edit method with invalid data.
     *
     * @return void
     *
     * @covers ::resource()
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

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
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
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testDelete()
    {
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete('/users/5');

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');

        $this->configRequestHeaders();
        $this->get('/users/5');
        $this->assertResponseCode(404);

        $userDeleted = TableRegistry::get('Users')->get(5);
        $this->assertEquals($userDeleted->deleted, 1);
    }

    /**
     * Test related method to list related objects.
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
                'self' => 'http://api.example.com/users/1/roles',
                'first' => 'http://api.example.com/users/1/roles',
                'last' => 'http://api.example.com/users/1/roles',
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
                    ],
                    'meta' => [
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

        $this->configRequestHeaders();
        $this->get('/users/1/roles');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }
}
