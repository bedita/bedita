<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Test\TestCase\Model\Action;

use BEdita\API\Model\Action\UpdateRelatedAction;
use BEdita\Core\Model\Action\SetRelatedObjectsAction;
use BEdita\Core\Utility\LoggedUser;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\ServerRequest;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use Exception;

/**
 * @covers \BEdita\API\Model\Action\UpdateRelatedAction
 */
class UpdateRelatedActionTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
        'plugin.BEdita/Core.History',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
    ];

    /**
     * Data provider for {@see UpdateRelatedActionTest::testInvocation()} test case.
     *
     * @return array[]
     */
    public function invocationProvider(): array
    {
        return [
            'simple' => [
                [1, 2],
                'Documents',
                'Test',
                3,
                [
                    ['id' => 'first-user', 'type' => 'users'],
                    ['id' => 'title-one', 'type' => 'documents'],
                ],
            ],
            'duplicate entry' => [
                [1, 5],
                'Locations',
                'InverseAnotherTest',
                8,
                [
                    [
                        'id' => 'first-user',
                        'type' => 'users',
                        '_meta' => ['relation' => ['params' => ['name' => 'foo']]],
                    ],
                    [
                        'id' => 'second-user',
                        'type' => 'users',
                        '_meta' => ['relation' => ['params' => ['name' => 'bar']]],
                    ],
                    [
                        'id' => 1,
                        'type' => 'users',
                        '_meta' => ['relation' => ['params' => ['name' => 'baz']]],
                    ],
                ],
            ],
            'nothing to do' => [
                [1, 5],
                'Locations',
                'InverseAnotherTest',
                8,
                [
                    [
                        'id' => '1',
                        'type' => 'users',
                        '_meta' => ['relation' => ['params' => ['name' => 'foo']]],
                    ],
                    [
                        'id' => '5',
                        'type' => 'users',
                        '_meta' => ['relation' => ['params' => ['name' => 'bar']]],
                    ],
                    [
                        'id' => 1,
                        'type' => 'users',
                        '_meta' => ['relation' => ['params' => ['name' => 'baz']]],
                    ],
                ],
            ],
            'uname not found' => [
                new RecordNotFoundException('Record not found in table "users"'),
                'Documents',
                'Test',
                3,
                [
                    ['id' => 2, 'type' => 'documents'],
                    ['id' => Text::uuid(), 'type' => 'users'],
                ],
            ],
            'not an object table' => [
                [1, 2],
                'Users',
                'Roles',
                1,
                [
                    ['id' => 1, 'type' => 'roles'],
                    ['id' => 2, 'type' => 'roles'],
                ],
            ],
        ];
    }

    /**
     * Test invocation of command.
     *
     * @param int[]|\Exception Expected result.
     * @param string $table Table to use.
     * @param string $association Association to use.
     * @param int $id Entity ID to update relations for.
     * @param int|int[]|null $data Related entity(-ies).
     * @return void
     * @dataProvider invocationProvider()
     */
    public function testInvocation($expected, string $table, string $association, int $id, $data): void
    {
        if ($expected instanceof Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        LoggedUser::setUser(['id' => 1, 'roles' => [['id' => 1]]]);
        $request = new ServerRequest();
        $request = $request->withParsedBody($data);
        $association = $this->getTableLocator()->get($table)->getAssociation($association);
        $parentAction = new SetRelatedObjectsAction(compact('association'));
        $action = new UpdateRelatedAction(['action' => $parentAction, 'request' => $request]);

        $action(['primaryKey' => $id]);
        if ($expected instanceof Exception) {
            return;
        }

        $matching = [];
        if ($data !== null) {
            $matching = Hash::extract(
                $association->getSource()
                    ->get($id, ['contain' => [$association->getName()]])
                    ->get($association->getProperty()),
                '{*}.id'
            );
        }

        static::assertEquals($expected, $matching);
    }
}
