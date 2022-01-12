<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
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
use Cake\Core\Exception\Exception;
use Cake\Event\Event;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;

/**
 * @covers \BEdita\Core\Model\Action\SetAssociatedAction
 * @covers \BEdita\Core\Model\Action\UpdateAssociatedAction
 * @covers \BEdita\Core\Model\Action\AssociatedTrait
 */
class SetAssociatedActionTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.FakeAnimals',
        'plugin.BEdita/Core.FakeArticles',
        'plugin.BEdita/Core.FakeTags',
        'plugin.BEdita/Core.FakeArticlesTags',
        'plugin.BEdita/Core.FakeLabels',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        TableRegistry::getTableLocator()->get('FakeLabels')
            ->belongsTo('FakeTags');

        TableRegistry::getTableLocator()->get('FakeTags')
            ->belongsToMany('FakeArticles', [
                'joinTable' => 'fake_articles_tags',
            ])
            ->getSource()
            ->hasOne('FakeLabels');

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
            'hasOneEmptyArray' => [
                0,
                'FakeTags',
                'FakeLabels',
                1,
                [],
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
     * @param int|\Exception $expected Expected result.
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

        $association = TableRegistry::getTableLocator()->get($table)->getAssociation($association);
        $toMany = $association instanceof BelongsToMany || $association instanceof HasMany;
        $action = new SetAssociatedAction(compact('association'));

        $entity = $association->getSource()->get($entity, ['contain' => [$association->getName()]]);

        $relatedEntities = null;
        if (is_int($related)) {
            $relatedEntities = $association->getTarget()->get($related);
        } elseif (is_array($related)) {
            if (empty($related)) {
                $relatedEntities = [];
            } else {
                $relatedEntities = $association->getTarget()->find()
                    ->where([
                        $association->getTarget()->getPrimaryKey() . ' IN' => $related,
                    ])
                    ->toArray();
            }
        }

        $beforeSaveTriggered = $afterSaveTriggered = 0;
        $action->getEventManager()->on('Associated.beforeSave', function (Event $event) use ($association, $toMany, $entity, $relatedEntities, &$beforeSaveTriggered) {
            $beforeSaveTriggered++;
            static::assertSame('Associated.beforeSave', $event->getName());
            static::assertSame('set', $event->getData('action'));
            static::assertSame($association, $event->getData('association'));
            static::assertSame($entity, $event->getData('entity'));
            static::assertInstanceOf(\ArrayObject::class, $event->getData('relatedEntities'));
            $rel = is_object($relatedEntities) || !$toMany ? [$relatedEntities] : (array)$relatedEntities;
            static::assertSameSize($rel, $event->getData('relatedEntities'));
            $n = count($rel);
            for ($i = 0; $i < $n; $i++) {
                static::assertSame($rel[$i] ?: null, $event->getData('relatedEntities')[$i]);
            }
        });
        $action->getEventManager()->on('Associated.afterSave', function (Event $event) use ($association, $toMany, $entity, $related, &$afterSaveTriggered) {
            $afterSaveTriggered++;
            static::assertSame('Associated.afterSave', $event->getName());
            static::assertSame('set', $event->getData('action'));
            static::assertSame($association, $event->getData('association'));
            static::assertSame($entity, $event->getData('entity'));
            static::assertSameSize($toMany ? (array)$related : [$related], $event->getData('relatedEntities'));
        });

        $result = $action(compact('entity', 'relatedEntities'));
        static::assertSame(1, $beforeSaveTriggered);
        static::assertSame($expected === 0 && !$toMany ? 0 : 1, $afterSaveTriggered);

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
        $this->expectException(\Cake\Http\Exception\BadRequestException::class);
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

            $action = new SetAssociatedAction(compact('association'));
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

    /**
     * Data provider for `testInvocationWithValidationErrors` test case.
     *
     * @return array
     */
    public function invocationWithValidationErrorsProvider()
    {
        return [
            'new link' => [1, 2],
            'existing link' => [1, 1],
        ];
    }

    /**
     * Test that an exception is raised with details about the validation error.
     *
     * @param int $source Source entity ID.
     * @param int $target Target entity ID.
     * @return void
     *
     * @dataProvider invocationWithValidationErrorsProvider()
     */
    public function testInvocationWithValidationErrors($source, $target)
    {
        $this->expectException(\Cake\Http\Exception\BadRequestException::class);
        $this->expectExceptionCode('400');
        $field = 'some_field';
        $validationErrorMessage = 'Invalid email';

        try {
            $table = TableRegistry::getTableLocator()->get('FakeArticles');
            /** @var \Cake\ORM\Association\BelongsToMany $association */
            $association = $table->getAssociation('FakeTags');

            $association->junction()->getValidator()
                ->email($field, false, $validationErrorMessage);

            $entity = $table->get($source);
            $relatedEntities = [
                $association->getTarget()->get($target)
                    ->set('_joinData', [$field => 'not-an-email']),
            ];

            $action = new SetAssociatedAction(compact('association'));
            $action(compact('entity', 'relatedEntities'));
        } catch (Exception $e) {
            $expected = [
                'title' => 'Invalid data',
                'detail' => [
                    $field => [
                        'email' => $validationErrorMessage,
                    ],
                ],
            ];

            static::assertSame($expected, $e->getAttributes());

            throw $e;
        }
    }

    /**
     * Data provider for testInvocationOKWithJoinData
     *
     * @return array
     */
    public function joinDataProvider()
    {
        return [
            'entity' => [
                1,
                2,
                true,
            ],
            'array' => [
                1,
                2,
                false,
            ],
        ];
    }

    /**
     * Test that saving an assocation with an entity in `_joinData` works rightly.
     *
     * @param int $articleId Article entity id.
     * @param int $tagId Tag entity id.
     * @param bool $joinDataAsEntity It says if join data is to treat as entity.
     * @return void
     *
     * @dataProvider joinDataProvider()
     */
    public function testInvocationOKWithJoinData($articleId, $tagId, $joinDataAsEntity)
    {
        $expected = 'Coffee please!';

        $table = TableRegistry::getTableLocator()->get('FakeArticles');
        /** @var \Cake\ORM\Association\BelongsToMany $association */
        $association = $table->getAssociation('FakeTags');
        $entity = $table->get($articleId);

        $joinData = ['fake_params' => $expected];
        if ($joinDataAsEntity) {
            $joinData = $association->junction()->newEntity($joinData);
        }

        // replace associations
        $relatedEntities = [
            $association->getTarget()
                ->get($tagId)
                ->set('_joinData', $joinData),
        ];

        $action = new SetAssociatedAction(compact('association'));
        $action(compact('entity', 'relatedEntities'));

        $joinEntity = $association->junction()
            ->find()
            ->where([
                'fake_article_id' => $articleId,
                'fake_tag_id' => $tagId,
                ])
            ->first();

        $countAssociations = $association->junction()
            ->find()
            ->where(['fake_article_id' => $articleId])
            ->count();

        static::assertSame(1, $countAssociations);
        static::assertSame($expected, $joinEntity->get('fake_params'));
    }
}
