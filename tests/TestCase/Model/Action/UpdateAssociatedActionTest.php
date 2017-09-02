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

namespace BEdita\API\Test\TestCase\Model\Action;

use BEdita\API\Model\Action\UpdateAssociatedAction;
use BEdita\Core\Model\Action\SetAssociatedAction;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\ServerRequest;
use Cake\Network\Exception\BadRequestException;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;

/**
 * @covers \BEdita\API\Model\Action\UpdateAssociatedAction
 */
class UpdateAssociatedActionTest extends TestCase
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
        /* @var \Cake\ORM\Association\BelongsToMany $association */
        $association = TableRegistry::get('FakeTags')->association('FakeArticles');
        $association->junction()
            ->getValidator()
            ->email('fake_params');

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
            'belongsToManyDuplicateEntry' => [
                1,
                'FakeTags',
                'FakeArticles',
                1,
                [
                    ['id' => 1],
                    ['id' => 2],
                    ['id' => 2],
                ],
            ],
            'belongsToManySingleDuplicateEntry' => [
                2,
                'FakeTags',
                'FakeArticles',
                1,
                [
                    ['id' => 2],
                    ['id' => 2],
                ],
            ],
            'belongsToManyEmpty' => [
                1,
                'FakeTags',
                'FakeArticles',
                1,
                [],
            ],
            'belongsToManyNothingToDo' => [
                0,
                'FakeTags',
                'FakeArticles',
                1,
                [
                    ['id' => 1],
                ],
            ],
            'hasManyNothingToDo' => [
                0,
                'FakeAnimals',
                'FakeArticles',
                1,
                [
                    ['id' => 1],
                    ['id' => 2],
                ],
            ],
            'unsupportedMultipleEntities' => [
                new \InvalidArgumentException(
                    'Unable to link multiple entities'
                ),
                'FakeArticles',
                'FakeAnimals',
                1,
                [
                    ['id' => 1],
                    ['id' => 2],
                ],
            ],
            'belongsTo' => [
                1,
                'FakeArticles',
                'FakeAnimals',
                1,
                [
                    'id' => 2,
                ],
            ],
            'belongsToNothingToDo' => [
                0,
                'FakeArticles',
                'FakeAnimals',
                1,
                [
                    'id' => 1,
                ],
            ],
            'missingEntity' => [
                new RecordNotFoundException('Record not found in table "fake_animals"'),
                'FakeArticles',
                'FakeAnimals',
                2,
                [
                    'id' => 99,
                ],
            ],
            'belongsToMany with parameters' => [
                2,
                'FakeTags',
                'FakeArticles',
                1,
                [
                    [
                        'id' => 2,
                        '_meta' => [
                            'relation' => [
                                'fake_params' => 'gustavo.supporto@example.org',
                            ],
                        ],
                    ],
                ],
            ],
            'belongsToMany invalid parameters' => [
                new BadRequestException([
                    'title' => 'Bad data',
                ]),
                'FakeTags',
                'FakeArticles',
                1,
                [
                    [
                        'id' => 2,
                        '_meta' => [
                            'relation' => [
                                'fake_params' => 'not an email',
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test invocation of command.
     *
     * @param bool|\Exception Expected result.
     * @param string $table Table to use.
     * @param string $association Association to use.
     * @param int $id Entity ID to update relations for.
     * @param int|int[]|null $data Related entity(-ies).
     * @return void
     *
     * @dataProvider invocationProvider()
     */
    public function testInvocation($expected, $table, $association, $id, $data)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $request = new ServerRequest();
        $request = $request->withParsedBody($data);
        $association = TableRegistry::get($table)->association($association);
        $parentAction = new SetAssociatedAction(compact('association'));
        $action = new UpdateAssociatedAction(['action' => $parentAction, 'request' => $request]);

        $result = $action(['primaryKey' => $id]);

        $count = 0;
        if ($data !== null) {
            $count = $association->getTarget()->find()
                ->matching(
                    Inflector::camelize($association->getSource()->getTable()),
                    function (Query $query) use ($association, $id) {
                        return $query->where([
                            $association->getSource()->aliasField($association->getSource()->getPrimaryKey()) => $id,
                        ]);
                    }
                )
                ->count();
        }

        static::assertEquals($expected, $result);
        static::assertEquals(count(array_unique($data, SORT_REGULAR)), $count);
    }
}
