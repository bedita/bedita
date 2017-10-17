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

namespace BEdita\Core\Test\TestCase\Model\Validation;

use BEdita\Core\Model\Validation\NotReserved;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Validation\NotReserved} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Validation\NotReserved
 */
class NotReservedTest extends TestCase
{
    /**
     * Data provider for `testReserved` test case.
     *
     * @return array
     */
    public function reservedProvider()
    {
        return [
            'simple' => [
                'losers',
                true,
            ],
            'fail' => [
                'role',
                false,
            ],
        ];
    }

    /**
     * Test reserved rule.
     *
     * @param string|null $expected Expected alias, or `null`.
     * @param string $alias Alias to search for.
     * @return void
     *
     * @dataProvider reservedProvider()
     * @covers ::loadReserved()
     * @covers ::allowed()
     */
    public function testReserved($value, $expected)
    {
        $rule = new NotReserved();
        $result = $rule->allowed($value);
        static::assertEquals($expected, $result);
        // avoid reload in repeated allowed
        $rule->allowed($value);
    }
}
