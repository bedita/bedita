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

use BEdita\Core\Utility\LoggedUser;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Table\RolesTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\RolesTable
 */
class RolesTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\RolesTable
     */
    public $Roles;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.roles_users',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Roles = TableRegistry::get('Roles');
        LoggedUser::setUser(['id' => 1]);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Roles);
        LoggedUser::resetUser();

        parent::tearDown();
    }

    /**
     * Test initialization.
     *
     * @return void
     * @coversNothing
     */
    public function testInitialization()
    {
        $this->Roles->initialize([]);
        $this->assertEquals('roles', $this->Roles->getTable());
        $this->assertEquals('id', $this->Roles->getPrimaryKey());
        $this->assertEquals('name', $this->Roles->getDisplayField());

        $this->assertInstanceOf('\Cake\ORM\Association\BelongsToMany', $this->Roles->Users);
        $this->assertInstanceOf('\BEdita\Core\Model\Table\UsersTable', $this->Roles->Users->getTarget());
        $this->assertInstanceOf('\Cake\ORM\Association\hasMany', $this->Roles->EndpointPermissions);
        $this->assertInstanceOf('\BEdita\Core\Model\Table\EndpointPermissionsTable', $this->Roles->EndpointPermissions->getTarget());
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
                    'name' => 'unique_role_name',
                ],
            ],
            'notUnique' => [
                false,
                [
                    'name' => 'first role',
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
        $role = $this->Roles->newEntity();
        $this->Roles->patchEntity($role, $data);

        $error = (bool)$role->getErrors();
        static::assertEquals($expected, !$error);

        if ($expected) {
            $success = $this->Roles->save($role);
            static::assertTrue((bool)$success);
        }
    }

    /**
     * Test finder for my objects.
     *
     * @return void
     *
     * @covers ::findMine()
     */
    public function testFindMine()
    {
        $expected = [
            1 => 1,
        ];

        $result = $this->Roles->find('mine')
            ->find('list', ['keyField' => 'id', 'valueField' => 'id'])
            ->toArray();

        static::assertEquals($expected, $result);
    }
}
