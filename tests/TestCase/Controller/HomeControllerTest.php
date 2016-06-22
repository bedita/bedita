<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestCase;

/**
 * @coversDefaultClass \BEdita\API\Controller\HomeController
 */
class HomeControllerTest extends IntegrationTestCase
{

    /**
     * Test index method.
     *
     * @return void
     *
     * @covers ::index()
     */
    public function testIndex()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/home',
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'resources' => [
                    '/objects' => [
                        'href' => 'http://api.example.com/objects',
                        'hints' => [
                            'allow' => [
                                'GET'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ]
                        ],
                    ],
                    '/users' => [
                        'href' => 'http://api.example.com/users',
                        'hints' => [
                            'allow' => [
                                'GET'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ]
                        ],
                    ],
                    '/roles' => [
                        'href' => 'http://api.example.com/roles',
                        'hints' => [
                            'allow' => [
                                'GET'
                            ],
                            'formats' => [
                                'application/json',
                                'application/vnd.api+json'
                            ]
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/home');
        $result = json_decode($this->_response->body(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }
}
