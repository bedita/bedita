<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2026 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Behavior;

use ArrayObject;
use BEdita\Core\Model\Behavior\AdjacencyListBehavior;
use Cake\Database\Driver\Sqlite;
use Cake\Database\Expression\CommonTableExpression;
use Cake\Database\Expression\IdentifierExpression;
use Cake\Database\ExpressionInterface;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Association;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Table;
use Cake\TestSuite\TestCase;
use DateTime;
use Exception;
use InvalidArgumentException;
use UnexpectedValueException;

/**
 * {@see \BEdita\Core\Model\Behavior\AdjacencyListBehavior} Test Case
 *
 * @covers \BEdita\Core\Model\Behavior\AdjacencyListBehavior
 */
final class AdjacencyListBehaviorTest extends TestCase
{
    /**
     * @inheritDoc
     */
    protected $fixtures = [
        'plugin.BEdita/Core.FakeCategories',
    ];

    /**
     * Table object.
     *
     * @var \Cake\ORM\Table
     */
    protected Table $table;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        if (ConnectionManager::get('default')->getDriver() instanceof Sqlite) {
            static::markTestSkipped('AdjacencyListBehavior tests cannot run on SQLite');
        }

        $this->table = $this->fetchTable('FakeCategories');
        $this->table->belongsTo('Parents', [
            'className' => $this->table->getAlias(),
            'targetTable' => (clone $this->table)->setAlias('Parents'),
            'foreignKey' => 'parent_id',
        ]);
        $this->table->hasMany('Children', [
            'className' => $this->table->getAlias(),
            'targetTable' => (clone $this->table)->setAlias('Children'),
            'foreignKey' => 'parent_id',
        ]);

