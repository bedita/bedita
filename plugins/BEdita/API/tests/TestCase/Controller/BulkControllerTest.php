<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 Chialab Srl
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
use BEdita\Core\State\CurrentApplication;
use BEdita\Core\Utility\LoggedUser;
use Cake\Utility\Hash;

/**
 * @coversDefaultClass \BEdita\API\Controller\BulkController
 */
class BulkControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Endpoints',
        'plugin.BEdita/Core.EndpointPermissions',
    ];

    /**
     * Backup of original endpoint permissions
     *
     * @var array
     */
    protected $originalEndpointPermissions = [];

    /**
     * Backup of original object permissions
     *
     * @var array
     */
    protected $originalObjectPermissions = [];

    /**
     * Set up method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        // Backup original endpoint permissions
        $endpointPermissionsTable = $this->fetchTable('EndpointPermissions');
        $this->originalEndpointPermissions = $endpointPermissionsTable->find()->toArray();

        // Backup original object permissions
        $objectPermissionsTable = $this->fetchTable('ObjectPermissions');
        $this->originalObjectPermissions = $objectPermissionsTable->find()->toArray();
    }

    /**
     * Tear down method
     *
     * @return void
     */
    public function tearDown(): void
    {
        // Restore original endpoint permissions
        $endpointPermissionsTable = $this->fetchTable('EndpointPermissions');
        $endpointPermissionsTable->deleteAll([]);

        foreach ($this->originalEndpointPermissions as $permission) {
            $entity = $endpointPermissionsTable->newEntity($permission->toArray());
            $endpointPermissionsTable->saveOrFail($entity);
        }

        // restore object permissions table
        $objectPermissionsTable = $this->fetchTable('ObjectPermissions');
        $objectPermissionsTable->deleteAll([]);
        foreach ($this->originalObjectPermissions as $permission) {
            $entity = $objectPermissionsTable->newEntity($permission->toArray());
            $objectPermissionsTable->saveOrFail($entity);
        }

        parent::tearDown();
    }

    /**
     * Test index method on GET /bulk.
     *
     * @return void
     * @covers ::index()
     */
    public function testIndex404(): void
    {
        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/bulk');
        $this->assertResponseCode(404);
    }

    /**
     * Test index method on GET /bulk/edit.
     *
     * @return void
     * @covers ::index()
     */
    public function testIndex405(): void
    {
        $this->configRequestHeaders('GET', $this->getUserAuthHeader());
        $this->get('/bulk/edit');
        $this->assertResponseCode(405);

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/bulk/something');
        $this->assertResponseCode(405);
    }

    /**
     * Test index method on POST /bulk/edit.
     *
     * @return void
     * @covers ::index()
     * @covers ::edit()
     * @covers ::canAccessEndpoint()
     */
    public function testEdit(): void
    {
        // test: admin
        $this->performCheck('first user', 'password1');

        // test: non admin
        $this->performCheck('second user', 'password2');
    }

    /**
     * Test index method on POST /bulk/edit with abstract type in payload.
     *
     * @return void
     * @covers ::index()
     * @covers ::edit()
     */
    public function testEditAbstractType(): void
    {
        // try to edit object 1 of abstract type 'objects'
        $this->configRequestHeaders('POST', $this->getUserAuthHeader('first user', 'password1'));
        $this->post('/bulk/edit', json_encode([
            'data' => [
                'attributes' => [
                    'status' => 'off',
                ],
                'objects' => [
                    'objects' => [1],
                ],
            ],
        ]));
        $this->assertResponseCode(200);

        // check response content
        $response = (array)json_decode((string)$this->_response->getBody(), true);
        $response = (array)Hash::get($response, 'data');
        $this->assertArrayHasKey('saved', $response);
        $this->assertArrayHasKey('errors', $response);
        $this->assertCount(0, Hash::get($response, 'saved'));
        $this->assertCount(1, Hash::get($response, 'errors'));
        $this->assertEquals(1, Hash::get($response, 'errors.0.id'));
        $this->assertEquals('Endpoint "objects" cannot be used for bulk edit: abstract or disabled', Hash::get($response, 'errors.0.message'));
    }

    /**
     * Test index method on POST /bulk/edit with endpoint access not allowed for current user.
     *
     * @return void
     * @covers ::index()
     * @covers ::edit()
     * @covers ::canAccessEndpoint()
     */
    public function testEditEndpointAccessFalse(): void
    {
        // insert into endpoint a record for /documents
        $endpointsTable = $this->fetchTable('Endpoints');
        $endpoint = $endpointsTable->newEntity([
            'name' => 'documents',
            'object_type_id' => 2,
            'enabled' => true,
        ]);
        $endpointsTable->saveOrFail($endpoint);
        $endpointId = $endpoint->get('id');

        // Clear any existing permissions for this endpoint to ensure clean test
        $endpointPermissionsTable = $this->fetchTable('EndpointPermissions');
        $endpointPermissionsTable->deleteAll([]);

        // Insert ONLY ONE blocking permission for the specific endpoint and role
        $endpointPermission = $endpointPermissionsTable->newEntity([
            'application_id' => CurrentApplication::getApplicationId(),
            'endpoint_id' => $endpointId,
            'role_id' => 2, // second role
            'permission' => 0, // block
        ]);
        $endpointPermissionsTable->saveOrFail($endpointPermission);

        // Verify that we have exactly one permission for this endpoint
        $permissionCount = $endpointPermissionsTable->find()
            ->where([
                'endpoint_id' => $endpointId,
                'application_id' => CurrentApplication::getApplicationId(),
                'role_id' => 2,
            ])
            ->count();
        $this->assertEquals(1, $permissionCount, 'Should have exactly one permission for this endpoint');

        // Verify the permission value is 0 (block)
        $permission = $endpointPermissionsTable->find()
            ->where([
                'endpoint_id' => $endpointId,
                'application_id' => CurrentApplication::getApplicationId(),
                'role_id' => 2,
            ])
            ->first();
        $this->assertEquals(0, $permission->get('permission'), 'Permission should be 0 (block)');

        // try to edit object 3 with user that is not admin
        $this->configRequestHeaders('POST', $this->getUserAuthHeader('second user', 'password2'));
        $this->post('/bulk/edit', json_encode([
            'data' => [
                'attributes' => [
                    'status' => 'off',
                ],
                'objects' => [
                    'documents' => [3],
                ],
            ],
        ]));
        $this->assertResponseCode(200);

        // check response content
        $response = (array)json_decode((string)$this->_response->getBody(), true);
        $response = (array)Hash::get($response, 'data');
        $this->assertArrayHasKey('saved', $response);
        $this->assertArrayHasKey('errors', $response);
        $this->assertCount(0, Hash::get($response, 'saved'));
        $this->assertCount(1, Hash::get($response, 'errors'));
        $this->assertEquals(3, Hash::get($response, 'errors.0.id'));
        $this->assertEquals('User cannot access "documents" endpoint', Hash::get($response, 'errors.0.message'));
    }

    /**
     * Test index method on POST /bulk/edit with object having perms that do not allow current user to edit it.
     *
     * @return void
     * @covers ::index()
     * @covers ::edit()
     * @covers ::canAccessEndpoint()
     */
    public function testEditObjectWithPerms(): void
    {
        LoggedUser::setUserAdmin();

        // Create a temporary object type with permissions enabled
        $objectTypesTable = $this->fetchTable('ObjectTypes');
        $tempObjectType = $objectTypesTable->newEntity([
            'name' => 'dummies',
            'singular' => 'dummy',
            'plugin' => 'BEdita/Core',
            'model' => 'Objects',
            'associations' => ['Permissions'], // Enable permissions for this type
            'enabled' => true,
        ]);
        $objectTypesTable->saveOrFail($tempObjectType);

        // Create a test object of this type
        $objectsTable = $this->fetchTable('dummies');
        $testObject = $objectsTable->newEntity([
            'status' => 'draft',
            'title' => 'Test Object with Permissions',
        ]);
        $objectsTable->saveOrFail($testObject);

        // Add object permissions - only role 1 can access
        $ObjectPermissions = $this->fetchTable('ObjectPermissions');
        $entity = $ObjectPermissions->newEntity(
            [
                'object_id' => $testObject->get('id'),
                'role_id' => 1, // first role only
                'created_by' => 1,
            ],
            [
                'accessibleFields' => ['created_by' => true],
            ],
        );
        $ObjectPermissions->saveOrFail($entity);

        // Verify permissions are set
        $testObject = $objectsTable->get($testObject->get('id'));
        $perms = $testObject->get('perms') ?? [];
        $this->assertEquals(['first role'], $perms['roles']);

        // Try to edit with user that doesn't have permissions (second user, role 2)
        $this->configRequestHeaders('POST', $this->getUserAuthHeader('second user', 'password2'));
        $this->post('/bulk/edit', json_encode([
            'data' => [
                'attributes' => [
                    'status' => 'off',
                ],
                'objects' => [
                    $tempObjectType->get('name') => [$testObject->get('id')],
                ],
            ],
        ]));
        $this->assertResponseCode(200);

        // check response content
        $response = (array)json_decode((string)$this->_response->getBody(), true);
        $response = (array)Hash::get($response, 'data');
        $this->assertArrayHasKey('saved', $response);
        $this->assertArrayHasKey('errors', $response);
        $this->assertCount(0, Hash::get($response, 'saved'));
        $this->assertCount(1, Hash::get($response, 'errors'));
        $this->assertEquals($testObject->get('id'), Hash::get($response, 'errors.0.id'));
        $this->assertEquals('User cannot save "' . $tempObjectType->get('name') . '" ' . $testObject->get('id'), Hash::get($response, 'errors.0.message'));
    }

    /**
     * Perform the actual check
     *
     * @param string $user User name
     * @param string $password User password
     * @return void
     */
    protected function performCheck(string $user, string $password): void
    {
        // object 2 is locked, status cannot be changed
        $o1 = $this->fetchTable('Objects')->get(2);
        $firstOriginalStatus = $o1->get('status');
        $this->assertEquals('on', $firstOriginalStatus);
        $o2 = $this->fetchTable('Objects')->get(3);
        $secondOriginalStatus = $o2->get('status');
        $this->assertEquals('draft', $secondOriginalStatus);
        $this->configRequestHeaders('POST', $this->getUserAuthHeader($user, $password));
        $map = [];
        $map[$o1->get('type')][] = $o1->get('id');
        $map[$o2->get('type')][] = $o2->get('id');
        $this->post('/bulk/edit', json_encode([
            'data' => [
                'attributes' => [
                    'status' => 'off',
                ],
                'objects' => $map,
            ],
        ]));
        $this->assertResponseCode(200);
        // check response content
        $response = (array)json_decode((string)$this->_response->getBody(), true);
        $response = (array)Hash::get($response, 'data');
        $this->assertArrayHasKey('saved', $response);
        $this->assertArrayHasKey('errors', $response);
        $this->assertEquals([3], Hash::get($response, 'saved'));
        $this->assertCount(1, Hash::get($response, 'errors'));
        $this->assertEquals(2, Hash::get($response, 'errors.0.id'));
        $this->assertEquals('Operation not allowed on "locked" objects', Hash::get($response, 'errors.0.message'));
        // check objects status
        $o1 = $this->fetchTable('Objects')->get(2);
        $firstStatus = $o1->get('status');
        $this->assertEquals($firstOriginalStatus, $firstStatus);
        $o2 = $this->fetchTable('Objects')->get(3);
        $secondStatus = $o2->get('status');
        $this->assertEquals('off', $secondStatus);
        $this->configRequestHeaders('POST', $this->getUserAuthHeader($user, $password));
        $this->post('/bulk/edit', json_encode([
            'data' => [
                'attributes' => [
                    'status' => 'draft',
                ],
                'objects' => [
                    $o2->get('type') => [$o2->get('id')],
                ],
            ],
        ]));
        $this->assertResponseCode(200);
        $o2 = $this->fetchTable('Objects')->get(3);
        $secondStatus = $o2->get('status');
        $this->assertEquals('draft', $secondStatus);
    }
}
