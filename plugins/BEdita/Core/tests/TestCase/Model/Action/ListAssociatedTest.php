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

use BEdita\Core\Model\Action\ListAssociated;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @covers \BEdita\Core\Model\Action\ListAssociated
 */
class ListAssociatedTest extends TestCase
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
     * {@inheritDoc}
     */
    public function tearDown()
    {
        TableRegistry::remove('FakeTags');
        TableRegistry::remove('FakeArticles');
        TableRegistry::remove('FakeAnimals');

        parent::tearDown();
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
     * @param array $expected Expected result.
     * @param string $table Table to use.
     * @param string $association Association to use.
     * @param int $id Entity ID to list relations for.
     * @return void
     *
     * @dataProvider invocationProvider()
     */
    public function testInvocation(array $expected, $table, $association, $id)
    {
        $association = TableRegistry::get($table)->association($association);
        $action = new ListAssociated($association);

        $result = json_decode(json_encode($action($id)), true);

        $this->assertEquals($expected, $result);
    }
}
