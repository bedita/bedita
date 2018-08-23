<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Database\Type;

use BEdita\Core\Database\Type\JsonObjectType;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Database\Type\JsonObjectType
 */
class JsonObjectTypeTest extends TestCase
{

    /**
     * Data provider for `testToPHP` test case.
     *
     * @return array
     */
    public function toPHPProvider()
    {
        $obj = new \stdClass();
        $obj->firstName = 'Gustavo';
        $obj->lastName = 'Supporto';
        $obj->age = 42;
        $obj->skills = [];
        $obj->randomEmptyObject = new \stdClass();

        return [
            'string' => [
                'gustavo',
                '"gustavo"',
            ],
            'number' => [
                42,
                '42',
            ],
            'boolean' => [
                true,
                'true',
            ],
            'null' => [
                null,
                'null',
            ],
            'array' => [
                ['Gustavo', 'Supporto', 42, true],
                '["Gustavo","Supporto",42,true]',
            ],
            'empty array' => [
                [],
                '[]',
            ],
            'empty object' => [
                new \stdClass(),
                '{}',
            ],
            'complex' => [
                $obj,
                '{"firstName":"Gustavo","lastName":"Supporto","age":42,"skills":[],"randomEmptyObject":{}}',
            ],
        ];
    }

    /**
     * Test `toPHP()` method.
     *
     * @param mixed $expected Expected result.
     * @param string $value Value to be decoded.
     * @return void
     *
     * @dataProvider toPHPProvider()
     * @covers ::toPHP()
     */
    public function testToPHP($expected, $value)
    {
        /** @var \Cake\Database\Connection $connection */
        $connection = ConnectionManager::get('default');

        $type = new JsonObjectType();
        $actual = $type->toPHP($value, $connection->getDriver());

        if (is_scalar($expected)) {
            static::assertSame($expected, $actual);
        } else {
            static::assertEquals($expected, $actual);
        }
    }
}
