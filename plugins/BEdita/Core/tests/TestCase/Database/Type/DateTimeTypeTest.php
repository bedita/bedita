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
     * Data provider for `testMarshalSuccess`.
     *
     * @return array
     */
    public function marshalSuccessProvider()
    {
        return [
            [
                '2017-03-01 12:12:12',
                '2017-03-01 12:12:12',
            ],
            [
                '2017-12-31T23:59:59Z',
                '2017-12-31T23:59:59Z',
            ],
            [
                '2018-01-01',
                '2018-01-01',
            ],
            [
                '2018-01-01 11:22',
                '2018-01-01 11:22',
            ],
            [
                '2017-01-01T11:22:33',
                '2017-01-01T11:22:33',
            ],
            [
                '2017-01-01 11:22:33',
                '2017-01-01 11:22:33',
            ],
            [
                '2017-01-01T11:22:33',
                '2017-01-01T11:22:33',
            ],
            [
                '2017-01-01 11:22:33Z',
                '2017-01-01 11:22:33Z',
            ],
            [
                '2017-04-01T11:22:33+10:00',
                '2017-04-01T11:22:33+10:00',
            ],
            [
                '2017-01-01T19:20:30.45+01:00',
                '2017-01-01T19:20:30.45+01:00',
            ],
            'datetime' => [
                new DateTime('2000-01-01 00:00:00'),
                new DateTime('2000-01-01 00:00:00'),
            ],
            'success immutable' => [
                '2017-03-01 12:12:12',
                '2017-03-01 12:12:12',
                true,
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
     * @dataProvider marshalSuccessProvider
     * @covers ::marshal
     */
    public function testMarshalSuccess($expected, $input, $useImmutable = false)
    {
        $dateTimeType = new DateTimeType();
        if ($useImmutable) {
            $dateTimeType->useImmutable();
        }

        $result = $dateTimeType->marshal($input);
        if (is_string($expected)) {
            static::assertInstanceOf($dateTimeType->getDateTimeClassName(), $result);
            $expected = Time::parse($expected);
        }
        static::assertSame($expected->getTimestamp(), $result->getTimestamp());
    }

    /**
     * Data provider for `testMarshalFailure`.
     *
     * @return array
     */
    public function marshalFailureProvider()
    {
        return [
            [
                '20170301121212',
            ],
            [
                '2017 1 1',
            ],
            [
                '05 march 2017',
            ],
            [
                [1, 2, 3],
            ],
            [
                new \stdClass(),
            ],
        ];
    }

    /**
     * Test `marshal` method with invalid input
     *
     * @param mixed $input Input data to be marshaled.
     * @return void
     *
     * @dataProvider marshalFailureProvider
     * @covers ::marshal
     */
    public function testMarshalFailure($input)
    {
        $dateTimeType = new DateTimeType();
        $result = $dateTimeType->marshal($input);

        static::assertSame($input, $result);
    }

    /**
     * Test empty string `marshal`
     *
     * @return void
     *
     * @covers ::marshal
     */
    public function testMarshalEmpty()
    {
        $dateTimeType = new DateTimeType();
        $result = $dateTimeType->marshal('');
        static::assertNull($result);

        $result = $dateTimeType->marshal(false);
        static::assertNull($result);
    }
}
