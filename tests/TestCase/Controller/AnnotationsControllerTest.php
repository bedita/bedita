<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
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
use Cake\ORM\TableRegistry;

/**
 * @coversDefaultClass \BEdita\API\Controller\AnnotationsController
 */
class AnnotationsControllerTest extends IntegrationTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.annotations'
    ];

    /**
     * Test index method.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testIndex()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/annotations',
                'first' => 'http://api.example.com/annotations',
                'last' => 'http://api.example.com/annotations',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
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
                    'type' => 'annotations',
                    'attributes' => [
                        'object_id' => 2,
                        'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Best regards.',
                        'params' => 'something',
                    ],
                    'meta' => [
                        'created' => '2018-02-17T10:23:15+00:00',
                        'modified' => '2018-02-17T10:23:15+00:00',
                        'user_id' => 1,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/annotations/1',
                    ],
                    'relationships' => [
                        'object' => [
                            'links' => [
                                'related' => 'http://api.example.com/annotations/1/object',
                                'self' => 'http://api.example.com/annotations/1/relationships/object',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'annotations',
                    'attributes' => [
                        'object_id' => 3,
                        'description' => 'Gustavo for President!',
                        'params' => 1,
                    ],
                    'meta' => [
                        'created' => '2018-06-17T13:34:25+00:00',
                        'modified' => '2018-06-17T13:34:25+00:00',
                        'user_id' => 5,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/annotations/2',
                    ],
                    'relationships' => [
                        'object' => [
                            'links' => [
                                'related' => 'http://api.example.com/annotations/2/object',
                                'self' => 'http://api.example.com/annotations/2/relationships/object',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/annotations');
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
     * @coversNothing
     */
    public function testSingle()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/annotations/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'annotations',
                'attributes' => [
                    'object_id' => 2,
                    'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Best regards.',
                    'params' => 'something',
                ],
                'meta' => [
                    'created' => '2018-02-17T10:23:15+00:00',
                    'modified' => '2018-02-17T10:23:15+00:00',
                    'user_id' => 1,
                ],
                'relationships' => [
                    'object' => [
                        'links' => [
                            'related' => 'http://api.example.com/annotations/1/object',
                            'self' => 'http://api.example.com/annotations/1/relationships/object',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/annotations/1');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test add method.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testAdd()
    {
        $data = [
            'type' => 'annotations',
            'attributes' => [
                'object_id' => 3,
                'description' => 'a note',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/annotations', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayHasKey('data', $result);
        $this->assertHeader('Location', 'http://api.example.com/annotations/3');
    }

    /**
     * Test edit method.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testEdit()
    {
        $data = [
            'id' => '1',
            'type' => 'annotations',
            'attributes' => [
                'description' => 'Lorem ipsum NO MORE!',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/annotations/1', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test delete method.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testDelete()
    {
        // delete annotation 1
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete('/annotations/1');
        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $this->assertFalse(TableRegistry::get('Annotations')->exists(['id' => 1]));
    }

    /**
     * Test related objects.
     *
     * @return void
     *
     * @covers ::getAvailableUrl()
     */
    public function testRelated()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/annotations/1/object',
                'home' => 'http://api.example.com/home',
                'available' => 'http://api.example.com/objects',
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
                    'lang' => 'en',
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
                    ],
                    'inverse_test' => [
                        'links' => [
                            'self' => 'http://api.example.com/documents/2/relationships/inverse_test',
                            'related' => 'http://api.example.com/documents/2/inverse_test',
                        ],
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
        $this->get('/annotations/1/object');
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
     * @covers ::getAssociatedAction()
     */
    public function testRelationships()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/annotations/1/relationships/object',
                'home' => 'http://api.example.com/home',
                'available' => 'http://api.example.com/objects',
            ],
            'data' => [
                'id' => '2',
                'type' => 'documents',
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
        ];

        $this->configRequestHeaders();
        $this->get('/annotations/1/relationships/object');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }
}
