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
namespace BEdita\API\Test\TestCase\Controller;

use BEdita\API\TestSuite\IntegrationTestCase;

/**
 * @coversDefaultClass \BEdita\API\Controller\HistoryController
 */
class HistoryControllerTest extends IntegrationTestCase
{
    /**
     * Test index method.
     *
     * @return void
     *
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testIndex()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/history?filter%5Bresource_id%5D=2&filter%5Bresource_type%5D=objects',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/history?filter%5Bresource_id%5D=2&filter%5Bresource_type%5D=objects',
                'last' => 'http://api.example.com/history?filter%5Bresource_id%5D=2&filter%5Bresource_type%5D=objects',
                'prev' => null,
                'next' => null,
            ],
            'data' => [
                [
                    'id' => '1',
                    'type' => 'history',
                    'meta' => [
                        'resource_id' => 2,
                        'resource_type' => 'objects',
                        'created' => '2016-05-13T07:09:22+00:00',
                        'user_id' => 1,
                        'application_id' => 1,
                        'user_action' => 'create',
                        'changed' => '{"title":"title one","description":"description here"}',
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'history',
                    'meta' => [
                        'resource_id' => 2,
                        'resource_type' => 'objects',
                        'created' => '2016-05-13T07:09:23+00:00',
                        'user_id' => 5,
                        'application_id' => 1,
                        'user_action' => 'update',
                        'changed' => '{"body":"body here","extra":{"abstract":"abstract here","list": ["one", "two", "three"]}}',
                    ],
                ]
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
        ];

        $this->configRequestHeaders();
        $this->get('/history?filter[resource_id]=2&filter[resource_type]=objects');
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test empty view method.
     *
     * @return void
     *
     * @covers ::index()
     */
    public function testEmpty()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/history?filter%5Bresource_id%5D=999&filter%5Bresource_type%5D=objects',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/history?filter%5Bresource_id%5D=999&filter%5Bresource_type%5D=objects',
                'last' => 'http://api.example.com/history?filter%5Bresource_id%5D=999&filter%5Bresource_type%5D=objects',
                'prev' => null,
                'next' => null,
            ],
            'data' => [],
            'meta' => [
                'pagination' => [
                    'count' => 0,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 0,
                    'page_size' => 20,
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/history?filter[resource_id]=999&filter[resource_type]=objects');
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test `user_id` filter method.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testUser()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/history?filter%5Buser_id%5D=5',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/history?filter%5Buser_id%5D=5',
                'last' => 'http://api.example.com/history?filter%5Buser_id%5D=5',
                'prev' => null,
                'next' => null,
            ],
            'data' => [
                [
                    'id' => '2',
                    'type' => 'history',
                    'meta' => [
                        'resource_id' => 2,
                        'resource_type' => 'objects',
                        'created' => '2016-05-13T07:09:23+00:00',
                        'user_id' => 5,
                        'application_id' => 1,
                        'user_action' => 'update',
                        'changed' => '{"body":"body here","extra":{"abstract":"abstract here","list": ["one", "two", "three"]}}',
                    ],
                ]
            ],
            'meta' => [
                'pagination' => [
                    'count' => 1,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 1,
                    'page_size' => 20,
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/history?filter[user_id]=5');
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }
}
