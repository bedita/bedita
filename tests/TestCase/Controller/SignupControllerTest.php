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
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

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
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        Email::dropTransport('default');
        Email::setConfigTransport('default', [
            'className' => 'Debug'
        ]);
    }

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
            'missing activation_url' => [
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
                        'email' => 'gus.sup@channelweb.it',
                    ],
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
                    'meta' => [
                        'activation_url' => 'http://example.com',
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
                    'meta' => [
                        'activation_url' => 'http://example.com',
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
                    'meta' => [
                        'activation_url' => 'http://example.com',
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

    /**
     * Provider for `testActivationError()`
     *
     * @return array
     */
    public function activationErrorProvider()
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
                        'self' => 'http://api.example.com/signup/activation',
                        'home' => 'http://api.example.com/home',
                    ],
                ],
                'GET',
                []
            ],
            'missing uuid' => [
                400,
                [
                    'error' => [
                        'status' => '400',
                        'title' => 'Parameter "uuid" missing',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/signup/activation',
                        'home' => 'http://api.example.com/home',
                    ],
                ],
                'POST',
                []
            ],
            'not found uuid' => [
                404,
                [
                    'error' => [
                        'status' => '404',
                        'title' => 'Record not found in table "async_jobs"',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/signup/activation',
                        'home' => 'http://api.example.com/home',
                    ],
                ],
                'POST',
                [
                    'uuid' => '1111111'
                ]
            ],
            'not found user' => [
                404,
                [
                    'error' => [
                        'status' => '404',
                        'title' => 'Record not found in table "users"',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/signup/activation',
                        'home' => 'http://api.example.com/home',
                    ],
                ],
                'POST',
                [
                    'uuid' => '427ece75-71fb-4aca-bfab-1214cd98495a'
                ]
            ],
            'not valid async job' => [
                400,
                [
                    'error' => [
                        'status' => '400',
                        'title' => 'Invalid async job, missing user_id',
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/signup/activation',
                        'home' => 'http://api.example.com/home',
                    ],
                ],
                'POST',
                [
                    'uuid' => 'd6bb8c84-6b29-432e-bb84-c3c4b2c1b99c'
                ]
            ],
        ];
    }

    /**
     * Test some errors in /signup/activation endpoint
     *
     * @param int $statusCode Expected status code.
     * @param int $expected The expected content
     * @param string $method The HTTP method
     * @param array $data The payload to send
     * @return void
     *
     * @dataProvider activationErrorProvider
     * @covers ::activation()
     */
    public function testActivationError($statusCode, $expected, $method, $data)
    {
        $this->configRequestHeaders($method, [
            'Content-Type' => 'application/json'
        ]);
        $methodName = strtolower($method);
        $this->$method('/signup/activation', json_encode($data));

        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode($statusCode);
        static::assertArrayNotHasKey('data', $result);
        static::assertArrayHasKey('links', $result);
        static::assertArrayHasKey('error', $result);
        static::assertEquals($expected['links'], $result['links']);
        static::assertArraySubset($expected['error'], $result['error']);
        static::assertArrayHasKey('title', $result['error']);
        static::assertNotEmpty($result['error']['title']);
    }

    /**
     * Test success in /signup/activation endpoint
     *
     * @return void
     *
     * @covers ::activation()
     */
    public function testActivationOk()
    {
        // signup
        $this->configRequestHeaders('POST');
        $data = [
            'type' => 'users',
            'attributes' => [
                'username' => 'gustavo',
                'password' => 'password',
                'email' => 'gus.sup@channelweb.it',
            ],
            'meta' => [
                'activation_url' => 'http://example.com',
            ],
        ];
        $this->post('/signup', json_encode(compact('data')));

        $asyncJob = TableRegistry::get('AsyncJobs')->find()
            ->order(['AsyncJobs.created' => 'DESC'])
            ->first();

        $activationData = ['uuid' => $asyncJob->uuid];

        $this->configRequestHeaders('POST', [
            'Content-Type' => 'application/json'
        ]);
        $this->post('/signup/activation', json_encode($activationData));

        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(204);
        static::assertNull($result);
    }

    /**
     * Test success in /signup/activation endpoint
     *
     * @return void
     *
     * @covers ::activation()
     */
    public function testActivationConflict()
    {
        // signup
        $this->configRequestHeaders('POST');
        $data = [
            'type' => 'users',
            'attributes' => [
                'username' => 'gustavo',
                'password' => 'password',
                'email' => 'gus.sup@channelweb.it',
            ],
            'meta' => [
                'activation_url' => 'http://example.com',
            ],
        ];
        $this->post('/signup', json_encode(compact('data')));

        $asyncJob = TableRegistry::get('AsyncJobs')->find()
            ->order(['AsyncJobs.created' => 'DESC'])
            ->first();

        $activationData = ['uuid' => $asyncJob->uuid];

        $Users = TableRegistry::get('Users');
        $user = $Users->find()
            ->order(['created' => 'DESC'])
            ->first();

        $user->status = 'on';
        $Users->save($user);

        $this->configRequestHeaders('POST', [
            'Content-Type' => 'application/json'
        ]);
        $this->post('/signup/activation', json_encode($activationData));

        $result = json_decode((string)$this->_response->getBody(), true);
        $result = Hash::remove($result, 'error.meta');

        $expected = [
            'error' => [
                'status' => '409',
                'title' => 'User already active',
            ],
            'links' => [
                'self' => 'http://api.example.com/signup/activation',
                'home' => 'http://api.example.com/home',
            ],
        ];

        $this->assertResponseCode(409);
        static::assertEquals($expected, $result);
    }
}
