<?php
declare(strict_types=1);

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

use BadMethodCallException;
use BEdita\Core\ORM\Inheritance\AssociationCollection;
use BEdita\Core\ORM\Inheritance\Marshaller;
use BEdita\Core\ORM\Inheritance\Query\DeleteQuery;
use BEdita\Core\ORM\Inheritance\Query\InsertQuery;
use BEdita\Core\ORM\Inheritance\Query\SelectQuery;
use BEdita\Core\ORM\Inheritance\Query\UpdateQuery;
use Cake\Datasource\EntityInterface;
use Cake\I18n\DateTime;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \BEdita\Core\ORM\Inheritance\Table} Test Case
 */
#[CoversClass(Table::class)]
class TableTest extends TestCase
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
    }

    /**
     * Test marshaller
     *
     * @return void
     */
    public function testMarshaller(): void
    {
        static::assertInstanceOf(Marshaller::class, $this->fakeFelines->marshaller());
    }

    /**
     * Test that the query factory is correctly set up.
     *
     * @return void
     */
    public function testUseInheritanceQueryFactory(): void
    {
        static::assertInstanceOf(SelectQuery::class, $this->fakeFelines->query());
        static::assertInstanceOf(SelectQuery::class, $this->fakeFelines->selectQuery());
        static::assertInstanceOf(InsertQuery::class, $this->fakeFelines->insertQuery());
        static::assertInstanceOf(UpdateQuery::class, $this->fakeFelines->updateQuery());
        static::assertInstanceOf(DeleteQuery::class, $this->fakeFelines->deleteQuery());
    }

    /**
     * Test inheritance setup.
     *
     * @return void
     */
    public function testExtensionOf(): void
    {
        $this->fakeFelines->extensionOf('FakeAnimals');

        static::assertSame($this->fakeAnimals, $this->fakeFelines->inheritedTable());
        static::assertInstanceOf(AssociationCollection::class, $this->fakeFelines->associations());
    }

    /**
     * Test inherited tables
     *
     * @return void
     */
    public function testInheritedTables(): void
    {
        static::assertEquals(null, $this->fakeFelines->inheritedTable());
        static::assertEquals([], $this->fakeFelines->inheritedTables());

        $this->setupAssociations();

        $mammalsInheritance = $this->fakeMammals->inheritedTable();
        static::assertEquals('FakeAnimals', $mammalsInheritance->getAlias());

        $felinesInheritance = $this->fakeFelines->inheritedTable();
        static::assertEquals('FakeMammals', $felinesInheritance->getAlias());

        $felinesDeepInheritance = array_map(function (Table $inherited) {
            return $inherited->getAlias();
        }, $this->fakeFelines->inheritedTables());

        static::assertEquals(['FakeMammals', 'FakeAnimals'], $felinesDeepInheritance);
    }

    /**
     * Test method to find common inheritance tables.
     *
     * @return void
     */
    public function testCommonInheritance(): void
    {
        $this->setupAssociations();

        $expected = [$this->fakeMammals, $this->fakeAnimals];
        $common = $this->fakeFelines->commonInheritance($this->fakeMammals);
        $symmetricCommon = $this->fakeMammals->commonInheritance($this->fakeFelines);

        static::assertSame($expected, $common);
        static::assertSame($expected, $symmetricCommon);

        static::assertSame([], $this->fakeMammals->commonInheritance(TableRegistry::getTableLocator()->get('FakeArticles')));
    }

    /**
     * Test inherited tables
     *
     * @return void
     */
    public function testIsTableInherited(): void
    {
        static::assertFalse($this->fakeFelines->isTableInherited('FakeMammals'));
        static::assertFalse($this->fakeFelines->isTableInherited('FakeMammals', true));

        $this->setupAssociations();
        static::assertTrue($this->fakeFelines->isTableInherited('FakeAnimals', true));
        static::assertFalse($this->fakeFelines->isTableInherited('FakeAnimals'));
        static::assertTrue($this->fakeFelines->isTableInherited('FakeMammals', true));
        static::assertTrue($this->fakeFelines->isTableInherited('FakeMammals'));
    }

    /**
     * testBasicFindWithoutInheritance
     *
     * @return void
     */
    #[CoversNothing]
    public function testBasicFindWithoutInheritance(): void
    {
        // find felines
        $felines = $this->fakeFelines->find();
        static::assertEquals(1, $felines->count());

        $feline = $felines->first();
        $expected = [
            'id' => 1,
            'family' => 'purring cats',
        ];
        $result = $feline->extract($felines->first()->getVisible());
        ksort($expected);
        ksort($result);
        static::assertEquals($expected, $result);
    }

    /**
     * testBasicFindWithInheritance
     *
     * @return void
     */
    #[CoversNothing]
    public function testBasicFindWithInheritance(): void
    {
        $this->setupAssociations();

        // find felines
        $felines = $this->fakeFelines->find();
        static::assertEquals(1, $felines->count());

        $updatedAt = new DateTime('2018-02-20 09:50:00');

        $feline = $felines->first();
        $expected = [
            'id' => 1,
            'name' => 'cat',
            'legs' => 4,
            'modified' => $updatedAt,
            'subclass' => 'Eutheria',
            'family' => 'purring cats',
        ];
        $result = $feline->extract($felines->first()->getVisible());
        ksort($expected);
        ksort($result);
        static::assertEquals($expected, $result);

        static::assertFalse($feline->isDirty());

        // hydrate false
        $felines = $this->fakeFelines->find()->enableHydration(false);
        static::assertEquals(1, $felines->count());

        $result = $felines->first();
        ksort($expected);
        ksort($result);
        static::assertEquals($expected, $result);

        // find mammals
        $mammals = $this->fakeMammals->find()->enableHydration(false);
        static::assertEquals(2, $mammals->count());

        $expected = [
            [
                'id' => 1,
                'name' => 'cat',
                'legs' => 4,
                'modified' => $updatedAt,
                'subclass' => 'Eutheria',
            ],
            [
                'id' => 2,
                'name' => 'koala',
                'legs' => 4,
                'modified' => null,
                'subclass' => 'Marsupial',
            ],
        ];
        $expected = array_map(function ($a) {
            ksort($a);

            return $a;
        }, $expected);

        $result = array_map(function ($a) {
            ksort($a);

            return $a;
        }, $mammals->toArray());
        static::assertEquals($expected, $result);
    }

    /**
     * Test find using contain
     *
     * @return void
     */
    #[CoversNothing]
    public function testContainFind(): void
    {
        $this->setupAssociations();

        $felines = $this->fakeFelines
            ->find()
            ->contain('FakeArticles');
        static::assertEquals(1, $felines->count());

        $feline = $felines->first();

        static::assertTrue($feline->has('fake_articles'));
        static::assertEquals(2, count($feline->get('fake_articles')));
        static::assertFalse($feline->isDirty());

        $expected = [
            'id' => 1,
            'name' => 'cat',
            'legs' => 4,
            'modified' => new DateTime('2018-02-20 09:50:00'),
            'subclass' => 'Eutheria',
            'family' => 'purring cats',
            'fake_articles' => [
                [
                    'id' => 1,
                    'head_title' => 'The cat',
                    'main_body' => 'article body',
                    'fake_animal_id' => 1,
                ],
                [
                    'id' => 2,
                    'head_title' => 'Puss in boots',
                    'main_body' => 'text',
                    'fake_animal_id' => 1,
                ],
            ],
        ];
        ksort($expected);

        $result = $feline->toArray();
        ksort($result);

        static::assertEquals($expected, $result);
    }

    /**
     * Data provider for `testFixClause` test case.
     *
     * @return array
     */
    public static function selectProvider(): array
    {
        return [
            'fieldsFromAllInherited' => [
                ['family', 'subclass', 'name'],
                ['family', 'subclass', 'name'],
            ],
            'fieldsFromAncestor' => [
                ['name'],
                ['name'],
            ],
            'fieldsFromParent' => [
                ['subclass'],
                ['subclass'],
            ],
        ];
    }

    /**
     * testSelect
     *
     * @param array $expected Expected result.
     * @param array $select Select clause.
     * @return void
     */
    #[DataProvider('selectProvider')]
    #[CoversNothing]
    public function testSelect($expected, $select): void
    {
        $this->setupAssociations();

        $allColumns = $this->fakeFelines->getSchema()->columns();
        foreach ($this->fakeFelines->inheritedTables() as $t) {
            if (!($t instanceof Table)) {
                static::fail('Unexpected table object');
            }

            $allColumns = array_merge($allColumns, $t->getSchema()->columns());
        }
        $allColumns = array_unique($allColumns);

        $unexpectedFields = array_diff($allColumns, $expected);

        $felines = $this->fakeFelines->find()->select($select);

        foreach ($felines as $f) {
            if (!($f instanceof EntityInterface)) {
                static::fail('Unexpected entity');
            }

            foreach ($expected as $field) {
                static::assertTrue($f->has($field));
            }

            foreach ($unexpectedFields as $field) {
                static::assertFalse($f->has($field));
            }
        }
    }

    /**
     * testClauses
     *
     * @return void
     */
    #[CoversNothing]
    public function testClauses(): void
    {
        $this->setupAssociations();

        // add some row
        $data = [
            'legs' => 4,
            'subclass' => 'Another Sublcass',
            'family' => 'big cats',
        ];

        foreach (['tiger', 'lion', 'leopard'] as $animal) {
            $data['name'] = $animal;
            $feline = $this->fakeFelines->newEntity($data);
            $this->fakeFelines->save($feline);
        }

        $query = $this->fakeFelines->find();
        $result = $query->select(['subclass', 'count' => $query->func()->count('*')])
            ->groupBy(['subclass'])
            ->enableHydration(false);

        foreach ($result as $item) {
            if ($item['subclass'] == 'Eutheria') {
                static::assertEquals(1, $item['count']);
            } elseif ($item['subclass'] == 'Another Sublcass') {
                static::assertEquals(3, $item['count']);
            }
        }
    }

    /**
     * Provider for `testFindList`
     *
     * @return array
     */
    public static function findListProvider(): array
    {
        return [
            'fieldsOnMain' => [
                [
                    1 => 'purring cats',
                    4 => 'big cats',
                    5 => 'big cats',
                    6 => 'big cats',
                ],
                [
                    'keyField' => 'id',
                    'valueField' => 'family',
                ],
                ['id' => 'asc'],
            ],
            'fieldsOnMainAndParent' => [
                [
                    1 => 'Eutheria',
                    4 => 'Another Sublcass',
                    5 => 'Another Sublcass',
                    6 => 'Another Sublcass',
                ],
                [
                    'keyField' => 'id',
                    'valueField' => 'subclass',
                ],
                ['id' => 'asc'],
            ],
            'fieldsOnParentAndAncestor' => [
                [
                    'cat' => 'Eutheria',
                    'leopard' => 'Another Sublcass',
                    'lion' => 'Another Sublcass',
                    'tiger' => 'Another Sublcass',
                ],
                [
                    'keyField' => 'name',
                    'valueField' => 'subclass',
                ],
                ['name' => 'asc'],
            ],
            'fieldsOnAncestor' => [
                [
                    'cat' => 4,
                    'leopard' => 4,
                    'lion' => 4,
                    'tiger' => 4,
                ],
                [
                    'keyField' => 'name',
                    'valueField' => 'legs',
                ],
                ['name' => 'asc'],
            ],
        ];
    }

    /**
     * testFindList
     *
     * @param array $expected Expected results.
     * @param array $listParams Options for `find('list')`.
     * @param array $order Order clause.
     * @return void
     */
    #[DataProvider('findListProvider')]
    #[CoversNothing]
    public function testFindList($expected, $listParams, $order): void
    {
        $this->setupAssociations();

        // add some row
        $data = [
            'legs' => 4,
            'subclass' => 'Another Sublcass',
            'family' => 'big cats',
        ];

        foreach (['tiger', 'lion', 'leopard'] as $animal) {
            $data['name'] = $animal;
            $feline = $this->fakeFelines->newEntity($data);
            $this->fakeFelines->save($feline);
        }

        $query = $this->fakeFelines->find('list', ...$listParams);
        $query->orderBy($order);

        $result = $query->toArray();
        static::assertEquals($expected, $result);
    }

    /**
     * Test `hasFinder` method.
     *
     * @return void
     */
    public function testHasFinder(): void
    {
        $this->setupAssociations();

        static::assertFalse($this->fakeAnimals->hasFinder('children'));
        static::assertFalse($this->fakeMammals->hasFinder('children'));
        static::assertFalse($this->fakeFelines->hasFinder('children'));

        $this->fakeMammals->addBehavior('Tree');

        static::assertFalse($this->fakeAnimals->hasFinder('children'));
        static::assertTrue($this->fakeMammals->hasFinder('children'));
        static::assertTrue($this->fakeFelines->hasFinder('children'));
    }

    /**
     * Test `callFinder` method.
     *
     * @return void
     */
    public function testCallFinder(): void
    {
        $this->setupAssociations();

        $this->fakeAnimals->addBehavior('Tree');

        $animalsAlias = $this->fakeAnimals->getAlias();
        $mammalsAlias = $this->fakeMammals->getAlias();
        $felinesAlias = $this->fakeFelines->getAlias();
        $checkAliases = function () use ($animalsAlias, $mammalsAlias, $felinesAlias) {
            static::assertSame($felinesAlias, $this->fakeFelines->getAlias());
            static::assertSame($mammalsAlias, $this->fakeMammals->getAlias());
            static::assertSame($animalsAlias, $this->fakeAnimals->getAlias());
        };

        static::assertInstanceOf(SelectQuery::class, $this->fakeMammals->find('children', for: 1, direct: true));
        $checkAliases();
        static::assertInstanceOf(SelectQuery::class, $this->fakeFelines->find('children', for: 1, direct: true));
        $checkAliases();

        static::assertTextNotContains('FakeAnimals', $this->fakeMammals->find('children', for: 1, direct: true)->sql());
        $checkAliases();
        static::assertTextNotContains('FakeAnimals', $this->fakeFelines->find('children', for: 1, direct: true)->sql());
        $checkAliases();
    }

    /**
     * Test `callFinder` method.
     *
     * @return void
     */
    public function testCallMissingFinder(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Unknown finder method `gustavo`');
        $this->fakeMammals->find('gustavo');
    }

    /**
     * Test `hasField` method.
     *
     * @return void
     */
    public function testHasField(): void
    {
        $this->setupAssociations();

        static::assertTrue($this->fakeMammals->hasField('legs'));
        static::assertFalse($this->fakeMammals->hasField('legs', false));
        static::assertTrue($this->fakeAnimals->hasField('legs'));
    }

    /**
     * Test cloning of a table.
     *
     * @return void
     */
    public function testClone(): void
    {
        $clone = clone $this->fakeMammals;

        static::assertEquals($clone->associations(), $this->fakeMammals->associations());
        static::assertNotSame($clone->associations(), $this->fakeMammals->associations());

        static::assertEquals($clone->behaviors(), $this->fakeMammals->behaviors());
        static::assertNotSame($clone->behaviors(), $this->fakeMammals->behaviors());

        static::assertEquals($clone->getEventManager(), $this->fakeMammals->getEventManager());
        static::assertNotSame($clone->getEventManager(), $this->fakeMammals->getEventManager());
    }

    /**
     * Test `hasFilter` method.
     *
     * @return void
     */
    public function testHasFilter(): void
    {
        static::assertFalse($this->fakeFelines->hasFilter('children'));

        $this->setupAssociations();
        static::assertFalse($this->fakeFelines->hasFilter('children'));
        static::assertFalse($this->fakeMammals->hasFilter('children'));

        $this->fakeFelines->addBehavior('Tree');
        static::assertTrue($this->fakeFelines->hasFilter('children'));
        static::assertFalse($this->fakeMammals->hasFilter('children'));

        $this->fakeFelines->removeBehavior('Tree');
        $this->fakeMammals->addBehavior('Tree');
        static::assertTrue($this->fakeFelines->hasFilter('children'));
        static::assertTrue($this->fakeMammals->hasFilter('children'));
    }

    /**
     * Test `callFilter` method.
     *
     * @return void
     */
    public function testCallFilter(): void
    {
        // test callFilter on a tabel without inheritance
        $this->fakeFelines->addBehavior('Tree');
        $queryFelines = $this->fakeFelines->callFilter('children', $this->fakeFelines->find(), ['for' => 1, 'direct' => true]);
        static::assertInstanceOf(SelectQuery::class, $queryFelines);
        $this->fakeFelines->removeBehavior('Tree');

        $this->setupAssociations();

        // test callFilter having filter on a table in the middle of the inheritance chain
        $this->fakeMammals->addBehavior('Tree');
        $queryFelines = $this->fakeFelines->callFilter('children', $this->fakeFelines->find(), ['for' => 1, 'direct' => true]);
        static::assertInstanceOf(SelectQuery::class, $queryFelines);
        $this->fakeMammals->removeBehavior('Tree');

        // test callFilter having filter on the last table of the inheritance chain
        $this->fakeAnimals->addBehavior('Tree');
        $animalsAlias = $this->fakeAnimals->getAlias();
        $mammalsAlias = $this->fakeMammals->getAlias();
        $felinesAlias = $this->fakeFelines->getAlias();
        $checkAliases = function () use ($animalsAlias, $mammalsAlias, $felinesAlias) {
            static::assertSame($felinesAlias, $this->fakeFelines->getAlias());
            static::assertSame($mammalsAlias, $this->fakeMammals->getAlias());
            static::assertSame($animalsAlias, $this->fakeAnimals->getAlias());
        };

        $queryMammals = $this->fakeMammals->callFilter('children', $this->fakeMammals->find(), ['for' => 1, 'direct' => true]);
        static::assertInstanceOf(SelectQuery::class, $queryMammals);
        static::assertTextNotContains('FakeAnimals', $queryMammals->sql());
        $checkAliases();

        $queryFelines = $this->fakeFelines->callFilter('children', $this->fakeFelines->find(), ['for' => 1, 'direct' => true]);
        static::assertInstanceOf(SelectQuery::class, $queryFelines);
        static::assertTextNotContains('FakeAnimals', $queryFelines->sql());
        static::assertTextNotContains('FakeMammals', $queryFelines->sql());
        $checkAliases();
    }

    /**
     * Test `callFilter` method.
     *
     * @return void
     */
    public function testCallMissingFilter(): void
    {
        $this->expectException(BadMethodCallException::class);
        $this->expectExceptionMessage('Unknown filter method `gustavo`');
        $this->fakeMammals->callFilter('gustavo', $this->fakeMammals->find(), null);
    }
}
