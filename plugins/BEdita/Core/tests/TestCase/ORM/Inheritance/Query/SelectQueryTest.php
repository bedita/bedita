<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\ORM\Inheritance\Query;

use BEdita\Core\Test\TestCase\ORM\Inheritance\FakeAnimalsTrait;
use Cake\Database\ValueBinder;
use Cake\ORM\Query\SelectQuery as CakeSelectQuery;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\ORM\Inheritance\Query\SelectQuery} Test Case
 *
 * @coversDefaultClass \BEdita\Core\ORM\Inheritance\Query\SelectQuery
 */
class SelectQueryTest extends TestCase
{
    use FakeAnimalsTrait;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->setupTables();
        $this->setupAssociations();
    }

    /**
     * Data provider for `testAddDefaultFields` test case.
     *
     * @return array
     */
    public static function addDefaultFieldsProvider(): array
    {
        return [
            'default' => [
                [
                    'FakeFelines.id',
                    'FakeFelines.name',
                    'FakeFelines.legs',
                    'FakeFelines.modified',
                    'FakeFelines.subclass',
                    'FakeFelines.family',
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
     * @covers ::_addDefaultFields()
     * @dataProvider addDefaultFieldsProvider()
     */
    public function testAddDefaultFields(array $expected, array $select, bool $autoFields): void
    {
        $query = $this->fakeFelines->find()
            ->select($select)
            ->enableAutoFields($autoFields);
        $query->sql();

        $selected = array_values($query->clause('select'));
        sort($selected);
        sort($expected);
        static::assertEquals($expected, $selected, '');
        static::assertEqualsCanonicalizing($expected, $selected, '');
        static::assertEqualsWithDelta($expected, $selected, 0, '');
    }

    /**
     * Test builder for CTI sub-query.
     *
     * @covers ::_transformQuery()
     * @covers ::getInheritanceSubQuery()
     * @covers ::subQueryAliasFields()
     */
    public function testTransformQuery(): void
    {
        $expectedFields = [
            'id' => 'fake_felines.id',
            'name' => 'fake_animals.name',
            'legs' => 'fake_animals.legs',
            'modified' => 'fake_animals.modified',
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
        static::assertInstanceOf(CakeSelectQuery::class, $from['FakeFelines']);

        /** @var \Cake\ORM\Query $subQuery */
        $subQuery = $from['FakeFelines'];
        static::assertEquals($expectedFields, $subQuery->clause('select'));

        $joins = $subQuery->clause('join');
        static::assertCount(2, $joins);
        foreach ($expectedJoins as $alias => $table) {
            static::assertArrayHasKey($alias, $joins);
            static::assertSame('INNER', $joins[$alias]['type']);
            static::assertSame($table, $joins[$alias]['table']);
            static::assertSame($alias, $joins[$alias]['alias']);

            /** @var \Cake\Database\Expression\QueryExpression $exp */
            $exp = $joins[$alias]['conditions'];
            static::assertSame(
                $alias . '.id = fake_felines.id',
                $exp->sql(new ValueBinder())
            );
        }
    }
}
