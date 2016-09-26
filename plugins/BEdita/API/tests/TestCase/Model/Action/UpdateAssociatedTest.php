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

use BEdita\API\Model\Action\UpdateAssociated;
use BEdita\Core\Model\Action\SetAssociated;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Network\Request;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;

/**
 * @covers \BEdita\API\Model\Action\UpdateAssociated
 */
class UpdateAssociatedTest extends TestCase
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
            ->source()
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
            $this->setExpectedException(get_class($expected), $expected->getMessage());
        }

        $request = new Request();
        $request->data = $data;
        $association = TableRegistry::get($table)->association($association);
        $parentAction = new SetAssociated($association);
        $action = new UpdateAssociated($parentAction, $request);

        $result = $action($id);

        $count = 0;
        if ($data !== null) {
            $count = $association->target()->find()
                ->matching(
                    Inflector::camelize($association->source()->table()),
                    function (Query $query) use ($association, $id) {
                        return $query->where([
                            $association->source()->aliasField($association->source()->primaryKey()) => $id,
                        ]);
                    }
                )
                ->count();
        }

        $this->assertEquals($expected, $result);
        $this->assertEquals(count(array_unique($data, SORT_REGULAR)), $count);
    }
}
