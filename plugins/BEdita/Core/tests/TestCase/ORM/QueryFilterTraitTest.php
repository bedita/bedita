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

namespace BEdita\Core\Test\TestCase\ORM;

use BEdita\Core\ORM\QueryFilterTrait;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see BEdita\Core\ORM\QueryFilterTrait} Test Case
 *
 * @coversDefaultClass \BEdita\Core\ORM\QueryFilterTrait
 */
class QueryFilterTraitTest extends TestCase
{
    use QueryFilterTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.fake_animals',
    ];

    /**
     * Table FakeAnimals
     *
     * @var \Cake\ORM\Table
     */
    public $fakeAnimals;

    /**
     * Query Filter class.
     *
     * @var QueryFilterTestCase
     */
    protected $queryFilter;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->fakeAnimals = TableRegistry::get('FakeAnimals');
    }

    /**
     * Data provider for `testFieldsFilter` test case.
     *
     * @return array
     */
    public function fieldsFilterProvider()
    {
        return [
            'more' => [
                [
                    'legs' => ['gt' => 2],
                ],
                2,
            ],
            'nameLess' => [
                [
                    'name' => ['lt' => 'name'],
                ],
                3,
            ],
            'nameNull' => [
                [
                    'name' => null,
                ],
                0,
            ],
            'nameEagle' => [
                [
                    'name' => 'eagle',
                ],
                1,
            ],
            'allLegs' => [
                [
                    'legs' => [2, 3, 4],
                ],
                3,
            ],
            'legss' => [
                [
                    'legs' => ['le' => 2],
                ],
                1,
            ],
            'legsGe' => [
                [
                    'legs' => ['ge' => 3],
                ],
                2,
            ],
            'legsno' => [
                [
                    'name' => 'koala',
                    'legs' => ['eq' => 3],
                ],
                0,
            ],
            'nobird' => [
                [
                    'name' => ['ne' => 'bird'],
                ],
                3,
            ],
            'nosnake' => [
                [
                    'name' => ['eq' => 'snake'],
                    'legs' => ['lt' => 1],
                ],
                0,
            ],
            'catcat' => [
                [
                    'name' => 'cat',
                    'legs' => ['gt' => 2],
                ],
                1,
            ],
            'multicat' => [
                [
                    'legs' => ['>=' => 2, '<' => 4],
                ],
                1,
            ],
        ];
    }

    /**
     * Test fields filter method.
     *
     * @param array $conditions Date conditions.
     * @param array|false $numExpected Number of expected results.
     * @return void
     *
     * @dataProvider fieldsFilterProvider
     * @covers ::fieldsFilter()
     */
    public function testFieldsFilter($options, $numExpected)
    {
        $query = new Query($this->fakeAnimals->getConnection(), $this->fakeAnimals);
        $query = $this->fieldsFilter($query, $options);
        $found = $query->toArray();
        static::assertEquals(count($found), $numExpected);
    }
}
