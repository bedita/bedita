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

use BEdita\Core\Model\Action\SetAssociatedAction;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;

/**
 * @covers \BEdita\Core\Model\Action\SetAssociatedAction
 * @covers \BEdita\Core\Model\Action\UpdateAssociatedAction
 */
class SetAssociatedActionTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.fake_animals',
        'plugin.BEdita/Core.fake_articles',
        'plugin.BEdita/Core.fake_tags',
        'plugin.BEdita/Core.fake_articles_tags',
        'plugin.BEdita/Core.fake_labels',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        TableRegistry::get('FakeLabels')
            ->belongsTo('FakeTags');

        TableRegistry::get('FakeTags')
            ->belongsToMany('FakeArticles', [
                'joinTable' => 'fake_articles_tags',
            ])
            ->getSource()
            ->hasOne('FakeLabels');

        TableRegistry::get('FakeArticles')
            ->belongsToMany('FakeTags', [
                'joinTable' => 'fake_articles_tags',
            ])
            ->getSource()
            ->belongsTo('FakeAnimals');

        TableRegistry::get('FakeAnimals')
            ->hasMany('FakeArticles');
    }

    /**
     * Data provider for `testInvocation` test case.
     *
     * @return array
     */
    public function invocationProvider()
    {
        return [
            'belongsToManyEmpty' => [
                1,
                'FakeTags',
                'FakeArticles',
                1,
                null,
            ],
            'belongsToManyNothingToDo' => [
                0,
                'FakeTags',
                'FakeArticles',
                1,
                1,
            ],
            'hasMany' => [
                2,
                'FakeAnimals',
                'FakeArticles',
                2,
                [1, 2],
            ],
            'hasManyNothingToDo' => [
                0,
                'FakeAnimals',
                'FakeArticles',
                1,
                [1, 2],
            ],
            'unsupportedMultipleEntities' => [
                new \InvalidArgumentException(
                    'Unable to link multiple entities'
                ),
                'FakeArticles',
                'FakeAnimals',
                1,
                [1, 2],
            ],
            'belongsToEmpty' => [
                1,
                'FakeArticles',
                'FakeAnimals',
                1,
                null,
            ],
            'belongsTo' => [
                1,
                'FakeArticles',
                'FakeAnimals',
                1,
                2,
            ],
            'belongsToNothingToDo' => [
                0,
                'FakeArticles',
                'FakeAnimals',
                1,
                1,
            ],
            'hasOne' => [
                1,
                'FakeTags',
                'FakeLabels',
                1,
                1,
            ],
            'hasOneEmpty' => [
                0,
                'FakeTags',
                'FakeLabels',
                1,
                null,
            ],
            'hasOneNothingToDo' => [
                0,
                'FakeTags',
                'FakeLabels',
                1,
                2,
            ],
            'hasOneNothingToDoEmpty' => [
                0,
                'FakeTags',
                'FakeLabels',
                2,
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
     */
    public function testInvocation($expected, $table, $association, $entity, $related)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $association = TableRegistry::get($table)->association($association);
        $action = new SetAssociatedAction(compact('association'));

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
                ->matching(
                    Inflector::camelize($association->getSource()->getTable()),
                    function (Query $query) use ($association, $entity) {
                        return $query->where([
                            $association->getSource()->aliasField($association->getSource()->getPrimaryKey()) => $entity->id,
                        ]);
                    }
                )
                ->count();
        }

        static::assertEquals($expected, $result);
        static::assertEquals(count($related), $count);
    }
}
