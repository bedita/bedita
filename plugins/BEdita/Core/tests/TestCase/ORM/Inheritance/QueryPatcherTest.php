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
use Cake\Database\Expression\IdentifierExpression;
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

    /**
     * Data provider for `testAliasField` test case.
     *
     * @return array
     */
    public function aliasFieldProvider()
    {
        return [
            'id' => [
                'FakeFelines.id',
                'id',
            ],
            'aliasRight' => [
                'FakeFelines.family',
                'FakeFelines.family',
            ],
            'inheritedAlias' => [
                'FakeMammals.subclass',
                'FakeFelines.subclass',
            ],
            'inheritedAlias2' => [
                'FakeAnimals.legs',
                'FakeFelines.legs',
            ],
            'inheritedJustName' => [
                'FakeMammals.subclass',
                'subclass',
            ],
            'inheritedJustName2' => [
                'FakeAnimals.legs',
                'legs',
            ],
            'notInherited' => [
                'FakeArticles.title',
                'FakeArticles.title',
            ],
        ];
    }

    /**
     * testAliasField
     *
     * @param string $expected Expected result.
     * @param string $field The starting field.
     * @return void
     *
     * @dataProvider aliasFieldProvider
     * @covers ::aliasField()
     * @covers ::extractField()
     */
    public function testAliasField($expected, $field)
    {
        $queryPatcher = new QueryPatcher($this->fakeFelines);
        $this->assertEquals($expected, $queryPatcher->aliasField($field));
    }

    /**
     * Data provider for `testFixClause` test case.
     *
     * @return array
     */
    public function fixClauseProvider()
    {
        return [
            'selectString' => [
                ['FakeFelines.id'],
                'select',
                'id',
            ],
            'selectArray' => [
                [
                    'FakeFelines.id',
                    'FakeAnimals.legs',
                    'FakeMammals.subclass',
                    'FakeAnimals.name',
                    'sc' => 'FakeMammals.subclass',
                ],
                'select',
                ['id', 'legs', 'subclass', 'FakeAnimals.name', 'sc' => 'subclass'],
            ],
            'group' => [
                ['FakeFelines.id', 'FakeAnimals.legs'],
                'group',
                ['id', 'legs'],
            ],
            'noDistinct' => [
                false,
                'distinct',
                false
            ],
            'distinct' => [
                true,
                'distinct',
                true
            ],
            'distinctField' => [
                ['FakeFelines.id'],
                'distinct',
                'id'
            ],
            'distinctArray' => [
                ['FakeFelines.id', 'FakeAnimals.name'],
                'distinct',
                ['id', 'name']
            ]
        ];
    }

    /**
     * testFixClause
     *
     * @param array $expected Expected result.
     * @param string $clause The sql clause
     * @param array|string|\Cake\Database\ExpressionInterface|bool the clause data
     * @return void
     *
     * @dataProvider fixClauseProvider
     * @covers ::fixClause()
     * @covers ::aliasField()
     * @covers ::extractField()
     */
    public function testFixClause($expected, $clause, $data)
    {
        $query = $this->fakeFelines->find();
        $queryPatcher = new QueryPatcher($this->fakeFelines);
        $queryPatcher->patch($query)
            ->fixClause($data, $clause);

        if (!is_bool($data)) {
            $this->assertEquals($expected, $query->clause($clause));
        }

        // start from Query::clause()
        $query->{$clause}($data, true);
        $queryPatcher->fixClause(null, $clause);
        $this->assertEquals($expected, $query->clause($clause));
    }

    /**
     * testFixExpression
     *
     * @covers ::fixExpression()
     * @covers ::aliasField()
     * @covers ::extractField()
     */
    public function testFixExpression()
    {
        $query = $this->fakeFelines->find();
        $queryPatcher = new QueryPatcher($this->fakeFelines);

        // where: test \Cake\Database\Expression\FieldInterface case
        $where = ['id' => 1, 'name' => 'cat'];
        $whereExpected = ['FakeFelines.id' => 1, 'FakeAnimals.name' => 'cat'];
        $query->where($where);
        $whereExpression = $query->clause('where');
        $whereExpression->iterateParts(function ($value, $key) use ($queryPatcher, $whereExpected) {
            $whereKeys = array_keys($whereExpected);
            $queryPatcher->fixExpression($value);
            $expected = $whereKeys[$key];
            $this->assertEquals($expected, $value->getField());
            $this->assertEquals($whereExpected[$expected], $value->getValue());

            return $value;
        });

        // order: test \Cake\Database\Expression\QueryExpression case
        $query->order(['subclass' => 'ASC']);
        $orderExpression = $query->clause('order');
        $queryPatcher->fixExpression($orderExpression);
        $orderExpression->iterateParts(function ($value, $key) use ($queryPatcher) {
            $this->assertEquals('FakeMammals.subclass', $key);

            return $value;
        });

        // test \Cake\Database\Expression\IdentifierExpression case
        $identifierTest = [
            'id' => 'FakeFelines.id',
            'FakeFelines.name' => 'FakeAnimals.name',
            'subclass' => 'FakeMammals.subclass',
            'FakeFelines.family' => 'FakeFelines.family',
            'FakeArticles.title' => 'FakeArticles.title'
        ];
        foreach ($identifierTest as $test => $expected) {
            $identifierExpression = new IdentifierExpression($test);
            $queryPatcher->fixExpression($identifierExpression);
            $this->assertEquals($expected, $identifierExpression->getIdentifier());
        }
    }

    /**
     * testAll
     *
     * @covers ::all()
     * @covers ::contain()
     * @covers ::fixClause()
     * @covers ::fixExpression()
     */
    public function testAll()
    {
        $query = $this->fakeFelines->find();
        $queryPatcher = new QueryPatcher($this->fakeFelines);

        $query->select([
            'custom_name' => 'name',
            'subclass',
            'family',
            'count' => $query->func()->count('id')
        ])
            ->where(['family' => 'purring cats'])
            ->andWhere(['id' => 1])
            ->group('legs')
            ->order(['subclass' => 'ASC']);

        $queryPatcher->patch($query)->all();

        // contain
        $this->assertEquals(
            [
                'FakeMammals' => [
                    'FakeAnimals' => []
                    ]
            ],
            $query->contain()
        );

        // select
        $selectClause = $query->clause('select');
        $selectExpected = [
            'custom_name' => 'FakeAnimals.name',
            'FakeMammals.subclass',
            'FakeFelines.family',
            'count' => $query->func()->count('FakeFelines.id'),
        ];
        foreach ($selectClause as $k => $s) {
            $this->assertEquals($selectExpected[$k], $s);
        }

        // where
        $whereClause = $query->clause('where');
        $whereExpected = [
            'FakeFelines.family' => 'purring cats',
            'FakeFelines.id' => 1
        ];

        $whereClause->iterateParts(function ($value, $key) use ($whereExpected) {
            $whereKeys = array_keys($whereExpected);
            $expected = $whereKeys[$key];
            $this->assertEquals($expected, $value->getField());
            $this->assertEquals($whereExpected[$expected], $value->getValue());

            return $value;
        });

        // group
        $groupClause = $query->clause('group');
        $this->assertEquals('FakeAnimals.legs', $groupClause[0]);

        // order
        $orderClause = $query->clause('order');
        $orderClause->iterateParts(function ($value, $key) use ($queryPatcher) {
            $this->assertEquals('FakeMammals.subclass', $key);

            return $value;
        });

        // check sql just for MySQL
        if ($query->connection()->driver() instanceof \Cake\Database\Driver\Mysql) {
            $sql = $query->sql();
            $sql = preg_replace('/(\s){2,}/', ' ', $sql);

            $expected = 'SELECT FakeAnimals.name AS `custom_name`, FakeMammals.subclass AS `FakeMammals__subclass`, ' .
                'FakeFelines.family AS `FakeFelines__family`, (COUNT(FakeFelines.id)) AS `count` ' .
                'FROM fake_felines FakeFelines INNER JOIN fake_mammals FakeMammals ON FakeMammals.id = (FakeFelines.id) ' .
                'INNER JOIN fake_animals FakeAnimals ON FakeAnimals.id = (FakeMammals.id) ' .
                'WHERE (FakeFelines.family = :c0 AND FakeFelines.id = :c1) ' .
                'GROUP BY FakeAnimals.legs ' .
                'ORDER BY FakeMammals.subclass ASC';

            $this->assertEquals($expected, $sql);
        }
    }
}
