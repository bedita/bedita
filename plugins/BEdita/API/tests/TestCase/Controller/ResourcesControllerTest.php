<?php
declare(strict_types=1);

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

use Authentication\AuthenticationService;
use BEdita\API\Controller\ResourcesController;
use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\Core\Model\Table\UsersTable;
use Cake\Http\ServerRequest;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

/**
 * @coversDefaultClass \BEdita\API\Controller\ResourcesController
 */
class ResourcesControllerTest extends IntegrationTestCase
{
    use ArraySubsetAsserts;

    /**
     * Test modelClass property copied to defaultTable.
     *
     * @return void
     * @covers ::initialize()
     */
    public function testModelClassProp()
    {
        $serviceMock = $this->getMockBuilder(AuthenticationService::class)
            ->disableOriginalConstructor()
            ->getMock();

        $request = (new ServerRequest())->withAttribute('authentication', $serviceMock);

        $controller = new class ($request) extends ResourcesController {
            protected $modelClass = 'Users';

            public function getDefaultTable()
            {
                return $this->defaultTable;
            }

            public function getTable()
            {
                return $this->Table;
            }
        };

        static::assertEquals('Users', $controller->getDefaultTable());
        static::assertInstanceOf(UsersTable::class, $controller->getTable());
    }

    /**
     * Test relationships method to list existing relationships.
     *
     * @return void
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
        static::assertEquals($expected, $result);
    }

    /**
     * Test relationships method to list existing relationships.
     *
     * @return void
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
        $this->assertResponseEmpty();
    }

    /**
     * Test relationships method to delete existing relationships.
     *
     * @return void
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
            ],
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
        $this->assertResponseEmpty();
    }

    /**
     * Utility method to add new role
     *
     * @return void
     */
    protected function addRole()
    {
        $data = [
            'type' => 'roles',
            'attributes' => [
                'name' => 'support',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/roles', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(201);
    }

    /**
     * Test delete method
     *
     * @return void
     * @covers ::index()
     */
    public function testDeleteMulti(): void
    {
        // add new role to delete
        $data = [
            'type' => 'roles',
            'attributes' => [
                'name' => 'todelete',
            ],
        ];
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/roles', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete('/roles?ids=' . $result['data']['id']);
        $this->assertResponseCode(204);
    }

    /**
     * Test index method on DELETE with internal error.
     *
     * @return void
     * @covers ::index()
     */
    public function testIndexDeleteException(): void
    {
        $authHeader = $this->getUserAuthHeader();
        $this->configRequestHeaders('DELETE', $authHeader);
        $this->_sendRequest('/roles?ids=', 'DELETE');
        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseContains('Missing required parameter');
        $this->configRequestHeaders('DELETE', $authHeader);
        $this->_sendRequest('/roles?ids=abc', 'DELETE');
        $this->assertResponseCode(500);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test relationships method to replace existing relationships.
     *
     * @return void
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testSetAssociations()
    {
        // add role id 3
        $this->addRole();
        // 200 <-- PATCH /roles/3/relationships/users
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/roles/3/relationships/users',
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
        $this->patch('/roles/3/relationships/users', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test relationships method to replace existing relationships.
     *
     * @return void
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testSetAssociationsFailure()
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
    }

    /**
     * Test relationships method to replace existing relationships.
     *
     * @return void
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testSetAssociationsEmpty()
    {
        // add role id 3
        $this->addRole();
        $data = [];
        // 204 <-- PATCH /roles/3/relationships/users
        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/roles/3/relationships/users', json_encode(compact('data')));
        $this->assertResponseCode(204);
        $this->assertResponseEmpty();
    }

    /**
     * Test relationships method to replace existing relationships.
     *
     * @return void
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testSetAssociationsEmptyFailure()
    {
        $data = [];
        // 403 <-- PATCH /roles/1/relationships/users
        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/roles/1/relationships/users', json_encode(compact('data')));
        $this->assertResponseCode(403);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test relationships method to replace existing relationships.
     *
     * @return void
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
        $this->assertResponseEmpty();
    }

    /**
     * Test relationships method to update relationships with a non-existing object ID.
     *
     * @return void
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testUpdateAssociationsMissingId()
    {
        $expected = [
            'status' => '404',
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

        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
        $this->assertArrayHasKey('error', $result);
        $this->assertArraySubset($expected, $result['error']);
    }

    /**
     * Test relationships method with a non-existing association.
     *
     * @return void
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
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testUpdateAssociationsUnsupportedType()
    {
        $expected = [
            'status' => '409',
            'title' => 'Unsupported resource type',
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
}
