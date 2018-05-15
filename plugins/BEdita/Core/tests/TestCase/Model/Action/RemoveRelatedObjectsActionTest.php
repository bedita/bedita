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
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;

/**
 * @coversDefaultClass \BEdita\Core\Model\Action\RemoveRelatedObjectsAction
 */
class RemoveRelatedObjectsActionTest extends TestCase
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
        'plugin.BEdita/Core.properties',
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.roles_users',
        'plugin.BEdita/Core.trees',
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
     * @param bool|\Exception Expected result.
     * @param string $table Table to use.
     * @param string $association Association to use.
     * @param int $entity Entity to update relations for.
     * @param int|int[]|null $related Related entity(-ies).
     * @return void
     *
     * @dataProvider invocationProvider()
     * @covers ::update()
     * @covers \BEdita\Core\Model\Action\UpdateRelatedObjectsAction::execute()
     * @covers \BEdita\Core\Model\Action\UpdateRelatedObjectsAction::getEntity()
     */
    public function testInvocation($expected, $table, $association, $entity, $related)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $association = TableRegistry::get($table)->association($association);
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

        $result = $action(compact('entity', 'relatedEntities'));

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
