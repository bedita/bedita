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

namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Model\Entity\DateRange;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Model\Entity\DateRange
 */
class DateRangeTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.date_ranges',
    ];

    /**
     * Date Ranges table.
     *
     * @var \BEdita\Core\Model\Table\DateRangesTable
     */
    protected $DateRanges;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->DateRanges = TableRegistry::get('DateRanges');
    }

    /**
     * Data provider for `testIsBefore` test case.
     *
     * @return array
     */
    public function isBeforeProvider()
    {
        return [
            [
                true,
                [
                    'start_date' => '2017-01-01',
                    'end_date' => '2017-01-10',
                ],
                [
                    'start_date' => '2017-02-01',
                    'end_date' => '2017-02-10',
                ],
            ],
            [
                true,
                [
                    'start_date' => '2017-01-01',
                    'end_date' => '2017-02-01',
                ],
                [
                    'start_date' => '2017-02-01',
                    'end_date' => null,
                ],
            ],
            [
                true,
                [
                    'start_date' => '2017-01-01',
                    'end_date' => null,
                ],
                [
                    'start_date' => '2017-02-01',
                    'end_date' => '2017-02-10',
                ],
            ],
            [
                true,
                [
                    'start_date' => '2017-01-01',
                    'end_date' => null,
                ],
                [
                    'start_date' => '2017-02-01',
                    'end_date' => null,
                ],
            ],
            [
                false,
                [
                    'start_date' => '2017-02-01',
                    'end_date' => '2017-02-10',
                ],
                [
                    'start_date' => '2017-01-01',
                    'end_date' => '2017-01-10',
                ],
            ],
            [
                false,
                [
                    'start_date' => '2017-01-01',
                    'end_date' => '2017-01-10',
                ],
                [
                    'start_date' => '2017-01-01',
                    'end_date' => '2017-01-10',
                ],
            ],
            [
                false,
                [
                    'start_date' => '2017-01-01',
                    'end_date' => '2017-01-10',
                ],
                [
                    'start_date' => '2017-01-05',
                    'end_date' => null,
                ],
            ],
            [
                false,
                [
                    'start_date' => '2017-01-05',
                    'end_date' => null,
                ],
                [
                    'start_date' => '2017-01-01',
                    'end_date' => '2017-01-10',
                ],
            ],
            [
                false,
                [
                    'start_date' => '2017-01-05',
                    'end_date' => null,
                ],
                [
                    'start_date' => '2017-01-01',
                    'end_date' => null,
                ],
            ],
            [
                false,
                [
                    'start_date' => '2017-01-05',
                    'end_date' => null,
                ],
                [
                    'start_date' => '2017-01-05',
                    'end_date' => null,
                ],
            ],
        ];
    }

    /**
     * Test `isBefore` method.
     *
     * @param bool $expected Expected result
     * @param array $dateRange1 Date Range 1.
     * @param array $dateRange2 Date Range 2.
     * @return void
     *
     * @covers ::isBefore()
     * @dataProvider isBeforeProvider()
     */
    public function testIsBefore($expected, array $dateRange1, array $dateRange2)
    {
        $dateRange1 = $this->DateRanges->newEntity($dateRange1);
        $dateRange2 = $this->DateRanges->newEntity($dateRange2);

        $result = $dateRange1->isBefore($dateRange2);

        static::assertSame($expected, $result);
    }

    /**
     * Data provider for `testIsAfter` test case.
     *
     * @return array
     */
    public function isAfterProvider()
    {
        return [
            [
                true,
                [
                    'start_date' => '2017-02-01',
                    'end_date' => '2017-02-10',
                ],
                [
                    'start_date' => '2017-01-01',
                    'end_date' => '2017-01-10',
                ],
            ],
            [
                true,
                [
                    'start_date' => '2017-02-01',
                    'end_date' => null,
                ],
                [
                    'start_date' => '2017-01-01',
                    'end_date' => '2017-02-01',
                ],
            ],
            [
                true,
                [
                    'start_date' => '2017-02-01',
                    'end_date' => '2017-02-10',
                ],
                [
                    'start_date' => '2017-01-01',
                    'end_date' => null,
                ],
            ],
            [
                true,
                [
                    'start_date' => '2017-02-01',
                    'end_date' => null,
                ],
                [
                    'start_date' => '2017-01-01',
                    'end_date' => null,
                ],
            ],
            [
                false,
                [
                    'start_date' => '2017-01-01',
                    'end_date' => '2017-01-10',
                ],
                [
                    'start_date' => '2017-02-01',
                    'end_date' => '2017-02-10',
                ],
            ],
            [
                false,
                [
                    'start_date' => '2017-01-01',
                    'end_date' => '2017-01-10',
                ],
                [
                    'start_date' => '2017-01-01',
                    'end_date' => '2017-01-10',
                ],
            ],
            [
                false,
                [
                    'start_date' => '2017-01-01',
                    'end_date' => '2017-01-10',
                ],
                [
                    'start_date' => '2017-01-05',
                    'end_date' => null,
                ],
            ],
            [
                false,
                [
                    'start_date' => '2017-01-05',
                    'end_date' => null,
                ],
                [
                    'start_date' => '2017-01-01',
                    'end_date' => '2017-01-10',
                ],
            ],
            [
                false,
                [
                    'start_date' => '2017-01-01',
                    'end_date' => null,
                ],
                [
                    'start_date' => '2017-01-05',
                    'end_date' => null,
                ],
            ],
            [
                false,
                [
                    'start_date' => '2017-01-05',
                    'end_date' => null,
                ],
                [
                    'start_date' => '2017-01-05',
                    'end_date' => null,
                ],
            ],
        ];
    }

    /**
     * Test `$this->isAfter()` method.
     *
     * @param bool $expected Expected result
     * @param array $dateRange1 Date Range 1.
     * @param array $dateRange2 Date Range 2.
     * @return void
     *
     * @covers ::isAfter()
     * @dataProvider isAfterProvider()
     */
    public function testIsAfter($expected, array $dateRange1, array $dateRange2)
    {
        $dateRange1 = $this->DateRanges->newEntity($dateRange1);
        $dateRange2 = $this->DateRanges->newEntity($dateRange2);

        $result = $dateRange1->isAfter($dateRange2);

        static::assertSame($expected, $result);
    }

    /**
     * Data provider for `testNormalize` test case.
     *
     * @return array
     */
    public function normalizeProvider()
    {
        return [
            'empty' => [
                [],
                [],
            ],
            'sort' => [
                [
                    [
                        'start_date' => '2017-01-01',
                        'end_date' => '2017-01-10',
                    ],
                    [
                        'start_date' => '2017-01-20',
                        'end_date' => null,
                    ],
                    [
                        'start_date' => '2017-02-01',
                        'end_date' => '2017-02-10',
                    ],
                ],
                [
                    [
                        'start_date' => '2017-02-01',
                        'end_date' => '2017-02-10',
                    ],
                    [
                        'start_date' => '2017-01-01',
                        'end_date' => '2017-01-10',
                    ],
                    [
                        'start_date' => '2017-01-20',
                        'end_date' => null,
                    ],
                ],
            ],
            'merge' => [
                [
                    [
                        'start_date' => '2017-01-01',
                        'end_date' => '2017-01-10',
                    ],
                    [
                        'start_date' => '2017-01-20',
                        'end_date' => null,
                    ],
                    [
                        'start_date' => '2017-02-01',
                        'end_date' => '2017-02-10',
                    ],
                ],
                [
                    [
                        'start_date' => '2017-02-01',
                        'end_date' => '2017-02-07',
                    ],
                    [
                        'start_date' => '2017-01-01',
                        'end_date' => '2017-01-05',
                    ],
                    [
                        'start_date' => '2017-02-02',
                        'end_date' => '2017-02-06',
                    ],
                    [
                        'start_date' => '2017-02-04',
                        'end_date' => null,
                    ],
                    [
                        'start_date' => '2017-01-05',
                        'end_date' => '2017-01-10',
                    ],
                    [
                        'start_date' => '2017-01-20',
                        'end_date' => null,
                    ],
                    [
                        'start_date' => '2017-02-07',
                        'end_date' => '2017-02-10',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test case for normalization method.
     *
     * @param array $expected Expected result.
     * @param array $dateRanges Date Ranges.
     * @return void
     *
     * @covers ::normalize()
     * @dataProvider normalizeProvider()
     */
    public function testNormalize(array $expected, array $dateRanges)
    {
        $expected = $this->DateRanges->newEntities($expected);
        $dateRanges = $this->DateRanges->newEntities($dateRanges);

        $result = DateRange::normalize($dateRanges);

        foreach ($dateRanges as $dateRange) {
            foreach ($result as $item) {
                static::assertNotSame($dateRange, $item);
            }
        }

        $expected = json_decode(json_encode($expected), true);
        $result = json_decode(json_encode($result), true);

        static::assertSame($expected, $result);
    }

    /**
     * Test case for union method.
     *
     * @return void
     *
     * @covers ::union()
     */
    public function testUnion()
    {
        $expected = $this->DateRanges->newEntities([
            [
                'start_date' => '2017-01-01',
                'end_date' => '2017-01-10',
            ],
            [
                'start_date' => '2017-01-20',
                'end_date' => null,
            ],
            [
                'start_date' => '2017-02-01',
                'end_date' => '2017-02-10',
            ],
        ]);

        $dateRanges1 = $this->DateRanges->newEntities([
            [
                'start_date' => '2017-01-01',
                'end_date' => '2017-01-05',
            ],
            [
                'start_date' => '2017-01-20',
                'end_date' => null,
            ],
        ]);
        $dateRanges2 = $this->DateRanges->newEntities([
            [
                'start_date' => '2017-02-05',
                'end_date' => null,
            ],
            [
                'start_date' => '2017-01-20',
                'end_date' => null,
            ],
        ]);
        $dateRanges3 = $this->DateRanges->newEntities([
            [
                'start_date' => '2017-01-05',
                'end_date' => '2017-01-10',
            ],
            [
                'start_date' => '2017-02-01',
                'end_date' => '2017-02-10',
            ],
            [
                'start_date' => '2017-01-20',
                'end_date' => null,
            ],
        ]);

        $result = DateRange::union($dateRanges1, $dateRanges2, $dateRanges3);

        $expected = json_decode(json_encode($expected), true);
        $result = json_decode(json_encode($result), true);

        static::assertSame($expected, $result);
    }

    /**
     * Data provider for `testDiff` test case.
     *
     * @return array
     */
    public function diffProvider()
    {
        return [
            'empty' => [
                [],
                [],
                [
                    [
                        'start_date' => '2017-01-01',
                        'end_date' => '2017-01-10',
                    ],
                ],
            ],
            'complex' => [
                [
                    [
                        'start_date' => '2017-01-01',
                        'end_date' => '2017-01-02',
                    ],
                    [
                        'start_date' => '2017-01-03',
                        'end_date' => '2017-01-08',
                    ],
                    [
                        'start_date' => '2017-01-20',
                        'end_date' => null,
                    ],
                    [
                        'start_date' => '2017-02-05',
                        'end_date' => '2017-02-07',
                    ],
                    [
                        'start_date' => '2017-03-02',
                        'end_date' => '2017-03-05',
                    ],
                ],
                [
                    [
                        'start_date' => '2017-01-01',
                        'end_date' => '2017-01-10',
                    ],
                    [
                        'start_date' => '2017-01-17',
                        'end_date' => null,
                    ],
                    [
                        'start_date' => '2017-01-20',
                        'end_date' => null,
                    ],
                    [
                        'start_date' => '2017-01-27',
                        'end_date' => null,
                    ],
                    [
                        'start_date' => '2017-02-01',
                        'end_date' => '2017-02-10',
                    ],
                    [
                        'start_date' => '2017-02-15',
                        'end_date' => '2017-02-20',
                    ],
                    [
                        'start_date' => '2017-01-25',
                        'end_date' => null,
                    ],
                    [
                        'start_date' => '2017-03-01',
                        'end_date' => '2017-03-05',
                    ],
                ],
                [
                    [
                        'start_date' => '2017-01-02',
                        'end_date' => '2017-01-03',
                    ],
                    [
                        'start_date' => '2017-01-05',
                        'end_date' => null,
                    ],
                    [
                        'start_date' => '2017-01-08',
                        'end_date' => '2017-01-09',
                    ],
                    [
                        'start_date' => '2017-01-09',
                        'end_date' => '2017-01-15',
                    ],
                    [
                        'start_date' => '2017-01-17',
                        'end_date' => null,
                    ],
                    [
                        'start_date' => '2017-01-25',
                        'end_date' => '2017-02-05',
                    ],
                    [
                        'start_date' => '2017-02-07',
                        'end_date' => '2017-03-02',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test case for difference method.
     *
     * @param array $expected Expected result.
     * @param array $dateRanges1 Date Ranges.
     * @param array $dateRanges2 Date Ranges.
     * @return void
     *
     * @covers ::diff()
     * @dataProvider diffProvider()
     */
    public function testDiff(array $expected, array $dateRanges1, array $dateRanges2)
    {
        $expected = $this->DateRanges->newEntities($expected);
        $dateRanges1 = $this->DateRanges->newEntities($dateRanges1);
        $dateRanges2 = $this->DateRanges->newEntities($dateRanges2);

        $result = DateRange::diff($dateRanges1, $dateRanges2);

        $expected = json_decode(json_encode($expected), true);
        $result = json_decode(json_encode($result), true);

        static::assertEquals($expected, $result);
    }

    /**
     * Data provider for `testCheckWellFormed` test case.
     *
     * @return array
     */
    public function checkWellFormedProvider()
    {
        return [
            'not a date range' => [
                new \LogicException('Invalid Date Range entity class: expected "BEdita\Core\Model\Entity\DateRange", got "resource"'),
                [
                    fopen(__FILE__, 'r'),
                ],
                false,
            ],
            'not a date range /2' => [
                new \LogicException('Invalid Date Range entity class: expected "BEdita\Core\Model\Entity\DateRange", got "Cake\ORM\TableRegistry"'),
                [
                    new TableRegistry(),
                ],
                false,
            ],
            'invalid start date' => [
                new \LogicException('Invalid "start_date": expected "Cake\I18n\Time", got "NULL"'),
                [
                    [
                        'start_date' => null,
                        'end_date' => '2017-01-01',
                    ],
                ],
            ],
            'invalid end date' => [
                new \LogicException('Invalid "end_date": expected "Cake\I18n\Time", got "string"'),
                [
                    [
                        'start_date' => '2017-01-01',
                        'end_date' => 'better than yesterday, worse than tomorrow',
                    ],
                ],
            ],
            'ok' => [
                true,
                [
                    [
                        'start_date' => '2017-01-01',
                        'end_date' => null,
                    ],
                ],
            ],
        ];
    }

    /**
     * Test case for checker method.
     *
     * @param bool|\Exception $expected Expected result
     * @param array $dateRanges Date Ranges.
     * @param bool $marshal Should entities be marshalled first?
     * @return void
     *
     * @covers ::checkWellFormed()
     * @dataProvider checkWellFormedProvider()
     */
    public function testCheckWellFormed($expected, array $dateRanges, $marshal = true)
    {
        if ($expected instanceof \Exception) {
            static::expectException(get_class($expected));
            static::expectExceptionMessage($expected->getMessage());
        }

        if ($marshal) {
            $dateRanges = $this->DateRanges->newEntities($dateRanges);
        }

        DateRange::checkWellFormed(...$dateRanges);

        // If we reached this point, either we `$expect === true` or something went wrong.
        static::assertTrue($expected);
    }
}
