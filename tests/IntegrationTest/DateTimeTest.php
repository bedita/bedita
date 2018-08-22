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
namespace BEdita\API\Test\IntegrationTest;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\Utility\Hash;

/**
 * Test date & datetime input/output
 */
class DateTimeTest extends IntegrationTestCase
{
    public function dateTimeInputProvider()
    {
        return [
            'simple date' => [
                [
                    'birthdate' => '2018-02-02',
                ],
                [
                    'birthdate' => '2018-02-02',
                ],
            ],
            'date with time' => [
                [
                    'birthdate' => '2018-02-02',
                ],
                [
                    'birthdate' => '2018-02-02 12:30',
                ],
            ],
            'date with iso time' => [
                [
                    'birthdate' => '2018-01-02',
                ],
                [
                    'birthdate' => '2018-01-02T14:23:23+00:00',
                ],
            ],
            'simple date time' => [
                [
                    'publish_start' => '2018-08-02T14:23:23+00:00',
                ],
                [
                    'publish_start' => '2018-08-02 14:23:23',
                ],
            ],
            'datetime without time' => [
                [
                    'publish_start' => '2018-08-02T00:00:00+00:00',
                ],
                [
                    'publish_start' => '2018-08-02',
                ],
            ],
            'timezone' => [
                [
                    'publish_start' => '2018-08-02T16:23:23+00:00',
                ],
                [
                    'publish_start' => '2018-08-02T16:23:23+00:00',
                ],
            ],
            'date timestamp' => [
                [
                    'birthdate' => '2018-08-01',
                ],
                [
                    'birthdate' => '1533117600',
                ],
            ],
            'datetime timestamp' => [
                [
                    'publish_start' => '2018-08-01T10:00:00+00:00',
                ],
                [
                    'publish_start' => '1533117600',
                ],
            ],
            'date fail' => [
                [
                    'error' => [
                        'status' => '400',
                        'title' => 'Invalid data',
                        'detail' => '[birthdate.date]: Invalid date or datetime "July, 1"',
                    ],
                ],
                [
                    'birthdate' => 'July, 1',
                ],
            ],
            'datetime fail' => [
                [
                    'error' => [
                        'status' => '400',
                        'title' => 'Invalid data',
                        'detail' => '[birthdate.date]: Invalid date or datetime "gustavo"',
                    ],
                ],
                [
                    'birthdate' => 'gustavo',
                ],
            ],
            'bad datetime' => [
                [
                    'error' => [
                        'status' => '400',
                        'title' => 'Invalid data',
                        'detail' => '[birthdate.date]: Invalid date or datetime "2008 June, 1"',
                    ],
                ],
                [
                    'birthdate' => '2008 June, 1',
                ],
            ],
        ];
    }

    /**
     * Test date & date time save using `profiles`
     *
     * @param array $expected Extected result
     * @param array $input Input data
     * @dataProvider dateTimeInputProvider
     * @return void
     * @coversNothing
     */
    public function testDateTimeInput($expected, $input)
    {
        $data = [
            'type' => 'profiles',
            'id' => '4',
            'attributes' => $input,
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/profiles/4', json_encode(compact('data')));
        $this->assertResponseCode((int)Hash::get($expected, 'error.status', 200));
        $result = json_decode((string)$this->_response->getBody(), true);

        if (empty($expected['error'])) {
            $attributes = Hash::get($result, 'data.attributes', []);
        } else {
            $attributes = Hash::get($result, 'error', []);
            unset($attributes['meta']);
            $expected = $expected['error'];
        }
        static::assertNotEmpty($attributes);
        $fields = array_intersect_key($attributes, $expected);
        static::assertEquals($expected, $fields);
    }
}
