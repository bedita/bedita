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

use BEdita\Core\Exception\InvalidDataException;
use BEdita\Core\Model\Action\AddAssociatedAction;
use Cake\Core\Exception\CakeException as Exception;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;

/**
 * @covers \BEdita\Core\Model\Action\AddAssociatedAction
 * @covers \BEdita\Core\Model\Action\UpdateAssociatedAction
 * @covers \BEdita\Core\Model\Action\AssociatedTrait
 */
class AddAssociatedActionTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.FakeAnimals',
        'plugin.BEdita/Core.FakeArticles',
        'plugin.BEdita/Core.FakeTags',
        'plugin.BEdita/Core.FakeArticlesTags',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        TableRegistry::getTableLocator()->get('FakeTags')
            ->belongsToMany('FakeArticles', [
                'joinTable' => 'fake_articles_tags',
            ]);

        TableRegistry::getTableLocator()->get('FakeArticles')
            ->belongsToMany('FakeTags', [
                'joinTable' => 'fake_articles_tags',
            ])
            ->getSource()
            ->belongsTo('FakeAnimals');

        TableRegistry::getTableLocator()->get('FakeAnimals')
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
     * @param int|\Exception Expected result.
     * @param string $table Table to use.
     * @param string $association Association to use.
     * @param int $entity Entity to update relations for.
     * @param int|int[]|null $related Related entity(-ies).
     * @return void
     * @dataProvider invocationProvider()
     */
    public function testInvocation($expected, $table, $association, $entity, $related)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $association = TableRegistry::getTableLocator()->get($table)->getAssociation($association);
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

        $beforeSaveTriggered = $afterSaveTriggered = 0;
        $action->getEventManager()->on('Associated.beforeSave', function (Event $event) use ($association, $entity, $relatedEntities, &$beforeSaveTriggered) {
            $beforeSaveTriggered++;
            static::assertSame('Associated.beforeSave', $event->getName());
            static::assertSame('add', $event->getData('action'));
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
            static::assertSame('add', $event->getData('action'));
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
     */
    public function testInvocationWithLinkErrors()
    {
        $this->expectException(InvalidDataException::class);
        $this->expectExceptionCode('400');
        try {
            $table = TableRegistry::getTableLocator()->get('FakeArticles');
            /** @var \Cake\ORM\Association\BelongsToMany $association */
            $association = $table->getAssociation('FakeTags');

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
                'detail' => [
                    'gustavo' => [
                        'sampleRule' => 'This is a sample error',
                    ],
                ],
            ];

            static::assertSame($expected, $e->getAttributes());
            static::assertSame('Error linking entities', $e->getMessage());

            throw $e;
        }
    }

    /**
     * Test that join data are correctly updated when needed.
     *
     * @return void
     */
    public function testInvocationWithJoinData()
    {
        $expected = [
            1 => '1',
            2 => '2',
        ];

        /** @var \Cake\ORM\Association\BelongsToMany $association */
        $association = TableRegistry::getTableLocator()->get('FakeArticles')->getAssociation('FakeTags');
        $action = new AddAssociatedAction(compact('association'));

        $entity = $association->getSource()->get(1, ['contain' => [$association->getName()]]);
        $relatedEntities = array_map(
            function ($id) use ($association) {
                $relatedEntity = $association->getTarget()->get($id);
                $relatedEntity->_joinData = $association->junction()->newEntity([
                    'fake_params' => (string)$id,
                ]);

                return $relatedEntity;
            },
            [1, 2]
        );

        $result = $action(compact('entity', 'relatedEntities'));

        $actual = $association->junction()
            ->find('list', [
                'keyField' => $association->getTargetForeignKey(),
                'valueField' => 'fake_params',
            ])
            ->toArray();

        static::assertEquals(count($expected), $result);
        static::assertEquals($expected, $actual);
    }
}
