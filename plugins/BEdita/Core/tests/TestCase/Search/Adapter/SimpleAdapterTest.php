<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Search\Adapter;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\ORM\Inheritance\Table;
use BEdita\Core\Search\Adapter\SimpleAdapter;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Search\Adapter\SimpleAdapter
 */
class SimpleAdapterTest extends TestCase
{
    /**
     * SimpleAdapter instance
     *
     * @var \BEdita\Core\Search\Adapter\SimpleAdapter
     */
    protected $adapter = null;

    /**
     * @inheritDoc
     */
    protected $fixtures = [
        'plugin.BEdita/Core.FakeAnimals',
        'plugin.BEdita/Core.FakeMammals',
        'plugin.BEdita/Core.FakeFelines',
        'plugin.BEdita/Core.FakeSearches',
    ];

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->adapter = new SimpleAdapter();
        $this->fetchTable('FakeMammals', ['className' => Table::class])
            ->setDisplayField('name')
            ->extensionOf('FakeAnimals');
        $this->fetchTable('FakeFelines', ['className' => Table::class])
            ->setDisplayField('name')
            ->extensionOf('FakeMammals');
    }

    /**
     * @inheritDoc
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        $this->adapter = null;
    }

    /**
     * Data provider for `testSearch` test case.
     *
     * @return array
     */
    public function searchProvider()
    {
        return [
            'basic' => [
                [
                    2 => 'koala',
                ],
                'ala',
            ],
            'two words' => [
                [],
                'koala eagle',
            ],
            'two words, different fields' => [
                [
                    1 => 'cat',
                ],
                'eutheria cat',
                [],
                'FakeMammals',
                [
                    'name' => 1,
                    'subclass' => 1,
                ],
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
                [],
                'FakeSearches',
            ],
            'search underscore' => [
                [
                    2 => 'lion_snake',
                ],
                'lion_snake',
                [],
                'FakeSearches',
            ],
            'search underscore 2' => [
                [
                   2 => 'lion_snake',
                ],
                'li_n',
                [],
                'FakeSearches',
            ],
            'search case' => [
                [
                    1 => 'hippo-tiger',
                ],
                'HIPPO',
                [],
                'FakeSearches',
            ],
            'exact false' => [
                [
                    3 => 'big mouse',
                    4 => 'mouse big',
                ],
                'big mouse',
                [
                    'exact' => 0,
                ],
                'FakeSearches',
            ],
            'exact' => [
                [
                    3 => 'big mouse',
                ],
                'big mouse',
                [
                    'exact' => 1,
                ],
                'FakeSearches',
            ],
        ];
    }

    /**
     * Test search.
     *
     * @param array|\Exception $expected Expected result
     * @param string $text Text to search
     * @param array $options Search options
     * @param string $tableName Table name
     * @return void
     * @dataProvider searchProvider()
     * @covers ::search()
     * @covers ::prepareText()
     * @covers ::getValidator()
     */
    public function testSearch($expected, string $text, array $options = [], $tableName = 'FakeAnimals', array $fields = [])
    {
        if ($expected instanceof \Exception) {
            $this->expectExceptionObject($expected);
        }

        $table = $this->fetchTable($tableName);
        $query = $table->find();

        $fields += ['name' => 1];

        $config = [
            'minLength' => 3,
            'maxWords' => 10,
        ] + compact('fields');

        $result = $this->adapter
            ->search($query, $text, $options, $config)
            ->find('list')
            ->toArray();

        static::assertEquals($expected, $result);
    }
}
