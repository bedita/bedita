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

use BEdita\Core\Database\Type\DateType;
use Cake\I18n\Time;
use Cake\TestSuite\TestCase;
use DateTime;

/**
 * {@see \BEdita\Core\Database\Type\DateType} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Database\Type\DateType
 */
class DateTypeTest extends TestCase
{

    /**
     * Data provider for `testMarshal`.
     *
     * @return array
     */
    public function marshalProvider()
    {
        return [
            [
                '2018-03-01',
                '2018-03-01 12:12:12',
            ],
            [
                '2017-12-31',
                '2017-12-31T23:59:59Z',
            ],
            [
                '2018-01-01',
                '2018-01-01',
            ],
            [
                '2018-01-01',
                '2018-01-01 11:22',
            ],
            [
                '2017-01-01',
                '2017-01-01T11:22:33',
            ],
            [
                '2018-08-01',
                1533117600,
            ],
            'datetime' => [
                new DateTime('2008-02-01 00:00:00'),
                new DateTime('2008-02-01 11:12:00'),
            ],
        ];
    }

    /**
     * Test `marshal` method
     *
     * @param \DateTimeInterface|string $expected Expected result
     * @param mixed $input Input data to be marshaled.
     * @param bool $useImmutable Should immutable datetime objects be used?
     * @return void
     *
     * @dataProvider marshalProvider
     * @covers ::marshal
     */
    public function testMarshal($expected, $input, $useImmutable = false)
    {
        $dateTimeType = new DateType();
        $result = $dateTimeType->marshal($input);
        if (is_string($expected)) {
            static::assertInstanceOf($dateTimeType->getDateTimeClassName(), $result);
            $expected = Time::parse($expected);
        }
        static::assertSame($expected->getTimestamp(), $result->getTimestamp());
    }
}
