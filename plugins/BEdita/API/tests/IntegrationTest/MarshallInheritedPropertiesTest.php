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

namespace BEdita\API\Test\IntegrationTest;

use BEdita\API\TestSuite\IntegrationTestCase;

/**
 * Test that the inherited propertiesare correctly marshalled into main entity.
 */
class MarshallInheritedPropertiesTest extends IntegrationTestCase
{
    /**
     * Provider for testMarshall()
     *
     * @return array
     */
    public function marshallProvider()
    {
        return [
            'date' => [
                [
                    'username' => 'ovatsug',
                    'password' => 'anonymous',
                    'publish_start' => '2018-02-20 00:00:00',
                ],
                [
                    'publish_start' => '2018-02-20T00:00:00+00:00',
                ],
            ],
            'emptyDate' => [
                [
                    'username' => 'ovatsug',
                    'password' => 'anonymous',
                    'publish_start' => '',
                ],
                [
                    'publish_start' => null,
                ],
            ],
        ];
    }

    /**
     * Test marshalled
     *
     * @param array $attributes The attributes to save
     * @param array $expected The expected results
     * @return void
     *
     * @dataProvider marshallProvider()
     * @coversNothing
     */
    public function testMarshall($attributes, $expected)
    {
        $data = [
            'type' => 'users',
            'attributes' => $attributes,
        ];

        $authHeader = $this->getUserAuthHeader();

        $this->configRequestHeaders('POST', $authHeader);
        $this->post('/users', json_encode(compact('data')));
        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $this->configRequestHeaders();
        $this->get('/users/' . $this->lastObjectId());
        $result = json_decode((string)$this->_response->getBody(), true);
        $actual = array_intersect_key($result['data']['attributes'], $expected);

        static::assertEquals($expected, $actual);
    }
}
