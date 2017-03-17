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

namespace BEdita\Core\Test\TestCase\Model\Action;

use BEdita\Core\Model\Action\AddRelatedObjectsAction;
use BEdita\Core\ORM\Association\RelatedTo;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;

/**
 * @covers \BEdita\Core\Model\Action\AddRelatedObjectsAction
 * @covers \BEdita\Core\Model\Action\UpdateRelatedObjectsAction
 */
class AddRelatedObjectsActionTest extends TestCase
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
            'nothingToDo' => [
                [],
                'Documents',
                'test',
                2,
                [
                    3 => [
                        'priority' => 2,
                        'inv_priority' => 1,
                        'params' => null,
                    ],
                    4 => [
                        'priority' => 1,
                        'inv_priority' => 2,
                        'params' => null,
                    ],
                ],
            ],
            'empty' => [
                [],
                'Documents',
                'test',
                2,
                [],
            ],
            'add' => [
                [2],
                'Documents',
                'test',
                3,
                [
                    2 => [
                        'priority' => 2,
                        'inv_priority' => 1,
                        'params' => null,
                    ],
                ],
            ],
            'update' => [
                [4],
                'Documents',
                'test',
                3,
                [
                    4 => [
                        'priority' => 1,
                        'inv_priority' => 1,
                        'params' => [
                            'key' => 'value',
                        ],
                    ],
                ],
            ],
            'noJoinData' => [
                [2],
                'Documents',
                'test',
                3,
                [
                    2 => null,
                ],
            ],
        ];
    }

    /**
     * Test invocation of command.
     *
     * @param bool|\Exception Expected result.
     * @param string $objectType Table to use.
     * @param string $relation Association to use.
     * @param int $id Entity to update relations for.
     * @param int[] $related Related entity(-ies).
     * @return void
     *
     * @dataProvider invocationProvider()
     */
    public function testInvocation($expected, $objectType, $relation, $id, array $related)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $alias = Inflector::camelize(Inflector::underscore($relation));
        $association = TableRegistry::get($objectType)->association($alias);
        $action = new AddRelatedObjectsAction(compact('association'));

        $entity = $association->getSource()->get($id);
        $relatedEntities = [];
        if (!empty($related)) {
            $relatedEntities = $association->getTarget()->find()
                ->where(function (QueryExpression $exp) use ($association, $related) {
                    return $exp->in($association->getTarget()->getPrimaryKey(), array_keys($related));
                })
                ->map(function (EntityInterface $entity) use ($association, $related) {
                    $data = $related[$entity->id];
                    if (!empty($data) && $association instanceof RelatedTo) {
                        $entity->set('_joinData', $association->junction()->newEntity($data));
                    }

                    return $entity;
                })
                ->toArray();
        }

        $result = $action(compact('entity', 'relatedEntities'));

        static::assertEquals($expected, $result, '', 0, 10, true);
    }
}
