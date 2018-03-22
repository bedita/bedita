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
        $this->assertEquals('endpoint_permissions', $this->EndpointPermissions->getTable());
        $this->assertEquals('id', $this->EndpointPermissions->getPrimaryKey());
        $this->assertEquals('id', $this->EndpointPermissions->getDisplayField());

        $this->assertInstanceOf('\Cake\ORM\Association\belongsTo', $this->EndpointPermissions->Endpoints);
        $this->assertInstanceOf('\BEdita\Core\Model\Table\EndpointsTable', $this->EndpointPermissions->Endpoints->getTarget());
        $this->assertInstanceOf('\Cake\ORM\Association\belongsTo', $this->EndpointPermissions->Applications);
        $this->assertInstanceOf('\BEdita\Core\Model\Table\ApplicationsTable', $this->EndpointPermissions->Applications->getTarget());
        $this->assertInstanceOf('\Cake\ORM\Association\belongsTo', $this->EndpointPermissions->Roles);
        $this->assertInstanceOf('\BEdita\Core\Model\Table\RolesTable', $this->EndpointPermissions->Roles->getTarget());
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
        $error = (bool)$endpointPermission->getErrors();
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
        $this->assertEquals($expected, (bool)$success, print_r($endpointPermission->getErrors(), true));
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
                3,
                1,
            ],
            'home' => [
                4,
                2,
            ],
            'null' => [
                2,
                '',
            ],
            'auth,home' => [
                5,
                [1, 2],
            ],
            'auth (strict)' => [
                1,
                1,
                true,
            ],
            'empty (strict)' => [
                0,
                '',
                true,
            ],
        ];
    }

    /**
     * Test finder by endpoint ID.
     *
     * @param int $expected Expected count.
     * @param array|int $endpointIds Endpoint id(s).
     * @param bool $strict Is strict mode enabled?
     * @return void
     *
     * @covers ::findByEndpoint()
     * @dataProvider findByEndpointProvider()
     */
    public function testFindByEndpoint($expected, $endpointIds, $strict = false)
    {
        $count = $this->EndpointPermissions->find('byEndpoint', compact('endpointIds', 'strict'))->count();

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
                2,
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
            'application one (strict)' => [
                1,
                1,
                true,
            ],
            'empty (strict)' => [
                0,
                '',
                true,
            ],
        ];
    }

    /**
     * Test finder by application ID.
     *
     * @param int $expected Expected count.
     * @param int $applicationId Application id.
     * @param bool $strict Is strict mode enabled?
     * @return void
     *
     * @covers ::findByApplication()
     * @dataProvider findByApplicationProvider()
     */
    public function testFindByApplication($expected, $applicationId, $strict = false)
    {
        $count = $this->EndpointPermissions->find('byApplication', compact('applicationId', 'strict'))->count();

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
                4,
                2,
            ],
            'null' => [
                2,
                '',
            ],
            'first,second' => [
                5,
                [1, 2],
            ],
            'first (strict)' => [
                1,
                1,
                true,
            ],
            'empty (strict)' => [
                0,
                '',
                true,
            ],
        ];
    }

    /**
     * Test finder by role ID.
     *
     * @param int $expected Expected count.
     * @param array|int $roleIds Role id(s).
     * @param bool $strict Is strict mode enabled?
     * @return void
     *
     * @covers ::findByRole()
     * @dataProvider findByRoleProvider()
     */
    public function testFindByRole($expected, $roleIds, $strict = false)
    {
        $count = $this->EndpointPermissions->find('byRole', compact('roleIds', 'strict'))->count();

        static::assertSame($expected, $count);
    }
}
