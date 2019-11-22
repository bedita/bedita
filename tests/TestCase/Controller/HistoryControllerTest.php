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
     * Test view method.
     *
     * @return void
     *
     * @covers ::view()
     * @covers ::initialize()
     * @covers ::checkExistence()
     */
    public function testView()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/history/2',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/history/2',
                'last' => 'http://api.example.com/history/2',
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
        $this->get('/history/2');
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
     * @covers ::view()
     */
    public function testEmpty()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/history/3',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/history/3',
                'last' => 'http://api.example.com/history/3',
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
        $this->get('/history/3');
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test missing data method.
     *
     * @return void
     *
     * @covers ::view()
     * @covers ::checkExistence()
     */
    public function testMissing()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/history/999',
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '404',
                'title' => 'Unable to find "Objects" with ID "999"'
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/history/999');
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
        unset($result['error']['meta']);
        $this->assertEquals($expected, $result);
    }

    /**
     * Test `user` method.
     *
     * @return void
     *
     * @covers ::user()
     * @covers ::checkExistence()
     */
    public function testUser()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/history/user/5',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/history/user/5',
                'last' => 'http://api.example.com/history/user/5',
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
        $this->get('/history/user/5');
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }
}
