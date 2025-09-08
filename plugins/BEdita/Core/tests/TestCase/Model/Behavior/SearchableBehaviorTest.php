<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Model\Behavior;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Model\Behavior\SearchableBehavior;
use BEdita\Core\ORM\Inheritance\Table;
use BEdita\Core\Search\BaseAdapter;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\ORM\Query\SelectQuery;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;

/**
 * {@see \BEdita\Core\Model\Behavior\SearchableBehavior} Test Case
 */
#[CoversClass(SearchableBehavior::class)]
class SearchableBehaviorTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.FakeAnimals',
        'plugin.BEdita/Core.FakeMammals',
        'plugin.BEdita/Core.FakeFelines',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->fetchTable('FakeMammals', ['className' => Table::class])
            ->setDisplayField('name')
            ->extensionOf('FakeAnimals');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();

        $this->getTableLocator()->remove('FakeMammals');
        $this->getTableLocator()->remove('FakeAnimals');
    }

    /**
     * Data provider for `testFindQuery` test case.
     *
     * @return array
     */
    public static function findQueryProvider(): array
    {
        return [
            'ok with "string" key' => [
                [
                    2 => 'koala',
                ],
                ['string' => 'ala'],
            ],
            'query with string param an empty string' => [
                new BadFilterException([
                    'title' => 'Invalid data',
                    'detail' => 'query filter requires a non-empty query string',
                ]),
                [
                    'string' => '',
                ],
            ],
        ];
    }

    /**
     * Test finder for query string.
     *
     * @param array|\Exception $expected Expected result.
     * @param string|array $query Query string.
     * @return void
     */
    #[DataProvider('findQueryProvider')]
    public function testFindQuery(array|Exception $expected, array $query): void
    {
        if ($expected instanceof Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $table = $this->fetchTable('FakeMammals');
        $table->addBehavior('BEdita/Core.Searchable');

        static::assertTrue($table->hasFinder('query'));

        $result = $table
            ->find('query', ...$query)
            ->find('list')
            ->toArray();

        static::assertEquals($expected, $result);
    }

    /**
     * Data provider for `testGetAdapter` test case.
     *
     * @return array
     */
    public static function getAdapterProvider(): array
    {
        $newFakeAdapter = fn (array $condition = []) => new class ($condition) extends BaseAdapter {
            protected array $condition;

            public function __construct(array $condition)
            {
                $this->condition = $condition;
            }

            public function search(SelectQuery $query, string $text, array $options = []): SelectQuery
            {
                return empty($this->condition) ? $query : $query->where($this->condition);
            }

            public function indexResource(EntityInterface $entity, string $operation): void
            {
            }
        };

        return [
            'searchable with empty scopes => default used' => [
                [
                    'use' => ['default', 'eutheria'],
                    'adapters' => [
                            'default' => [
                                'className' => $newFakeAdapter(),
                                'scopes' => ['scope_1', 'scope_2'],
                            ],
                            'eutheria' => [
                                'className' => $newFakeAdapter(['subclass' => 'Eutheria']),
                                'scopes' => ['scope_1', 'scope_2'],
                            ],
                        ],
                    ],
                [],
                '{n}.subclass',
                ['Eutheria', 'Marsupial'],
            ],
            'searchable with scope_1 => eutheria used' => [
                [
                    'use' => ['default', 'eutheria'],
                    'adapters' => [
                        'default' => [
                            'className' => $newFakeAdapter(),
                            'scopes' => ['scope_2'],
                        ],
                        'eutheria' => [
                            'className' => $newFakeAdapter(['subclass' => 'Eutheria']),
                            'scopes' => ['scope_1', 'scope_2'],
                        ],
                    ],
                ],
                ['scope_1'],
                '{n}.subclass',
                ['Eutheria'],
            ],
            'searchable with empty scopes + use with scopes => default used' => [
                [
                    'use' => [
                        'default' => ['scope_2'],
                        'eutheria' => ['scope_1'],
                    ],
                    'adapters' => [
                        'default' => [
                            'className' => $newFakeAdapter(),
                            'scopes' => ['scope_1', 'scope_2'],
                        ],
                        'eutheria' => [
                            'className' => $newFakeAdapter(['subclass' => 'Eutheria']),
                            'scopes' => ['scope_1', 'scope_2'],
                        ],
                    ],
                ],
                [],
                '{n}.subclass',
                ['Eutheria', 'Marsupial'],
            ],
            'searchable with scope_1 + use with scopes => eutheria used' => [
                [
                    'use' => [
                        'default' => ['scope_2'],
                        'eutheria' => ['scope_1'],
                    ],
                    'adapters' => [
                        'default' => [
                            'className' => $newFakeAdapter(),
                            'scopes' => ['scope_1', 'scope_2'],
                        ],
                        'eutheria' => [
                            'className' => $newFakeAdapter(['subclass' => 'Eutheria']),
                            'scopes' => ['scope_1', 'scope_2'],
                        ],
                    ],
                ],
                ['scope_1'],
                '{n}.subclass',
                ['Eutheria'],
            ],
            'searchable with scope_3 => marsupial used' => [
                [
                    'use' => [
                        'default' => ['scope_2'],
                        'eutheria' => ['scope_1'],
                        'marsupial',
                    ],
                    'adapters' => [
                        'default' => [
                            'className' => $newFakeAdapter(),
                            'scopes' => ['scope_1', 'scope_2'],
                        ],
                        'eutheria' => [
                            'className' => $newFakeAdapter(['subclass' => 'Eutheria']),
                            'scopes' => ['scope_1', 'scope_2'],
                        ],
                        'marsupial' => [
                            'className' => $newFakeAdapter(['subclass' => 'Marsupial']),
                        ],
                    ],
                ],
                ['scope_3'],
                '{n}.subclass',
                ['Marsupial'],
            ],
        ];
    }

    /**
     * Test `getAdapter` method.
     *
     * @param array $searchConfig Search config.
     * @param array $scopes Scopes.
     * @param string $expectedPath Expected path.
     * @param array $expected Expected result.
     * @return void
     */
    #[DataProvider('getAdapterProvider')]
    public function testGetAdapter(array $searchConfig, array $scopes, string $expectedPath, array $expected): void
    {
        $backupConf = Configure::read('Search');
        Configure::write('Search', $searchConfig);
        $table = $this->fetchTable('FakeMammals');
        if (!empty($scopes)) {
            $table->addBehavior('BEdita/Core.Searchable', ['scopes' => $scopes]);
        } else {
            $table->addBehavior('BEdita/Core.Searchable');
        }
        $result = $table->find('query', string: 'word')->toArray();
        $actual = Hash::extract($result, $expectedPath);
        static::assertEquals($expected, $actual);
        Configure::write('Search', $backupConf); // restore original config
    }

    /**
     * Test exception when Search config is wrong.
     *
     * @return void
     */
    public function testGetAdapterException(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('No search adapter found for current scopes');
        Configure::write('Search.use', ['test']);
        Configure::write('Search.adapters.test.scopes', ['foo2_scope']);
        $table = $this->fetchTable('FakeMammals');
        $table->addBehavior('BEdita/Core.Searchable', ['scopes' => ['foo_scope']]);
        $table->find('query', string: 'ala')->find('list')->toArray();
    }

    /**
     * Test afterSave() and afterDelete()
     *
     * @return void
     */
    public function testAfterSaveDelete(): void
    {
        $newAdapter = fn () => new class extends BaseAdapter {
            public $initializedCount = 0;
            public $afterDeleteCount = 0;
            public $afterSaveCount = 0;

            public function search(SelectQuery $query, string $text, array $options = []): SelectQuery
            {
                return $query;
            }

            public function indexResource(EntityInterface $entity, string $operation): void
            {
                if ($operation === 'edit') {
                    $this->afterSaveCount++;
                }

                if ($operation === 'delete') {
                    $this->afterDeleteCount++;
                }
            }
        };

        EventManager::instance()->on('SearchAdapter.initialize', function (Event $event) {
            $event->getSubject()->initializedCount++;
        });

        $default = $newAdapter();
        $foo = $newAdapter();
        Configure::write('Search.adapters', [
            'default' => ['className' => $default],
            'foo' => ['className' => $foo, 'scopes' => ['foo']],
        ]);

        $table = $this->fetchTable('FakeMammals');
        $table->addBehavior('BEdita/Core.Searchable');
        $entity = $table->get(2);

        static::assertEquals(0, $default->afterSaveCount);
        static::assertEquals(0, $foo->afterSaveCount);
        $entity->setDirty('name');
        $table->saveOrFail($entity);
        static::assertEquals(1, $default->afterSaveCount);
        static::assertEquals(1, $foo->afterSaveCount);
        static::assertEquals(1, $default->initializedCount);
        static::assertEquals(1, $foo->initializedCount);

        static::assertEquals(0, $default->afterDeleteCount);
        static::assertEquals(0, $foo->afterDeleteCount);
        $entity->setDirty('name');
        $table->saveOrFail($entity, ['_primary' => false]);
        static::assertEquals(1, $default->afterSaveCount);
        static::assertEquals(1, $foo->initializedCount);

        static::assertEquals(0, $default->afterDeleteCount);
        static::assertEquals(0, $foo->afterDeleteCount);
        $entity->setDirty('name');
        $table->saveOrFail($entity, ['_skipSearchIndex' => true]);
        static::assertEquals(1, $default->afterSaveCount);
        static::assertEquals(1, $foo->initializedCount);

        static::assertEquals(0, $foo->afterDeleteCount);
        $table->deleteOrFail($entity);
        static::assertEquals(1, $default->afterDeleteCount);
        static::assertEquals(1, $foo->afterDeleteCount);
        static::assertEquals(1, $default->initializedCount);
        static::assertEquals(1, $foo->initializedCount);

        EventManager::instance()->off('SearchAdapter.initialize');
    }

    /**
     * Test afterSave() and afterDelete()
     *
     * @return void
     */
    public function testAfterSaveDeleteScopes(): void
    {
        $newAdapter = fn () => new class extends BaseAdapter {
            public $initializedCount = 0;
            public $afterDeleteCount = 0;
            public $afterSaveCount = 0;

            public function search(SelectQuery $query, string $text, array $options = []): SelectQuery
            {
                return $query;
            }

            public function indexResource(EntityInterface $entity, string $operation): void
            {
                if ($operation === 'edit') {
                    $this->afterSaveCount++;
                }

                if ($operation === 'delete') {
                    $this->afterDeleteCount++;
                }
            }
        };

        EventManager::instance()->on('SearchAdapter.initialize', function (Event $event) {
            $event->getSubject()->initializedCount++;
        });

        $default = $newAdapter();
        $foo = $newAdapter();
        $bar = $newAdapter();
        $baz = $newAdapter();
        Configure::write('Search.adapters', [
            'default' => ['className' => $default],
            'foo' => ['className' => $foo, 'scopes' => ['foo']],
            'bar' => ['className' => $bar, 'scopes' => ['bar']],
            'baz' => ['className' => $baz, 'scopes' => ['baz', 'foo']],
        ]);

        $table = $this->fetchTable('FakeMammals');
        $table->addBehavior('BEdita/Core.Searchable', ['scopes' => ['foo']]);
        $entity = $table->get(2);

        static::assertEquals(0, $default->afterSaveCount);
        static::assertEquals(0, $foo->afterSaveCount);
        static::assertEquals(0, $bar->afterSaveCount);
        static::assertEquals(0, $baz->afterSaveCount);
        $entity->setDirty('name');
        $table->saveOrFail($entity);
        static::assertEquals(1, $default->afterSaveCount);
        static::assertEquals(1, $foo->afterSaveCount);
        static::assertEquals(0, $bar->afterSaveCount);
        static::assertEquals(1, $baz->afterSaveCount);
        static::assertEquals(1, $default->initializedCount);
        static::assertEquals(1, $foo->initializedCount);
        static::assertEquals(0, $bar->initializedCount);
        static::assertEquals(1, $baz->initializedCount);

        static::assertEquals(0, $default->afterDeleteCount);
        static::assertEquals(0, $foo->afterDeleteCount);
        static::assertEquals(0, $bar->afterDeleteCount);
        static::assertEquals(0, $baz->afterDeleteCount);
        $table->deleteOrFail($entity);
        static::assertEquals(1, $default->afterDeleteCount);
        static::assertEquals(1, $foo->afterDeleteCount);
        static::assertEquals(0, $bar->afterDeleteCount);
        static::assertEquals(1, $baz->afterDeleteCount);
        static::assertEquals(1, $default->initializedCount);
        static::assertEquals(1, $foo->initializedCount);
        static::assertEquals(0, $bar->initializedCount);
        static::assertEquals(1, $baz->initializedCount);

        EventManager::instance()->off('SearchAdapter.initialize');
    }
}
