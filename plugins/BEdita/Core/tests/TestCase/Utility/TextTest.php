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

namespace BEdita\Core\Test\TestCase\Utility;

use BEdita\Core\Utility\Text;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Utility\Text} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Utility\Text
 */
class TextTest extends TestCase
{

    /**
     * Data provider for `testUuid5` test case.
     *
     * @return array
     */
    public function uuid5Provider()
    {
        return [
            /* @see https://github.com/lootils/uuid/blob/master/test/UUIDTest.php */
            'nil' => [
                'd221f29a-4332-5f0d-b323-c5206a2e86ce',
                'team',
                Text::UUID_NIL,
            ],

            /* @see http://docs.python.org/library/uuid.html */
            'python.org' => [
                '886313e1-3b8a-5372-9b90-0c9aee199e5d',
                'python.org',
                Text::NAMESPACE_DNS,
            ],

            /* Generated using Python. */
            'bedita.com' => [
                '90f5f08a-184e-56b8-b8ed-ed7f7de5bb88',
                'bedita.com',
                Text::NAMESPACE_DNS,
            ],
            'http://bedita.com' => [
                '4610cf5d-8f9a-5f50-99bf-ff90c38f0661',
                'http://bedita.com',
                Text::NAMESPACE_URL,
            ],
            'Intel Corporation' => [
                '6aab0456-7392-582a-b92a-ba5a7096945d',
                '1.3.6.1.4.1.343',
                Text::NAMESPACE_OID,
            ],
        ];
    }

    /**
     * Test UUID version 5 generator.
     *
     * @param string $expected Expected result.
     * @param string $name Name.
     * @param string $namespace Namespace.
     * @return void
     *
     * @dataProvider uuid5Provider()
     * @covers ::uuid5()
     */
    public function testUuid5($expected, $name, $namespace)
    {
        $result = Text::uuid5($name, $namespace);

        static::assertSame($expected, $result);
    }
}
