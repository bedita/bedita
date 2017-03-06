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

use BEdita\Core\Model\Action\ListAssociatedAction;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\InvalidPrimaryKeyException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @covers \BEdita\Core\Model\Action\ListAssociatedAction
 */
class ListAssociatedActionTest extends TestCase
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
            'belongsToMany' => [
                [
                    ['id' => 1],
                ],
                'FakeTags',
                'FakeArticles',
                1,
            ],
            'belongsToManyMissing' => [
                new RecordNotFoundException('Record not found in table "fake_tags"'),
                'FakeTags',
                'FakeArticles',
                99,
            ],
            'invalidPrimaryKey' => [
                new InvalidPrimaryKeyException('Record not found in table "fake_tags" with primary key [\'invalid\', \'pk\']'),
                'FakeTags',
                'FakeArticles',
                ['invalid', 'pk'],
            ],
            'hasMany' => [
                [
                    ['id' => 1],
                    ['id' => 2],
                ],
                'FakeAnimals',
                'FakeArticles',
                1,
            ],
            'hasManyNoResults' => [
                [],
                'FakeAnimals',
                'FakeArticles',
                2,
            ],
            'belongsTo' => [
                [
                    'id' => 1,
                ],
                'FakeArticles',
                'FakeAnimals',
                1,
            ],
        ];
    }

    /**
     * Test invocation of command.
     *
     * @param array|\Exception $expected Expected result.
     * @param string $table Table to use.
     * @param string $association Association to use.
     * @param int $id Entity ID to list relations for.
     * @return void
     *
     * @dataProvider invocationProvider()
     */
    public function testInvocation($expected, $table, $association, $id)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $association = TableRegistry::get($table)->association($association);
        $action = new ListAssociatedAction(compact('association'));

        $result = $action(['primaryKey' => $id]);

        if ($result instanceof Query) {
            $result = $result->enableHydration(false)->toArray();
        } elseif ($result instanceof EntityInterface) {
            $result = $result->toArray();
        }

        $this->assertEquals($expected, $result);
    }
}
