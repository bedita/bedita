<?php
declare(strict_types=1);

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
use Cake\I18n\DateTime;
use Cake\TestSuite\TestCase;
use DateTime as PHPdateTime;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \BEdita\Core\Database\Type\DateType} Test Case
 */
#[CoversClass(DateType::class)]
class DateTypeTest extends TestCase
{
    /**
     * Data provider for `testMarshal`.
     *
     * @return array
     */
    public static function marshalProvider(): array
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
            [
                '2024-11-18',
                '2024-11-18T00:10:00+00:00',
            ],
            'datetime' => [
                new DateTime('2008-02-01 00:00:00'),
                new DateTime('2008-02-01 11:12:00'),
            ],
            'PHP datetime' => [
                '2024-11-18',
                new PHPdateTime('2024-11-18'),
            ],
        ];
    }

    /**
     * Test `marshal` method
     *
     * @param \DateTimeInterface|string $expected Expected result
     * @param mixed $input Input data to be marshaled.
     * @return void
     */
    #[DataProvider('marshalProvider')]
    public function testMarshal($expected, $input): void
    {
        $dateType = new DateType();
        $result = $dateType->marshal($input);
        if (is_string($expected)) {
            static::assertInstanceOf($dateType->getDateClassName(), $result);
            $expected = DateTime::parse($expected);
        }
        static::assertSame($expected->format('U'), $result->format('U'));
    }
}
