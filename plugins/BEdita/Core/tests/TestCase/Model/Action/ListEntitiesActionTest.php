<?php
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

use BEdita\Core\Model\Action\ListEntitiesAction;
use BEdita\Core\ORM\Inheritance\Table;
use Cake\Database\Driver\Mysql;
use Cake\Datasource\ConnectionManager;
use Cake\I18n\Time;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Model\Action\ListEntitiesAction
 */
class ListEntitiesActionTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.FakeAnimals',
        'plugin.BEdita/Core.FakeArticles',
        'plugin.BEdita/Core.FakeMammals',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
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
    public function parseFilterProvider()
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
     *
     * @dataProvider parseFilterProvider()
     * @covers ::parseFilter()
     */
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
    public function executeProvider()
    {
        return [
            'plain' => [
                [
                    [
                        'id' => 1,
                        'name' => 'cat',
                        'legs' => 4,
                        'updated_at' => new Time('2018-02-20 09:50:00'),
                    ],
                    [
                        'id' => 2,
                        'name' => 'koala',
                        'legs' => 4,
                        'updated_at' => null,
                    ],
                    [
                        'id' => 3,
                        'name' => 'eagle',
                        'legs' => 2,
                        'updated_at' => null,
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
                        'updated_at' => new Time('2018-02-20 09:50:00'),
                    ],
                    [
                        'id' => 2,
                        'name' => 'koala',
                        'legs' => 4,
                        'updated_at' => null,
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
                        'updated_at' => new Time('2018-02-20 09:50:00'),
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
                        'updated_at' => new Time('2018-02-20 09:50:00'),
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
                        'updated_at' => new Time('2018-02-20 09:50:00'),
                        'subclass' => 'Eutheria',
                    ],
                ],
                [
                    'name' => 'cat',
                ],
                'FakeMammals',
            ],
            'finder1' => [
                [
                ],
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
        ];
    }

    /**
     * Test command execution.
     *
     * @param array $expected Expected results.
     * @param mixed $filter Filter.
     * @param string $table Table name.
     * @return void
     *
     * @dataProvider executeProvider()
     * @covers ::initialize()
     * @covers ::buildFilter()
     * @covers ::execute()
     */
    public function testExecute(array $expected, $filter, $table = 'FakeAnimals')
    {
        $table = TableRegistry::getTableLocator()->get($table);
        $action = new ListEntitiesAction(compact('table'));

        $result = $action(compact('filter'));

        static::assertInstanceOf(Query::class, $result);
        static::assertEquals($expected, $result->enableHydration(false)->toArray());
    }

    /**
     * Test command execution with custom prop filter.
     *
     * @param array $expected Expected results.
     * @param mixed $filter Filter.
     * @param string $table Table name.
     * @return void
     *
     * @covers ::initialize()
     * @covers ::buildFilter()
     * @covers ::execute()
     */
    public function testFilterCustomProp()
    {
        $this->skipUnless(ConnectionManager::get('default')->getDriver() instanceof Mysql);

        $table = $this->getTableLocator()->get('Files');
        $action = new ListEntitiesAction(compact('table'));

        $result = $action(['filter' => ['media_property' => true]]);
        static::assertInstanceOf(Query::class, $result);

        $result = $result->toArray();
        static::assertCount(1, $result);
        static::assertEquals(10, $result[0]->id);
    }

    /**
     * Test command execution with contained entities.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::buildFilter()
     * @covers ::execute()
     */
    public function testExecuteContain()
    {
        $expected = [
            [
                'id' => 1,
                'name' => 'cat',
                'legs' => 4,
                'updated_at' => new Time('2018-02-20 09:50:00'),
                'fake_articles' => [
                    [
                        'id' => 1,
                        'title' => 'The cat',
                        'body' => 'article body',
                        'fake_animal_id' => 1,
                    ],
                    [
                        'id' => 2,
                        'title' => 'Puss in boots',
                        'body' => 'text',
                        'fake_animal_id' => 1,
                    ],
                ],
            ],
            [
                'id' => 2,
                'name' => 'koala',
                'legs' => 4,
                'updated_at' => null,
                'fake_articles' => [],
            ],
            [
                'id' => 3,
                'name' => 'eagle',
                'legs' => 2,
                'updated_at' => null,
                'fake_articles' => [],
            ],
        ];

        $table = TableRegistry::getTableLocator()->get('FakeAnimals');
        $contain = ['FakeArticles'];
        $action = new ListEntitiesAction(compact('table'));

        $result = $action(compact('contain'));

        static::assertInstanceOf(Query::class, $result);
        static::assertEquals($expected, $result->enableHydration(false)->toArray());
    }

    /**
     * Test filter error.
     *
     * @return void
     *
     * @covers ::buildFilter()
     * @covers ::execute()
     */
    public function testBadFilter()
    {
        $table = TableRegistry::getTableLocator()->get('FakeAnimals');
        $action = new ListEntitiesAction(compact('table'));

        $this->expectException('BEdita\Core\Exception\BadFilterException');

        $action(['filter' => 'really_cool_filter']);
    }
}
