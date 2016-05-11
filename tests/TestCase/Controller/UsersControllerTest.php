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
use Cake\ORM\TableRegistry;
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
     * @covers ::initialize()
     */
    public function testIndex()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/users',
                'first' => 'http://api.example.com/users',
                'last' => 'http://api.example.com/users',
                'prev' => null,
                'next' => null,
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
                    'type' => 'users',
                    'attributes' => [
                        'username' => 'first user',
                        'blocked' => false,
                        'last_login' => null,
                        'last_login_err' => null,
                        'num_login_err' => 1,
                        'created' => '2016-03-15T09:57:38+0000',
                        'modified' => '2016-03-15T09:57:38+0000',
                    ],
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
        $this->get('/users');
        $result = json_decode($this->_response->body(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test index method.
     *
     * @return void
     *
     * @covers ::view()
     * @covers ::initialize()
     */
    public function testEmpty()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/users',
                'first' => 'http://api.example.com/users',
                'last' => 'http://api.example.com/users',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'pagination' => [
                    'count' => 0,
                    'page' => 1,
                    'page_count' => 0,
                    'page_items' => 0,
                    'page_size' => 20,
                ],
            ],
            'data' => [],
        ];

        TableRegistry::get('Users')->deleteAll([]);

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/users');
        $result = json_decode($this->_response->body(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test view method.
     *
     * @return void
     *
     * @covers ::view()
     * @covers ::initialize()
     */
    public function testSingle()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/users/1',
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
                ],
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/users/1');
        $result = json_decode($this->_response->body(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test view method.
     *
     * @return void
     *
     * @covers ::view()
     * @covers ::initialize()
     * @covers \BEdita\API\Error\ExceptionRenderer
     */
    public function testMissing()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/users/99',
            ],
            'error' => [
                'status' => '404',
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/users/99');
        $result = json_decode($this->_response->body(), true);

        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
        $this->assertArrayNotHasKey('data', $result);
        $this->assertArrayHasKey('links', $result);
        $this->assertArrayHasKey('error', $result);
        $this->assertEquals($expected['links'], $result['links']);
        $this->assertArraySubset($expected['error'], $result['error']);
        $this->assertArrayHasKey('title', $result['error']);
        $this->assertNotEmpty($result['error']['title']);
    }

    /**
     * Data provider for `testContentType` test case.
     *
     * @return array
     */
    public function contentTypeProvider()
    {
        return [
            'json' => [
                200,
                'application/json',
                'application/json',
            ],
            'jsonApi' => [
                200,
                'application/vnd.api+json',
                'application/vnd.api+json',
            ],
            'jsonApiWrongMediaType' => [
                415,
                'application/vnd.api+json',
                'application/vnd.api+json; m=test',
            ],
            'htmlNotAllowed' => [
                406,
                null,
                'text/html,application/xhtml+xml',
                [
                    'debug' => 0,
                    'Accept.html' => 0,
                ],
            ],
            'htmlDebugMode' => [
                200,
                'text/html',
                'text/html,application/xhtml+xml',
                [
                    'debug' => 1,
                    'Accept.html' => 0,
                ],
            ],
            'htmlAccepted' => [
                200,
                'text/html',
                'text/html,application/xhtml+xml',
                [
                    'debug' => 0,
                    'Accept.html' => 1,
                ],
            ],
        ];
    }

    /**
     * Test content type negotiation rules.
     *
     * @param int $expectedCode Expected response code.
     * @param string|null $expectedContentType Expected content type.
     * @param string $accept Request's "Accept" header.
     * @param array|null $config Configuration to be written.
     * @return void
     *
     * @dataProvider contentTypeProvider
     * @covers \BEdita\API\Controller\AppController
     * @covers \BEdita\API\Controller\Component\JsonApiComponent
     */
    public function testContentType($expectedCode, $expectedContentType, $accept, array $config = null)
    {
        Configure::write($config);

        $this->configRequest([
            'headers' => ['Accept' => $accept],
        ]);

        $this->get('/users');

        $this->assertResponseCode($expectedCode);
        if ($expectedContentType) {
            $this->assertContentType($expectedContentType);
        }
    }
}
