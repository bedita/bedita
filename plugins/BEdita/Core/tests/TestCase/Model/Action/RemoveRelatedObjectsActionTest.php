<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Action;

use BEdita\Core\Model\Action\RemoveRelatedObjectsAction;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;

/**
 * @covers \BEdita\Core\Model\Action\RemoveRelatedObjectsAction
 * @covers \BEdita\Core\Model\Action\AssociatedTrait
 */
class RemoveRelatedObjectsActionTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.Trees',
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
                0,
                'Documents',
                'Test',
                1,
                null,
            ],
            'removingNotExistingRelation' => [
                0,
                'Documents',
                'Test',
                2,
                5,
            ],
            'removingOneRelation' => [
                1,
                'Documents',
                'Test',
                2,
                4,
            ],
            'removingAllRelations' => [
                2,
                'Documents',
                'Test',
                2,
                [4, 3],
            ],
            'removingParent' => [
                new \RuntimeException(
                    'Unable to remove existing links with association of type "Cake\ORM\Association\BelongsTo"'
                ),
                'Folders',
                'Parents',
                12,
                null,
            ],
        ];
    }

    /**
     * Test invocation of command.
     *
     * @param int|\Exception $expected Expected result.
     * @param string $table Table to use.
     * @param string $association Association to use.
     * @param int $entity Entity to update relations for.
     * @param int|int[]|null $related Related entity(-ies).
     * @return void
     *
     * @dataProvider invocationProvider()
     * @covers \BEdita\Core\Model\Action\UpdateRelatedObjectsAction::prepareData()
     */
    public function testInvocation($expected, $table, $association, $entity, $related)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $association = TableRegistry::getTableLocator()->get($table)->getAssociation($association);
        $action = new RemoveRelatedObjectsAction(compact('association'));

        $entity = $association->getSource()->get($entity, ['contain' => [$association->getName()]]);
        $relatedEntities = null;
        if (is_int($related)) {
            $relatedEntities = $association->getTarget()->get($related);
        } elseif (is_array($related)) {
            $relatedEntities = $association->getTarget()->find()
                ->where([
                    $association->getTarget()->getPrimaryKey() . ' IN' => $related,
                ])
                ->toArray();
        }

        $beforeSaveTriggered = $afterSaveTriggered = 0;
        $action->getEventManager()->on('Associated.beforeSave', function (Event $event) use ($association, $entity, $relatedEntities, &$beforeSaveTriggered) {
            $beforeSaveTriggered++;
            static::assertSame('Associated.beforeSave', $event->getName());
            static::assertSame('remove', $event->getData('action'));
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
        $action->getEventManager()->on('Associated.afterSave', function (Event $event) use ($association, $entity, $expected, &$afterSaveTriggered) {
            $afterSaveTriggered++;
            static::assertSame('Associated.afterSave', $event->getName());
            static::assertSame('remove', $event->getData('action'));
            static::assertSame($association, $event->getData('association'));
            static::assertSame($entity, $event->getData('entity'));
            static::assertCount($expected, $event->getData('relatedEntities'));
        });

        $result = $action(compact('entity', 'relatedEntities'));
        static::assertSame(1, $beforeSaveTriggered);
        static::assertSame(1, $afterSaveTriggered);

        $count = 0;
        if ($related !== null) {
            $count = $association->getTarget()->find()
                ->where([
                    $association->getTarget()->aliasField($association->getTarget()->getPrimaryKey()) . ' IN' => $related,
                ])
                ->matching(
                    Inflector::camelize($association->getSource()->getAlias()),
                    function (Query $query) use ($association, $entity) {
                        return $query->where([
                            $association->getSource()->aliasField($association->getSource()->getPrimaryKey()) => $entity->id,
                        ]);
                    }
                )
                ->count();
        }

        $this->assertEquals($expected, $result);
        $this->assertEquals(0, $count);
    }
}
