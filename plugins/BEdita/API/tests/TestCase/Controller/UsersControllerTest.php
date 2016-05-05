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

use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestCase;

/**
 * @coversDefaultClass \BEdita\API\Controller\UsersController
 */
class UsersControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.users',
    ];

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
                'self' => 'http://api.example.com/users'
            ],
            'data' => [
                [
                    'id' => '1',
                    'type' => 'users',
                    'attributes' => [
                        'username' => 'first user',
                        'blocked' => false,
                        'last_login' => null,
                        'last_login_err' => null,
                        'num_login_err' => 1,
                        'created' => '2016-03-15T09:57:38+0000',
                        'modified' => '2016-03-15T09:57:38+0000',
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'users',
                    'attributes' => [
                        'username' => 'second user',
                        'blocked' => false,
                        'last_login' => '2016-03-15T09:57:38+0000',
                        'last_login_err' => '2016-03-15T09:57:38+0000',
                        'num_login_err' => 0,
                        'created' => '2016-03-15T09:57:38+0000',
                        'modified' => '2016-03-15T09:57:38+0000',
                    ]
                ],
            ]
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/json',
            ],
        ]);
        $this->get('/users');
        $result = json_decode($this->_response->body(), true);

        $this->assertResponseCode(200);
        $this->assertEquals($expected, $result);
    }

    /**
     * Test view method.
     *
     * @return void
     *
     * @covers ::view()
     */
    public function testSingle()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/users/1'
            ],
            'data' => [
                'id' => '1',
                'type' => 'users',
                'attributes' => [
                    'username' => 'first user',
                    'blocked' => false,
                    'last_login' => null,
                    'last_login_err' => null,
                    'num_login_err' => 1,
                    'created' => '2016-03-15T09:57:38+0000',
                    'modified' => '2016-03-15T09:57:38+0000',
                ]
            ]
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/json',
            ],
        ]);
        $this->get('/users/1');
        $result = json_decode($this->_response->body(), true);

        $this->assertResponseCode(200);
        $this->assertEquals($expected, $result);
    }

    /**
     * Test view method.
     *
     * @return void
     *
     * @covers ::view()
     */
    public function testMissing()
    {
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/json',
            ],
        ]);
        $this->get('/users/99');

        $this->assertResponseCode(404);
    }


    public function testContentType()
    {
        $this->configRequest([
            'headers' => ['Accept' => 'application/json']
        ]);
        $result = $this->get('/users');
        $this->assertResponseOk();
        $this->assertContentType('application/json');

        $this->configRequest([
            'headers' => ['Accept' => 'application/vnd.api+json']
        ]);
        $result = $this->get('/users');
        $this->assertResponseOk();
        $this->assertContentType('application/vnd.api+json');

        $this->configRequest([
            'headers' => ['Accept' => 'application/vnd.api+json; m=test']
        ]);
        $result = $this->get('/users');
        $this->assertResponseError();
        // http://jsonapi.org/format/#content-negotiation-servers
        $this->assertResponseCode(415);

        Configure::write('Accept.html', 0);
        Configure::write('debug', 0);
        $this->configRequest([
            'headers' => ['Accept' => 'text/html,application/xhtml+xml']
        ]);
        $result = $this->get('/users');
        $this->assertResponseError();
        $this->assertResponseCode(406);

        Configure::write('Accept.html', 1);
        $this->configRequest([
            'headers' => ['Accept' => 'text/html,application/xhtml+xml']
        ]);
        $result = $this->get('/users');
        $this->assertResponseCode(200);
        $this->assertContentType('text/html');

        Configure::write('Accept.html', 0);
        Configure::write('debug', 1);
        $this->configRequest([
            'headers' => ['Accept' => 'text/html,application/xhtml+xml']
        ]);
        $result = $this->get('/users');
        $this->assertResponseCode(200);
        $this->assertContentType('text/html');
    }
}
