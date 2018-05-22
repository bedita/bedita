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

use BEdita\Core\Model\Action\AddAssociatedAction;
use Cake\Core\Exception\Exception;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;

/**
 * @covers \BEdita\Core\Model\Action\AddAssociatedAction
 * @covers \BEdita\Core\Model\Action\UpdateAssociatedAction
 */
class AddAssociatedActionTest extends TestCase
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
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        TableRegistry::get('FakeTags')
            ->belongsToMany('FakeArticles', [
                'joinTable' => 'fake_articles_tags',
            ]);

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
            'nothingToDo' => [
                0,
                'FakeTags',
                'FakeArticles',
                1,
                null,
            ],
            'alreadyPresent' => [
                0,
                'FakeTags',
                'FakeArticles',
                1,
                1,
            ],
            'belongsToMany' => [
                1,
                'FakeTags',
                'FakeArticles',
                1,
                2,
            ],
            'hasMany' => [
                2,
                'FakeAnimals',
                'FakeArticles',
                2,
                [1, 2],
            ],
            'belongsTo' => [
                new \RuntimeException(
                    'Unable to add additional links with association of type "Cake\ORM\Association\BelongsTo"'
                ),
                'FakeArticles',
                'FakeAnimals',
                1,
                [1, 2],
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
        $action = new AddAssociatedAction(compact('association'));

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
        $relCount = is_array($related) ? count($related) : (empty($related) ? 0 : 1);
        static::assertEquals($relCount, $count);
    }

    /**
     * Test that an exception is raised with details about the validation error.
     *
     * @return void
     *
     * @expectedException \Cake\Network\Exception\BadRequestException
     * @expectedExceptionCode 400
     */
    public function testInvocationWithLinkErrors()
    {
        try {
            $table = TableRegistry::get('FakeArticles');
            /** @var \Cake\ORM\Association\BelongsToMany $association */
            $association = $table->association('FakeTags');

            $association->junction()->rulesChecker()->add(
                function () {
                    return false;
                },
                'sampleRule',
                [
                    'errorField' => 'gustavo',
                    'message' => 'This is a sample error',
                ]
            );

            $entity = $table->get(1);
            $relatedEntities = $association->find()->toArray();

            $action = new AddAssociatedAction(compact('association'));
            $action(compact('entity', 'relatedEntities'));
        } catch (Exception $e) {
            $expected = [
                'title' => 'Error linking entities',
                'detail' => [
                    'gustavo' => [
                        'sampleRule' => 'This is a sample error',
                    ],
                ],
            ];

            static::assertSame($expected, $e->getAttributes());

            throw $e;
        }
    }
}
