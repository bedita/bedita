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

namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Model\Table\DateRangesTable;
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
    public $fixtures = [
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.date_ranges',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->DateRanges = TableRegistry::get('DateRanges');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->DateRanges);

        parent::tearDown();
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
                    'startAfter' => '2017-01-01',
                ],
                1,
            ],
            'startBefore' => [
                [
                    'startBefore' => '2017-01-01',
                ],
                0,
            ],
            'endBefore' => [
                [
                    'endBefore' => '2017-01-01',
                ],
                0,
            ],
            'endAfter' => [
                [
                    'endAfter' => '2017-01-01',
                ],
                1,
            ],
            'combinedOK' => [
                [
                    'startAfter' => '2017-03-01',
                    'endBefore' => '2017-04-01',
                ],
                1,
            ],
            'combinedKO' => [
                [
                    'startBefore' => '2017-01-01',
                    'endAfter' => '2017-05-01',
                ],
                0,
            ],
        ];
    }

    /**
     * Test object date range finder.
     *
     * @param array $conditions Date conditions.
     * @param array|false $numExpected Number of expected results.
     * @return void
     *
     * @dataProvider findDateProvider
     * @covers ::findDate()
     */
    public function testFindDate($conditions, $numExpected)
    {
        $objects = TableRegistry::get('Objects');
        $result = $objects->find()->find('date', $conditions)->toArray();

        $this->assertEquals($numExpected, count($result));
    }
}
