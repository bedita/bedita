<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
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
use Cake\Core\Configure;

/**
 * Test `DefaultValues` config upon object creation
 */
class DefaultValuesTest extends IntegrationTestCase
{
    /**
     * {@inheritDoc}
     */
    public $fixtures = [
        'plugin.BEdita/Core.Streams',
    ];

    /**
     * Data provider for testCreate
     *
     * @return array
     */
    public function createProvider() : array
    {
        return [
            'files on' => [
                ['status' => 'on'],
                'files',
                ['title' => 'My File'],
                [
                    'files' => [
                        'status' => 'on',
                    ],
                ],
            ],

            'files draft' => [
                ['status' => 'draft'],
                'files',
                ['title' => 'My File'],
                [],
            ],

            'folders description' => [
                ['description' => 'some text'],
                'folders',
                ['title' => 'My Folder'],
                [
                    'folders' => [
                        'description' => 'some text',
                    ],
                ],
            ],

            'locations country' => [
                ['country_name' => 'Italy'],
                'locations',
                ['title' => 'My Location'],
                [
                    'locations' => [
                        'country_name' => 'Italy',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test create a new object and verify DefaultValues.
     *
     * @param array $expected Expected result.
     * @param string $type Object type.
     * @param array $attributes Object attributes.
     * @param array $config DefaultValues config.
     * @return void
     *
     * @dataProvider createProvider
     * @coversNothing
     */
    public function testCreate(array $expected, string $type, array $attributes, array $config) : void
    {
        Configure::write('DefaultValues', $config);

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $data = [
            'type' => $type,
            'attributes' => $attributes,
        ];

        $endpoint = '/' . $type;
        $requestBody = json_encode(compact('data'));
        $this->post($endpoint, $requestBody);

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        $this->assertHeader('Location', sprintf('http://api.example.com/%s/%s', $type, $this->lastObjectId()));
        $this->assertResponseNotEmpty();
        $body = json_decode((string)$this->_response->getBody(), true);

        static::assertArrayHasKey('data', $body);
        static::assertArrayHasKey('attributes', $body['data']);

        $attributes = array_intersect_key($body['data']['attributes'], $expected);
        static::assertEquals($expected, $attributes);
    }
}
