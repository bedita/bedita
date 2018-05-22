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
use BEdita\API\Test\TestConstants;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * @coversDefaultClass \BEdita\API\Controller\TrashController
 */
class TrashControllerTest extends IntegrationTestCase
{
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
                'schema' => [
                    'documents' => [
                        '$id' => 'http://api.example.com/model/schema/documents',
                        'revision' => TestConstants::SCHEMA_REVISIONS['documents'],
                    ],
                ],
            ],
            'data' => [
                [
                    'id' => '6',
                    'type' => 'documents',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'title-one-deleted',
                        'title' => 'title one deleted',
                        'description' => 'description removed',
                        'body' => 'body no more',
                        'extra' => [
                            'abstract' => 'what?',
                        ],
                        'lang' => 'eng',
                        'publish_start' => '2016-10-13T07:09:23+00:00',
                        'publish_end' => '2016-10-13T07:09:23+00:00',
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2016-10-13T07:09:23+00:00',
                        'modified' => '2016-10-13T07:09:23+00:00',
                        'published' => '2016-10-13T07:09:23+00:00',
                        'created_by' => 1,
                        'modified_by' => 1,
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
                        'title' => 'title two deleted',
                        'description' => 'description removed',
                        'body' => 'body no more',
                        'extra' => [
                            'abstract' => 'what?',
                        ],
                        'lang' => 'eng',
                        'publish_start' => '2016-10-13T07:09:23+00:00',
                        'publish_end' => '2016-10-13T07:09:23+00:00',
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2016-10-13T07:09:23+00:00',
                        'modified' => '2016-10-13T07:09:23+00:00',
                        'published' => '2016-10-13T07:09:23+00:00',
                        'created_by' => 1,
                        'modified_by' => 1,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/trash/7',
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
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
                    'page_count' => 1,
                    'page_items' => 0,
                    'page_size' => 20,
                ],
            ],
            'data' => [],
        ];

        TableRegistry::get('Objects')->deleteAll([]);

        $this->configRequestHeaders();
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
                    'title' => 'title one deleted',
                    'description' => 'description removed',
                    'body' => 'body no more',
                    'extra' => [
                        'abstract' => 'what?',
                    ],
                    'lang' => 'eng',
                    'publish_start' => '2016-10-13T07:09:23+00:00',
                    'publish_end' => '2016-10-13T07:09:23+00:00',
                    'another_title' => null,
                    'another_description' => null,
                ],
                'meta' => [
                    'locked' => false,
                    'created' => '2016-10-13T07:09:23+00:00',
                    'modified' => '2016-10-13T07:09:23+00:00',
                    'published' => '2016-10-13T07:09:23+00:00',
                    'created_by' => 1,
                    'modified_by' => 1,
                ],
            ],
            'meta' => [
                'schema' => [
                    'documents' => [
                        '$id' => 'http://api.example.com/model/schema/documents',
                        'revision' => TestConstants::SCHEMA_REVISIONS['documents'],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/trash/6');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Data provider for `testRestore()`
     *
     * @return array
     */
    public function restoreProvider()
    {
        return [
            'not found' => [
                404,
                '66666',
                [
                    'id' => '6',
                    'type' => 'objects',
                ],
            ],
            'conflict' => [
                409,
                '6',
                [
                    'id' => '66',
                    'type' => 'objects',
                ],
            ],
            'ok' => [
                204,
                '6',
                [
                    'id' => '6',
                    'type' => 'objects',
                ],
            ],
        ];
    }

    /**
     * Test delete restore method.
     *
     * @return void
     *
     * @dataProvider restoreProvider
     * @covers ::restore()
     * @covers ::initialize()
     */
    public function testRestore($expected, $id, $data)
    {
        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch("/trash/$id", json_encode(compact('data')));
        $this->assertResponseCode($expected);
        $this->assertContentType('application/vnd.api+json');

        // if restored
        if ($this->_response->getStatusCode() === 204) {
            $trash = $this->Objects->get($id);
            $this->assertFalse($trash['deleted']);
        }
    }

    /**
     * Test delete method.
     *
     * @return void
     *
     * @covers ::delete()
     * @covers ::initialize()
     */
    public function testDelete()
    {
        $authHeader = $this->getUserAuthHeader();

        // success test
        $this->configRequestHeaders('DELETE', $authHeader);
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
        $this->configRequestHeaders('DELETE', $authHeader);
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

        $this->configRequestHeaders();
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
