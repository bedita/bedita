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

namespace BEdita\Core\Test\TestCase\Model\Action;

use BEdita\Core\Model\Action\ListRelatedObjectsAction;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;

/**
 * @covers \BEdita\Core\Model\Action\ListRelatedObjectsAction
 */
class ListRelatedObjectsActionTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.object_relations',
        'plugin.BEdita/Core.profiles',
    ];

    /**
     * Data provider for `testInvocation` test case.
     *
     * @return array
     */
    public function invocationProvider()
    {
        return [
            [
                [
                    [
                        'id' => 4,
                        'type' => 'profiles',
                        '_joinData' => [
                            'priority' => 1,
                            'inv_priority' => 2,
                            'params' => null,
                        ],
                    ],
                    [
                        'id' => 3,
                        'type' => 'documents',
                        '_joinData' => [
                            'priority' => 2,
                            'inv_priority' => 1,
                            'params' => null,
                        ],
                    ],
                ],
                'Documents',
                'test',
                2,
            ],
            [
                [
                    [
                        'id' => 4,
                        'type' => 'profiles',
                        '_joinData' => [
                            'priority' => 1,
                            'inv_priority' => 1,
                            'params' => null,
                        ],
                    ],
                ],
                'Documents',
                'test',
                3,
            ],
            [
                [
                    [
                        'id' => 3,
                        'type' => 'documents',
                        '_joinData' => [
                            'priority' => 1,
                            'inv_priority' => 1,
                            'params' => null,
                        ],
                    ],
                    [
                        'id' => 2,
                        'type' => 'documents',
                        '_joinData' => [
                            'priority' => 1,
                            'inv_priority' => 2,
                            'params' => null,
                        ],
                    ],
                ],
                'Profiles',
                'inverse_test',
                4,
            ],
        ];
    }

    /**
     * Test command invocation.
     *
     * @param array $expected Expected result.
     * @param string $objectType Object type name.
     * @param string $relation Relation name.
     * @param int $id ID.
     * @return void
     *
     * @dataProvider invocationProvider()
     */
    public function testInvocation($expected, $objectType, $relation, $id)
    {
        $alias = Inflector::camelize(Inflector::underscore($relation));
        $association = TableRegistry::get($objectType)->association($alias);
        $action = new ListRelatedObjectsAction(compact('association'));

        $result = $action(['primaryKey' => $id, 'list' => true]);
        $result = json_decode(json_encode($result->toArray()), true);

        static::assertEquals($expected, $result);
    }
}
