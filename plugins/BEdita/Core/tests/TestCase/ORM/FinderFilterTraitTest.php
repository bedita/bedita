<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\ORM;

use BadMethodCallException;
use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\ORM\FinderFilterTrait;
use Cake\ORM\Behavior;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\Table;
use Cake\TestSuite\TestCase;
use LogicException;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see BEdita\Core\ORM\FinderFilterTrait} Test Case
 */
#[CoversTrait(FinderFilterTrait::class)]
class FinderFilterTraitTest extends TestCase
{
    /**
     * @inheritDoc
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.FakeAnimals',
    ];

    /**
     * Table instance.
     *
     * @var \Cake\ORM\Table
     */
    protected Table $Table;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Table = new class (['alias' => 'TestFinderFilterTraitTable']) extends Table {
            use FinderFilterTrait;

            public function initialize(array $config): void
            {
                $this->setTable('fake_animals');
            }

            protected function findProtected(SelectQuery $query): SelectQuery
            {
                return $query;
            }

            public function findPublic(SelectQuery $query): SelectQuery
            {
                return $query;
            }

            public function findNoParam(): null
            {
                return null;
            }

            public function findOneParam(SelectQuery $query): SelectQuery
            {
                return $query->select(['one_param' => 'name']);
            }

            public function findTwoParams(SelectQuery $query, string $alias): SelectQuery
            {
                return $query->select([$alias => 'name']);
            }

            public function findAssocParams(SelectQuery $query, string $aliasName, string $aliasLegs): SelectQuery
            {
                return $query->select([$aliasName => 'name', $aliasLegs => 'legs']);
            }

            public function findTyped(SelectQuery $query, string $alias, bool $flag = false, int $num = 0, float $dec = 0.0): SelectQuery
            {
                return $query->select([$alias => sprintf('%s_%d_%s', var_export($flag, true), $num, $dec)]);
            }

            public function findVariadic(SelectQuery $query, ...$args): SelectQuery
            {
                return $query->select([$args['value'] => 'name']);
            }
        };
    }

    /**
     * Test that `hasFilter` method throws an exception if called on a class that is not a Table instance.
     *
     * @return void
     */
    public function testHasFilterNoTableInstance(): void
    {
        $subject = new class () {
            use FinderFilterTrait;
        };
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage(sprintf(
            'Filters are only available for `%s` instances. Got `%s` instead.',
            Table::class,
            get_class($subject),
        ));
        $subject->hasFilter('test');
    }

    /**
     * Test that `hasFilter` method returns true if a filter is available in the Table.
     *
     * @return void
     */
    public function testHasFinderTable(): void
    {
        static::assertFalse($this->Table->hasFilter('protected'));
        static::assertTrue($this->Table->hasFilter('public'));

        // Test behavior filters
        $behavior = new class ($this->Table) extends Behavior {
            protected array $_defaultConfig = [
                'implementedFinders' => [
                    'one' => 'findOne',
                    'two' => 'findTwo',
                ],
            ];

            public function findOne(SelectQuery $query): SelectQuery
            {
                return $query;
            }

            public function findTwo(SelectQuery $query): SelectQuery
            {
                return $query;
            }
        };

        $this->Table->addBehavior('Test', ['className' => $behavior]);

        static::assertTrue($this->Table->hasFilter('one'));
        static::assertTrue($this->Table->hasFilter('two'));

        $this->Table->getBehavior('Test')->setConfig('implementedFilters', ['one']);
        static::assertTrue($this->Table->hasFilter('one'));
        static::assertFalse($this->Table->hasFilter('two'));
    }

    /**
     * Data provider for `testCallFilter` test case.
     *
     * @return array
     */
    public static function callFilterProvider(): array
    {
        return [
            'missingFinder' => [
                new BadMethodCallException('Unknown filter method `protected`'),
                'protected',
            ],
            'noParam' => [
                new BadMethodCallException('filter `findNoParam` must accept at least one parameter'),
                'noParam',
            ],
            'oneParam' => [
                ['one_param' => 'name'],
                'oneParam',
            ],
            'twoParams' => [
                ['alias_name' => 'name'],
                'twoParams',
                'alias_name',
            ],
            'assocParams' => [
                ['alias_name' => 'name', 'alias_legs' => 'legs'],
                'assocParams',
                ['aliasName' => 'alias_name', 'aliasLegs' => 'alias_legs'],
            ],
            'variadic' => [
                ['alias_name' => 'name'],
                'variadic',
                'alias_name',
            ],
            'typedCastTrue' => [
                ['a' => 'true_10_1.5'],
                'typed',
                ['alias' => 'a', 'flag' => '1', 'num' => '10', 'dec' => '1.5'],
            ],
            'typedCastTrueString' => [
                ['a' => 'true_0_0'],
                'typed',
                ['alias' => 'a', 'flag' => 'true'],
            ],
            'typedCastFalse' => [
                ['a' => 'false_0_0'],
                'typed',
                ['alias' => 'a', 'flag' => '0'],
            ],
            'typedCastSingleValue' => [
                ['a' => 'false_0_0'],
                'typed',
                'a',
            ],
        ];
    }

    /**
     * Test `callFilter()` method.
     *
     * @return void
     */
    #[DataProvider('callFilterProvider')]
    public function testCallFilter(BadMethodCallException|array $expected, string $filterName, array|string|null $value = null): void
    {
        if ($expected instanceof BadMethodCallException) {
            $this->expectExceptionObject($expected);
        }

        $q = $this->Table->callFilter($filterName, $this->Table->find(), $value);
        static::assertEquals($expected, $q->clause('select'));
    }

    /**
     * Data provider for `testCallFilterInvalidArgument()`.
     *
     * @return array
     */
    public static function callFilterInvalidArgumentProvider(): array
    {
        return [
            'invalidBool' => [
                'filter parameter `flag` must be of type bool',
                ['alias' => 'a', 'flag' => 'abc'],
            ],
            'invalidInt' => [
                'filter parameter `num` must be of type int',
                ['alias' => 'a', 'num' => '1.5'],
            ],
            'invalidFloat' => [
                'filter parameter `dec` must be of type float',
                ['alias' => 'a', 'dec' => 'abc'],
            ],
            'arrayToScalar' => [
                'filter parameter `flag` must be of type bool',
                ['alias' => 'a', 'flag' => ['1']],
            ],
        ];
    }

    /**
     * Test `callFilter()` method with arguments that can't be cast to the declared parameter type.
     *
     * @return void
     */
    #[DataProvider('callFilterInvalidArgumentProvider')]
    public function testCallFilterInvalidArgument(string $expected, array $value): void
    {
        try {
            $this->Table->callFilter('typed', $this->Table->find(), $value);
            static::fail('Expected BadFilterException was not thrown');
        } catch (BadFilterException $e) {
            static::assertSame(400, $e->getCode());
            static::assertSame($expected, $e->getAttributes()['detail']);
        }
    }
}
