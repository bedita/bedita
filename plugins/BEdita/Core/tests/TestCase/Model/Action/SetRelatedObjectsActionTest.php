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

use BEdita\Core\Model\Action\SetRelatedObjectsAction;
use BEdita\Core\ORM\Association\RelatedTo;
use BEdita\Core\Utility\LoggedUser;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;

/**
 * @covers \BEdita\Core\Model\Action\SetRelatedObjectsAction
 * @covers \BEdita\Core\Model\Action\UpdateRelatedObjectsAction
 * @covers \BEdita\Core\Model\Action\AssociatedTrait
 */
class SetRelatedObjectsActionTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        LoggedUser::setUserAdmin();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();

        LoggedUser::resetUser();
    }

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
                [3, 4],
                'Documents',
                'test',
                2,
                [],
            ],
            'remove' => [
                [3],
                'Documents',
                'test',
                2,
                [
                    4 => [
                        'priority' => 1,
                        'inv_priority' => 2,
                        'params' => null,
                    ],
                ],
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
                    4 => [
                        'priority' => 1,
                        'inv_priority' => 1,
                        'params' => null,
                    ],
                ],
            ],
            'update' => [
                [2, 3],
                'Profiles',
                'inverse_test',
                4,
                [
                    3 => [
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
                    4 => [
                        'priority' => 1,
                        'inv_priority' => 1,
                        'params' => null,
                    ],
                ],
            ],
        ];
    }

    /**
     * Test invocation of command.
     *
     * @param int[]|\Exception Expected result.
     * @param string $objectType Table to use.
     * @param string $relation Association to use.
     * @param int $id Entity to update relations for.
     * @param int[] $related Related entity(-ies).
     * @return void
     * @dataProvider invocationProvider()
     */
    public function testInvocation($expected, $objectType, $relation, $id, array $related)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $alias = Inflector::camelize(Inflector::underscore($relation));
        $association = TableRegistry::getTableLocator()->get($objectType)->getAssociation($alias);
        $action = new SetRelatedObjectsAction(compact('association'));

        $entity = $association->getSource()->get($id);
        $relatedEntities = [];
        if (!empty($related)) {
            $relatedEntities = $association->getTarget()->find()
                ->where(function (QueryExpression $exp) use ($association, $related) {
                    return $exp->in($association->getTarget()->getPrimaryKey(), array_keys($related));
                })
                ->all()
                ->map(function (EntityInterface $entity) use ($association, $related) {
                    $data = $related[$entity->id];
                    if (!empty($data) && $association instanceof RelatedTo) {
                        $entity->set('_joinData', $association->junction()->newEntity($data));
                    }

                    return $entity;
                })
                ->toArray();
        }

        $beforeSaveTriggered = $afterSaveTriggered = 0;
        $action->getEventManager()->on('Associated.beforeSave', function (Event $event) use ($association, $entity, $relatedEntities, &$beforeSaveTriggered) {
            $beforeSaveTriggered++;
            static::assertSame('Associated.beforeSave', $event->getName());
            static::assertSame('set', $event->getData('action'));
            static::assertSame($association, $event->getData('association'));
            static::assertSame($entity, $event->getData('entity'));
            static::assertInstanceOf(\ArrayObject::class, $event->getData('relatedEntities'));
            $rel = is_object($relatedEntities) ? [$relatedEntities] : (array)$relatedEntities;
            static::assertSameSize($rel, $event->getData('relatedEntities'));
            $n = count($rel);
            for ($i = 0; $i < $n; $i++) {
                static::assertSame($rel[$i], $event->getData('relatedEntities')[$i]);
            }
        });
        $action->getEventManager()->on('Associated.afterSave', function (Event $event) use ($association, $entity, $related, &$afterSaveTriggered) {
            $afterSaveTriggered++;
            static::assertSame('Associated.afterSave', $event->getName());
            static::assertSame('set', $event->getData('action'));
            static::assertSame($association, $event->getData('association'));
            static::assertSame($entity, $event->getData('entity'));
            static::assertSameSize($related, $event->getData('relatedEntities'));
        });

        $result = $action(compact('entity', 'relatedEntities'));
        static::assertSame(1, $beforeSaveTriggered);
        static::assertSame(1, $afterSaveTriggered);

        sort($expected);
        sort($result);
        static::assertEquals($expected, $result, '');
        static::assertEqualsCanonicalizing($expected, $result, '');
        static::assertEqualsWithDelta($expected, $result, 0, '');
    }

    /**
     * Test invocation of command with fallback to default action.
     *
     * @return void
     */
    public function testInvocationFallback()
    {
        $association = TableRegistry::getTableLocator()->get('Users')->getAssociation('Roles');
        $entity = $association->getSource()->get(1);
        $relatedEntities = $association->getTarget()->find()->toArray();
        LoggedUser::setUserAdmin();
        $action = new SetRelatedObjectsAction(compact('association'));
        $result = $action(compact('entity', 'relatedEntities'));

        static::assertSame(1, $result);
    }

    /**
     * Test that setting related entities loaded from another entity works.
     *
     * @return void
     */
    public function testSetEntitiesRelatedToOtherObject(): void
    {
        $Documents = TableRegistry::getTableLocator()->get('Documents');
        $relatedEntities = $Documents->get(3, ['contain' => ['Test']])->get('test');

        $entity = $Documents->get(2, ['contain' => ['Test']]);
        static::assertCount(2, $entity->get('test'));

        $association = $Documents->getAssociation('Test');
        $action = new SetRelatedObjectsAction(compact('association'));
        $action(compact('entity', 'relatedEntities'));

        $entity = $Documents->get(2, ['contain' => ['Test']]);
        static::assertCount(1, $entity->get('test'));

        $expected = collection($relatedEntities)->sortBy('id')->extract('id')->toList();
        $actual = collection($entity->get('test'))->sortBy('id')->extract('id')->toList();

        static::assertEquals($expected, $actual);
    }
}
