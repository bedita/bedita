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
namespace BEdita\API\Test\TestCase\Controller;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\Mailer\Email;

/**
 * @coversDefaultClass \BEdita\API\Controller\SignupController
 */
class SignupControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.async_jobs',
    ];

    /**
     * Provider for `testSignup()`
     *
     * @return array
     */
    public function signupProvider()
    {
        return [
            'not allowed' => [
                405,
                [
                    'error' => [
                        'status' => '405',
                        'title' => 'Method Not Allowed',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/signup',
                        'home' => 'http://api.example.com/home',
                    ],
                ],
                'GET',
                [],
            ],
            'ok' => [
                202,
                [
                    'data' => [
                        'type' => 'users',
                        'attributes' => [
                            'username' => 'gustavo',
                            'email' => 'gus.sup@channelweb.it',
                        ],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/signup',
                        'home' => 'http://api.example.com/home',
                    ],
                ],
                'POST',
                [
                    'type' => 'users',
                    'attributes' => [
                        'username' => 'gustavo',
                        'password' => 'supporto',
                        'email' => 'gus.sup@channelweb.it',
                    ]
                ],
            ],
            'ok with activation_url' => [
                202,
                [
                    'data' => [
                        'type' => 'users',
                        'attributes' => [
                            'username' => 'gustavo',
                            'email' => 'gus.sup@channelweb.it',
                        ],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/signup',
                        'home' => 'http://api.example.com/home',
                    ],
                ],
                'POST',
                [
                    'type' => 'users',
                    'attributes' => [
                        'username' => 'gustavo',
                        'password' => 'supporto',
                        'email' => 'gus.sup@channelweb.it',
                    ],
                    'meta' => [
                        'activation_url' => 'http://example.com'
                    ]
                ],
            ],
            'ok with activation_url and redirect_url' => [
                202,
                [
                    'data' => [
                        'type' => 'users',
                        'attributes' => [
                            'username' => 'gustavo',
                            'email' => 'gus.sup@channelweb.it',
                        ],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/signup',
                        'home' => 'http://api.example.com/home',
                    ],
                ],
                'POST',
                [
                    'type' => 'users',
                    'attributes' => [
                        'username' => 'gustavo',
                        'password' => 'supporto',
                        'email' => 'gus.sup@channelweb.it',
                    ],
                    'meta' => [
                        'activation_url' => 'http://example.com',
                        'redirect_url' => 'myapp://'
                    ]
                ],
            ],
            'missing username' => [
                400,
                [
                    'error' => [
                        'status' => '400',
                        'title' => 'Invalid data',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/signup',
                        'home' => 'http://api.example.com/home',
                    ],
                ],
                'POST',
                [
                    'type' => 'users',
                    'attributes' => [
                        'username' => '',
                        'password' => 'supporto',
                        'email' => 'gus.sup@channelweb.it',
                    ],
                ],
            ],
            'missing password' => [
                400,
                [
                    'error' => [
                        'status' => '400',
                        'title' => 'Invalid data',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/signup',
                        'home' => 'http://api.example.com/home',
                    ],
                ],
                'POST',
                [
                    'type' => 'users',
                    'attributes' => [
                        'username' => 'gustavo',
                        'email' => 'gus.sup@channelweb.it',
                    ],
                ],
            ],
            'missing email' => [
                400,
                [
                    'error' => [
                        'status' => '400',
                        'title' => 'Invalid data',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/signup',
                        'home' => 'http://api.example.com/home',
                    ],
                ],
                'POST',
                [
                    'type' => 'users',
                    'attributes' => [
                        'username' => 'gustavo',
                        'password' => 'supporto',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test /signup endpoint
     *
     * @param int $statusCode Expected status code.
     * @param int $expected The expected content
     * @param string $method The HTTP method
     * @param array $data The payload to send
     * @return void
     *
     * @dataProvider signupProvider
     * @covers ::signup()
     */
    public function testSignup($statusCode, $expected, $method, $data)
    {
        Email::dropTransport('default');
        Email::setConfigTransport('default', [
            'className' => 'Debug'
        ]);

        $this->configRequestHeaders($method);
        $methodName = strtolower($method);
        $this->$methodName('/signup', json_encode(compact('data')));

        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode($statusCode);
        $this->assertContentType('application/vnd.api+json');

        if ($statusCode === 202) {
            static::assertArrayNotHasKey('error', $result);
            static::assertArrayHasKey('links', $result);
            static::assertArrayHasKey('data', $result);
            static::assertEquals($expected['links'], $result['links']);
            static::assertArraySubset($expected['data'], $result['data']);
        } else {
            static::assertArrayNotHasKey('data', $result);
            static::assertArrayHasKey('links', $result);
            static::assertArrayHasKey('error', $result);
            static::assertEquals($expected['links'], $result['links']);
            static::assertArraySubset($expected['error'], $result['error']);
            static::assertArrayHasKey('title', $result['error']);
            static::assertNotEmpty($result['error']['title']);
        }
    }
}
