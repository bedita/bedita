<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Test\TestCase\Controller;

use BEdita\API\Test\TestConstants;
use BEdita\API\TestSuite\IntegrationTestCase;

/**
 * @coversDefaultClass \BEdita\API\Controller\HistoryController
 */
class HistoryControllerTest extends IntegrationTestCase
{
    /**
     * Test index method.
     *
     * @return void
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testIndex()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/history?filter%5Bresource_id%5D=2&filter%5Bresource_type%5D=objects',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/history?filter%5Bresource_id%5D=2&filter%5Bresource_type%5D=objects',
                'last' => 'http://api.example.com/history?filter%5Bresource_id%5D=2&filter%5Bresource_type%5D=objects',
                'prev' => null,
                'next' => null,
            ],
            'data' => [
                [
                    'id' => '1',
                    'type' => 'history',
                    'meta' => [
                        'resource_id' => 2,
                        'resource_type' => 'objects',
                        'created' => '2016-05-13T07:09:22+00:00',
                        'user_id' => 1,
                        'application_id' => 1,
                        'user_action' => 'create',
                        'changed' => '{"title":"title one","description":"description here"}',
                    ],
                    'relationships' => [
                        'user' => [
                            'links' => [
                                'related' => 'http://api.example.com/history/1/user',
                                'self' => 'http://api.example.com/history/1/relationships/user',
                            ],
                        ],
                        'application' => [
                            'links' => [
                                'related' => 'http://api.example.com/history/1/application',
                                'self' => 'http://api.example.com/history/1/relationships/application',
                            ],
                        ],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/history/1',
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'history',
                    'meta' => [
                        'resource_id' => 2,
                        'resource_type' => 'objects',
                        'created' => '2016-05-13T07:09:23+00:00',
                        'user_id' => 5,
                        'application_id' => 1,
                        'user_action' => 'update',
                        'changed' => '{"body":"body here","extra":{"abstract":"abstract here","list": ["one", "two", "three"]}}',
                    ],
                    'relationships' => [
                        'user' => [
                            'links' => [
                                'related' => 'http://api.example.com/history/2/user',
                                'self' => 'http://api.example.com/history/2/relationships/user',
                            ],
                        ],
                        'application' => [
                            'links' => [
                                'related' => 'http://api.example.com/history/2/application',
                                'self' => 'http://api.example.com/history/2/relationships/application',
                            ],
                        ],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/history/2',
                    ],
                ],
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
        ];

        $this->configRequestHeaders();
        $this->get('/history?filter[resource_id]=2&filter[resource_type]=objects');
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test empty view method.
     *
     * @return void
     * @covers ::index()
     */
    public function testEmpty()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/history?filter%5Bresource_id%5D=999&filter%5Bresource_type%5D=objects',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/history?filter%5Bresource_id%5D=999&filter%5Bresource_type%5D=objects',
                'last' => 'http://api.example.com/history?filter%5Bresource_id%5D=999&filter%5Bresource_type%5D=objects',
                'prev' => null,
                'next' => null,
            ],
            'data' => [],
            'meta' => [
                'pagination' => [
                    'count' => 0,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 0,
                    'page_size' => 20,
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/history?filter[resource_id]=999&filter[resource_type]=objects');
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test `user_id` filter method.
     *
     * @return void
     * @coversNothing
     */
    public function testUser()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/history?filter%5Buser_id%5D=5',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/history?filter%5Buser_id%5D=5',
                'last' => 'http://api.example.com/history?filter%5Buser_id%5D=5',
                'prev' => null,
                'next' => null,
            ],
            'data' => [
                [
                    'id' => '2',
                    'type' => 'history',
                    'meta' => [
                        'resource_id' => 2,
                        'resource_type' => 'objects',
                        'created' => '2016-05-13T07:09:23+00:00',
                        'user_id' => 5,
                        'application_id' => 1,
                        'user_action' => 'update',
                        'changed' => '{"body":"body here","extra":{"abstract":"abstract here","list": ["one", "two", "three"]}}',
                    ],
                    'relationships' => [
                        'user' => [
                            'links' => [
                                'related' => 'http://api.example.com/history/2/user',
                                'self' => 'http://api.example.com/history/2/relationships/user',
                            ],
                        ],
                        'application' => [
                            'links' => [
                                'related' => 'http://api.example.com/history/2/application',
                                'self' => 'http://api.example.com/history/2/relationships/application',
                            ],
                        ],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/history/2',
                    ],
                ],
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
        ];

        $this->configRequestHeaders();
        $this->get('/history?filter[user_id]=5');
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test `related` method.
     *
     * @return void
     * @covers ::initialize()
     * @covers ::related()
     * @covers ::findAssociation()
     * @covers ::getAvailableUrl()
     */
    public function testRelated()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/history/1/user',
                'home' => 'http://api.example.com/home',
                'available' => null,
            ],
            'meta' => [
                'schema' => [
                    'users' => [
                        '$id' => 'http://api.example.com/model/schema/users',
                        'revision' => TestConstants::SCHEMA_REVISIONS['users'],
                    ],
                ],
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
                    'lang' => 'en',
                    'name' => 'First',
                    'surname' => 'User',
                    'email' => 'first.user@example.com',
                    'person_title' => 'Mr.',
                    'gender' => null,
                    'birthdate' => '1945-04-25',
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
                    'pseudonym' => null,
                    'user_preferences' => null,
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
                    'password_modified' => '2017-05-29T11:36:00+00:00',
                    'external_auth' => [
                        [
                            'provider' => 'example',
                            'username' => 'first_user',
                        ],
                    ],
                ],
                'relationships' => [
                    'roles' => [
                        'links' => [
                            'related' => 'http://api.example.com/users/1/roles',
                            'self' => 'http://api.example.com/users/1/relationships/roles',
                        ],
                    ],
                    'another_test' => [
                        'links' => [
                            'related' => 'http://api.example.com/users/1/another_test',
                            'self' => 'http://api.example.com/users/1/relationships/another_test',
                        ],
                    ],
                    'parents' => [
                        'links' => [
                            'related' => 'http://api.example.com/users/1/parents',
                            'self' => 'http://api.example.com/users/1/relationships/parents',
                        ],
                    ],
                    'translations' => [
                        'links' => [
                            'related' => 'http://api.example.com/users/1/translations',
                            'self' => 'http://api.example.com/users/1/relationships/translations',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/history/1/user');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test `setRelationshipsAllowedMethods` method.
     *
     * @return void
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testSetRelationshipsAllowedMethods()
    {
        $authHeader = $this->getUserAuthHeader();
        $this->configRequestHeaders('POST', $authHeader);
        $this->post('/history/1/relationships/user', [
            'id' => 5,
        ]);
        $this->assertResponseCode(405);
    }

    /**
     * Test `include` method.
     *
     * @return void
     * @covers ::findAssociation()
     */
    public function testInclude()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/history?include=user',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/history?include=user',
                'last' => 'http://api.example.com/history?include=user',
                'prev' => null,
                'next' => null,
            ],
            'data' => [
                [
                    'id' => '1',
                    'type' => 'history',
                    'meta' => [
                        'resource_id' => 2,
                        'resource_type' => 'objects',
                        'created' => '2016-05-13T07:09:22+00:00',
                        'user_id' => 1,
                        'application_id' => 1,
                        'user_action' => 'create',
                        'changed' => '{"title":"title one","description":"description here"}',
                    ],
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'id' => '1',
                                'type' => 'users',
                            ],
                            'links' => [
                                'related' => 'http://api.example.com/history/1/user',
                                'self' => 'http://api.example.com/history/1/relationships/user',
                            ],
                        ],
                        'application' => [
                            'links' => [
                                'related' => 'http://api.example.com/history/1/application',
                                'self' => 'http://api.example.com/history/1/relationships/application',
                            ],
                        ],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/history/1',
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'history',
                    'meta' => [
                        'resource_id' => 2,
                        'resource_type' => 'objects',
                        'created' => '2016-05-13T07:09:23+00:00',
                        'user_id' => 5,
                        'application_id' => 1,
                        'user_action' => 'update',
                        'changed' => '{"body":"body here","extra":{"abstract":"abstract here","list": ["one", "two", "three"]}}',
                    ],
                    'relationships' => [
                        'user' => [
                            'data' => [
                                'id' => '5',
                                'type' => 'users',
                            ],
                            'links' => [
                                'related' => 'http://api.example.com/history/2/user',
                                'self' => 'http://api.example.com/history/2/relationships/user',
                            ],
                        ],
                        'application' => [
                            'links' => [
                                'related' => 'http://api.example.com/history/2/application',
                                'self' => 'http://api.example.com/history/2/relationships/application',
                            ],
                        ],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/history/2',
                    ],
                ],
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
                    'users' => [
                        '$id' => 'http://api.example.com/model/schema/users',
                        'revision' => '1833541891',
                    ],
                ],
            ],
            'included' => [
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
                        'lang' => 'en',
                        'name' => 'First',
                        'surname' => 'User',
                        'email' => 'first.user@example.com',
                        'person_title' => 'Mr.',
                        'gender' => null,
                        'birthdate' => '1945-04-25',
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
                        'pseudonym' => null,
                        'user_preferences' => null,
                    ],
                    'relationships' => [
                        'roles' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/1/roles',
                                'self' => 'http://api.example.com/users/1/relationships/roles',
                            ],
                        ],
                        'another_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/1/another_test',
                                'self' => 'http://api.example.com/users/1/relationships/another_test',
                            ],
                        ],
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/1/parents',
                                'self' => 'http://api.example.com/users/1/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/1/translations',
                                'self' => 'http://api.example.com/users/1/relationships/translations',
                            ],
                        ],
                    ],
                    'meta' => [
                        'blocked' => false,
                        'last_login' => null,
                        'last_login_err' => null,
                        'num_login_err' => 1,
                        'verified' => '2017-05-29T11:36:00+00:00',
                        'password_modified' => '2017-05-29T11:36:00+00:00',
                        'external_auth' => [
                            [
                                'provider' => 'example',
                                'username' => 'first_user',
                            ],
                        ],
                        'locked' => true,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/users/1',
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
                        'lang' => 'en',
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
                        'another_username' => 'synapse', // custom property
                        'another_email' => 'synapse@example.org', // custom property
                        'pseudonym' => null,
                        'user_preferences' => null,
                    ],
                    'relationships' => [
                        'roles' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/5/roles',
                                'self' => 'http://api.example.com/users/5/relationships/roles',
                            ],
                        ],
                        'another_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/5/another_test',
                                'self' => 'http://api.example.com/users/5/relationships/another_test',
                            ],
                        ],
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/5/parents',
                                'self' => 'http://api.example.com/users/5/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/5/translations',
                                'self' => 'http://api.example.com/users/5/relationships/translations',
                            ],
                        ],
                    ],
                    'meta' => [
                        'blocked' => false,
                        'last_login' => '2016-03-15T09:57:38+00:00',
                        'last_login_err' => '2016-03-15T09:57:38+00:00',
                        'num_login_err' => 0,
                        'verified' => null,
                        'password_modified' => '2016-03-15T09:57:38+00:00',
                        'external_auth' => [
                            [
                                'provider' => 'uuid',
                                'username' => '17fec0fa-068a-4d7c-8283-da91d47cef7d',
                            ],
                        ],
                        'locked' => false,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => null,
                        'created_by' => 5,
                        'modified_by' => 5,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/users/5',
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/history?include=user');
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }
}
