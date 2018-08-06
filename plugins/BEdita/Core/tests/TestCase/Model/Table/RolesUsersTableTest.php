<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
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
 * {@see \BEdita\Core\Model\Table\RolesUsersTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\RolesUsersTable
 */
class RolesUsersTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\RolesUsersTable
     */
    public $RolesUsers;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.roles_users',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->RolesUsers = TableRegistry::get('RolesUsers');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->RolesUsers);

        parent::tearDown();
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
                    'role_id' => 2,
                    'user_id' => 1,
                ],
            ],
            'notUnique' => [
                false,
                [
                    'role_id' => 1,
                    'user_id' => 1,
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     * @return void
     *
     * @dataProvider validationProvider()
     * @coversNothing
     */
    public function testValidation($expected, array $data)
    {
        $objectType = $this->RolesUsers->newEntity();
        $this->RolesUsers->patchEntity($objectType, $data);

        $success = $this->RolesUsers->save($objectType);
        static::assertEquals($expected, (bool)$success);
    }

    /**
     * Test delete admin role association
     *
     * @expectedException \BEdita\Core\Exception\ImmutableResourceException
     * @expectedExceptionCode 403
     * @expectedExceptionMessage Could not update relationship for users/roles for ADMIN_USER and ADMIN_ROLE
     * @covers ::beforeDelete
     */
    public function testDeleteAdminRole()
    {
        $entity = $this->RolesUsers->get(1);
        $this->RolesUsers->delete($entity);
    }

    /**
     * Test delete second role association
     *
     * @covers ::beforeDelete
     */
    public function testDeleteSecondRole()
    {
        $entity = $this->RolesUsers->get(2);
        $success = $this->RolesUsers->delete($entity);
        static::assertNotEmpty($success);
    }
}
