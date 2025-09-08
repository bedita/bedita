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

use BEdita\Core\Model\Table\DateRangesTable;
use Cake\I18n\DateTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \BEdita\Core\Model\Table\DateRangesTable} Test Case
 */
#[CoversClass(DateRangesTable::class)]
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
    protected array $fixtures = [
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
     */
    #[CoversNothing]
    public function testMarshal(): void
    {
        $dateRange = $this->DateRanges->newEntity([
            'start_date' => '2017-01-01',
            'end_date' => '2017-01-10T17:18:19Z',
        ]);

        static::assertInstanceOf(DateTime::class, $dateRange->start_date);
        static::assertInstanceOf(DateTime::class, $dateRange->end_date);
    }

    /**
     * Data provider for `testFindDate` test case.
     *
     * @return array
     */
    public static function findDateProvider(): array
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
     * @param int $numExpected Number of expected results.
     * @return void
     */
    #[DataProvider('findDateProvider')]
    public function testFindDate(array $conditions, int $numExpected): void
    {
        $result = $this->DateRanges->find('dateRanges', ...$conditions)->toArray();

        static::assertEquals($numExpected, count($result));
    }

    /**
     * Test date ranges finder failure.
     *
     * @return void
     */
    public function testFindDateFail(): void
    {
        $this->expectException('BEdita\Core\Exception\BadFilterException');
        $this->DateRanges->find('dateRanges', start_date: null)->toArray();
    }

    /**
     * Data provider for `testFromToDateFilter` test case.
     *
     * @return array
     */
    public static function fromToDateFilterProvider(): array
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
     * @param int $numExpected Number of expected results.
     * @return void
     */
    #[DataProvider('fromToDateFilterProvider')]
    public function testFromToDateFilter(array $conditions, int $numExpected): void
    {
        $result = $this->DateRanges->find('dateRanges', ...$conditions)->toArray();

        static::assertEquals($numExpected, count($result));
    }

    /**
     * Test `getTime` failure.
     *
     * @return void
     */
    public function testGetTimeFailure(): void
    {
        $this->expectException('BEdita\Core\Exception\BadFilterException');
        $this->DateRanges->find('dateRanges', from_date: 'gustavo')->toArray();
    }
}
