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

/**
 * @coversDefaultClass \BEdita\API\Controller\ResourcesController
 */
class ResourcesControllerTest extends IntegrationTestCase
{
    /**
     * Test relationships method to list existing relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::getAvailableUrl()
     * @covers ::setRelationshipsAllowedMethods()
     * @covers ::getAssociatedAction()
     */
    public function testListAssociations()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/roles/1/relationships/users',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/roles/1/relationships/users',
                'last' => 'http://api.example.com/roles/1/relationships/users',
                'prev' => null,
                'next' => null,
                'available' => 'http://api.example.com/users',
            ],
            'data' => [
                [
                    'id' => '1',
                    'type' => 'users',
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
        $this->get('/roles/1/relationships/users');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test relationships method to list existing relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::setRelationshipsAllowedMethods()
     * @covers ::getAssociatedAction()
     */
    public function testListAssociationsNotFound()
    {
        $this->configRequestHeaders();
        $this->get('/roles/99/relationships/users');

        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test relationships method to add new relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testAddAssociations()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/roles/1/relationships/users',
                'home' => 'http://api.example.com/home',
            ],
        ];

        $data = [
            [
                'id' => '5',
                'type' => 'users',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/roles/1/relationships/users', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test relationships method to add new relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testAddAssociationsDuplicateEntry()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/roles/1/relationships/users',
                'home' => 'http://api.example.com/home',
            ],
        ];

        $data = [
            [
                'id' => '5',
                'type' => 'users',
            ],
            [
                'id' => '5',
                'type' => 'users',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/roles/1/relationships/users', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test relationships method to add new relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testAddAssociationsNoContent()
    {
        $data = [
            [
                'id' => '1',
                'type' => 'users',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/roles/1/relationships/users', json_encode(compact('data')));

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseEmpty();
    }

    /**
     * Test relationships method to delete existing relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testDeleteAssociations()
    {
        // remove association from role 1 and user 1: must be forbidden
        $data = [
            [
                'id' => '1',
                'type' => 'users',
            ],
            [
                'id' => '5',
                'type' => 'users',
            ],
        ];
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/roles/1/relationships/users',
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '403',
                'title' => 'Could not update relationship for users/roles for ADMIN_USER and ADMIN_ROLE',
            ]
        ];
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        // Cannot use `IntegrationTestCase::delete()`, as it does not allow sending payload with the request.
        $this->_sendRequest('/roles/1/relationships/users', 'DELETE', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(403);
        $this->assertContentType('application/vnd.api+json');
        $this->assertArraySubset($expected['links'], $result['links']);
        $this->assertEquals($expected['error']['title'], $result['error']['title']);

        // add association for user 1 role 2
        $data = [
            [
                'id' => '1',
                'type' => 'users',
            ],
        ];
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/roles/2/relationships/users', json_encode(compact('data')));
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        // remove association for user 1 role 2
        $data = [
            [
                'id' => '1',
                'type' => 'users',
            ],
        ];
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/roles/2/relationships/users',
                'home' => 'http://api.example.com/home',
            ],
        ];
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        // Cannot use `IntegrationTestCase::delete()`, as it does not allow sending payload with the request.
        $this->_sendRequest('/roles/2/relationships/users', 'DELETE', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test relationships method to delete existing relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testDeleteAssociationsNoContent()
    {
        $data = [
            [
                'id' => '5',
                'type' => 'users',
            ],
        ];

        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        // Cannot use `IntegrationTestCase::delete()`, as it does not allow sending payload with the request.
        $this->_sendRequest('/roles/1/relationships/users', 'DELETE', json_encode(compact('data')));

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseEmpty();
    }

    /**
     * Test relationships method to replace existing relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testSetAssociations()
    {
        // 403 <-- PATCH /roles/1/relationships/users
        $data = [
            [
                'id' => '5',
                'type' => 'users',
            ],
        ];
        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/roles/1/relationships/users', json_encode(compact('data')));
        $this->assertResponseCode(403);
        $this->assertContentType('application/vnd.api+json');
        // 200 <-- PATCH /roles/2/relationships/users
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/roles/2/relationships/users',
                'home' => 'http://api.example.com/home',
            ],
        ];
        $data = [
            [
                'id' => '5',
                'type' => 'users',
            ],
        ];
        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/roles/2/relationships/users', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test relationships method to replace existing relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testSetAssociationsEmpty()
    {
        $data = [];
        // 403 <-- PATCH /roles/1/relationships/users
        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/roles/1/relationships/users', json_encode(compact('data')));
        $this->assertResponseCode(403);
        $this->assertContentType('application/vnd.api+json');
        // 204 <-- PATCH /roles/2/relationships/users
        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/roles/2/relationships/users', json_encode(compact('data')));
        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test relationships method to replace existing relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testSetAssociationsNoContent()
    {
        $data = [
            [
                'id' => '1',
                'type' => 'users',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/roles/1/relationships/users', json_encode(compact('data')));

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseEmpty();
    }

    /**
     * Test relationships method to update relationships with a non-existing object ID.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testUpdateAssociationsMissingId()
    {
        $expected = [
            'status' => '400',
            'title' => 'Record not found in table "users"',
        ];

        $data = [
            [
                'id' => '99',
                'type' => 'users',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/roles/1/relationships/users', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        $this->assertArrayHasKey('error', $result);
        $this->assertArraySubset($expected, $result['error']);
    }

    /**
     * Test relationships method with a non-existing association.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testWrongAssociation()
    {
        $expected = [
            'status' => '404',
            'title' => 'Relationship "this_relationship_does_not_exist" does not exist',
        ];

        $this->configRequestHeaders();
        $this->get('/roles/1/relationships/this_relationship_does_not_exist');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
        $this->assertArrayHasKey('error', $result);
        $this->assertArraySubset($expected, $result['error']);
    }

    /**
     * Test relationships method to update relationships with a wrong type.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testUpdateAssociationsUnsupportedType()
    {
        $expected = [
            'status' => '409',
            'title' => 'Unsupported resource type myCustomType',
        ];

        $data = [
            [
                'id' => '1',
                'type' => 'myCustomType',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/roles/1/relationships/users', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        $this->assertArrayHasKey('error', $result);
        $this->assertArraySubset($expected, $result['error']);
    }

    /**
     * Test included resources.
     *
     * @return void
     *
     * @covers ::prepareInclude()
     */
    public function testInclude()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/roles/1?include=users',
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
                        'data' => [
                            [
                                'id' => '1',
                                'type' => 'users',
                            ],
                        ],
                    ],
                ],
            ],
            'included' => [
                [
                    'id' => '1',
                    'type' => 'users',
                    'attributes' => [
                        'username' => 'first user',
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
                        'status' => 'on',
                        'uname' => 'first-user',
                        'title' => 'Mr. First User',
                        'description' => null,
                        'body' => null,
                        'extra' => null,
                        'lang' => 'eng',
                        'publish_start' => null,
                        'publish_end' => null,
                        'another_username' => null, // custom property
                        'another_email' => null, // custom property
                    ],
                    'meta' => [
                        'blocked' => false,
                        'last_login' => null,
                        'last_login_err' => null,
                        'num_login_err' => 1,
                        'locked' => true,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
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
            'meta' => [
                'schema' => [
                    'roles' => [
                        '$id' => 'http://api.example.com/model/schema/roles',
                        'revision' => TestConstants::SCHEMA_REVISIONS['roles'],
                    ],
                    'users' => [
                        '$id' => 'http://api.example.com/model/schema/users',
                        'revision' => TestConstants::SCHEMA_REVISIONS['users'],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/roles/1?include=users');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Data provider for `testIncludeError` test case.
     *
     * @return array
     */
    public function includeErrorProvider()
    {
        return [
            'not a string' => [
                400,
                'Invalid "include" query parameter (Must be a comma-separated string)',
                ['not', 'a', 'string'],
            ],
            'nested resources' => [
                400,
                'Inclusion of nested resources is not yet supported',
                'users.roles',
            ],
            'not found' => [
                400,
                'Invalid "include" query parameter (Relationship "gustavo" does not exist)',
                'users,gustavo',
            ],
        ];
    }

    /**
     * Test included resources.
     *
     * @param int $expectedStatus Expected status.
     * @param string $expectedErrorTitle Expected error message.
     * @param mixed $include `include` query parameter.
     * @return void
     *
     * @dataProvider includeErrorProvider()
     * @covers ::prepareInclude()
     */
    public function testIncludeError($expectedStatus, $expectedErrorTitle, $include)
    {
        $expected = [
            'status' => (string)$expectedStatus,
            'title' => $expectedErrorTitle,
        ];

        $this->configRequestHeaders();
        $this->get('/roles?' . http_build_query(compact('include')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode($expectedStatus);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayHasKey('error', $result);
        static::assertArraySubset($expected, $result['error']);
    }
}