        // Add cycle in categories tree
        $connection = $this->table->getConnection();
        $connection->transactional(function ($connection): void {
            $connection->disableConstraints(function ($connection): void {
                $connection->insert('fake_categories', ['id' => 10, 'name' => 'Example circular reference', 'parent_id' => 11, 'left_idx' => 19, 'right_idx' => 21]);
                $connection->insert('fake_categories', ['id' => 11, 'name' => 'Example circular reference', 'parent_id' => 10, 'left_idx' => 20, 'right_idx' => 22]);
                $connection->insert('fake_categories', ['id' => 12, 'name' => 'Example self-reference', 'parent_id' => 12, 'left_idx' => 23, 'right_idx' => 24]);
            });
        });
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->table);

        parent::tearDown();
    }

    /**
     * Helper to get associations created by the adjacency list, which have an alphanumeric suffix.
     *
     * @param string $name Association name
     * @param \Cake\ORM\Table|null $table The table to search associations in
     * @return \Cake\ORM\Association|null
     */
    public function getAssociation(string $name, ?Table $table = null): ?Association
    {
        foreach (($table ?? $this->table)->associations() as $n => $a) {
            if (str_starts_with($n, $name)) {
                return $a;
            }
        }

        return null;
    }

    /**
     * Data provider for {@see AdjacencyListBehaviorTest::testInitialize()} test case.
     *
     * @return array[]
     */
    public static function initializeProvider(): array
    {
        return [
            'default name, association name' => [
                'fake_categories_matrix',
                ['parentAssociation' => 'Parents'],
            ],
            'default name, association instance' => [
                'fake_categories_matrix',
                fn(Table $table): array => ['parentAssociation' => $table->getAssociation('Parents')],
            ],
            'custom name, association name' => [
                'foo',
                ['parentAssociation' => 'Parents', 'cteName' => 'foo'],
            ],
            'custom name, association instance' => [
                'foo',
                fn(Table $table): array => ['parentAssociation' => $table->getAssociation('Parents'), 'cteName' => 'foo'],
            ],
            'empty name' => [
                new InvalidArgumentException('CTE name should be a non empty string, or be omitted to use default'),
                ['parentAssociation' => 'Parents', 'cteName' => ''],
            ],
            'invalid name' => [
                new InvalidArgumentException('CTE name should be a non empty string, or be omitted to use default'),
                ['parentAssociation' => 'Parents', 'cteName' => new DateTime()],
            ],
            'invalid association (name references an invalid association type)' => [
                new UnexpectedValueException('Configuration `parentAssociation` should be a string or an instance of Cake\ORM\Association\BelongsTo, got Cake\ORM\Association\HasMany'),
                ['parentAssociation' => 'Children'],
            ],
            'invalid association (invalid association type)' => [
                new UnexpectedValueException('Configuration `parentAssociation` should be a string or an instance of Cake\ORM\Association\BelongsTo, got Cake\ORM\Association\HasMany'),
                fn(Table $table): array => ['parentAssociation' => $table->getAssociation('Children')],
            ],
            'invalid association (not a string)' => [
                new UnexpectedValueException('Configuration `parentAssociation` should be a string or an instance of Cake\ORM\Association\BelongsTo, got array'),
                ['parentAssociation' => ['Children']],
            ],
        ];
    }

    /**
     * Test {@see AdjacencyListBehavior::initialize()} method.
     *
     * @param string|\Exception $expected Expected CTE name, or thrown exception.
     * @param array|callable $config Behavior configuration.
     * @return void
     * @dataProvider initializeProvider()
     */
    public function testInitialize(string|Exception $expected, array|callable $config): void
    {
        if ($expected instanceof Exception) {
            $this->expectExceptionObject($expected);
        }

        if (is_callable($config)) {
            $config = $config($this->table);
        }
        $behavior = new class ($this->table, $config) extends AdjacencyListBehavior {
            public function getCteName(): string
            {
                return $this->cteName;
            }

            public function getParentAssociation(): BelongsTo
            {
                return $this->parentAssociation;
            }
        };

        static::assertSame($expected, $behavior->getCteName());
        static::assertSame($this->table->getAssociation('Parents'), $behavior->getParentAssociation());
    }

    /**
     * Test {@see AdjacencyListBehavior::aliasCteField()} method.
     *
     * @return void
     */
    public function testAliasCteField(): void
    {
        $behavior = new class ($this->table, ['parentAssociation' => 'Parents', 'cteName' => 'foo']) extends AdjacencyListBehavior {
            public function aliasCteField(string $field, string|null $suffix = null): string
            {
                return parent::aliasCteField($field, $suffix);
            }
        };

        static::assertSame('foo.bar', $behavior->aliasCteField('bar'));
        static::assertSame('foo_baz.bar', $behavior->aliasCteField('bar', 'baz'));
        static::assertSame('foo.baz', $behavior->aliasCteField('foo.baz'), 'Fields should not be prefixed twice');
    }

    /**
     * Test {@see AdjacencyListBehavior::prefix()} method.
     *
     * @return void
     */
    public function testPrefix(): void
    {
        $behavior = new class ($this->table, ['parentAssociation' => 'Parents']) extends AdjacencyListBehavior {
            public static function prefix(array $fields, string $prefix): array
            {
                return parent::prefix($fields, $prefix);
            }
        };

        $expected = ['foo_bar', 'foo_baz'];
        $actual = $behavior::prefix(['bar', 'baz'], 'foo_');

        static::assertSame($expected, $actual);
    }

    /**
     * Test {@see AdjacencyListBehavior::toIdentifiers()} method.
     *
     * @return void
     */
    public function testToIdentifiers(): void
    {
        $behavior = new class ($this->table, ['parentAssociation' => 'Parents']) extends AdjacencyListBehavior {
            public static function toIdentifiers(array $fields, callable $aliasFn, string|null $suffix = null): array
            {
                return parent::toIdentifiers($fields, $aliasFn, $suffix);
            }
        };

        $expected = [new IdentifierExpression('BAR'), new IdentifierExpression('BAZ')];
        $actual = $behavior::toIdentifiers(['bar', 'baz'], 'mb_strtoupper');

        static::assertEquals($expected, $actual);
    }

    /**
     * Test {@see AdjacencyListBehavior::getCteSchema()} method.
     *
     * @return void
     */
    public function testGetCteSchema(): void
    {
        $expected = [
            'ancestor_id' => 'integer',
            'descendant_id' => 'integer',
            'level' => 'integer',
            'cyclic' => 'boolean',
        ];

        $behavior = new class ($this->table, ['parentAssociation' => 'Parents', 'cteName' => 'foo']) extends AdjacencyListBehavior {
            public function getCteSchema(): TableSchema
            {
                return parent::getCteSchema();
            }
        };

        $schema = $behavior->getCteSchema();
        static::assertSame('foo', $schema->name());
        static::assertSameSize($expected, $schema->columns());

        foreach ($expected as $name => $type) {
            static::assertTrue($schema->hasColumn($name));
            static::assertSame($type, $schema->getColumnType($name));
        }
    }

    /**
     * Data provider for {@see AdjacencyListBehaviorTest::testGetInheritanceAssociation()} test case.
     *
     * @return array[]
     */
    public static function getInheritanceAssociationProvider(): array
    {
        return [
            'descendants' => [
                [
                    'name' => 'Example',
                    'table' => 'foo',
                    'foreignKey' => ['ancestor_id'],
                    'targetForeignKey' => ['descendant_id'],
                ],
                ['parentAssociation' => 'Parents', 'cteName' => 'foo', 'descendantsAssociation' => 'Example'],
                true,
            ],
            'descendants, empty association name' => [
                new InvalidArgumentException('Configuration `descendantsAssociation` must be a non-empty string'),
                ['parentAssociation' => 'Parents', 'cteName' => 'foo', 'descendantsAssociation' => ''],
                true,
            ],
            'descendants, non-string association name' => [
                new InvalidArgumentException('Configuration `descendantsAssociation` must be a non-empty string'),
                ['parentAssociation' => 'Parents', 'cteName' => 'foo', 'descendantsAssociation' => [1, 2, 3]],
                true,
            ],
            'ancestors' => [
                [
                    'name' => 'Example',
                    'table' => 'foo',
                    'foreignKey' => ['descendant_id'],
                    'targetForeignKey' => ['ancestor_id'],
                ],
                ['parentAssociation' => 'Parents', 'cteName' => 'foo', 'ancestorsAssociation' => 'Example'],
                false,
            ],
            'ancestors, empty association name' => [
                new InvalidArgumentException('Configuration `ancestorsAssociation` must be a non-empty string'),
                ['parentAssociation' => 'Parents', 'cteName' => 'foo', 'ancestorsAssociation' => ''],
                false,
            ],
            'ancestors, non-string association name' => [
                new InvalidArgumentException('Configuration `ancestorsAssociation` must be a non-empty string'),
                ['parentAssociation' => 'Parents', 'cteName' => 'foo', 'ancestorsAssociation' => [1, 2, 3]],
                false,
            ],
        ];
    }

    /**
     * Test {@see AdjacencyListBehavior::getInheritanceAssociation()} method.
     *
     * @param array|\Exception $expected Expected outcome.
     * @param array $config Configuration.
     * @param bool $descendants `true` for descendants, `false` for ancestors.
     * @return void
     * @dataProvider getInheritanceAssociationProvider()
     */
    public function testGetInheritanceAssociation(array|Exception $expected, array $config, bool $descendants): void
    {
        if ($expected instanceof Exception) {
            $this->expectExceptionObject($expected);
        }

        $behavior = new class ($this->table, $config) extends AdjacencyListBehavior {
            public function getInheritanceAssociation(bool $descendants, array|null $for = null): BelongsToMany
            {
                return parent::getInheritanceAssociation($descendants, $for);
            }
        };

        $association = $behavior->getInheritanceAssociation($descendants);
        if ($expected instanceof Exception) {
            return;
        }

        static::assertTrue($this->table->hasAssociation($expected['name']));
        static::assertSame($association, $this->table->getAssociation($expected['name']));
        static::assertSame($expected['foreignKey'], $association->getForeignKey());
        static::assertSame($expected['targetForeignKey'], $association->getTargetForeignKey());

        $target = $association->getTarget();
        static::assertNotSame($this->table, $target);
        static::assertSame($this->table->getTable(), $target->getTable());
        static::assertSame($expected['name'], $target->getAlias());
        static::assertNotSame($this->table->associations(), $target->associations());

        $junction = $association->junction();
        static::assertSame($expected['table'], $junction->getTable());
        static::assertSame($expected['name'] . 'Through', $junction->getAlias());

        $expectedColumns = [
            'ancestor_id' => 'integer',
            'descendant_id' => 'integer',
            'level' => 'integer',
            'cyclic' => 'boolean',
        ];
        $schema = $junction->getSchema();
        static::assertSameSize($expectedColumns, $schema->columns());
        foreach ($expectedColumns as $name => $type) {
            static::assertTrue($schema->hasColumn($name));
            static::assertSame($type, $schema->getColumnType($name));
        }

        $anotherAssociation = $behavior->getInheritanceAssociation($descendants);
        static::assertSame($association, $anotherAssociation);
    }

    /**
     * Test {@see AdjacencyListBehavior::getInheritanceAssociation()} method with an association of the wrong type.
     *
     * @param bool $descendants `true` for descendants, `false` for ancestors.
     * @return void
     * @testWith    [true]
     *              [false]
     */
    public function testGetInheritanceAssociationWrongType(bool $descendants): void
    {
        $this->expectExceptionObject(new UnexpectedValueException(sprintf('Unexpected association type `%s`', BelongsTo::class)));

        $config = [
            'parentAssociation' => 'Parents',
            'ancestorsAssociation' => 'Parents',
            'descendantsAssociation' => 'Parents',
        ];
        $behavior = new class ($this->table, $config) extends AdjacencyListBehavior {
            public function getInheritanceAssociation(bool $descendants, array|null $for = null): BelongsToMany
            {
                return parent::getInheritanceAssociation($descendants, $for);
            }
        };

        $behavior->getInheritanceAssociation($descendants);
    }

    /**
     * Test {@see AdjacencyListBehavior::cteBuilder()} method.
     *
     * @return void
     */
    public function testCteBuilder(): void
    {
        $behavior = new class ($this->table, ['parentAssociation' => 'Parents', 'cteName' => 'foo']) extends AdjacencyListBehavior {
            public function cteBuilder(): CommonTableExpression
            {
                return parent::cteBuilder();
            }
        };

        $expression = $behavior->cteBuilder();
        static::assertTrue($expression->isRecursive());

        $expected = <<<SQL
        foo(ancestor_id, descendant_id, level, cyclic) AS (
            (
                SELECT (FakeCategories.id), (FakeCategories.id), 0, (FALSE)
                FROM fake_categories FakeCategories
            )
            UNION ALL
            (
                SELECT (foo.ancestor_id), (FakeCategories.id), ((foo.level) + 1), (COALESCE((foo.ancestor_id)=(FakeCategories.id), FALSE))
                FROM fake_categories FakeCategories
                INNER JOIN foo foo
                    ON ((foo.descendant_id) = (FakeCategories.parent_id) AND NOT (foo.cyclic))
            )
        )
        SQL;
        $actual = $expression->sql($this->table->getConnection()->insertQuery()->getValueBinder());
        $normalize = fn(string $string): string => (string)preg_replace(['/(?<=[()])\s+|\s+(?=[()])/', '/\s+/'], ['', ' '], $string);

        static::assertEquals($normalize($expected), $normalize($actual));
    }

    /**
     * Data provider for {@see AdjacencyListBehaviorTest::testExtractFields()} test case.
     *
     * @return array[]
     */
    public static function extractFieldsProvider(): array
    {
        return [
            'scalar' => [[42], 42, ['id']],
            'list of scalars' => [[[42], [666]], [42, 666], ['id']],
            'associative array' => [['baz', 42], ['foo' => 42, 'bar' => 'baz'], ['bar', 'foo']],
            'list of associative arrays' => [[['barbaz', null], ['baz', 42]], [['bar' => 'barbaz'], ['foo' => 42, 'bar' => 'baz']], ['bar', 'foo']],
            'ArrayAccess' => [['baz', 42], new ArrayObject(['foo' => 42, 'bar' => 'baz']), ['bar', 'foo']],
            'invalid' => [new InvalidArgumentException('Cannot extract fields.'), new DateTime(), ['foo', 'bar']],
            'list with invalid item' => [new InvalidArgumentException('Cannot extract fields.'), [['foo' => 42, 'bar' => 'baz'], new DateTime()], ['foo', 'bar']],
        ];
    }

    /**
     * Test {@see AdjacencyListBehavior::extractFields()} method.
     *
     * @param mixed $expected Expected result, or exception thrown.
     * @param mixed $from Object to extract fields from.
     * @param string[] $fields Fields to extract.
     * @return void
     * @dataProvider extractFieldsProvider()
     */
    public function testExtractFields(mixed $expected, mixed $from, array $fields): void
    {
        if ($expected instanceof Exception) {
            $this->expectExceptionObject($expected);
        }

        $behavior = new class ($this->table, ['parentAssociation' => 'Parents']) extends AdjacencyListBehavior {
            public static function extractFields(mixed $from, array $fields): ExpressionInterface|array
            {
                return parent::extractFields($from, $fields);
            }
        };

        $actual = $behavior::extractFields($from, $fields);

        static::assertSame($expected, $actual);
    }

    /**
     * Test {@see AdjacencyListBehavior::findInheritanceMatrix()} finder.
     *
     * @return void
     */
    public function testFindInheritanceMatrix(): void
    {
        $this->table->addBehavior('BEdita/Core.AdjacencyList', ['parentAssociation' => 'Parents', 'cteName' => 'foo']);
        $query = $this->table->find();

        $query = $query->find('inheritanceMatrix');
        /** @var \Cake\Database\Expression\CommonTableExpression[] $with */
        $with = $query->clause('with');
        static::assertCount(1, $with);

        $cte = $with[array_key_first($with)];
        static::assertInstanceOf(CommonTableExpression::class, $cte);
        static::assertStringStartsWith('foo(ancestor_id, descendant_id, level, cyclic) AS (', $cte->sql($query->getValueBinder()));

        $query = $query->find('inheritanceMatrix');
        $anotherWith = $query->clause('with');
        static::assertSame($with, $anotherWith);
    }

    /**
     * Data provider for {@see AdjacencyListBehaviorTest::testFindAncestors()} test case.
     *
     * @return array[]
     */
    public static function findAncestorsProvider(): array
    {
        return [
            'id' => [
                [
                    ['id' => 1, 'name' => 'Science', 'level' => -2],
                    ['id' => 2, 'name' => 'Mathematics', 'level' => -1],
                ],
                ['for' => 4],
            ],
            'id, include self' => [
                [
                    ['id' => 1, 'name' => 'Science', 'level' => -2],
                    ['id' => 2, 'name' => 'Mathematics', 'level' => -1],
                    ['id' => 4, 'name' => 'Algebra', 'level' => 0],
                ],
                ['for' => 4, 'includeSelf' => true],
            ],
            'associative array' => [
                [
                    ['id' => 1, 'name' => 'Science', 'level' => -2],
                    ['id' => 2, 'name' => 'Mathematics', 'level' => -1],
                ],
                ['for' => ['id' => 3]],
            ],
            'entity' => [
                [
                    ['id' => 1, 'name' => 'Science', 'level' => -1],
                    ['id' => 2, 'name' => 'Mathematics', 'level' => 0],
                ],
                fn(Table $table): array => ['for' => $table->get(2), 'includeSelf' => true],
            ],
            'sub-query' => [
                [
                    ['id' => 1, 'name' => 'Science', 'level' => -1],
                    ['id' => 2, 'name' => 'Mathematics', 'level' => 0],
                ],
                fn(Table $table): array => [
                    'for' => $table->find()->select((array)$table->getPrimaryKey())->where(['id >' => 1, 'id <' => 3]),
                    'includeSelf' => true,
                ],
            ],
            'circular reference' => [
                [
                    ['id' => 11, 'name' => 'Example circular reference', 'level' => -2],
                    ['id' => 10, 'name' => 'Example circular reference', 'level' => -1],
                    ['id' => 11, 'name' => 'Example circular reference', 'level' => 0],
                ],
                ['for' => 11, 'includeSelf' => true],
            ],
            'self-reference' => [
                [
                    ['id' => 12, 'name' => 'Example self-reference', 'level' => -1],
                    ['id' => 12, 'name' => 'Example self-reference', 'level' => 0],
                ],
                ['for' => 12, 'includeSelf' => true],
            ],
            'empty results' => [
                [],
                ['for' => 1, 'includeSelf' => false],
            ],
            'missing required option' => [
                new InvalidArgumentException('Missing required `for` option'),
                ['for' => null, 'includeSelf' => true],
            ],
        ];
    }

    /**
     * Test {@see AdjacencyListBehavior::findAncestors()} finder.
     *
     * @param array|\Exception $expected Expected outcome.
     * @param array|callable $options Finder options.
     * @return void
     * @dataProvider findAncestorsProvider()
     */
    public function testFindAncestors(array|Exception $expected, array|callable $options): void
    {
        if ($expected instanceof Exception) {
            $this->expectExceptionObject($expected);
        }
        if (is_callable($options)) {
            $options = $options($this->table);
        }

        $this->table->addBehavior('BEdita/Core.AdjacencyList', ['parentAssociation' => 'Parents']);
        $query = $this->table->find('ancestors', $options);
        $association = $this->getAssociation('Descendants');

        static::assertNotNull($association);
        static::assertInstanceOf(BelongsToMany::class, $association);

        $actual = $query
            ->select(array_merge(
                (array)$this->table->getPrimaryKey(),
                (array)$this->table->getDisplayField(),
                [AdjacencyListBehavior::CTE_FIELD_LEVEL => $association->junction()->aliasField(AdjacencyListBehavior::CTE_FIELD_LEVEL)],
            ))
            ->orderAsc(AdjacencyListBehavior::CTE_FIELD_LEVEL)
            ->disableHydration()
            ->all()
            ->toList();

        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for {@see AdjacencyListBehaviorTest::testFindDescendants()} test case.
     *
     * @return array[]
     */
    public static function findDescendantsProvider(): array
    {
        return [
            'id' => [
                [
                    ['id' => 2, 'name' => 'Mathematics', 'level' => 1],
                    ['id' => 6, 'name' => 'Physics', 'level' => 1],
                    ['id' => 3, 'name' => 'Geometry', 'level' => 2],
                    ['id' => 4, 'name' => 'Algebra', 'level' => 2],
                    ['id' => 5, 'name' => 'Mathematical Logic', 'level' => 2],
                    ['id' => 7, 'name' => 'Fluid Mechanics', 'level' => 2],
                    ['id' => 8, 'name' => 'Rational Mechanics', 'level' => 2],
                ],
                ['for' => 1],
            ],
            'id, include self' => [
                [
                    ['id' => 1, 'name' => 'Science', 'level' => 0],
                    ['id' => 2, 'name' => 'Mathematics', 'level' => 1],
                    ['id' => 6, 'name' => 'Physics', 'level' => 1],
                    ['id' => 3, 'name' => 'Geometry', 'level' => 2],
                    ['id' => 4, 'name' => 'Algebra', 'level' => 2],
                    ['id' => 5, 'name' => 'Mathematical Logic', 'level' => 2],
                    ['id' => 7, 'name' => 'Fluid Mechanics', 'level' => 2],
                    ['id' => 8, 'name' => 'Rational Mechanics', 'level' => 2],
                ],
                ['for' => 1, 'includeSelf' => true],
            ],
            'associative array' => [
                [
                    ['id' => 3, 'name' => 'Geometry', 'level' => 1],
                    ['id' => 4, 'name' => 'Algebra', 'level' => 1],
                    ['id' => 5, 'name' => 'Mathematical Logic', 'level' => 1],
                ],
                ['for' => ['id' => 2]],
            ],
            'entity' => [
                [
                    ['id' => 2, 'name' => 'Mathematics', 'level' => 0],
                    ['id' => 3, 'name' => 'Geometry', 'level' => 1],
                    ['id' => 4, 'name' => 'Algebra', 'level' => 1],
                    ['id' => 5, 'name' => 'Mathematical Logic', 'level' => 1],
                ],
                fn(Table $table): array => ['for' => $table->get(2), 'includeSelf' => true],
            ],
            'sub-query' => [
                [
                    ['id' => 2, 'name' => 'Mathematics', 'level' => 0],
                    ['id' => 3, 'name' => 'Geometry', 'level' => 1],
                    ['id' => 4, 'name' => 'Algebra', 'level' => 1],
                    ['id' => 5, 'name' => 'Mathematical Logic', 'level' => 1],
                ],
                fn(Table $table): array => [
                    'for' => $table->find()->select((array)$table->getPrimaryKey())->where(['id >' => 1, 'id <' => 3]),
                    'includeSelf' => true,
                ],
            ],
            'circular reference' => [
                [
                    ['id' => 11, 'name' => 'Example circular reference', 'level' => 0],
                    ['id' => 10, 'name' => 'Example circular reference', 'level' => 1],
                    ['id' => 11, 'name' => 'Example circular reference', 'level' => 2],
                ],
                ['for' => 11, 'includeSelf' => true],
            ],
            'self-reference' => [
                [
                    ['id' => 12, 'name' => 'Example self-reference', 'level' => 0],
                    ['id' => 12, 'name' => 'Example self-reference', 'level' => 1],
                ],
                ['for' => 12, 'includeSelf' => true],
            ],
            'empty results' => [
                [],
                ['for' => 3, 'includeSelf' => false],
            ],
            'missing required option' => [
                new InvalidArgumentException('Missing required `for` option'),
                ['for' => null, 'includeSelf' => true],
            ],
        ];
    }

    /**
     * Test {@see AdjacencyListBehavior::findDescendants()} finder.
     *
     * @param array|\Exception $expected Expected outcome.
     * @param array|callable $options Finder options.
     * @return void
     * @dataProvider findDescendantsProvider()
     */
    public function testFindDescendants(array|Exception $expected, array|callable $options): void
    {
        if ($expected instanceof Exception) {
            $this->expectExceptionObject($expected);
        }
        if (is_callable($options)) {
            $options = $options($this->table);
        }

        $this->table->addBehavior('BEdita/Core.AdjacencyList', ['parentAssociation' => 'Parents']);
        $query = $this->table->find('descendants', $options);

        static::assertTrue($this->table->hasAssociation('Ancestors'));
        /** @var \Cake\ORM\Association\BelongsToMany $association */
        $association = $this->table->getAssociation('Ancestors');
        static::assertInstanceOf(BelongsToMany::class, $association);

        $actual = $query
            ->select(array_merge(
                (array)$this->table->getPrimaryKey(),
                (array)$this->table->getDisplayField(),
                [AdjacencyListBehavior::CTE_FIELD_LEVEL => $association->junction()->aliasField(AdjacencyListBehavior::CTE_FIELD_LEVEL)],
            ))
            ->order(array_merge(
                [AdjacencyListBehavior::CTE_FIELD_LEVEL],
                array_map([$this->table, 'aliasField'], (array)$this->table->getPrimaryKey()),
            ))
            ->disableHydration()
            ->all()
            ->toList();

        static::assertSame($expected, $actual);
    }

    /**
     * Test that {@see AdjacencyListBehavior::findAncestors()} and {@see AdjacencyListBehavior::findDescendants()} play well together.
     *
     * @return void
     */
    public function testFindAncestorsAndDescendants(): void
    {
        $expected = [
            ['id' => 2, 'name' => 'Mathematics'],
        ];

        $this->table->addBehavior('BEdita/Core.AdjacencyList', ['parentAssociation' => 'Parents']);
        $query = $this->table
            ->find('ancestors', ['for' => 3])
            ->find('descendants', ['for' => 1]);

        static::assertTrue($this->table->hasAssociation('Ancestors'));
        static::assertInstanceOf(BelongsToMany::class, $this->table->getAssociation('Ancestors'));
        $descendants = $this->getAssociation('Descendants');
        static::assertNotNull($descendants);
        static::assertInstanceOf(BelongsToMany::class, $descendants);

        $actual = $query
            ->select(array_merge((array)$this->table->getPrimaryKey(), (array)$this->table->getDisplayField()))
            ->order(array_map([$this->table, 'aliasField'], (array)$this->table->getPrimaryKey()))
            ->disableHydration()
            ->all()
            ->toList();

        static::assertSame($expected, $actual);
    }
}
