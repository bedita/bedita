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
use BEdita\API\Test\TestConstants;
use Cake\ORM\TableRegistry;

/**
 * @coversDefaultClass \BEdita\API\Controller\RolesController
 */
class RolesControllerTest extends IntegrationTestCase
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
                'schema' => [
                    'roles' => [
                        '$id' => 'http://api.example.com/model/schema/roles',
                        'revision' => TestConstants::SCHEMA_REVISIONS['roles'],
                    ],
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
                [
                    'id' => '2',
                    'type' => 'roles',
                    'attributes' => [
                        'name' => 'second role',
                        'description' => 'this is a second role',
                    ],
                    'meta' => [
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

        $this->configRequestHeaders();
        $this->get('/roles');
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
                    'page_count' => 1,
                    'page_items' => 0,
                    'page_size' => 20,
                ],
            ],
            'data' => [],
        ];

        TableRegistry::get('EndpointPermissions')->updateAll(['role_id' => null], ['role_id IS NOT' => null]);
        TableRegistry::get('Roles')->deleteAll([]);

        $this->configRequestHeaders();
        $this->get('/roles');
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
                'self' => 'http://api.example.com/roles/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
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
                'relationships' => [
                    'users' => [
                        'links' => [
                            'self' => 'http://api.example.com/roles/1/relationships/users',
                            'related' => 'http://api.example.com/roles/1/users',
                        ],
                    ],
                ],
            ],
            'meta' => [
                'schema' => [
                    'roles' => [
                        '$id' => 'http://api.example.com/model/schema/roles',
                        'revision' => TestConstants::SCHEMA_REVISIONS['roles'],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/roles/1');
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

        $this->configRequestHeaders();
        $this->get('/roles/99');
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
            'type' => 'roles',
            'attributes' => [
                'name' => 'head_of_support',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/roles', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayHasKey('data', $result);
        $this->assertHeader('Location', 'http://api.example.com/roles/3');
        static::assertTrue(TableRegistry::get('Roles')->exists(['name' => 'head_of_support']));
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
            'type' => 'roles',
            'attributes' => [
                'description' => 'Anonymous role.',
            ],
        ];

        $count = TableRegistry::get('Roles')->find()->count();

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
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
     * @covers ::resource()
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

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
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
     * @covers ::resource()
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

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
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
     * @covers ::resource()
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

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
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
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testDelete()
    {
        // delete role 1 - it must be forbidden
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete('/roles/1');
        $this->assertResponseCode(403);
        $this->assertContentType('application/vnd.api+json');
        $this->assertTrue(TableRegistry::get('Roles')->exists(['id' => 1]));

        // delete role 2
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete('/roles/2');
        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $this->assertFalse(TableRegistry::get('Roles')->exists(['id' => 2]));
    }

    /**
     * Test related method to list related objects.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::related()
     * @covers ::findAssociation()
     * @covers ::getAvailableUrl()
     */
    public function testRelated()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/roles/1/users',
                'first' => 'http://api.example.com/roles/1/users',
                'last' => 'http://api.example.com/roles/1/users',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
                'available' => 'http://api.example.com/users',
            ],
            'meta' => [
                'pagination' => [
                    'count' => 1,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 1,
                    'page_size' => 20,
                ],
                'schema' => [
                    'users' => [
                        '$id' => 'http://api.example.com/model/schema/users',
                        'revision' => TestConstants::SCHEMA_REVISIONS['users'],
                    ],
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
                        'another_username' => null, // custom property
                        'another_email' => null, // custom property
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
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/1/parents',
                                'self' => 'http://api.example.com/users/1/relationships/parents',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/roles/1/users');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }
}
