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

namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Table\EndpointPermissionsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * BEdita\Core\Model\Table\EndpointPermissionsTable Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\EndpointPermissionsTable
 *
 * @since 4.0.0
 */
class EndpointPermissionsTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\EndpointPermissionsTable
     */
    public $EndpointPermissions;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.endpoints',
        'plugin.BEdita/Core.applications',
        'plugin.BEdita/Core.endpoint_permissions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->EndpointPermissions = TableRegistry::get('EndpointPermissions');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->EndpointPermissions);

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->EndpointPermissions->initialize([]);
        $this->assertEquals('endpoint_permissions', $this->EndpointPermissions->table());
        $this->assertEquals('id', $this->EndpointPermissions->primaryKey());
        $this->assertEquals('id', $this->EndpointPermissions->displayField());

        $this->assertInstanceOf('\Cake\ORM\Association\belongsTo', $this->EndpointPermissions->Endpoints);
        $this->assertInstanceOf('\BEdita\Core\Model\Table\EndpointsTable', $this->EndpointPermissions->Endpoints->target());
        $this->assertInstanceOf('\Cake\ORM\Association\belongsTo', $this->EndpointPermissions->Applications);
        $this->assertInstanceOf('\BEdita\Core\Model\Table\ApplicationsTable', $this->EndpointPermissions->Applications->target());
        $this->assertInstanceOf('\Cake\ORM\Association\belongsTo', $this->EndpointPermissions->Roles);
        $this->assertInstanceOf('\BEdita\Core\Model\Table\RolesTable', $this->EndpointPermissions->Roles->target());
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'valid' => [
                true,
                [
                    'endpoint_id' => 2,
                    'application_id' => 1,
                    'role_id' => 1,
                    'permission' => 1
                ],
            ],
            'valid2' => [
                true,
                [
                    'endpoint_id' => 1,
                ],
            ],
            'emptyPermission' => [
                false,
                [
                    'permission' => '',
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     *
     * @return void
     * @dataProvider validationProvider
     * @coversNothing
     */
    public function testValidation($expected, array $data)
    {
        $endpointPermission = $this->EndpointPermissions->newEntity($data);
        $error = (bool)$endpointPermission->errors();
        $this->assertEquals($expected, !$error);
        if ($expected) {
            $success = $this->EndpointPermissions->save($endpointPermission);
            $this->assertTrue((bool)$success);
        }
    }

    /**
     * Data provider for `testBuildRules` test case.
     *
     * @return array
     */
    public function buildRulesProvider()
    {
        return [
            'inValidEndpoint' => [
                false,
                [
                    'endpoint_id' => 1234,
                    'application_id' => 1,
                    'role_id' => 1,
                ],
            ],
            'inValidApp' => [
                false,
                [
                    'endpoint_id' => 1,
                    'application_id' => 1234,
                    'role_id' => 1,
                ],
            ],
            'inValidRole' => [
                false,
                [
                    'endpoint_id' => 1,
                    'application_id' => 1,
                    'role_id' => 1234,
                ],
            ],
        ];
    }

    /**
     * Test build rules validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     *
     * @return void
     * @dataProvider buildRulesProvider
     * @coversNothing
     */
    public function testBuildRules($expected, array $data)
    {
        $endpointPermission = $this->EndpointPermissions->newEntity($data, ['validate' => false]);
        $success = $this->EndpointPermissions->save($endpointPermission);
        $this->assertEquals($expected, (bool)$success, print_r($endpointPermission->errors(), true));
    }

    /**
     * Data provider for `testFindByEndpoint` test case.
     *
     * @return array
     */
    public function findByEndpointProvider()
    {
        return [
            'auth' => [
                2,
                1,
            ],
            'home' => [
                3,
                2,
            ],
            'null' => [
                1,
                '',
            ],
            'auth,home' => [
                4,
                [1, 2],
            ],
        ];
    }

    /**
     * Test finder by endpoint ID.
     *
     * @param int $expected Expected count.
     * @param array|int $endpointIds Endpoint id(s).
     * @return void
     *
     * @covers ::findByEndpoint()
     * @dataProvider findByEndpointProvider()
     */
    public function testFindByEndpoint($expected, $endpointIds)
    {
        $count = $this->EndpointPermissions->find('byEndpoint', compact('endpointIds'))->count();

        static::assertSame($expected, $count);
    }

    /**
     * Data provider for `testFindByApplication` test case.
     *
     * @return array
     */
    public function findByApplicationProvider()
    {
        return [
            'application one' => [
                1,
                1,
            ],
            'application two' => [
                4,
                2,
            ],
            'null' => [
                1,
                '',
            ],
        ];
    }

    /**
     * Test finder by application ID.
     *
     * @param int $expected Expected count.
     * @param int $applicationId Application id.
     * @return void
     *
     * @covers ::findByApplication()
     * @dataProvider findByApplicationProvider()
     */
    public function testFindByApplication($expected, $applicationId)
    {
        $count = $this->EndpointPermissions->find('byApplication', compact('applicationId'))->count();

        static::assertSame($expected, $count);
    }

    /**
     * Data provider for `testFindByRole` test case.
     *
     * @return array
     */
    public function findByRoleProvider()
    {
        return [
            'first' => [
                3,
                1,
            ],
            'second' => [
                3,
                2,
            ],
            'null' => [
                2,
                '',
            ],
            'first,second' => [
                4,
                [1, 2],
            ],
        ];
    }

    /**
     * Test finder by role ID.
     *
     * @param int $expected Expected count.
     * @param array|int $roleIds Role id(s).
     * @return void
     *
     * @covers ::findByRole()
     * @dataProvider findByRoleProvider()
     */
    public function testFindByRole($expected, $roleIds)
    {
        $count = $this->EndpointPermissions->find('byRole', compact('roleIds'))->count();

        static::assertSame($expected, $count);
    }
}
