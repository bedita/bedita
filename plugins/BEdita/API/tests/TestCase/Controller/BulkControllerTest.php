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
    ];

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
     * @covers ::canSave()
     */
    public function testEdit(): void
    {
        // test: admin
        $this->performCheck('first user', 'password1');

        // test: non admin
        $this->performCheck('second user', 'password2');

        // insert more data in endpoints and endpoints permissions to make the test more meaningful
        // insert into endpoint a record for /documents
        $endpointsTable = $this->fetchTable('Endpoints');
        $endpoint = $endpointsTable->newEntity([
            'name' => 'documents',
            'object_type_id' => 2,
            'enabled' => true,
        ]);
        $endpointsTable->saveOrFail($endpoint);
        // insert into endpoint_permissions a record for the just created endpoint
        $endpointPermissionsTable = $this->fetchTable('EndpointPermissions');
        $endpointPermission = $endpointPermissionsTable->newEntity([
            'endpoint_id' => $endpoint->get('id'),
            'role_id' => 2, // second role
            'permission' => 15, // 1111
        ]);
        $endpointPermissionsTable->saveOrFail($endpointPermission);

        $this->performCheck('second user', 'password2');
    }

    /**
     * Test index method on POST /bulk/edit with object having perms that do not allow current user to edit it.
     *
     * @return void
     * @covers ::index()
     * @covers ::edit()
     * @covers ::canSave()
     */
    public function testEditObjectWithPerms(): void
    {
        // set perms roles admin on object id 3
        $objectTypesTable = $this->fetchTable('ObjectTypes');
        $objectTypeEntity = $objectTypesTable->get('documents');
        $objectTypeEntity->associations = ['Permissions'];
        $objectTypesTable->saveOrFail($objectTypeEntity);

        // add object permissions
        $ObjectPermissions = $this->fetchTable('ObjectPermissions');
        $entity = $ObjectPermissions->newEntity(
            [
                'object_id' => 3,
                'role_id' => 1,
                'created_by' => 1,
            ],
            [
                'accessibleFields' => ['created_by' => true],
            ]
        );
        $ObjectPermissions->saveOrFail($entity);
        $objectsTable = $this->fetchTable('Objects');
        $o1 = $objectsTable->get(3);
        $perms = $o1->get('perms') ?? [];
        $this->assertEquals(['first role'], $perms['roles']);

        // try to edit object 3 with user that is not admin
        $authHeader = $this->getUserAuthHeader();
        $authHeader['Content-Type'] = 'application/json';
        $this->configRequestHeaders('POST', $this->getUserAuthHeader('second user', 'password2'));
        $this->post('/bulk/edit', json_encode([
            'ids' => [3],
            'data' => [
                'status' => 'off',
            ],
        ]));
        $this->assertResponseCode(200);
        // check response content
        $response = (array)json_decode((string)$this->_response->getBody(), true);
        $this->assertArrayHasKey('saved', $response);
        $this->assertArrayHasKey('errors', $response);
        $this->assertCount(0, Hash::get($response, 'saved'));
        $this->assertCount(1, Hash::get($response, 'errors'));
        $this->assertEquals(3, Hash::get($response, 'errors.0.id'));
        $this->assertEquals('User cannot save "documents" 3', Hash::get($response, 'errors.0.message'));

        // restore object type
        $objectTypeEntity->associations = [];
        $objectTypesTable->saveOrFail($objectTypeEntity);
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
        $authHeader = $this->getUserAuthHeader();
        $authHeader['Content-Type'] = 'application/json';
        $this->configRequestHeaders('POST', $this->getUserAuthHeader($user, $password));
        $this->post('/bulk/edit', json_encode([
            'ids' => [2, 3],
            'data' => [
                'status' => 'off',
            ],
        ]));
        $this->assertResponseCode(200);
        // check response content
        $response = (array)json_decode((string)$this->_response->getBody(), true);
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
        // restore objects status
        $authHeader = $this->getUserAuthHeader();
        $authHeader['Content-Type'] = 'application/json';
        $this->configRequestHeaders('POST', $this->getUserAuthHeader($user, $password));
        $this->post('/bulk/edit', json_encode([
            'ids' => [3],
            'data' => [
                'status' => 'draft',
            ],
        ]));
        $this->assertResponseCode(200);
        $o2 = $this->fetchTable('Objects')->get(3);
        $secondStatus = $o2->get('status');
        $this->assertEquals('draft', $secondStatus);
    }
}
