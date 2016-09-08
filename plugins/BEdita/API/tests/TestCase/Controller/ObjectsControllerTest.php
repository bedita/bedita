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
 * @coversDefaultClass \BEdita\API\Controller\ObjectsController
 */
class ObjectsControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.object_types',
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
                'self' => 'http://api.example.com/objects',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/objects',
                'last' => 'http://api.example.com/objects',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'pagination' => [
                    'count' => 3,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 3,
                    'page_size' => 20,
                ],
            ],
            'data' => [
                [
                    'id' => '1',
                    'type' => 'document',
                    'attributes' => [
                        'object_type_id' => 1,
                        'status' => 'on',
                        'uname' => 'title-one',
                        'locked' => true,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => '2016-05-13T07:09:23+00:00',
                        'title' => 'title one',
                        'description' => 'description here',
                        'body' => 'body here',
                        'extra' => [
                            'abstract' => 'abstract here',
                            'list' => "[\"one\", \"two\", \"three\"]"
                        ],
                        'lang' => 'eng',
                        'created_by' => 1,
                        'modified_by' => 1,
                        'publish_start' => '2016-05-13T07:09:23+00:00',
                        'publish_end' => '2016-05-13T07:09:23+00:00'
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/objects/1',
                    ]
                ],
                [
                    'id' => '2',
                    'type' => 'document',
                    'attributes' => [
                        'object_type_id' => 1,
                        'status' => 'draft',
                        'uname' => 'title-two',
                        'locked' => false,
                        'created' => '2016-05-12T07:09:23+00:00',
                        'modified' => '2016-05-13T08:30:00+00:00',
                        'published' => null,
                        'title' => 'title two',
                        'description' => 'description here',
                        'body' => 'body here',
                        'extra' => null,
                        'lang' => 'eng',
                        'created_by' => 1,
                        'modified_by' => 2,
                        'publish_start' => null,
                        'publish_end' => null
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/objects/2',
                    ]
                ],
                [
                    'id' => '3',
                    'type' => 'profile',
                    'attributes' => [
                        'object_type_id' => 2,
                        'status' => 'on',
                        'uname' => 'gustavo-supporto',
                        'locked' => false,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => null,
                        'title' => 'Gustavo Supporto profile',
                        'description' => 'Some description about Gustavo',
                        'lang' => 'eng',
                        'created_by' => 1,
                        'modified_by' => 1,
                        'body' => null,
                        'extra' => null,
                        'publish_start' => null,
                        'publish_end' => null
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/objects/3',
                    ]
                ],
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/objects');
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
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testEmpty()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/objects',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/objects',
                'last' => 'http://api.example.com/objects',
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

        TableRegistry::get('Objects')->deleteAll([]);

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/objects');
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
                'self' => 'http://api.example.com/objects/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'document',
                'attributes' => [
                    'object_type_id' => 1,
                    'status' => 'on',
                    'uname' => 'title-one',
                    'locked' => true,
                    'created' => '2016-05-13T07:09:23+00:00',
                    'modified' => '2016-05-13T07:09:23+00:00',
                    'published' => '2016-05-13T07:09:23+00:00',
                    'title' => 'title one',
                    'description' => 'description here',
                    'body' => 'body here',
                    'extra' => [
                        'abstract' => 'abstract here',
                        'list' => "[\"one\", \"two\", \"three\"]"
                    ],
                    'lang' => 'eng',
                    'created_by' => 1,
                    'modified_by' => 1,
                    'publish_start' => '2016-05-13T07:09:23+00:00',
                    'publish_end' => '2016-05-13T07:09:23+00:00'
                ],
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/objects/1');
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
                'self' => 'http://api.example.com/objects/99',
                'home' => 'http://api.example.com/home',
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
        $this->get('/objects/99');
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
}
