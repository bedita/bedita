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

use BEdita\Core\State\CurrentApplication;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestCase;

/**
 * @coversDefaultClass \BEdita\API\Controller\TrashController
 */
class TrashControllerTest extends IntegrationTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.endpoints',
        'plugin.BEdita/Core.applications',
        'plugin.BEdita/Core.endpoint_permissions',
        'plugin.BEdita/Core.objects',
    ];

    /**
     * Objects table instance.
     *
     * @var \BEdita\Core\Model\Table\ObjectsTable
     */
    protected $Objects;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Objects = TableRegistry::get('Objects');

        CurrentApplication::setFromApiKey(API_KEY);
    }

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
                'self' => 'http://api.example.com/trash',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/trash',
                'last' => 'http://api.example.com/trash',
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
                    'id' => '6',
                    'type' => 'documents',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'title-one-deleted',
                        'locked' => false,
                        'created' => '2016-10-13T07:09:23+00:00',
                        'modified' => '2016-10-13T07:09:23+00:00',
                        'published' => '2016-10-13T07:09:23+00:00',
                        'title' => 'title one deleted',
                        'description' => 'description removed',
                        'body' => 'body no more',
                        'extra' => [
                            'abstract' => 'what?'
                        ],
                        'lang' => 'eng',
                        'created_by' => 1,
                        'modified_by' => 1,
                        'publish_start' => '2016-10-13T07:09:23+00:00',
                        'publish_end' => '2016-10-13T07:09:23+00:00'
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/trash/6',
                    ],
                ],
                [
                    'id' => '7',
                    'type' => 'documents',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'title-two-deleted',
                        'locked' => false,
                        'created' => '2016-10-13T07:09:23+00:00',
                        'modified' => '2016-10-13T07:09:23+00:00',
                        'published' => '2016-10-13T07:09:23+00:00',
                        'title' => 'title two deleted',
                        'description' => 'description removed',
                        'body' => 'body no more',
                        'extra' => [
                            'abstract' => 'what?'
                        ],
                        'lang' => 'eng',
                        'created_by' => 1,
                        'modified_by' => 1,
                        'publish_start' => '2016-10-13T07:09:23+00:00',
                        'publish_end' => '2016-10-13T07:09:23+00:00'
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/trash/7',
                    ],
                ]
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/trash');
        $result = json_decode((string)$this->_response->getBody(), true);

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
                'self' => 'http://api.example.com/trash',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/trash',
                'last' => 'http://api.example.com/trash',
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
        $this->get('/trash');
        $result = json_decode((string)$this->_response->getBody(), true);

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
                'self' => 'http://api.example.com/trash/6',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '6',
                'type' => 'documents',
                'attributes' => [
                    'status' => 'on',
                    'uname' => 'title-one-deleted',
                    'locked' => false,
                    'created' => '2016-10-13T07:09:23+00:00',
                    'modified' => '2016-10-13T07:09:23+00:00',
                    'published' => '2016-10-13T07:09:23+00:00',
                    'title' => 'title one deleted',
                    'description' => 'description removed',
                    'body' => 'body no more',
                    'extra' => [
                        'abstract' => 'what?'
                    ],
                    'lang' => 'eng',
                    'created_by' => 1,
                    'modified_by' => 1,
                    'publish_start' => '2016-10-13T07:09:23+00:00',
                    'publish_end' => '2016-10-13T07:09:23+00:00'
                ],
            ],
        ];

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/trash/6');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test delete restore method.
     *
     * @return void
     */
    public function testRestore()
    {
        $data = [
            'id' => '6',
            'type' => 'objects'
        ];

        // failure test
        $data['id'] = '66666';
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->patch('/trash/66666', json_encode(compact('data')));
        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');

        // conflict test
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->patch('/trash/666', json_encode(compact('data')));
        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');

        // success test
        $data['id'] = '6';
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->patch('/trash/6', json_encode(compact('data')));
        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $trash = $this->Objects->get(6);
        $this->assertFalse($trash['deleted']);
    }
    /**
     * Test delete method.
     *
     * @return void
     */
    public function testDelete()
    {
        // success test
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->delete('/trash/7');
        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $notFound = false;
        try {
            $this->Objects->get(7);
        } catch (RecordNotFoundException $e) {
            $notFound = true;
        }
        $this->assertTrue($notFound);

        // failure test
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->delete('/trash/77777');
        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
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
                'self' => 'http://api.example.com/trash/99',
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
        $this->get('/trash/99');
        $result = json_decode((string)$this->_response->getBody(), true);

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
