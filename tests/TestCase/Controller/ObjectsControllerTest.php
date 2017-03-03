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

use BEdita\Core\State\CurrentApplication;
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
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.endpoints',
        'plugin.BEdita/Core.applications',
        'plugin.BEdita/Core.endpoint_permissions',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.locations',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.object_relations',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

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
                'self' => 'http://api.example.com/objects',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/objects',
                'last' => 'http://api.example.com/objects',
                'prev' => null,
                'next' => null,
            ],
            'meta' => [
                'pagination' => [
                    'count' => 6,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 6,
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
                        'locked' => true,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => null,
                        'title' => 'Mr. First User',
                        'description' => null,
                        'body' => null,
                        'extra' => null,
                        'lang' => 'eng',
                        'created_by' => 1,
                        'modified_by' => 1,
                        'publish_start' => null,
                        'publish_end' => null,
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
                        'locked' => true,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => '2016-05-13T07:09:23+00:00',
                        'title' => 'title one',
                        'description' => 'description here',
                        'body' => 'body here',
                        'extra' => [
                            'abstract' => 'abstract here',
                            'list' => ['one', 'two', 'three'],
                        ],
                        'lang' => 'eng',
                        'created_by' => 1,
                        'modified_by' => 1,
                        'publish_start' => '2016-05-13T07:09:23+00:00',
                        'publish_end' => '2016-05-13T07:09:23+00:00',
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
                        'inverse-test' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/2/inverse-test',
                                'self' => 'http://api.example.com/documents/2/relationships/inverse-test',
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
                        'self' => 'http://api.example.com/documents/3',
                    ],
                    'relationships' => [
                        'test' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/test',
                                'self' => 'http://api.example.com/documents/3/relationships/test',
                            ],
                        ],
                        'inverse-test' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/inverse-test',
                                'self' => 'http://api.example.com/documents/3/relationships/inverse-test',
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
                        'self' => 'http://api.example.com/profiles/4',
                    ],
                    'relationships' => [
                        'inverse-test' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/inverse-test',
                                'self' => 'http://api.example.com/profiles/4/relationships/inverse-test',
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
                        'locked' => false,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => null,
                        'title' => 'Miss Second User',
                        'description' => null,
                        'body' => null,
                        'extra' => null,
                        'lang' => 'eng',
                        'created_by' => 1,
                        'modified_by' => 1,
                        'publish_start' => null,
                        'publish_end' => null,
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
                        'locked' => false,
                        'created' => '2017-02-20T07:09:23+00:00',
                        'modified' => '2017-02-20T07:09:23+00:00',
                        'published' => '2017-02-20T07:09:23+00:00',
                        'title' => 'The Two Towers',
                        'description' => null,
                        'body' => null,
                        'extra' => null,
                        'lang' => 'eng',
                        'created_by' => 1,
                        'modified_by' => 1,
                        'publish_start' => null,
                        'publish_end' => null,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/locations/8',
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

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
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
     * @covers ::view()
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
                    'locked' => true,
                    'created' => '2016-05-13T07:09:23+00:00',
                    'modified' => '2016-05-13T07:09:23+00:00',
                    'published' => '2016-05-13T07:09:23+00:00',
                    'title' => 'title one',
                    'description' => 'description here',
                    'body' => 'body here',
                    'extra' => [
                        'abstract' => 'abstract here',
                        'list' => ['one', 'two', 'three'],
                    ],
                    'lang' => 'eng',
                    'created_by' => 1,
                    'modified_by' => 1,
                    'publish_start' => '2016-05-13T07:09:23+00:00',
                    'publish_end' => '2016-05-13T07:09:23+00:00',
                ],
                'relationships' => [
                    'test' => [
                        'links' => [
                            'related' => 'http://api.example.com/documents/2/test',
                            'self' => 'http://api.example.com/documents/2/relationships/test',
                        ],
                    ],
                    'inverse-test' => [
                        'links' => [
                            'related' => 'http://api.example.com/documents/2/inverse-test',
                            'self' => 'http://api.example.com/documents/2/relationships/inverse-test',
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
     * @covers ::view()
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
                    'locked' => false,
                    'created' => '2016-10-13T07:09:23+00:00',
                    'published' => '2016-10-13T07:09:23+00:00',
                    'title' => 'title one deleted',
                    'description' => 'description removed',
                    'body' => 'body no more',
                    'extra' => [
                        'abstract' => 'what?',
                    ],
                    'lang' => 'eng',
                    'created_by' => 1,
                    'modified_by' => 1,
                    'publish_start' => '2016-10-13T07:09:23+00:00',
                    'publish_end' => '2016-10-13T07:09:23+00:00'
                ],
                'relationships' => [
                    'test' => [
                        'links' => [
                            'related' => 'http://api.example.com/documents/6/test',
                            'self' => 'http://api.example.com/documents/6/relationships/test',
                        ],
                    ],
                    'inverse-test' => [
                        'links' => [
                            'related' => 'http://api.example.com/documents/6/inverse-test',
                            'self' => 'http://api.example.com/documents/6/relationships/inverse-test',
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
        $this->get('/objects/6');
        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');

        // restore object -> deleted = false
        $objectsTable = TableRegistry::get('Objects');
        $object = $objectsTable->get(6);
        $object->deleted = false;
        $success = $objectsTable->save($object);
        $this->assertEquals(true, (bool)$success);

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/objects/6');
        $result = json_decode((string)$this->_response->getBody(), true);
        unset($result['data']['attributes']['modified']);
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
     * @covers ::add()
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

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->post('/documents', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        $this->assertHeader('Location', 'http://api.example.com/documents/9');
        $this->assertTrue(TableRegistry::get('Documents')->exists(['title' => 'A new document']));
    }

    /**
     * Test add method.
     *
     * @return void
     *
     * @covers ::add()
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

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->post('/news', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test edit method.
     *
     * @return void
     *
     * @covers ::edit()
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

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->patch('/documents/2', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $Documents = TableRegistry::get('Documents');
        $this->assertEquals($newTitle, $Documents->get(2)->get('title'));

        // restore field value
        $doc = $Documents->get(2);
        $doc = $Documents->patchEntity($doc, ['title' => 'title one']);
        $success = $Documents->save($doc);
        $this->assertTrue((bool)$success);
    }

    /**
     * Test edit method with ID and type conflict.
     *
     * @return void
     *
     * @covers ::edit()
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

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->patch('/documents/2', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals('title two', TableRegistry::get('Documents')->get(3)->get('title'));
        $this->assertEquals('title one', TableRegistry::get('Documents')->get(2)->get('title'));

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->patch('/news/3', json_encode(compact('data')));

        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test edit method with invalid data.
     *
     * @return void
     *
     * @covers ::edit()
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

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->patch('/documents/2', json_encode(compact('data')));

        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals('title-one', TableRegistry::get('Documents')->get(2)->get('uname'));

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
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
     * @covers ::delete()
     * @covers ::initialize()
     */
    public function testDelete()
    {
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
                'Content-Type' => 'application/vnd.api+json',
            ],
        ]);
        $this->delete('/documents/3');

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/documents/3');
        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');

        $docDeleted = TableRegistry::get('Documents')->get(7);
        $this->assertEquals($docDeleted->deleted, 1);

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->delete('/documents/33');
        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
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
                        'self' => 'http://api.example.com/profiles/4',
                    ],
                    'relationships' => [
                        'inverse-test' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/inverse-test',
                                'self' => 'http://api.example.com/profiles/4/relationships/inverse-test',
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
                        'self' => 'http://api.example.com/documents/3',
                    ],
                    'relationships' => [
                        'test' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/test',
                                'self' => 'http://api.example.com/documents/3/relationships/test',
                            ],
                        ],
                        'inverse-test' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/inverse-test',
                                'self' => 'http://api.example.com/documents/3/relationships/inverse-test',
                            ],
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

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
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
                        'inverse-test' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/inverse-test',
                                'self' => 'http://api.example.com/profiles/4/relationships/inverse-test',
                            ],
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
                        'inverse-test' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/inverse-test',
                                'self' => 'http://api.example.com/documents/3/relationships/inverse-test',
                            ],
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

        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
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
        $this->configRequest([
            'headers' => [
                'Host' => 'api.example.com',
                'Accept' => 'application/vnd.api+json',
            ],
        ]);
        $this->get('/documents/99/relationships/test');

        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
    }
}
