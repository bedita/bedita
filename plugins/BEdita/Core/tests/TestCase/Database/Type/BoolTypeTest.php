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
namespace BEdita\Core\Test\TestCase\Database\Type;

use BEdita\Core\Database\Type\BoolType;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use InvalidArgumentException;

/**
 * {@see \BEdita\Core\Database\Type\BoolType} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Database\Type\BoolType
 */
class BoolTypeTest extends TestCase
{
    /**
     * Data provider for `testToDatabase`.
     *
     * @return array
     */
    public function toDatabaseProvider()
    {
        return [
            [
                1,
                true,
            ],
            [
                0,
                false,
            ],
            [
                "1",
                true,
            ],
            [
                "0",
                false,
            ],
            [
                true,
                true,
            ],
            [
                false,
                false,
            ],
            [
                null,
                null,
            ],
            [
                "true",
                true,
            ],
            [
                "false",
                false,
            ],
            [
                "gustavo",
                null,
            ],
            [
                [1, 2, 3],
                new InvalidArgumentException('Cannot convert value to bool'),
            ]
        ];
    }

    /**
     * Test `toDatabase` method
     *
     * @param mixed $input Input data to be marshaled.
     * @param mixed $expected Expected result
     * @return void
     *
     * @dataProvider toDatabaseProvider
     * @covers ::toDatabase()
     */
    public function testToDatabase($input, $expected)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }
        $boolType = new BoolType();
        $result = $boolType->toDatabase($input, ConnectionManager::get('default')->getDriver());

        static::assertSame($expected, $result);
    }
}
