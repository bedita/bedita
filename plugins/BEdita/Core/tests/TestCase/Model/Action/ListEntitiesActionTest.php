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
namespace BEdita\Core\Test\TestCase\Model\Action;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Model\Action\ListEntitiesAction;
use BEdita\Core\ORM\Inheritance\Table;
use Cake\Database\Driver\Mysql;
use Cake\Database\Driver\Postgres;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\DateTime;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Exception;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \BEdita\Core\Model\Action\ListEntitiesAction} Test Case
 */
#[CoversClass(ListEntitiesAction::class)]
class ListEntitiesActionTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.FakeAnimals',
        'plugin.BEdita/Core.FakeArticles',
        'plugin.BEdita/Core.FakeMammals',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        TableRegistry::getTableLocator()->get('FakeAnimals', ['className' => Table::class])
            ->hasMany('FakeArticles');
        TableRegistry::getTableLocator()->get('FakeMammals', ['className' => Table::class])
            ->extensionOf('FakeAnimals');
    }

    /**
     * Data provider for `testParseFilter` test case.
     *
     * @return array
     */
    public static function parseFilterProvider(): array
    {
        return [
            'normal' => [
                [
                    'filter' => 'key=value',
                    'dangling' => true,
                    'gustavo' => 'supporto',
                    'empty' => null,
                ],
                'filter=key=value,dangling,gustavo=supporto,empty=null',
            ],
            'empty' => [
                [],
                ',=value',
            ],
            'array' => [
                [
                    'key' => 'value',
                ],
                [
                    'key' => 'value',
                ],
            ],
            'not a string' => [
                [],
                123,
            ],
        ];
    }

    /**
     * Test filter parser.
     *
     * @param array $expected Expected result.
     * @param string $filter Filter to be parsed
     * @return void
     */
    #[DataProvider('parseFilterProvider')]
    public function testParseFilter(array $expected, $filter)
    {
        $result = ListEntitiesAction::parseFilter($filter);

        static::assertSame($expected, $result);
    }

    /**
     * Data provider for `testExecute` test case.
     *
     * @return array
     */
    public static function executeProvider(): array
    {
        return [
            'plain' => [
                [
                    [
                        'id' => 1,
                        'name' => 'cat',
                        'legs' => 4,
                        'modified' => new DateTime('2018-02-20 09:50:00'),
                    ],
                    [
                        'id' => 2,
                        'name' => 'koala',
                        'legs' => 4,
                        'modified' => null,
                    ],
                    [
                        'id' => 3,
                        'name' => 'eagle',
                        'legs' => 2,
                        'modified' => null,
                    ],
                ],
                null,
            ],
            'field' => [
                [
                    [
                        'id' => 1,
                        'name' => 'cat',
                        'legs' => 4,
                        'modified' => new DateTime('2018-02-20 09:50:00'),
                    ],
                    [
                        'id' => 2,
                        'name' => 'koala',
                        'legs' => 4,
                        'modified' => null,
                    ],
                ],
                [
                    'legs' => 4,
                ],
            ],
            'field (null)' => [
                [],
                'legs=null',
            ],
            'association' => [
                [
                    [
                        'id' => 1,
                        'name' => 'cat',
                        'legs' => 4,
                        'modified' => new DateTime('2018-02-20 09:50:00'),
                    ],
                ],
                'fake_articles=1',
            ],
            'associationList' => [
                [
                    [
                        'id' => 1,
                        'name' => 'cat',
                        'legs' => 4,
                        'modified' => new DateTime('2018-02-20 09:50:00'),
                    ],
                ],
                ['fake_articles' => [1, 2] ],
            ],
            'inheritedField' => [
                [
                    [
                        'id' => 1,
                        'name' => 'cat',
                        'legs' => 4,
                        'modified' => new DateTime('2018-02-20 09:50:00'),
                        'subclass' => 'Eutheria',
                    ],
                ],
                [
                    'name' => 'cat',
                ],
                'FakeMammals',
            ],
            'filter finder not found' => [
                new BadFilterException('Invalid data'),
                [
                    'byName' => ['name' => 'not_found_relation'],
                ],
                'Relations',
            ],
            'find mine' => [
                [
                ],
                [
                    'mine' => true,
                ],
                'Users',
            ],
            'find mine no inheritance' => [
                [
                ],
                [
                    'mine' => true,
                ],
                'News',
            ],
            'wrong named argument' => [
                new BadFilterException('Invalid data'),
                [
                    'geo' => ['banana' => 'yeah'],
                ],
                'Locations',
            ],
        ];
    }

    /**
     * Test command execution.
     *
     * @param array|\Exception $expected Expected results.
     * @param mixed $filter Filter.
     * @param string $table Table name.
     * @return void
     */
    #[DataProvider('executeProvider')]
    public function testExecute(array|Exception $expected, mixed $filter, string $table = 'FakeAnimals'): void
    {
        if ($expected instanceof Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }
        $table = TableRegistry::getTableLocator()->get($table);
        $action = new ListEntitiesAction(compact('table'));

        $result = $action(compact('filter'));

        static::assertInstanceOf(SelectQuery::class, $result);
        static::assertEquals($expected, $result->enableHydration(false)->toArray());
    }

    /**
     * Test command execution with custom prop filter.
     *
     * @return void
     */
    public function testFilterCustomProp(): void
    {
        $driver = ConnectionManager::get('default')->getDriver();
        $this->skipUnless(($driver instanceof Mysql) || ($driver instanceof Postgres));

        $table = $this->getTableLocator()->get('Files');
        $action = new ListEntitiesAction(compact('table'));

        $result = $action(['filter' => ['media_property' => true]]);
        static::assertInstanceOf(SelectQuery::class, $result);

        $result = $result->toArray();
        static::assertCount(1, $result);
        static::assertEquals(10, $result[0]->id);
    }

    /**
     * Test command execution with contained entities.
     *
     * @return void
     */
    public function testExecuteContain()
    {
        $expected = [
            [
                'id' => 1,
                'name' => 'cat',
                'legs' => 4,
                'modified' => new DateTime('2018-02-20 09:50:00'),
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
            ],
            [
                'id' => 2,
                'name' => 'koala',
                'legs' => 4,
                'modified' => null,
                'fake_articles' => [],
            ],
            [
                'id' => 3,
                'name' => 'eagle',
                'legs' => 2,
                'modified' => null,
                'fake_articles' => [],
            ],
        ];

        $table = TableRegistry::getTableLocator()->get('FakeAnimals');
        $contain = ['FakeArticles'];
        $action = new ListEntitiesAction(compact('table'));

        $result = $action(compact('contain'));

        static::assertInstanceOf(SelectQuery::class, $result);
        static::assertEquals($expected, $result->enableHydration(false)->toArray());
    }

    /**
     * Test filter error.
     *
     * @return void
     */
    public function testBadFilter()
    {
        $table = TableRegistry::getTableLocator()->get('FakeAnimals');
        $action = new ListEntitiesAction(compact('table'));

        $this->expectException('BEdita\Core\Exception\BadFilterException');

        $action(['filter' => 'really_cool_filter']);
    }
}
