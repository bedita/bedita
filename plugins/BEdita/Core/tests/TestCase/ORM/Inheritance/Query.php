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

use BEdita\Core\ORM\Inheritance\Query;
use Cake\Database\Expression\IdentifierExpression;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\ORM\Inheritance\Query} Test Case
 *
 * @coversDefaultClass \BEdita\Core\ORM\Inheritance\Query
 */
class QueryTest extends TestCase
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
     * Table options used for initialization
     *
     * @var \Cake\ORM\Table
     */
    protected $tableOptions = ['className' => 'BEdita\Core\ORM\Inheritance\Table'];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->fakeAnimals = TableRegistry::get('FakeAnimals', $this->tableOptions);
        $this->fakeAnimals->hasMany('FakeArticles');

        $this->fakeMammals = TableRegistry::get('FakeMammals', $this->tableOptions);
        $this->fakeMammals->extensionOf('FakeAnimals');

        $this->fakeFelines = TableRegistry::get('FakeFelines', $this->tableOptions);
        $this->fakeFelines->extensionOf('FakeMammals');
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
     * @covers ::inheritedTables()
     */
    public function testBuildContainString($expected, $tableName)
    {
        $query = new Query($this->fakeFelines->connection(), $this->fakeFelines);
        $containString = $query->buildContainString($tableName);
        $this->assertEquals($expected, $containString);
    }

    /**
     * Data provider for `testPatchContain` test case.
     *
     * @return array
     */
    public function fixContainProvider()
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
     * testFixContain
     *
     * @param array $expected Expected result.
     * @param array $contain The contain data.
     * @return void
     *
     * @dataProvider fixContainProvider
     * @covers ::fixContain()
     * @covers ::buildContainString()
     * @covers ::inheritedTables()
     */
    public function testFixContain($expected, $contain)
    {
        $query = $this->fakeFelines->find()->contain($contain);
        $query->fixContain();
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
     * @covers ::fixAliasField()
     * @covers ::extractField()
     */
    public function testFixAliasField($expected, $field)
    {
        $query = new Query($this->fakeFelines->connection(), $this->fakeFelines);
        $this->assertEquals($expected, $query->fixAliasField($field));
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
     * @covers ::fixAliasField()
     * @covers ::extractField()
     */
    public function testFixClause($expected, $clause, $data)
    {
        $query = $this->fakeFelines->find();
        $query->fixClause($data, $clause);

        if (!is_bool($data)) {
            $this->assertEquals($expected, $query->clause($clause));
        }

        // start from Query::clause()
        $query->{$clause}($data, true);
        $query->fixClause(null, $clause);
        $this->assertEquals($expected, $query->clause($clause));
    }

    /**
     * testFixExpression
     *
     * @covers ::fixExpression()
     * @covers ::fixAliasField()
     * @covers ::extractField()
     */
    public function testFixExpression()
    {
        $query = $this->fakeFelines->find();

        // where: test \Cake\Database\Expression\FieldInterface case
        $where = ['id' => 1, 'name' => 'cat'];
        $whereExpected = ['FakeFelines.id' => 1, 'FakeAnimals.name' => 'cat'];
        $query->where($where);
        $whereExpression = $query->clause('where');
        $whereExpression->iterateParts(function ($value, $key) use ($query, $whereExpected) {
            $whereKeys = array_keys($whereExpected);
            $query->fixExpression($value);
            $expected = $whereKeys[$key];
            $this->assertEquals($expected, $value->getField());
            $this->assertEquals($whereExpected[$expected], $value->getValue());

            return $value;
        });

        // order: test \Cake\Database\Expression\QueryExpression case
        $query->order(['subclass' => 'ASC']);
        $orderExpression = $query->clause('order');
        $query->fixExpression($orderExpression);
        $orderExpression->iterateParts(function ($value, $key) use ($query) {
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
            $query->fixExpression($identifierExpression);
            $this->assertEquals($expected, $identifierExpression->getIdentifier());
        }
    }

    /**
     * testAll
     *
     * This test create a useless and maybe not valid SQL query but it is intended
     * to check that all query parts are fixed in the right way
     *
     * @covers ::fixAll()
     * @covers ::fixContain()
     * @covers ::fixClause()
     * @covers ::fixExpression()
     */
    public function testAll()
    {
        $query = $this->fakeFelines->find();

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

        $query->fixAll();

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
        $orderClause->iterateParts(function ($value, $key) {
            $this->assertEquals('FakeMammals.subclass', $key);

            return $value;
        });

        // check sql just for MySQL
        if ($query->connection()->driver() instanceof \Cake\Database\Driver\Mysql) {
            $startQuote = $endQuote = '`';
        } else {
            $startQuote = $endQuote = '"';
        }

        $expected = 'SELECT FakeAnimals.name AS {sq}custom_name{eq}, FakeMammals.subclass AS {sq}FakeMammals__subclass{eq}, ' .
            'FakeFelines.family AS {sq}FakeFelines__family{eq}, (COUNT(FakeFelines.id)) AS {sq}count{eq} ' .
            'FROM fake_felines FakeFelines INNER JOIN fake_mammals FakeMammals ON FakeMammals.id = (FakeFelines.id) ' .
            'INNER JOIN fake_animals FakeAnimals ON FakeAnimals.id = (FakeMammals.id) ' .
            'WHERE (FakeFelines.family = :c0 AND FakeFelines.id = :c1) ' .
            'GROUP BY FakeAnimals.legs ' .
            'ORDER BY FakeMammals.subclass ASC';

        $expected = str_replace(['{sq}', '{eq}'], [$startQuote, $endQuote], $expected);

        $sql = $query->sql();
        $sql = preg_replace('/(\s){2,}/', ' ', $sql);
        $this->assertEquals($expected, $sql);
    }
}
