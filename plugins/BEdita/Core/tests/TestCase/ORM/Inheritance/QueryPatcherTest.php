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

namespace BEdita\Core\Test\TestCase\ORM\Inheritance;

use BEdita\Core\ORM\Inheritance\QueryPatcher;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\ORM\Inheritance\QueryPatcher} Test Case
 *
 * @coversDefaultClass \BEdita\Core\ORM\Inheritance\QueryPatcher
 */
class QueryPatcherTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.fake_animals',
        'plugin.BEdita/Core.fake_mammals',
        'plugin.BEdita/Core.fake_felines',
        'plugin.BEdita/Core.fake_articles',
    ];

    /**
     * Table FakeAnimals
     *
     * @var \Cake\ORM\Table
     */
    public $fakeAnimals;

    /**
     * Table FakeMammals
     *
     * @var \Cake\ORM\Table
     */
    public $fakeMammals;

    /**
     * Table FakeFelines
     *
     * @var \Cake\ORM\Table
     */
    public $fakeFelines;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->fakeAnimals = TableRegistry::get('FakeAnimals');
        $this->fakeAnimals->hasMany('FakeArticles');

        $this->fakeMammals = TableRegistry::get('FakeMammals');
        $this->fakeMammals->addBehavior('BEdita/Core.ClassTableInheritance', [
            'table' => [
                'tableName' => 'FakeAnimals'
            ]
        ]);

        $this->fakeFelines = TableRegistry::get('FakeFelines');
        $this->fakeFelines->addBehavior('BEdita/Core.ClassTableInheritance', [
            'table' => [
                'tableName' => 'FakeMammals'
            ]
        ]);
    }

    /**
     * testNewQueryPatcherWithWrongTable method
     *
     * @return void
     * @covers ::__construct()
     */
    public function testNewQueryPatcherWithWrongTable()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new QueryPatcher($this->fakeAnimals);
    }

    /**
     * Data provider for `testBuildContainString` test case.
     *
     * @return array
     */
    public function containStringProvider()
    {
        return [
            // expected, start value
            ['FakeMammals', 'FakeMammals'],
            ['FakeMammals.FakeAnimals', 'FakeAnimals'],
            ['FakeMammals.FakeAnimals.FakeArticles', 'FakeArticles'],
            [false, 'WrongAssociation']
        ];
    }

    /**
     * Test build contain string
     *
     * @param string|bool $expected Expected result.
     * @param string $tableName The Table name.
     * @return void
     *
     * @dataProvider containStringProvider
     * @covers ::buildContainString()
     */
    public function testBuildContainString($expected, $tableName)
    {
        $queryPatcher = new QueryPatcher($this->fakeFelines);
        $containString = $queryPatcher->buildContainString($tableName);
        $this->assertEquals($expected, $containString);
    }

    /**
     * Data provider for `testPatchContain` test case.
     *
     * @return array
     */
    public function patchContainProvider()
    {
        return [
            'empty' => [
                [
                    'FakeMammals' => [
                        'FakeAnimals' => []
                    ]
                ],
                []
            ],
            'nestedAssociation' => [
                [
                    'FakeMammals' => [
                        'FakeAnimals' => [
                            'FakeArticles' => []
                        ]
                    ]
                ],
                ['FakeArticles']
            ]
        ];
    }

    /**
     * testPatchContain
     *
     * @param array $expected Expected result.
     * @param array $contain The contain data.
     * @return void
     *
     * @dataProvider patchContainProvider
     * @covers ::contain()
     */
    public function testPatchContain($expected, $contain)
    {
        $query = $this->fakeFelines->find()->contain($contain);
        $queryPatcher = new QueryPatcher($this->fakeFelines);
        $queryPatcher->patch($query)->contain();
        $this->assertEquals($expected, $query->contain());
    }
}
