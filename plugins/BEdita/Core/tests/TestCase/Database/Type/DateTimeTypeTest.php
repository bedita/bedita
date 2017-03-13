<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Database\Type;

use BEdita\Core\Database\Type\DateTimeType;
use Cake\I18n\Time;
use Cake\TestSuite\TestCase;
use DateTime;

/**
 * {@see \BEdita\Core\Database\Type\DateTimeType} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Database\Type\DateTimeType
 */
class DateTimeTypeTest extends TestCase
{

    /**
     * Data provider for `testMarshal`
     */
    public function marshalProvider()
    {
        return [
            'success' => [
                [
                    '2017-03-01 12:12:12',
                    '2017-12-31T23:59:59Z',
                    '2018-01-01',
                    '2018-01-01 11:22',
                    '2017-01-01T11:22:33',
                    '2017-01-01 11:22:33',
                    '2017-01-01T11:22:33',
                    '2017-01-01 11:22:33Z',
                    '2017-04-01T11:22:33+10:00',
                    '2017-01-01T19:20:30.45+01:00',
                ],
                true
            ],
            'fail' => [
                [
                    '20170301121212',
                    '2017 1 1',
                    '05 march 2017',
                ],
                false
            ],
            'datetime' => [
                [
                    new DateTime(),
                ],
                false
            ],
        ];
    }

    /**
     * Test `marshal` method
     *
     * @return void
     * @dataProvider marshalProvider
     * @covers ::marshal
     */
    public function testMarshal($dates, $success)
    {
        $dateTimeType = new DateTimeType();
        foreach ($dates as $date) {
            $result = $dateTimeType->marshal($date);
            if ($success) {
                $this->assertEquals(Time::parse($date)->toUnixString(), $result->toUnixString());
            } else {
                $this->assertEquals($date, $result);
            }
        }
    }
}
