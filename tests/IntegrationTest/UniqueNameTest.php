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
 * Test on `uname` field
 */
class UniqueNameTest extends IntegrationTestCase
{
    /**
     * {@inheritDoc}
     */
    public $fixtures = [
        'plugin.BEdita/Core.locations',
    ];

    /**
     * Data provider for testDoubleInsert
     *
     * @return array
     */
    public function doubleInsertProvider()
    {
        return [
            'sameTitle' => [
                ['title' => 'test double test double'],
            ],
            'emptyTitle' => [
                ['title' => ''],
            ],
            'empty' => [
                []
            ],
        ];
    }

    /**
     * Test inserting the same data two times for different object types.
     *
     * @return void
     * @dataProvider doubleInsertProvider
     * @coversNothing
     */
    public function testDoubleInsert($attributes)
    {
        $authHeader = $this->getUserAuthHeader();

        $sendRequest = function ($type) use ($attributes, $authHeader) {
            $data = [
                'type' => $type,
                'attributes' => $attributes,
            ];
            $endpoint = '/' . $type;
            $requestBody = json_encode(compact('data'));
            $this->configRequestHeaders('POST', $authHeader);

            $this->post($endpoint, $requestBody);

            $this->assertResponseCode(201);
            $this->assertContentType('application/vnd.api+json');
            $this->assertHeader('Location', sprintf('http://api.example.com/%s/%s', $type, $this->lastObjectId()));
            $this->assertResponseNotEmpty();
            $body = json_decode((string)$this->_response->getBody(), true);
            static::assertArrayHasKey('data', $body);
            static::assertArrayHasKey('attributes', $body['data']);
            static::assertArrayHasKey('uname', $body['data']['attributes']);

            return $body;
        };

        foreach (['documents', 'locations'] as $type) {
            $bodyFirst = $sendRequest($type);
            $bodySecond = $sendRequest($type);
            $this->assertNotEquals($bodyFirst['data']['attributes']['uname'], $bodySecond['data']['attributes']['uname']);
        }
    }
}
