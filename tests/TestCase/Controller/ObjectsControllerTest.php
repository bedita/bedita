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

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;

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
        'plugin.BEdita/Core.locations',
        'plugin.BEdita/Core.object_relations',
        'plugin.BEdita/Core.streams',
        'plugin.BEdita/Core.media',
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
                    'count' => 8,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 8,
                    'page_size' => 20,
                ],
            ],
            'data' => [
                [
                    'id' => '1',
                    'type' => 'users',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'first-user',
                        'title' => 'Mr. First User',
                        'description' => null,
                        'body' => null,
                        'extra' => null,
                        'lang' => 'eng',
                        'publish_start' => null,
                        'publish_end' => null,
                    ],
                    'meta' => [
                        'locked' => true,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/users/1',
                    ],
                    'relationships' => [
                        'roles' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/1/roles',
                                'self' => 'http://api.example.com/users/1/relationships/roles',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'documents',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'title-one',
                        'title' => 'title one',
                        'description' => 'description here',
                        'body' => 'body here',
                        'extra' => [
                            'abstract' => 'abstract here',
                            'list' => ['one', 'two', 'three'],
                        ],
                        'lang' => 'eng',
                        'publish_start' => '2016-05-13T07:09:23+00:00',
                        'publish_end' => '2016-05-13T07:09:23+00:00',
                    ],
                    'meta' => [
                        'locked' => true,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => '2016-05-13T07:09:23+00:00',
                        'created_by' => 1,
                        'modified_by' => 1,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/documents/2',
                    ],
                    'relationships' => [
                        'test' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/2/test',
                                'self' => 'http://api.example.com/documents/2/relationships/test',
                            ],
                        ],
                        'inverse_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/2/inverse_test',
                                'self' => 'http://api.example.com/documents/2/relationships/inverse_test',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'documents',
                    'attributes' => [
                        'status' => 'draft',
                        'uname' => 'title-two',
                        'title' => 'title two',
                        'description' => 'description here',
                        'body' => 'body here',
                        'extra' => null,
                        'lang' => 'eng',
                        'publish_start' => null,
                        'publish_end' => null,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2016-05-12T07:09:23+00:00',
                        'modified' => '2016-05-13T08:30:00+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 2,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/documents/3',
                    ],
                    'relationships' => [
                        'test' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/test',
                                'self' => 'http://api.example.com/documents/3/relationships/test',
                            ],
                        ],
                        'inverse_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/inverse_test',
                                'self' => 'http://api.example.com/documents/3/relationships/inverse_test',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '4',
                    'type' => 'profiles',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'gustavo-supporto',
                        'title' => 'Gustavo Supporto profile',
                        'description' => 'Some description about Gustavo',
                        'lang' => 'eng',
                        'body' => null,
                        'extra' => null,
                        'publish_start' => null,
                        'publish_end' => null,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/profiles/4',
                    ],
                    'relationships' => [
                        'inverse_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/inverse_test',
                                'self' => 'http://api.example.com/profiles/4/relationships/inverse_test',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '5',
                    'type' => 'users',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'second-user',
                        'title' => 'Miss Second User',
                        'description' => null,
                        'body' => null,
                        'extra' => null,
                        'lang' => 'eng',
                        'publish_start' => null,
                        'publish_end' => null,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/users/5',
                    ],
                    'relationships' => [
                        'roles' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/5/roles',
                                'self' => 'http://api.example.com/users/5/relationships/roles',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '8',
                    'type' => 'locations',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'the-two-towers',
                        'title' => 'The Two Towers',
                        'description' => null,
                        'body' => null,
                        'extra' => null,
                        'lang' => 'eng',
                        'publish_start' => null,
                        'publish_end' => null,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2017-02-20T07:09:23+00:00',
                        'modified' => '2017-02-20T07:09:23+00:00',
                        'published' => '2017-02-20T07:09:23+00:00',
                        'created_by' => 1,
                        'modified_by' => 1,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/locations/8',
                    ],
                    'relationships' => [
                        'another_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/locations/8/another_test',
                                'self' => 'http://api.example.com/locations/8/relationships/another_test',
                            ],
                        ],
                        'inverse_another_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/locations/8/inverse_another_test',
                                'self' => 'http://api.example.com/locations/8/relationships/inverse_another_test',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '9',
                    'type' => 'events',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'event-one',
                        'title' => 'first event',
                        'description' => 'event description goes here',
                        'body' => null,
                        'extra' => null,
                        'lang' => 'eng',
                        'publish_start' => null,
                        'publish_end' => null,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2017-03-08T07:09:23+00:00',
                        'modified' => '2016-03-08T08:30:00+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/events/9',
                    ],
                ],
                [
                    'id' => '10',
                    'type' => 'media',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'media-one',
                        'title' => 'first media',
                        'description' => 'media description goes here',
                        'body' => null,
                        'extra' => null,
                        'lang' => 'eng',
                        'publish_start' => null,
                        'publish_end' => null,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2017-03-08T07:09:23+00:00',
                        'modified' => '2017-03-08T08:30:00+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/media/10',
                    ],
                    'relationships' => [
                        'streams' => [
                            'links' => [
                                'related' => 'http://api.example.com/media/10/streams',
                                'self' => 'http://api.example.com/media/10/relationships/streams',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/objects');
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

        $this->configRequestHeaders();
        $this->get('/objects');
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
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testSingle()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/objects/2',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '2',
                'type' => 'documents',
                'attributes' => [
                    'status' => 'on',
                    'uname' => 'title-one',
                    'title' => 'title one',
                    'description' => 'description here',
                    'body' => 'body here',
                    'extra' => [
                        'abstract' => 'abstract here',
                        'list' => ['one', 'two', 'three'],
                    ],
                    'lang' => 'eng',
                    'publish_start' => '2016-05-13T07:09:23+00:00',
                    'publish_end' => '2016-05-13T07:09:23+00:00',
                ],
                'meta' => [
                    'locked' => true,
                    'created' => '2016-05-13T07:09:23+00:00',
                    'modified' => '2016-05-13T07:09:23+00:00',
                    'published' => '2016-05-13T07:09:23+00:00',
                    'created_by' => 1,
                    'modified_by' => 1,
                ],
                'relationships' => [
                    'test' => [
                        'links' => [
                            'related' => 'http://api.example.com/documents/2/test',
                            'self' => 'http://api.example.com/documents/2/relationships/test',
                        ],
                    ],
                    'inverse_test' => [
                        'links' => [
                            'related' => 'http://api.example.com/documents/2/inverse_test',
                            'self' => 'http://api.example.com/documents/2/relationships/inverse_test',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/objects/2');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test deleted object method.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testDeleted()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/objects/6',
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
                ],
                'meta' => [
                    'locked' => false,
                    'created' => '2016-10-13T07:09:23+00:00',
                    'published' => '2016-10-13T07:09:23+00:00',
                    'created_by' => 1,
                    'modified_by' => 1,
                ],
                'relationships' => [
                    'test' => [
                        'links' => [
                            'related' => 'http://api.example.com/documents/6/test',
                            'self' => 'http://api.example.com/documents/6/relationships/test',
                        ],
                    ],
                    'inverse_test' => [
                        'links' => [
                            'related' => 'http://api.example.com/documents/6/inverse_test',
                            'self' => 'http://api.example.com/documents/6/relationships/inverse_test',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/objects/6');
        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');

        // restore object -> deleted = false
        $objectsTable = TableRegistry::get('Objects');
        $object = $objectsTable->get(6);
        $object->deleted = false;
        $this->authUser();
        $success = $objectsTable->save($object);
        $this->assertEquals(true, (bool)$success);

        $this->configRequestHeaders();
        $this->get('/objects/6');
        $result = json_decode((string)$this->_response->getBody(), true);
        unset($result['data']['meta']['modified']);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);

        // undo restore -> deleted = true
        $object->deleted = true;
        $success = $objectsTable->save($object);
        $this->assertEquals(true, (bool)$success);
    }

    /**
     * Test view method.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
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

        $this->configRequestHeaders();
        $this->get('/objects/99');
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

    /**
     * Test add method.
     *
     * @return void
     *
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testAdd()
    {
        $data = [
            'type' => 'documents',
            'attributes' => [
                'title' => 'A new document',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/documents', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayHasKey('data', $result);
        static::assertArrayHasKey('attributes', $result['data']);
        static::assertArrayHasKey('status', $result['data']['attributes']);
        $this->assertHeader('Location', 'http://api.example.com/documents/11');
        static::assertTrue(TableRegistry::get('Documents')->exists(['title' => 'A new document']));
    }

    /**
     * Test add wrong type method.
     *
     * @return void
     *
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testAddTypeFail()
    {
        $data = [
            'type' => 'documents',
            'attributes' => [
                'title' => 'A new document',
                'uname' => 'a-new-document',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/news', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test edit method.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEdit()
    {
        $newTitle = 'A new funny title';
        $data = [
            'id' => '2',
            'type' => 'documents',
            'attributes' => [
                'title' => $newTitle,
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/documents/2', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $Documents = TableRegistry::get('Documents');
        static::assertEquals($newTitle, $Documents->get(2)->get('title'));
        static::assertEquals('documents', $Documents->get(2)->get('type'));

        $result = json_decode((string)$this->_response->getBody(), true);
        static::assertEquals($data['id'], $result['data']['id']);
        static::assertEquals($data['type'], $result['data']['type']);
        static::assertEquals($data['attributes']['title'], $result['data']['attributes']['title']);
    }

    /**
     * Test edit method with ID and type conflict.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEditConflict()
    {
        $data = [
            'id' => '3',
            'type' => 'documents',
            'attributes' => [
                'title' => 'some random title',
            ],
        ];

        $authHeader = $this->getUserAuthHeader();

        $this->configRequestHeaders('PATCH', $authHeader);
        $this->patch('/documents/2', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals('title two', TableRegistry::get('Documents')->get(3)->get('title'));
        $this->assertEquals('title one', TableRegistry::get('Documents')->get(2)->get('title'));

        $this->configRequestHeaders('PATCH', $authHeader);
        $this->patch('/news/3', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test edit method with invalid data.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEditInvalid()
    {
        $data = [
            'id' => '2',
            'type' => 'documents',
            'attributes' => [
                'uname' => 'first-user',
            ],
        ];

        $authHeader = $this->getUserAuthHeader();

        $this->configRequestHeaders('PATCH', $authHeader);
        $this->patch('/documents/2', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals('title-one', TableRegistry::get('Documents')->get(2)->get('uname'));

        $this->configRequestHeaders('PATCH', $authHeader);
        $data['id'] = 33;
        $this->patch('/documents/33', json_encode(compact('data')));

        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test delete method.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testDelete()
    {
        $authHeader = $this->getUserAuthHeader();

        $this->configRequestHeaders('DELETE', $authHeader);
        $this->delete('/documents/3');

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');

        $this->configRequestHeaders();
        $this->get('/documents/3');
        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');

        $docDeleted = TableRegistry::get('Documents')->get(7);
        $this->assertEquals($docDeleted->deleted, 1);

        $this->configRequestHeaders('DELETE', $authHeader);
        $this->delete('/documents/33');
        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');

        $this->configRequestHeaders('DELETE', $authHeader);
        $this->delete('/documents/4');
        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test related method to list related objects.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::related()
     * @covers ::findAssociation()
     */
    public function testRelated()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/documents/2/test',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/documents/2/test',
                'last' => 'http://api.example.com/documents/2/test',
                'prev' => null,
                'next' => null,
            ],
            'data' => [
                [
                    'id' => '4',
                    'type' => 'profiles',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'gustavo-supporto',
                        'title' => 'Gustavo Supporto profile',
                        'description' => 'Some description about Gustavo',
                        'lang' => 'eng',
                        'body' => null,
                        'extra' => null,
                        'publish_start' => null,
                        'publish_end' => null,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/profiles/4',
                    ],
                    'relationships' => [
                        'inverse_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/inverse_test',
                                'self' => 'http://api.example.com/profiles/4/relationships/inverse_test',
                            ],
                        ],
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                        'relation' => [
                            'priority' => 1,
                            'inv_priority' => 2,
                            'params' => null,
                        ],
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'documents',
                    'attributes' => [
                        'status' => 'draft',
                        'uname' => 'title-two',
                        'title' => 'title two',
                        'description' => 'description here',
                        'body' => 'body here',
                        'extra' => null,
                        'lang' => 'eng',
                        'publish_start' => null,
                        'publish_end' => null,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/documents/3',
                    ],
                    'relationships' => [
                        'test' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/test',
                                'self' => 'http://api.example.com/documents/3/relationships/test',
                            ],
                        ],
                        'inverse_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/inverse_test',
                                'self' => 'http://api.example.com/documents/3/relationships/inverse_test',
                            ],
                        ],
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2016-05-12T07:09:23+00:00',
                        'modified' => '2016-05-13T08:30:00+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 2,
                        'relation' => [
                            'priority' => 2,
                            'inv_priority' => 1,
                            'params' => null,
                        ],
                    ],
                ],
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
        $this->get('/documents/2/test');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test relationships method to list existing relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     */
    public function testListAssociations()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/documents/2/relationships/test',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/documents/2/relationships/test',
                'last' => 'http://api.example.com/documents/2/relationships/test',
                'prev' => null,
                'next' => null,
            ],
            'data' => [
                [
                    'id' => '4',
                    'type' => 'profiles',
                    'links' => [
                        'self' => 'http://api.example.com/profiles/4',
                    ],
                    'relationships' => [
                        'inverse_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/inverse_test',
                                'self' => 'http://api.example.com/profiles/4/relationships/inverse_test',
                            ],
                        ],
                    ],
                    'meta' => [
                        'relation' => [
                            'priority' => 1,
                            'inv_priority' => 2,
                            'params' => null,
                        ],
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'documents',
                    'links' => [
                        'self' => 'http://api.example.com/documents/3',
                    ],
                    'relationships' => [
                        'test' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/test',
                                'self' => 'http://api.example.com/documents/3/relationships/test',
                            ],
                        ],
                        'inverse_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/inverse_test',
                                'self' => 'http://api.example.com/documents/3/relationships/inverse_test',
                            ],
                        ],
                    ],
                    'meta' => [
                        'relation' => [
                            'priority' => 2,
                            'inv_priority' => 1,
                            'params' => null,
                        ],
                    ],
                ],
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
        $this->get('/documents/2/relationships/test');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test relationships method to list existing relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     */
    public function testListAssociationsNotFound()
    {
        $this->configRequestHeaders();
        $this->get('/documents/99/relationships/test');

        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test relationships method to add new relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     */
    public function testAddAssociations()
    {
        $expected = [
            'data' => [
                [
                    'id' => '4',
                    'type' => 'profiles',
                    'links' => [
                        'self' => 'http://api.example.com/profiles/4',
                    ],
                    'relationships' => [
                        'inverse_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/inverse_test',
                                'self' => 'http://api.example.com/profiles/4/relationships/inverse_test',
                            ],
                        ],
                    ],
                    'meta' => [
                        'relation' => [
                            'priority' => 1,
                            'inv_priority' => 2,
                            'params' => [
                                'gustavo' => 'supporto',
                            ],
                        ],
                    ],
                ],
            ],
            'links' => [
                'self' => 'http://api.example.com/documents/2/relationships/test',
                'home' => 'http://api.example.com/home',
            ],
        ];

        $data = [
            [
                'id' => '4',
                'type' => 'profiles',
                'meta' => [
                    'relation' => [
                        'priority' => 1,
                        'inv_priority' => 2,
                        'params' => [
                            'gustavo' => 'supporto',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/documents/2/relationships/test', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test relationships method to add new relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     */
    public function testAddAssociationsDuplicateEntry()
    {
        $expected = [
            'data' => [
                [
                    'id' => '4',
                    'type' => 'profiles',
                    'links' => [
                        'self' => 'http://api.example.com/profiles/4',
                    ],
                    'relationships' => [
                        'inverse_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/inverse_test',
                                'self' => 'http://api.example.com/profiles/4/relationships/inverse_test',
                            ],
                        ],
                    ],
                    'meta' => [
                        'relation' => [
                            'priority' => 1,
                            'inv_priority' => 2,
                            'params' => [
                                'gustavo' => 'supporto',
                            ],
                        ],
                    ],
                ],
            ],
            'links' => [
                'self' => 'http://api.example.com/documents/2/relationships/test',
                'home' => 'http://api.example.com/home',
            ],
        ];

        $data = [
            [
                'id' => '4',
                'type' => 'profiles',
                'meta' => [
                    'relation' => [
                        'priority' => 1,
                        'inv_priority' => 2,
                        'params' => [
                            'gustavo' => 'supporto',
                        ],
                    ],
                ],
            ],
            [
                'id' => '4',
                'type' => 'profiles',
                'meta' => [
                    'relation' => [
                        'priority' => 1,
                        'inv_priority' => 2,
                        'params' => [
                            'gustavo' => 'supporto',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/documents/2/relationships/test', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test relationships method to add new relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     */
    public function testAddAssociationsNoContent()
    {
        $data = [
            [
                'id' => '4',
                'type' => 'profiles',
                'meta' => [
                    'relation' => [
                        'priority' => 1,
                        'inv_priority' => 2,
                        'params' => null,
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/documents/2/relationships/test', json_encode(compact('data')));

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseEmpty();
    }

    /**
     * Test relationships method to delete existing relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     */
    public function testDeleteAssociations()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/documents/2/relationships/test',
                'home' => 'http://api.example.com/home',
            ],
        ];

        $data = [
            [
                'id' => '4',
                'type' => 'profiles',
            ],
            [
                'id' => '2',
                'type' => 'documents',
            ],
        ];

        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        // Cannot use `IntegrationTestCase::delete()`, as it does not allow sending payload with the request.
        $this->_sendRequest('/documents/2/relationships/test', 'DELETE', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test relationships method to delete existing relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     */
    public function testDeleteAssociationsNoContent()
    {
        $data = [
            [
                'id' => '2',
                'type' => 'documents',
            ],
        ];

        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        // Cannot use `IntegrationTestCase::delete()`, as it does not allow sending payload with the request.
        $this->_sendRequest('/documents/2/relationships/test', 'DELETE', json_encode(compact('data')));

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseEmpty();
    }

    /**
     * Test relationships method to replace existing relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     */
    public function testSetAssociations()
    {
        $expected = [
            'data' => [
                [
                    'id' => '4',
                    'type' => 'profiles',
                    'links' => [
                        'self' => 'http://api.example.com/profiles/4',
                    ],
                    'relationships' => [
                        'inverse_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/inverse_test',
                                'self' => 'http://api.example.com/profiles/4/relationships/inverse_test',
                            ],
                        ],
                    ],
                    'meta' => [
                        'relation' => [
                            'priority' => 1,
                            'inv_priority' => 2,
                            'params' => [
                                'gustavo' => 'supporto',
                            ],
                        ],
                    ],
                ],
            ],
            'links' => [
                'self' => 'http://api.example.com/documents/2/relationships/test',
                'home' => 'http://api.example.com/home',
            ],
        ];

        $data = [
            [
                'id' => '4',
                'type' => 'profiles',
                'meta' => [
                    'relation' => [
                        'priority' => 1,
                        'inv_priority' => 2,
                        'params' => [
                            'gustavo' => 'supporto',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/documents/2/relationships/test', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test relationships method to replace existing relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     */
    public function testSetAssociationsEmpty()
    {
        $expected = [
            'data' => [],
            'links' => [
                'self' => 'http://api.example.com/documents/2/relationships/test',
                'home' => 'http://api.example.com/home',
            ],
        ];

        $data = [];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/documents/2/relationships/test', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test relationships method to replace existing relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     */
    public function testSetAssociationsNoContent()
    {
        $data = [
            [
                'id' => '4',
                'type' => 'profiles',
                'meta' => [
                    'relation' => [
                        'priority' => 1,
                        'inv_priority' => 2,
                        'params' => null,
                    ],
                ],
            ],
            [
                'id' => '3',
                'type' => 'documents',
                'meta' => [
                    'relation' => [
                        'priority' => 2,
                        'inv_priority' => 1,
                        'params' => null,
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/documents/2/relationships/test', json_encode(compact('data')));

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $this->assertResponseEmpty();
    }

    /**
     * Test relationships method to update relationships with a non-existing object ID.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     */
    public function testUpdateAssociationsMissingId()
    {
        $expected = [
            'status' => '400',
            'title' => 'Record not found in table "profiles"',
        ];

        $data = [
            [
                'id' => '99',
                'type' => 'profiles',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/documents/2/relationships/test', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayHasKey('error', $result);
        static::assertArraySubset($expected, $result['error']);
    }

    /**
     * Test relationships method with a non-existing association.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     */
    public function testWrongAssociation()
    {
        $expected = [
            'status' => '404',
            'title' => 'Relationship "this_relationship_does_not_exist" does not exist',
        ];

        $this->configRequestHeaders();
        $this->get('/documents/2/relationships/this_relationship_does_not_exist');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayHasKey('error', $result);
        static::assertArraySubset($expected, $result['error']);
    }

    /**
     * Test relationships method to update relationships with a wrong type.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     */
    public function testUpdateAssociationsUnsupportedType()
    {
        $expected = [
            'status' => '409',
            'title' => 'Unsupported resource type',
        ];

        $data = [
            [
                'id' => '1',
                'type' => 'myCustomType',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/documents/2/relationships/test', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayHasKey('error', $result);
        static::assertArraySubset($expected, $result['error']);
    }

    /**
     * Test failure on object type not found.
     *
     * @return void
     *
     * @covers ::initialize()
     */
    public function testObjectTypeNotFound()
    {
        $this->configRequestHeaders();
        $this->get('/invalid_object_type');

        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Provider for testMissingAuth
     *
     * @return array
     */
    public function missingAuthProvider()
    {
        return [
            'get' => [
                200,
                'GET',
                'documents',
            ],
            'post' => [
                401,
                'POST',
                'documents',
                [
                    'type' => 'documents',
                    'attributes' => [
                        'title' => 'A new document',
                    ],
                ],
            ],
            'patch' => [
                401,
                'PATCH',
                'documents/2',
                [
                    'type' => 'documents',
                    'attributes' => [
                        'id' => '2',
                        'title' => 'Change title',
                    ],
                ],
            ],
            'delete' => [
                401,
                'DELETE',
                'documents/2',
            ],
        ];
    }

    /**
     * Test requests missing auth
     *
     * @param int $expected Expected response code.
     * @param string $method Request method.
     * @param string $endpoint Endpoint.
     * @param array $data Request data.
     * @return void
     *
     * @dataProvider missingAuthProvider
     * @coversNothing
     */
    public function testMissingAuth($expected, $method, $endpoint, array $data = [])
    {
        $this->configRequestHeaders($method);
        $requestMethod = strtolower($method);
        $this->$requestMethod('/' . $endpoint, json_encode(compact('data')));
        $this->assertResponseCode($expected);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test included resources.
     *
     * @return void
     *
     * @covers ::prepareInclude()
     */
    public function testInclude()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/documents/2?include=test%2Cinverse_test',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '2',
                'type' => 'documents',
                'attributes' => [
                    'status' => 'on',
                    'uname' => 'title-one',
                    'title' => 'title one',
                    'description' => 'description here',
                    'body' => 'body here',
                    'extra' => [
                        'abstract' => 'abstract here',
                        'list' => ['one', 'two', 'three'],
                    ],
                    'lang' => 'eng',
                    'publish_start' => '2016-05-13T07:09:23+00:00',
                    'publish_end' => '2016-05-13T07:09:23+00:00',
                ],
                'meta' => [
                    'locked' => true,
                    'created_by' => 1,
                    'modified_by' => 1,
                    'created' => '2016-05-13T07:09:23+00:00',
                    'modified' => '2016-05-13T07:09:23+00:00',
                    'published' => '2016-05-13T07:09:23+00:00',
                ],
                'relationships' => [
                    'test' => [
                        'links' => [
                            'self' => 'http://api.example.com/documents/2/relationships/test',
                            'related' => 'http://api.example.com/documents/2/test',
                        ],
                        'data' => [
                            [
                                'id' => '4',
                                'type' => 'profiles',
                            ],
                            [
                                'id' => '3',
                                'type' => 'documents',
                            ],
                        ],
                    ],
                    'inverse_test' => [
                        'links' => [
                            'self' => 'http://api.example.com/documents/2/relationships/inverse_test',
                            'related' => 'http://api.example.com/documents/2/inverse_test',
                        ],
                        'data' => [],
                    ],
                ],
            ],
            'included' => [
                [
                    'id' => '4',
                    'type' => 'profiles',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'gustavo-supporto',
                        'title' => 'Gustavo Supporto profile',
                        'description' => 'Some description about Gustavo',
                        'body' => null,
                        'extra' => null,
                        'lang' => 'eng',
                        'publish_start' => null,
                        'publish_end' => null,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                        'relation' => [
                            'priority' => 1,
                            'inv_priority' => 2,
                            'params' => null,
                        ],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/profiles/4',
                    ],
                    'relationships' => [
                        'inverse_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/inverse_test',
                                'self' => 'http://api.example.com/profiles/4/relationships/inverse_test',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'documents',
                    'attributes' => [
                        'status' => 'draft',
                        'uname' => 'title-two',
                        'title' => 'title two',
                        'description' => 'description here',
                        'body' => 'body here',
                        'extra' => null,
                        'lang' => 'eng',
                        'publish_start' => null,
                        'publish_end' => null,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2016-05-12T07:09:23+00:00',
                        'modified' => '2016-05-13T08:30:00+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 2,
                        'relation' => [
                            'priority' => 2,
                            'inv_priority' => 1,
                            'params' => null,
                        ],
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/documents/3',
                    ],
                    'relationships' => [
                        'test' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/test',
                                'self' => 'http://api.example.com/documents/3/relationships/test',
                            ],
                        ],
                        'inverse_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/inverse_test',
                                'self' => 'http://api.example.com/documents/3/relationships/inverse_test',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/documents/2?include=test,inverse_test');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test listing streams for an object.
     *
     * @return void
     *
     * @covers ::beforeFilter()
     */
    public function testStreamsRelationshipsList()
    {
        $id = '9e58fa47-db64-4479-a0ab-88a706180d59';
        $data = [
            [
                'id' => $id,
                'type' => 'streams',
                'meta' => [
                    'url' => null,
                ],
                'links' => [
                    'self' => sprintf('http://api.example.com/streams/%s', $id),
                ],
                'relationships' => [
                    'object' => [
                        'links' => [
                            'related' => sprintf('http://api.example.com/streams/%s/object', $id),
                            'self' => sprintf('http://api.example.com/streams/%s/relationships/object', $id),
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/media/10/relationships/streams');

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $response = json_decode((string)$this->_response->getBody(), true);

        static::assertArrayHasKey('data', $response);
        static::assertSame($data, $response['data']);
    }

    /**
     * Test that relationships can only be managed from the streams side.
     *
     * @return void
     *
     * @covers ::beforeFilter()
     */
    public function testStreamsRelationshipsManage()
    {
        $data = [
            [
                'id' => 'e5afe167-7341-458d-a1e6-042e8791b0fe',
                'type' => 'streams',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->authUser());
        $this->patch('/media/10/relationships/streams', json_encode(compact('data')));

        $this->assertResponseCode(403);
        $this->assertContentType('application/vnd.api+json');

        $this->assertResponseContains(__d(
            'bedita',
            'You are not authorized to manage an object relationship to streams, please update stream relationship to objects instead'
        ));
    }
}
