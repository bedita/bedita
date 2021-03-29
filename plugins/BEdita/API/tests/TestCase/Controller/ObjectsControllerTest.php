<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\API\Test\TestCase\Controller;

use BEdita\API\Controller\ObjectsController;
use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\API\Test\TestConstants;
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

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
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Streams',
        'plugin.BEdita/Core.DateRanges',
        'plugin.BEdita/Core.Media',
    ];

    /**
     * Test index method.
     *
     * @return void
     *
     * @covers ::index()
     * @covers ::initialize()
     * @covers ::addCount()
     * @covers ::prepareFilter()
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
                    'count' => 12,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 12,
                    'page_size' => 20,
                ],
                'schema' => [
                    'users' => [
                        '$id' => 'http://api.example.com/model/schema/users',
                        'revision' => TestConstants::SCHEMA_REVISIONS['users'],
                    ],
                    'documents' => [
                        '$id' => 'http://api.example.com/model/schema/documents',
                        'revision' => TestConstants::SCHEMA_REVISIONS['documents'],
                    ],
                    'profiles' => [
                        '$id' => 'http://api.example.com/model/schema/profiles',
                        'revision' => TestConstants::SCHEMA_REVISIONS['profiles'],
                    ],
                    'locations' => [
                        '$id' => 'http://api.example.com/model/schema/locations',
                        'revision' => TestConstants::SCHEMA_REVISIONS['locations'],
                    ],
                    'events' => [
                        '$id' => 'http://api.example.com/model/schema/events',
                        'revision' => TestConstants::SCHEMA_REVISIONS['events'],
                    ],
                    'files' => [
                        '$id' => 'http://api.example.com/model/schema/files',
                        'revision' => TestConstants::SCHEMA_REVISIONS['files'],
                    ],
                    'folders' => [
                        '$id' => 'http://api.example.com/model/schema/folders',
                        'revision' => TestConstants::SCHEMA_REVISIONS['folders'],
                    ],
                ]
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
                        'lang' => 'en',
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
                        'another_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/1/another_test',
                                'self' => 'http://api.example.com/users/1/relationships/another_test',
                            ],
                        ],
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/1/parents',
                                'self' => 'http://api.example.com/users/1/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/1/translations',
                                'self' => 'http://api.example.com/users/1/relationships/translations',
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
                        'lang' => 'en',
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
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/2/parents',
                                'self' => 'http://api.example.com/documents/2/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/2/translations',
                                'self' => 'http://api.example.com/documents/2/relationships/translations',
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
                        'lang' => null,
                        'publish_start' => null,
                        'publish_end' => null,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2016-05-12T07:09:23+00:00',
                        'modified' => '2016-05-13T08:30:00+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 5,
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
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/parents',
                                'self' => 'http://api.example.com/documents/3/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/translations',
                                'self' => 'http://api.example.com/documents/3/relationships/translations',
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
                        'lang' => 'en',
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
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/parents',
                                'self' => 'http://api.example.com/profiles/4/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/translations',
                                'self' => 'http://api.example.com/profiles/4/relationships/translations',
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
                        'lang' => 'en',
                        'publish_start' => null,
                        'publish_end' => null,
                        'another_username' => 'synapse', // custom property
                        'another_email' => 'synapse@example.org', // custom property
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => null,
                        'created_by' => 5,
                        'modified_by' => 5,
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
                        'another_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/5/another_test',
                                'self' => 'http://api.example.com/users/5/relationships/another_test',
                            ],
                        ],
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/5/parents',
                                'self' => 'http://api.example.com/users/5/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/users/5/translations',
                                'self' => 'http://api.example.com/users/5/relationships/translations',
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
                        'lang' => 'en',
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
                        'inverse_another_test' => [
                            'links' => [
                                'related' => 'http://api.example.com/locations/8/inverse_another_test',
                                'self' => 'http://api.example.com/locations/8/relationships/inverse_another_test',
                            ],
                        ],
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/locations/8/parents',
                                'self' => 'http://api.example.com/locations/8/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/locations/8/translations',
                                'self' => 'http://api.example.com/locations/8/relationships/translations',
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
                        'lang' => 'en',
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
                    'relationships' => [
                        'test_abstract' => [
                            'links' => [
                                'related' => 'http://api.example.com/events/9/test_abstract',
                                'self' => 'http://api.example.com/events/9/relationships/test_abstract',
                            ],
                        ],
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/events/9/parents',
                                'self' => 'http://api.example.com/events/9/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/events/9/translations',
                                'self' => 'http://api.example.com/events/9/relationships/translations',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '10',
                    'type' => 'files',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'media-one',
                        'title' => 'first media',
                        'description' => 'media description goes here',
                        'body' => null,
                        'extra' => null,
                        'lang' => 'en',
                        'publish_start' => null,
                        'publish_end' => null,
                        'media_property' => 'synapse', // inherited custom property
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
                        'self' => 'http://api.example.com/files/10',
                    ],
                    'relationships' => [
                        'streams' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/10/streams',
                                'self' => 'http://api.example.com/files/10/relationships/streams',
                            ],
                        ],
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/10/parents',
                                'self' => 'http://api.example.com/files/10/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/10/translations',
                                'self' => 'http://api.example.com/files/10/relationships/translations',
                            ],
                        ],
                        'inverse_test_abstract' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/10/inverse_test_abstract',
                                'self' => 'http://api.example.com/files/10/relationships/inverse_test_abstract',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '11',
                    'type' => 'folders',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'root-folder',
                        'title' => 'Root Folder',
                        'description' => 'first root folder',
                        'body' => null,
                        'extra' => null,
                        'lang' => 'en',
                        'publish_start' => null,
                        'publish_end' => null,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2018-01-31T07:09:23+00:00',
                        'modified' => '2018-01-31T08:30:00+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/folders/11',
                    ],
                    'relationships' => [
                        'parent' => [
                            'links' => [
                                'related' => 'http://api.example.com/folders/11/parent',
                                'self' => 'http://api.example.com/folders/11/relationships/parent',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/folders/11/translations',
                                'self' => 'http://api.example.com/folders/11/relationships/translations',
                            ],
                        ],
                        'children' => [
                            'links' => [
                                'related' => 'http://api.example.com/folders/11/children',
                                'self' => 'http://api.example.com/folders/11/relationships/children',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '12',
                    'type' => 'folders',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'sub-folder',
                        'title' => 'Sub Folder',
                        'description' => 'sub folder of root folder',
                        'body' => null,
                        'extra' => null,
                        'lang' => 'en',
                        'publish_start' => null,
                        'publish_end' => null,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2018-01-31T07:09:23+00:00',
                        'modified' => '2018-01-31T08:30:00+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/folders/12',
                    ],
                    'relationships' => [
                        'parent' => [
                            'links' => [
                                'related' => 'http://api.example.com/folders/12/parent',
                                'self' => 'http://api.example.com/folders/12/relationships/parent',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/folders/12/translations',
                                'self' => 'http://api.example.com/folders/12/relationships/translations',
                            ],
                        ],
                        'children' => [
                            'links' => [
                                'related' => 'http://api.example.com/folders/12/children',
                                'self' => 'http://api.example.com/folders/12/relationships/children',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '13',
                    'type' => 'folders',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'another-root-folder',
                        'title' => 'Another Root Folder',
                        'description' => 'second root folder',
                        'body' => null,
                        'extra' => null,
                        'lang' => 'en',
                        'publish_start' => null,
                        'publish_end' => null,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2018-03-08T12:20:00+00:00',
                        'modified' => '2018-03-08T12:20:00+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/folders/13',
                    ],
                    'relationships' => [
                        'parent' => [
                            'links' => [
                                'related' => 'http://api.example.com/folders/13/parent',
                                'self' => 'http://api.example.com/folders/13/relationships/parent',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/folders/13/translations',
                                'self' => 'http://api.example.com/folders/13/relationships/translations',
                            ],
                        ],
                        'children' => [
                            'links' => [
                                'related' => 'http://api.example.com/folders/13/children',
                                'self' => 'http://api.example.com/folders/13/relationships/children',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '14',
                    'type' => 'files',
                    'attributes' => [
                        'status' => 'on',
                        'uname' => 'media-two',
                        'title' => 'second media',
                        'description' => 'another media description goes here',
                        'body' => null,
                        'extra' => null,
                        'lang' => 'en',
                        'publish_start' => null,
                        'publish_end' => null,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2018-03-22T16:42:31+00:00',
                        'modified' => '2018-03-22T16:42:31+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 1,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/files/14',
                    ],
                    'relationships' => [
                        'streams' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/14/streams',
                                'self' => 'http://api.example.com/files/14/relationships/streams',
                            ],
                        ],
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/14/parents',
                                'self' => 'http://api.example.com/files/14/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/14/translations',
                                'self' => 'http://api.example.com/files/14/relationships/translations',
                            ],
                        ],
                        'inverse_test_abstract' => [
                            'links' => [
                                'related' => 'http://api.example.com/files/14/inverse_test_abstract',
                                'self' => 'http://api.example.com/files/14/relationships/inverse_test_abstract',
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
        static::assertEquals($expected, $result);
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
                    'page_count' => 1,
                    'page_items' => 0,
                    'page_size' => 20,
                ],
            ],
            'data' => [],
        ];

        TableRegistry::getTableLocator()->get('Translations')->deleteAll([]);
        TableRegistry::getTableLocator()->get('Objects')->deleteAll([]);

        $this->configRequestHeaders();
        $this->get('/objects');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test view method.
     *
     * @return void
     *
     * @covers ::resource()
     * @covers ::initialize()
     * @covers ::addCount()
     */
    public function testSingle()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/documents/2',
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
                    'categories' => [
                        [
                            'name' => 'first-cat',
                            'label' => 'First category',
                            'params' => '100',
                        ],
                        [
                            'name' => 'second-cat',
                            'label' => 'Second category',
                            'params' => null,
                        ]
                    ],
                    'lang' => 'en',
                    'publish_start' => '2016-05-13T07:09:23+00:00',
                    'publish_end' => '2016-05-13T07:09:23+00:00',
                    'another_title' => null,
                    'another_description' => null,
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
                    'parents' => [
                        'links' => [
                            'related' => 'http://api.example.com/documents/2/parents',
                            'self' => 'http://api.example.com/documents/2/relationships/parents',
                        ],
                    ],
                    'translations' => [
                        'links' => [
                            'related' => 'http://api.example.com/documents/2/translations',
                            'self' => 'http://api.example.com/documents/2/relationships/translations',
                        ],
                    ],
                ],
            ],
            'meta' => [
                'schema' => [
                    'documents' => [
                        '$id' => 'http://api.example.com/model/schema/documents',
                        'revision' => TestConstants::SCHEMA_REVISIONS['documents'],
                    ]
                ]
            ]
        ];

        $this->configRequestHeaders();
        $this->get('/documents/2');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
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
                    'lang' => 'en',
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
                    'parents' => [
                        'links' => [
                            'related' => 'http://api.example.com/documents/6/parents',
                            'self' => 'http://api.example.com/documents/6/relationships/parents',
                        ],
                    ],
                    'translations' => [
                        'links' => [
                            'related' => 'http://api.example.com/documents/6/translations',
                            'self' => 'http://api.example.com/documents/6/relationships/translations',
                        ],
                    ],
                ],
            ],
            'meta' => [
                'schema' => [
                    'documents' => [
                        '$id' => 'http://api.example.com/model/schema/documents',
                        'revision' => TestConstants::SCHEMA_REVISIONS['documents'],
                    ]
                ]
            ]
        ];

        $this->configRequestHeaders();
        $this->get('/objects/6');
        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');

        // restore object -> deleted = false
        $objectsTable = TableRegistry::getTableLocator()->get('Objects');
        $object = $objectsTable->get(6);
        $object->deleted = false;
        $this->authUser();
        $success = $objectsTable->save($object);
        static::assertTrue((bool)$success);

        $this->configRequestHeaders();
        $this->get('/objects/6');
        $result = json_decode((string)$this->_response->getBody(), true);
        unset($result['data']['meta']['modified']);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);

        // undo restore -> deleted = true
        $object->deleted = true;
        $success = $objectsTable->save($object);
        static::assertTrue((bool)$success);
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
     * @covers ::resourceUrl()
     */
    public function testAdd()
    {
        $data = [
            'type' => 'documents',
            'attributes' => [
                'title' => 'A new document',
            ],
        ];

        $newId = $this->lastObjectId() + 1;
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/documents', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayHasKey('data', $result);
        static::assertArrayHasKey('attributes', $result['data']);
        static::assertArrayHasKey('status', $result['data']['attributes']);
        $this->assertHeader('Location', 'http://api.example.com/documents/' . $newId);
        static::assertTrue(TableRegistry::getTableLocator()->get('Documents')->exists($data['attributes']));
    }

    /**
     * Test add method with an abstract object type.
     *
     * @return void
     *
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testAddAbstract()
    {
        $data = [
            'type' => 'objects',
            'attributes' => [
                'title' => 'A new generic object',
            ],
        ];
        $expected = [
            'status' => '403',
            'title' => 'Abstract object types cannot be instantiated',
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/objects', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(403);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayHasKey('error', $result);
        static::assertArraySubset($expected, $result['error']);
        static::assertFalse(TableRegistry::getTableLocator()->get('Documents')->exists($data['attributes']));
    }

    /**
     * Test add method with an abstract object type.
     *
     * @return void
     *
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testAddAbstractMedia()
    {
        $data = [
            'type' => 'media',
            'attributes' => [
                'title' => 'A new generic media',
            ],
        ];
        $expected = [
            'status' => '403',
            'title' => 'Abstract object types cannot be instantiated',
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/media', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(403);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayHasKey('error', $result);
        static::assertArraySubset($expected, $result['error']);
        static::assertFalse(TableRegistry::getTableLocator()->get('Documents')->exists($data['attributes']));
    }

    /**
     * Test add not `enabled` object type.
     *
     * @return void
     *
     * @covers ::initialize()
     */
    public function testAddNotEnabled()
    {
        $data = [
            'type' => 'news',
            'attributes' => [
                'title' => 'A new disabled object',
            ],
        ];
        $expected = [
            'status' => '404',
            'title' => 'A route matching "/news" could not be found.',
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/news', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayHasKey('error', $result);
        static::assertArraySubset($expected, $result['error']);
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
        $this->post('/profiles', json_encode(compact('data')));

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
     * @covers ::initObjectModel()
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
        $document = TableRegistry::getTableLocator()->get('Documents')->get('2');
        static::assertEquals($newTitle, $document->get('title'));
        static::assertEquals('documents', $document->get('type'));
        static::assertEquals('on', $document->get('status'));

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
        $this->assertEquals('title two', TableRegistry::getTableLocator()->get('Documents')->get(3)->get('title'));
        $this->assertEquals('title one', TableRegistry::getTableLocator()->get('Documents')->get(2)->get('title'));

        $this->configRequestHeaders('PATCH', $authHeader);
        $this->patch('/profiles/3', json_encode(compact('data')));

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
        $this->assertEquals('title-one', TableRegistry::getTableLocator()->get('Documents')->get(2)->get('uname'));

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
        $this->assertResponseEmpty();

        $this->configRequestHeaders();
        $this->get('/documents/3');
        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');

        $docDeleted = TableRegistry::getTableLocator()->get('Documents')->get(7);
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
     * @covers ::getAvailableUrl()
     * @covers ::getAvailableTypes()
     * @covers ::getAssociatedAction()
     * @covers ::addCount()
     */
    public function testRelated()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/documents/3/inverse_test',
                'home' => 'http://api.example.com/home',
                'first' => 'http://api.example.com/documents/3/inverse_test',
                'last' => 'http://api.example.com/documents/3/inverse_test',
                'prev' => null,
                'next' => null,
                'available' => sprintf(
                    'http://api.example.com/objects?%s',
                    http_build_query(['filter' => ['type' => ['documents']]])
                ),
            ],
            'data' => [
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
                        'categories' => [
                            [
                                'name' => 'first-cat',
                                'label' => 'First category',
                                'params' => '100',
                            ],
                            [
                                'name' => 'second-cat',
                                'label' => 'Second category',
                                'params' => null,
                            ]
                        ],
                        'lang' => 'en',
                        'publish_start' => '2016-05-13T07:09:23+00:00',
                        'publish_end' => '2016-05-13T07:09:23+00:00',
                        'another_title' => null,
                        'another_description' => null,
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
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/2/parents',
                                'self' => 'http://api.example.com/documents/2/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/2/translations',
                                'self' => 'http://api.example.com/documents/2/relationships/translations',
                            ],
                        ],
                    ],
                    'meta' => [
                        'locked' => true,
                        'created' => '2016-05-13T07:09:23+00:00',
                        'modified' => '2016-05-13T07:09:23+00:00',
                        'published' => '2016-05-13T07:09:23+00:00',
                        'created_by' => 1,
                        'modified_by' => 1,
                        'relation' => [
                            'priority' => 1,
                            'inv_priority' => 2,
                            'params' => null,
                        ],
                    ],
                ],
            ],
            'meta' => [
                'pagination' => [
                    'count' => 1,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 1,
                    'page_size' => 20,
                ],
                'schema' => [
                    'documents' => [
                        '$id' => 'http://api.example.com/model/schema/documents',
                        'revision' => TestConstants::SCHEMA_REVISIONS['documents'],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/documents/3/inverse_test');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test relationships method to list existing relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::getAvailableUrl()
     * @covers ::getAvailableTypes()
     * @covers ::getAssociatedAction()
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
                'available' => sprintf(
                    'http://api.example.com/objects?%s',
                    http_build_query(['filter' => ['type' => ['documents', 'profiles']]])
                ),
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
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/parents',
                                'self' => 'http://api.example.com/profiles/4/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/translations',
                                'self' => 'http://api.example.com/profiles/4/relationships/translations',
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
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/parents',
                                'self' => 'http://api.example.com/documents/3/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/translations',
                                'self' => 'http://api.example.com/documents/3/relationships/translations',
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
        static::assertEquals($expected, $result);
    }

    /**
     * Data provider for `testLinksAvailable`
     *
     * @return array
     */
    public function linksAvailableProvider()
    {
        return [
            'children' => [
                'http://api.example.com/objects',
                '/folders/12/children',
            ],
            'parents' => [
                'http://api.example.com/objects?filter[type][0]=folders',
                '/profiles/4/parents',
            ],
            'parent' => [
                'http://api.example.com/objects?filter[type][0]=folders',
                '/folders/12/parent',
            ],
            'inverse_test' => [
                'http://api.example.com/objects?filter[type][0]=documents',
                '/documents/2/inverse_test',
            ],
        ];
    }

    /**
     * Test related method on folder related relationships.
     *
     * @return void
     * @param string $expected Expected result
     * @param string $url Test URL
     *
     * @dataProvider linksAvailableProvider
     * @covers ::getAvailableUrl()
     * @covers ::getAvailableTypes()
     */
    public function testLinksAvailable($expected, $url)
    {
        $this->configRequestHeaders();
        $this->get($url);
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertEquals($expected, urldecode(Hash::get($result, 'links.available')));
    }

    /**
     * Test `getAvailableUrl` in case of not available types.
     *
     * @return void
     *
     * @covers ::getAvailableUrl()
     */
    public function testLinksAvailableEmpty()
    {
        $environment = ['REQUEST_METHOD' => 'GET'];
        $params = [
            'object_type' => 'documents',
            'relationship' => 'inverse_test',
            'related_id' => '2',
        ];
        $request = new ServerRequest(compact('environment', 'params'));

        $controller = $this->getMockBuilder(ObjectsController::class)
            ->setConstructorArgs([$request])
            ->setMethods(['getAvailableTypes'])
            ->getMock();

        $controller
            ->method('getAvailableTypes')
            ->willReturn([]);

        $controller->related();
        static::assertEquals(['available' => null], $controller->viewVars['_links']);
    }

    /**
     * Test relationships method to list existing relationships.
     *
     * @return void
     *
     * @covers ::initialize()
     * @covers ::relationships()
     * @covers ::findAssociation()
     * @covers ::getAssociatedAction()
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
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/parents',
                                'self' => 'http://api.example.com/profiles/4/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/translations',
                                'self' => 'http://api.example.com/profiles/4/relationships/translations',
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
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/parents',
                                'self' => 'http://api.example.com/profiles/4/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/translations',
                                'self' => 'http://api.example.com/profiles/4/relationships/translations',
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
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/parents',
                                'self' => 'http://api.example.com/profiles/4/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/translations',
                                'self' => 'http://api.example.com/profiles/4/relationships/translations',
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
                    'categories' => [
                        [
                            'name' => 'first-cat',
                            'label' => 'First category',
                            'params' => '100',
                        ],
                        [
                            'name' => 'second-cat',
                            'label' => 'Second category',
                            'params' => null,
                        ]
                    ],
                    'lang' => 'en',
                    'publish_start' => '2016-05-13T07:09:23+00:00',
                    'publish_end' => '2016-05-13T07:09:23+00:00',
                    'another_title' => null,
                    'another_description' => null,
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
                    'parents' => [
                        'links' => [
                            'self' => 'http://api.example.com/documents/2/relationships/parents',
                            'related' => 'http://api.example.com/documents/2/parents',
                        ],
                    ],
                    'translations' => [
                        'links' => [
                            'self' => 'http://api.example.com/documents/2/relationships/translations',
                            'related' => 'http://api.example.com/documents/2/translations',
                        ],
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
                        'lang' => 'en',
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
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/parents',
                                'self' => 'http://api.example.com/profiles/4/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/profiles/4/translations',
                                'self' => 'http://api.example.com/profiles/4/relationships/translations',
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
                        'lang' => null,
                        'publish_start' => null,
                        'publish_end' => null,
                    ],
                    'meta' => [
                        'locked' => false,
                        'created' => '2016-05-12T07:09:23+00:00',
                        'modified' => '2016-05-13T08:30:00+00:00',
                        'published' => null,
                        'created_by' => 1,
                        'modified_by' => 5,
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
                        'parents' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/parents',
                                'self' => 'http://api.example.com/documents/3/relationships/parents',
                            ],
                        ],
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/documents/3/translations',
                                'self' => 'http://api.example.com/documents/3/relationships/translations',
                            ],
                        ],
                    ],
                ],
            ],
            'meta' => [
                'schema' => [
                    'documents' => [
                        '$id' => 'http://api.example.com/model/schema/documents',
                        'revision' => TestConstants::SCHEMA_REVISIONS['documents'],
                    ],
                    'profiles' => [
                        '$id' => 'http://api.example.com/model/schema/profiles',
                        'revision' => TestConstants::SCHEMA_REVISIONS['profiles'],
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
     * Test `?include` query parameter on related endpoint.
     *
     * @return void
     *
     * @covers ::prepareInclude()
     */
    public function testRelatedInclude(): void
    {
        $this->configRequestHeaders();
        $this->get('/profiles/4/inverse_test?include=test');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertSame(['3', '2'], Hash::extract($result, 'data.{n}.id'));
        static::assertSame(['4'], Hash::extract($result, 'data.0.relationships.test.data.{n}.id'));
        static::assertSame(['4', '3'], Hash::extract($result, 'data.1.relationships.test.data.{n}.id'));
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

    /**
     * Data provider fo `testInitializeResourceTypes()`
     *
     * @return array
     */
    public function resourceTypeProvider()
    {
        return [
            'mainResource' => [
                ['documents'],
                [
                    'params' => [
                        'controller' => 'Documents',
                        'action' => 'index',
                    ],
                ],
            ],
            'beditaRelation' => [
                ['documents', 'profiles'],
                [
                    'params' => [
                        'controller' => 'Documents',
                        'action' => 'relationships',
                        'relationship' => 'test',
                    ],
                ],
            ],
            'parentRelationships' => [
                ['folders'],
                [
                    'params' => [
                        'controller' => 'Documents',
                        'action' => 'relationships',
                        'relationship' => 'parents',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `resourceTypes` config of `jsonApiComponent` set in `initialize()`
     *
     * @param array $expected The expected result
     * @param array $requestData The data needed to create the request
     * @return void
     *
     * @dataProvider resourceTypeProvider
     * @covers ::initialize()
     */
    public function testInitializeResourceTypes(array $expected, array $requestData)
    {
        $request = new ServerRequest($requestData + [
            'environment' => [
                'HTTP_ACCEPT' => 'application/vnd.api+json',
            ],
        ]);
        $controller = new ObjectsController($request);
        $resourceTypes = $controller->JsonApi->getConfig('resourceTypes');

        sort($expected);
        sort($resourceTypes);
        static::assertEquals($expected, array_values($resourceTypes));
    }

    /**
     * Data provider fo `testMissingRoute()`
     *
     * @return array
     */
    public function missingRouteProvider()
    {
        return [
            'document' => [
                '/document',
                'A route matching "/document" could not be found. Did you mean "documents"?',
            ],
            'id' => [
                '/2',
                'A route matching "/2" could not be found. Did you mean "documents"?',
            ],
            'badurl' => [
                '/badurl',
                'A route matching "/badurl" could not be found.',
            ],
        ];
    }

    /**
     * Test missing route errors.
     *
     * @param string $url The url
     * @param string $expected The expected error message
     * @return void
     *
     * @dataProvider missingRouteProvider
     * @covers ::initObjectModel()
     */
    public function testMissingRoute($url, $expected)
    {
        $this->configRequestHeaders();
        $this->get($url);

        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');

        $response = json_decode((string)$this->_response->getBody(), true);
        static::assertEquals($expected, $response['error']['title']);
    }

    /**
     * Test 'lang' filter.
     *
     * @return void
     *
     * @covers ::resource()
     */
    public function testLang()
    {
        $expected = [
            [
                'id' => '2',
                'type' => 'translations',
                'attributes' => [
                    'status' => 'on',
                    'lang' => 'fr',
                    'object_id' => 2,
                    'translated_fields' => [
                        'description' => 'description ici',
                        'extra' => [
                            'list' => ['on', 'deux', 'trois'],
                        ],
                    ],
                ],
                'meta' => [
                    'created' => '2018-01-01T00:00:00+00:00',
                    'modified' => '2018-01-01T00:00:00+00:00',
                    'created_by' => 1,
                    'modified_by' => 1,
                ],
                'links' => [
                    'self' => 'http://api.example.com/translations/2',
                ],
                'relationships' => [
                    'object' => [
                        'links' => [
                            'related' => 'http://api.example.com/translations/2/object',
                            'self' => 'http://api.example.com/translations/2/relationships/object',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/documents/2?lang=fr');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertNotEmpty($result['included']);
        static::assertEquals($expected, $result['included']);
    }

    /**
     * Test addCount()
     *
     * @return void
     *
     * @covers ::addCount()
     */
    public function testAddCount(): void
    {
        $this->configRequestHeaders();
        $this->get('/documents/2?count=test');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        static::assertEquals(2, Hash::get($result, 'data.relationships.test.meta.count'));
    }

    /**
     * Test prepareFilter()
     *
     * @return void
     *
     * @covers ::prepareFilter()
     */
    public function testPrepareFilter(): void
    {
        $this->configRequestHeaders();
        $this->get('/events?sort=date_ranges_min_start_date');
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        static::assertEquals(9, Hash::get($result, 'data.0.id'));
    }
}
