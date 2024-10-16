<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Table;

use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Table\DateRangesTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\DateRangesTable
 */
class DateRangesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\DateRangesTable
     */
    public $DateRanges;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.DateRanges',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->DateRanges = TableRegistry::getTableLocator()->get('DateRanges');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->DateRanges);

        parent::tearDown();
    }

    /**
     * Test marshalling of new entities.
     *
     * @return void
     * @coversNothing
     */
    public function testMarshal()
    {
        $dateRange = $this->DateRanges->newEntity([
            'start_date' => '2017-01-01',
            'end_date' => '2017-01-10T17:18:19Z',
        ]);

        static::assertInstanceOf(FrozenTime::class, $dateRange->start_date);
        static::assertInstanceOf(FrozenTime::class, $dateRange->end_date);
    }

    /**
     * Data provider for `testFindDate` test case.
     *
     * @return array
     */
    public function findDateProvider()
    {
        return [
            'startAfter' => [
                [
                    'start_date' => ['gt' => '2017-01-01'],
                ],
                1,
            ],
            'startBefore' => [
                [
                    'start_date' => ['lt' => '2017-01-01'],
                ],
                0,
            ],
            'endBefore' => [
                [
                    'end_date' => ['le' => '2017-01-01'],
                ],
                0,
            ],
            'endAfter' => [
                [
                    'end_date' => ['ge' => '2017-01-01'],
                ],
                1,
            ],
            'equals' => [
                [
                    'start_date' => '2017-03-07 12:40:19',
                    'end_date' => ['eq' => '2017-03-08 21:40:19'],
                ],
                1,
            ],
            'notEquals' => [
                [
                    'start_date' => ['ne' => '2017-03-07 12:40:19'],
                ],
                0,
            ],
            'combinedOK' => [
                [
                    'start_date' => ['gt' => '2017-03-01'],
                    'end_date' => ['lt' => '2017-04-01'],
                ],
                1,
            ],
            'combinedKO' => [
                [
                    'start_date' => ['lt' => '2017-01-01'],
                    'end_date' => ['gt' => '2017-05-01'],
                ],
                0,
            ],
            'multipleConditions' => [
                [
                    'start_date' => ['>=' => '2017-03-07', '<' => '2017-03-08'],
                ],
                1,
            ],
        ];
    }

    /**
     * Test `dateRanges` finder.
     *
     * @param array $conditions Date conditions.
     * @param array|false $numExpected Number of expected results.
     * @return void
     * @dataProvider findDateProvider
     * @covers ::findDateRanges()
     * @covers ::fromToDateFilter()
     */
    public function testFindDate($conditions, $numExpected)
    {
        $result = $this->DateRanges->find('dateRanges', $conditions)->toArray();

        static::assertEquals($numExpected, count($result));
    }

    /**
     * Test date ranges finder failure.
     *
     * @covers ::findDateRanges()
     */
    public function testFindDateFail()
    {
        $conditions = ['what_date' => ['lt' => '2017-01-01']];

        $this->expectException('BEdita\Core\Exception\BadFilterException');

        $this->DateRanges->find('dateRanges', $conditions)->toArray();
    }

    /**
     * Data provider for `testFromToDateFilter` test case.
     *
     * @return array
     */
    public function fromToDateFilterProvider()
    {
        return [
            'from ok' => [
                [
                    'from_date' => '2017-01-01',
                ],
                1,
            ],
            'from not' => [
                [
                    'from_date' => '2017-08-01',
                ],
                0,
            ],
            'to ok' => [
                [
                    'to_date' => '2018-01-01',
                ],
                1,
            ],
            'to not' => [
                [
                    'to_date' => '2017-01-01',
                ],
                0,
            ],
            'between ok' => [
                [
                    'from_date' => '2017-03-07 08:00:00',
                    'to_date' => '2017-03-07 12:40:20',
                ],
                1,
            ],
            'between not' => [
                [
                    'from_date' => '2018-01-01',
                    'to_date' => '2018-12-31',
                ],
                0,
            ],
        ];
    }

    /**
     * Test `dateRanges` finder with `from_date` and `to_date`
     *
     * @param array $conditions Date conditions.
     * @param array|false $numExpected Number of expected results.
     * @return void
     * @dataProvider fromToDateFilterProvider
     * @covers ::fromToDateFilter()
     * @covers ::getTime()
     * @covers ::fromDateFilter()
     * @covers ::toDateFilter()
     * @covers ::betweenDatesFilter()
     */
    public function testFromToDateFilter($conditions, $numExpected)
    {
        $result = $this->DateRanges->find('dateRanges', $conditions)->toArray();

        static::assertEquals($numExpected, count($result));
    }

    /**
     * Test `getTime` failure.
     *
     * @covers ::getTime()
     */
    public function testGetTimeFailure()
    {
        $conditions = ['from_date' => 'gustavo'];
        $this->expectException('BEdita\Core\Exception\BadFilterException');
        $this->DateRanges->find('dateRanges', $conditions)->toArray();
    }
}
