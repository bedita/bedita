<?php
declare(strict_types=1);

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

use BEdita\Core\Exception\ImmutableResourceException;
use BEdita\Core\Model\Table\RolesUsersTable;
use BEdita\Core\Utility\LoggedUser;
use Cake\Http\Exception\ForbiddenException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \BEdita\Core\Model\Table\RolesUsersTable} Test Case
 */
#[CoversClass(RolesUsersTable::class)]
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
    protected array $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.RolesUsers',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->RolesUsers = TableRegistry::getTableLocator()->get('RolesUsers');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->RolesUsers);

        parent::tearDown();
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public static function validationProvider(): array
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
     */
    #[DataProvider('validationProvider')]
    #[CoversNothing]
    public function testValidation($expected, array $data)
    {
        LoggedUser::setUserAdmin();
        $objectType = $this->RolesUsers->newEmptyEntity();
        $this->RolesUsers->patchEntity($objectType, $data);

        $success = $this->RolesUsers->save($objectType);
        static::assertEquals($expected, (bool)$success);
    }

    /**
     * Test delete admin role association
     *
     * @return void
     */
    public function testDeleteAdminRole()
    {
        $this->expectException(ImmutableResourceException::class);
        $this->expectExceptionCode('403');
        $this->expectExceptionMessage('Could not update relationship for users/roles for ADMIN_USER and ADMIN_ROLE');
        $entity = $this->RolesUsers->get(1);
        $this->RolesUsers->delete($entity);
    }

    /**
     * Test delete admin role association
     *
     * @return void
     */
    public function testDeleteAdminRoleForbidden()
    {
        LoggedUser::resetUser();
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionCode('403');
        $this->expectExceptionMessage('Could not update role. Insufficient priority');
        $entity = $this->RolesUsers->get(2);
        $this->RolesUsers->delete($entity);
    }

    /**
     * Test delete second role association
     *
     * @return void
     */
    public function testDeleteSecondRole()
    {
        LoggedUser::setUserAdmin();
        $entity = $this->RolesUsers->get(2);
        $success = $this->RolesUsers->delete($entity);
        static::assertNotEmpty($success);
    }

    /**
     * Test modify admin role association
     *
     * @return void
     */
    public function testModifyAdminRole()
    {
        LoggedUser::setUserAdmin();
        $entity = $this->RolesUsers->newEmptyEntity();
        $this->RolesUsers->patchEntity($entity, [
            'role_id' => 2,
            'user_id' => 1,
        ]);
        $actual = $this->RolesUsers->save($entity);
        static::assertTrue((bool)$actual);
    }

    /**
     * Test modify admin role association
     *
     * @return void
     */
    public function testModifyAdminRoleForbidden()
    {
        LoggedUser::resetUser();
        $this->expectException(ForbiddenException::class);
        $this->expectExceptionCode('403');
        $this->expectExceptionMessage('Could not update role. Insufficient priority');
        $entity = $this->RolesUsers->get(1);
        $entity->priority = 50;
        $this->RolesUsers->save($entity);
    }
}
