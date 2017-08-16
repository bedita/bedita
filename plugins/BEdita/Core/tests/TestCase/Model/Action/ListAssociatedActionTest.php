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
use BEdita\Core\ORM\Inheritance\Table;
use Cake\Datasource\Exception\InvalidPrimaryKeyException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Association;
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
        'plugin.BEdita/Core.fake_mammals',
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

        TableRegistry::get('FakeMammals', ['className' => Table::class])
            ->extensionOf('FakeAnimals');

        TableRegistry::get('FakeMammalArticles')
            ->setTable('fake_articles')
            ->belongsTo('FakeMammals', ['foreignKey' => 'fake_animal_id']);
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
            'missing primaryKey' => [
                new \InvalidArgumentException('Missing required option "primaryKey"'),
                'FakeTags',
                'FakeArticles',
                null,
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
            'inheritedTables' => [
                [
                    'id' => 1,
                    'name' => 'cat',
                    'legs' => 4,
                    'subclass' => 'Eutheria',
                ],
                'FakeMammalArticles',
                'FakeMammals',
                1,
                [
                    'list' => false,
                ],
            ],
            'only' => [
                [
                    ['id' => 1],
                ],
                'FakeAnimals',
                'FakeArticles',
                1,
                [
                    'list' => true,
                    'only' => 1,
                ],
            ],
            'joinData' => [
                [
                    [
                        'id' => 1,
                        '_joinData' => [
                            'id' => 1,
                            'fake_article_id' => 1,
                            'fake_tag_id' => 1,
                            'fake_params' => null,
                        ],
                    ],
                ],
                'FakeTags',
                'FakeArticles',
                1,
                [
                    'list' => true,
                    'joinData' => true,
                ],
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
     * @param array $options Additional options for action.
     * @return void
     *
     * @dataProvider invocationProvider()
     */
    public function testInvocation($expected, $table, $association, $id, array $options = null)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        if ($options === null) {
            $options = ['list' => true];
        }
        $association = TableRegistry::get($table)->association($association);
        $action = new ListAssociatedAction(compact('association'));

        $result = $action(['primaryKey' => $id] + $options);
        $result = json_decode(json_encode($result->toArray()), true);

        static::assertEquals($expected, $result);
    }

    /**
     * Test invocation of command with an unknown association type.
     *
     * @return void
     *
     * @expectedException \LogicException
     * @expectedExceptionMessageRegExp /^Unknown association type "\w+"$/
     */
    public function testUnknownAssociationType()
    {
        $sourceTable = TableRegistry::get('FakeArticles');
        $association = static::getMockForAbstractClass(Association::class, [
            'TestAssociation',
            compact('sourceTable'),
        ]);

        $action = new ListAssociatedAction(compact('association'));
        $action(['primaryKey' => 1]);
    }
}
