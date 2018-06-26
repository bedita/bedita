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
namespace BEdita\API\Test\TestCase\Controller;

use BEdita\API\TestSuite\IntegrationTestCase;

/**
 * @coversDefaultClass \BEdita\API\Controller\TranslationsController
 */
class TranslationsControllerTest extends IntegrationTestCase
{
    /**
     * Test index method.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testIndex()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/translations',
                'first' => 'http://api.example.com/translations',
                'last' => 'http://api.example.com/translations',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'pagination' => [
                    'count' => 2,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 2,
                    'page_size' => 20,
                ],
            ],
            'data' => [
                [
                    'id' => '1',
                    'type' => 'translations',
                    'attributes' => [
                        'status' => 'on',
                        'lang' => 'it-IT',
                        'object_id' => 2,
                        'translated_fields' => null,
                    ],
                    'meta' => [
                        'created' => '2018-01-01T00:00:00+00:00',
                        'modified' => '2018-01-01T00:00:00+00:00',
                        'created_by' => 1,
                        'modified_by' => 1,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/translations/1',
                    ],
                    'relationships' => [
                        'object' => [
                            'links' => [
                                'related' => 'http://api.example.com/translations/1/object',
                                'self' => 'http://api.example.com/translations/1/relationships/object',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'translations',
                    'attributes' => [
                        'status' => 'on',
                        'lang' => 'fr',
                        'object_id' => 2,
                        'translated_fields' => null,
                    ],
                    'meta' => [
                        'created' => '2018-01-01T00:00:00+00:00',
                        'modified' => '2018-01-01T00:00:00+00:00',
                        'created_by' => 1,
                        'modified_by' => 1,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/translations/2',
                    ],
                    'relationships' => [
                        'object' => [
                            'links' => [
                                'related' => 'http://api.example.com/translations/2/object',
                                'self' => 'http://api.example.com/translations/2/relationships/object',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/translations');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test view method.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testSingle()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/translations/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'translations',
                'attributes' => [
                    'status' => 'on',
                    'lang' => 'it-IT',
                    'object_id' => 2,
                    'translated_fields' => null,
                ],
                'meta' => [
                    'created' => '2018-01-01T00:00:00+00:00',
                    'modified' => '2018-01-01T00:00:00+00:00',
                    'created_by' => 1,
                    'modified_by' => 1,
                ],
                'relationships' => [
                    'object' => [
                        'links' => [
                            'related' => 'http://api.example.com/translations/1/object',
                            'self' => 'http://api.example.com/translations/1/relationships/object',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/translations/1');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }
}
