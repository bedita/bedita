<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Test\TestCase\Policy;

use Authentication\Identity as AuthenticationIdentity;
use Authorization\AuthorizationService;
use Authorization\Identity;
use Authorization\Policy\MapResolver;
use BEdita\API\Policy\ObjectPolicy;
use BEdita\Core\Utility\LoggedUser;
use Cake\ORM\Query;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\API\Policy\ObjectPolicy} Test Case.
 *
 * @coversDefaultClass \BEdita\API\Policy\ObjectPolicy
 */
class ObjectPolicyTest extends TestCase
{
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
     * Data provider for `testBefore()`.
     *
     * @return array
     */
    public function beforeProvider(): array
    {
        return [
            'no identity' => [
                null,
                null,
            ],
            'admin' => [
                true,
                LoggedUser::getUserAdmin(),
            ],
            'no-admin' => [
                null,
                [
                    'id' => 1,
                    'roles' => [
                        ['id' => 2],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `before()` method.
     *
     * @param null|true $expected The expected result
     * @param array|null $user The user data
     * @return void
     * @covers ::before()
     * @dataProvider beforeProvider
     */
    public function testBefore($expected, ?array $user): void
    {
        $identity = null;
        if ($user !== null) {
            $identity = new Identity(new AuthorizationService(new MapResolver()), new AuthenticationIdentity($user));
        }

        $policy = new ObjectPolicy();
        $actual = $policy->before($identity, null, null);
        static::assertEquals($expected, $actual);
    }

    /**
     * Data provider for `testCanUpdate()`
     *
     * @return array
     */
    public function canUpdateProvider(): array
    {
        return [
            'no permissions set' => [
                true,
                3,
                [
                    'id' => 5,
                    'roles' => [
                        [
                            'id' => 2,
                            'name' => 'second role',
                        ],
                    ],
                ],
            ],
            'user has role' => [
                true,
                2,
                LoggedUser::getUserAdmin(),
            ],
            'user has not role' => [
                false,
                2,
                [
                    'id' => 5,
                    'roles' => [
                        [
                            'id' => 2,
                            'name' => 'second role',
                        ],
                    ],
                ],
            ],
            'user without roles' => [
                false,
                2,
                [
                    'id' => 5,
                ],
            ],
        ];
    }

    /**
     * Test `canUpdate()` method.
     *
     * @param bool $expected The expected value
     * @param int $id The object id
     * @param array $user The user data
     * @return void
     * @covers ::canUpdate()
     * @covers ::extractRolesNames
     * @dataProvider canUpdateProvider
     */
    public function testCanUpdate(bool $expected, $id, array $user): void
    {
        $objectTypesTable = $this->fetchTable('ObjectTypes');
        /** @var \BEdita\Core\Model\Entity\ObjectType $objectType */
        $objectType = $objectTypesTable
            ->find()
            ->innerJoinWith('Objects', function (Query $q) use ($id) {
                return $q->where(['Objects.id' => $id]);
            })
            ->first();

        $objectType->addAssoc('Permissions');
        $objectTypesTable->saveOrFail($objectType);

        $object = $this->fetchTable($objectType->name)->get($id);
        $identity = new Identity(new AuthorizationService(new MapResolver()), new AuthenticationIdentity($user));
        $policy = new ObjectPolicy();

        static::assertEquals($expected, $policy->canUpdate($identity, $object));
    }
}
