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

namespace BEdita\Core\Test\TestCase\Model\Behavior;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\ORM\Inheritance\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Behavior\SearchableBehavior} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Behavior\SearchableBehavior
 */
class SearchableBehaviorTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.FakeAnimals',
        'plugin.BEdita/Core.FakeMammals',
        'plugin.BEdita/Core.FakeFelines',
        'plugin.BEdita/Core.FakeSearches',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        TableRegistry::getTableLocator()->get('FakeMammals', ['className' => Table::class])
            ->setDisplayField('name')
            ->extensionOf('FakeAnimals');
        TableRegistry::getTableLocator()->get('FakeFelines', ['className' => Table::class])
            ->setDisplayField('name')
            ->extensionOf('FakeMammals');
    }

    /**
     * Test behavior initialization process.
     *
     * @return void
     *
     * @covers ::initialize()
     */
    public function testInitialize()
    {
        $columnTypes = [
            'integer',
        ];
        $fields = [
            'my_field' => 17,
        ];

        $table = TableRegistry::getTableLocator()->get('FakeAnimals');
        $table->addBehavior('BEdita/Core.Searchable', compact('columnTypes', 'fields'));

        /* @var \BEdita\Core\Model\Behavior\SearchableBehavior $behavior */
        $behavior = $table->behaviors()->get('Searchable');

        static::assertEquals($columnTypes, $behavior->getConfig('columnTypes'));
        static::assertEquals($fields, $behavior->getConfig('fields'));
    }

    /**
     * Data provider for `testGetFields` test case.
     *
     * @return array
     */
    public function getFieldsProvider()
    {
        return [
            'default' => [
                [
                    'name' => 1,
                ],
            ],
            'inherited fields with custom priorities' => [
                [
                    'name' => 1,
                    'subclass' => 2,
                ],
                [
                    'fields' => [
                        '*' => 1,
                        'subclass' => 2,
                    ],
                ],
                'FakeMammals',
            ],
            'excluded fields' => [
                [
                    'subclass' => 2,
                    'family' => 5,
                ],
                [
                    'fields' => [
                        'subclass' => 2,
                        'family' => 5,
                    ],
                ],
                'FakeFelines',
            ],
        ];
    }

    /**
     * Test listing all searchable fields along with their priorities.
     *
     * @param array $expected Expected result.
     * @param array $config Behavior configuration.
     * @param string $table Table.
     * @return void
     *
     * @dataProvider getFieldsProvider()
     * @covers ::getAllFields()
     * @covers ::getFields()
     */
    public function testGetFields(array $expected, array $config = [], $table = 'FakeAnimals')
    {
        $table = TableRegistry::getTableLocator()->get($table);
        $table->addBehavior('BEdita/Core.Searchable', $config);

        /* @var \BEdita\Core\Model\Behavior\SearchableBehavior $behavior */
        $behavior = $table->behaviors()->get('Searchable');

        $fields = $behavior->getFields();

        static::assertEquals($expected, $fields);
    }

    /**
     * Data provider for `testFindQuery` test case.
     *
     * @return array
     */
    public function findQueryProvider()
    {
        return [
            'basic' => [
                [
                    2 => 'koala',
                ],
                'ala',
            ],
            'two words' => [
                null,
                'koala eagle',
            ],
            'two words, different fields' => [
                [
                    1 => 'cat',
                ],
                'eutheria cat',
                null,
                'FakeMammals',
            ],
            'bad type' => [
                new BadFilterException([
                    'title' => 'Invalid data',
                    'detail' => 'query filter requires a non-empty query string',
                ]),
                ['not', 'a', 'string'],
            ],
            'short words' => [
                new BadFilterException([
                    'title' => 'Invalid data',
                    'detail' => 'query strings must be at least 3 characters long',
                ]),
                'I am me',
            ],
            'too many words' => [
                new BadFilterException([
                    'title' => 'Invalid data',
                    'detail' => 'query string too long',
                ]),
                'this query string contains too many words thus will be rejected to avoid denial of service',
            ],
            'search hyphen' => [
                [
                    1 => 'hippo-tiger',
                ],
                'hippo-tiger',
                null,
                'FakeSearches',
            ],
            'search underscore' => [
                [
                    2 => 'lion_snake',
                ],
                'lion_snake',
                null,
                'FakeSearches',
            ],
            'search underscore 2' => [
                [
                   2 => 'lion_snake',
                ],
                'li_n',
                null,
                'FakeSearches',
            ],
            'search case' => [
                [
                    1 => 'hippo-tiger',
                ],
                'HIPPO',
                null,
                'FakeSearches',
            ],
            'basic with "string" param' => [
                [
                   1 => 'hippo-tiger',
                ],
                '',
                [
                    'string' => 'hippo',
                ],
                'FakeSearches',
            ],
            'exact false' => [
                [
                    1 => 'big mouse',
                    2 => 'mouse big',
                ],
                '',
                [
                    'string' => 'big mouse',
                    'exact' => 0,
                ],
                'FakeSearches',
            ],
            'exact' => [
                [
                    2 => 'big mouse',
                ],
                '',
                [
                    'string' => 'big mouse',
                    'exact' => 1,
                ],
                'FakeSearches',
            ],
        ];
    }

    /**
     * Test finder for query string.
     *
     * @param array|\Exception $expected Expected result.
     * @param string $query Query string.
     * @param array|null $options Array of options.
     * @param string $table Table.
     * @return void
     *
     * @dataProvider findQueryProvider()
     * @covers ::findQuery()
     */
    public function testFindQuery($expected, $query, $options = null, $table = 'FakeAnimals')
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $table = TableRegistry::getTableLocator()->get($table);
        $table->addBehavior('BEdita/Core.Searchable');

        static::assertTrue($table->hasFinder('query'));

        $params = isset($options) ? [$query, $options] : [$query];

        $result = $table
            ->find('query', $params)
            ->find('list')
            ->toArray();

        static::assertEquals($expected, $result);
    }
}
