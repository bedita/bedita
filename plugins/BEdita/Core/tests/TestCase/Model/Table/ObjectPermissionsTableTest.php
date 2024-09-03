<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
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
use Cake\Http\Exception\ForbiddenException;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Table\ObjectPermissionsTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\ObjectPermissionsTable
 */
class ObjectPermissionsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\ObjectPermissionsTable
     */
    protected $ObjectPermissions;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.ObjectPermissions',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ObjectPermissions = $this->fetchTable('ObjectPermissions');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ObjectPermissions);
        LoggedUser::resetUser();

        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     * @coversNothing
     */
    public function testInitialize()
    {
        $this->assertEquals('object_permissions', $this->ObjectPermissions->getTable());
        $this->assertEquals('id', $this->ObjectPermissions->getPrimaryKey());
        $this->assertEquals('id', $this->ObjectPermissions->getDisplayField());

        $this->assertInstanceOf(BelongsTo::class, $this->ObjectPermissions->CreatedByUsers);
        $this->assertInstanceOf(BelongsTo::class, $this->ObjectPermissions->Objects);
        $this->assertInstanceOf(BelongsTo::class, $this->ObjectPermissions->Roles);
        $this->assertInstanceOf(TimestampBehavior::class, $this->ObjectPermissions->behaviors()->get('Timestamp'));
    }

    /**
     * Data provider for `testBuildRules` test case.
     *
     * @return array
     */
    public function buildRulesProvider()
    {
        return [
            'invalidObject' => [
                false,
                [
                    'object_id' => 1234,
                    'role_id' => 1,
                    'created_by' => 1,
                ],
            ],
            'invalidRole' => [
                false,
                [
                    'object_id' => 2,
                    'role_id' => 1234,
                    'created_by' => 1,
                ],
            ],
            'invalidUser' => [
                false,
                [
                    'object_id' => 2,
                    'role_id' => 1,
                    'created_by' => 1234,
                ],
            ],
        ];
    }

    /**
     * Test build rules validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     * @return void
     * @dataProvider buildRulesProvider
     * @coversNothing
     */
    public function testBuildRules($expected, array $data): void
    {
        $entity = $this->ObjectPermissions->newEntity($data, ['accessibleFields' => ['created_by' => true]]);
        $success = $this->ObjectPermissions->save($entity);
        $this->assertEquals($expected, (bool)$success, print_r($entity->getErrors(), true));
    }

    /**
     * Data provider for `testBeforeSave()`.
     *
     * @return array
     */
    public function beforeSaveProvider(): array
    {
        return [
            'admin' => [
                2,
                LoggedUser::getUserAdmin(),
                [
                    'role_id' => 2,
                    'object_id' => 2,
                ],
            ],
            'perms not set' => [
                2,
                [
                    'id' => 5,
                    'roles' => [
                        [
                            'id' => 2,
                            'name' => 'second user',
                        ],
                    ],
                ],
                [
                    'role_id' => 2,
                    'object_id' => 3,
                ],
            ],
            'forbidden' => [
                new ForbiddenException('Save object permission is forbidden for user'),
                [
                    'id' => 5,
                    'roles' => [
                        [
                            'id' => 2,
                            'name' => 'second user',
                        ],
                    ],
                ],
                [
                    'role_id' => 2,
                    'object_id' => 2,
                ],
            ],
            'forbidden loading roles (no names)' => [
                new ForbiddenException('Save object permission is forbidden for user'),
                [
                    'id' => 5,
                    'roles' => [
                        ['id' => 2],
                    ],
                ],
                [
                    'role_id' => 2,
                    'object_id' => 2,
                ],
            ],
        ];
    }

    /**
     * Test save is forbidden when permission is set on object and user hasn't grant.
     *
     * @param \Exception|int $expected The expected value
     * @param array $user The logged user data
     * @param array $data Object permission data
     * @return void
     * @covers ::beforeSave()
     * @covers ::isEditable()
     * @dataProvider beforeSaveProvider
     */
    public function testBeforeSave($expected, array $user, array $data): void
    {
        if ($expected instanceof \Exception) {
            $this->expectExceptionObject($expected);
        }

        LoggedUser::setUser($user);
        $ObjectTypes = $this->fetchTable('ObjectTypes');
        /** @var \BEdita\Core\Model\Entity\ObjectType $ot */
        $ot = $ObjectTypes->get('documents');
        $ot->addAssoc('Permissions');
        $ObjectTypes->saveOrFail($ot);

        $entity = $this->ObjectPermissions->newEntity($data);
        $this->ObjectPermissions->saveOrFail($entity);

        static::assertEquals($expected, $entity->id);
    }

    /**
     * Test save is ok when permission is set on object and user has grant.
     *
     * @return void
     * @covers ::beforeSave()
     * @covers ::isEditable()
     */
    public function testBeforeSaveWithPermissionOk(): void
    {
        $user = $this->fetchTable('Users')->get(5, ['contain' => 'Roles']);
        LoggedUser::setUser($user->toArray());
        $ObjectTypes = $this->fetchTable('ObjectTypes');
        /** @var \BEdita\Core\Model\Entity\ObjectType $ot */
        $ot = $ObjectTypes->get('documents');
        $ot->addAssoc('Permissions');
        $ObjectTypes->saveOrFail($ot);
        static::assertFalse($this->ObjectPermissions->exists(['object_id' => 3]));
        $entity = $this->ObjectPermissions->newEntity([
            'role_id' => 2,
            'object_id' => 3,
        ]);

        $this->ObjectPermissions->saveOrFail($entity);
        static::assertEquals(2, $entity->id);

        $entity = $this->ObjectPermissions->newEntity([
            'role_id' => 1,
            'object_id' => 3,
        ]);
        $this->ObjectPermissions->saveOrFail($entity);
        static::assertEquals(3, $entity->id);
    }

    /**
     * Data provider for `testBeforeDelete()`
     *
     * @return array
     */
    public function beforeDeleteProvider(): array
    {
        return [
            'admin' => [
                null,
                LoggedUser::getUserAdmin(),
            ],
            'forbidden' => [
                new ForbiddenException('Delete object permission is forbidden for user'),
                [
                    'id' => 5,
                    'roles' => [
                        [
                            'id' => 2,
                            'name' => 'second user',
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test beforeDelete.
     *
     * @param mixed $expected The expected result
     * @param array $user The logged user data
     * @return void
     * @covers ::beforeDelete()
     * @covers ::isEditable()
     * @dataProvider beforeDeleteProvider
     */
    public function testBeforeDelete($expected, array $user): void
    {
        if ($expected instanceof \Exception) {
            $this->expectExceptionObject($expected);
        }

        LoggedUser::setUser($user);
        $ObjectTypes = $this->fetchTable('ObjectTypes');
        /** @var \BEdita\Core\Model\Entity\ObjectType $ot */
        $ot = $ObjectTypes->get('documents');
        $ot->addAssoc('Permissions');
        $ObjectTypes->saveOrFail($ot);

        $op = $this->ObjectPermissions->get(1);
        $this->ObjectPermissions->deleteOrFail($op);
        static::assertFalse($this->ObjectPermissions->exists(['id' => 1]));
    }
}
