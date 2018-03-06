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
use Cake\Database\ValueBinder;
use Cake\ORM\Query as CakeQuery;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\ORM\Inheritance\Query} Test Case
 *
 * @coversDefaultClass \BEdita\Core\ORM\Inheritance\Query
 */
class QueryTest extends TestCase
{
    use FakeAnimalsTrait;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->setupTables();
        $this->setupAssociations();
    }

    /**
     * Test adding default types of inherited columns to type map.
     *
     * @return void
     *
     * @covers ::addDefaultTypes()
     */
    public function testAddDefaultTypes()
    {
        $this->fakeAnimals->getSchema()->setColumnType('name', 'json');
        $query = new Query($this->fakeFelines->getConnection(), $this->fakeFelines);

        $defaults = $query->getTypeMap()->getDefaults();
        static::assertArrayHasKey('name', $defaults);
        static::assertArrayHasKey('FakeFelines.name', $defaults);
        static::assertArrayHasKey('FakeFelines__name', $defaults);

        static::assertSame('json', $defaults['name']);
        static::assertSame('json', $defaults['FakeFelines.name']);
        static::assertSame('json', $defaults['FakeFelines__name']);
    }

    /**
     * Data provider for `testAddDefaultFields` test case.
     *
     * @return array
     */
    public function addDefaultFieldsProvider()
    {
        return [
            'default' => [
                [
                    'FakeFelines.id',
                    'FakeFelines.name',
                    'FakeFelines.legs',
                    'FakeFelines.updated_at',
                    'FakeFelines.subclass',
                    'FakeFelines.family'
                ],
                [],
                true,
            ],
            'explicit no autoFields' => [
                ['FakeFelines.name', 'FakeFelines.legs'],
                ['FakeFelines.name', 'FakeFelines.legs'],
                false,
            ],
        ];
    }

    /**
     * Test adding fields of inherited tables to "select" clause by default.
     *
     * @param string[] $expected Expected fields.
     * @param string[] $select Fields to explicitly select.
     * @param bool $autoFields Is auto-fields enabled?
     * @return void
     *
     * @covers ::_addDefaultFields()
     * @dataProvider addDefaultFieldsProvider()
     */
    public function testAddDefaultFields(array $expected, array $select, $autoFields)
    {
        $query = $this->fakeFelines->find()
            ->select($select)
            ->enableAutoFields($autoFields);
        $query->sql();

        $selected = array_values($query->clause('select'));
        static::assertEquals($expected, $selected, '', 0, 10, true);
    }

    /**
     * Test builder for CTI sub-query.
     *
     * @covers ::_transformQuery()
     * @covers ::getInheritanceSubQuery()
     * @covers ::subQueryAliasFields()
     */
    public function testTransformQuery()
    {
        $expectedFields = [
            'id' => 'fake_felines.id',
            'name' => 'fake_animals.name',
            'legs' => 'fake_animals.legs',
            'updated_at' => 'fake_animals.updated_at',
            'subclass' => 'fake_mammals.subclass',
            'family' => 'fake_felines.family',
        ];
        $expectedJoins = [
            'fake_mammals' => 'fake_mammals',
            'fake_animals' => 'fake_animals',
        ];

        $query = $this->fakeFelines->find();
        $query->sql();

        $from = $query->clause('from');
        static::assertCount(1, $from);
        static::assertArrayHasKey('FakeFelines', $from);
        static::assertInstanceOf(CakeQuery::class, $from['FakeFelines']);

        /* @var \Cake\ORM\Query $subQuery */
        $subQuery = $from['FakeFelines'];
        static::assertEquals($expectedFields, $subQuery->clause('select'));

        $joins = $subQuery->clause('join');
        static::assertCount(2, $joins);
        foreach ($expectedJoins as $alias => $table) {
            static::assertArrayHasKey($alias, $joins);
            static::assertSame('INNER', $joins[$alias]['type']);
            static::assertSame($table, $joins[$alias]['table']);
            static::assertSame($alias, $joins[$alias]['alias']);

            /* @var \Cake\Database\Expression\QueryExpression $exp */
            $exp = $joins[$alias]['conditions'];
            static::assertSame(
                $alias . '.id = (fake_felines.id)',
                $exp->sql(new ValueBinder())
            );
        }
    }
}
