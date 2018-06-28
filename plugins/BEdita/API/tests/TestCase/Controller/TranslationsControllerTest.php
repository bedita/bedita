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
 * @coversDefaultClass \BEdita\API\Controller\TranslationsController
 */
class TranslationsControllerTest extends IntegrationTestCase
{
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
                'self' => 'http://api.example.com/translations',
                'first' => 'http://api.example.com/translations',
                'last' => 'http://api.example.com/translations',
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
                    'type' => 'translations',
                    'attributes' => [
                        'status' => 'on',
                        'lang' => 'it-IT',
                        'object_id' => 2,
                        'translated_fields' => [
                            'title' => 'titolo uno',
                            'description' => 'descrizione qui',
                            'body' => 'contenuto qui',
                            'extra' => [
                                'abstract' => 'estratto qui',
                                'list' => ['uno', 'due', 'tre'],
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
                        'self' => 'http://api.example.com/translations/1',
                    ],
                    'relationships' => [
                        'object' => [
                            'links' => [
                                'related' => 'http://api.example.com/translations/1/object',
                                'self' => 'http://api.example.com/translations/1/relationships/object',
                            ],
                        ],
                    ],
                ],
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
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/translations');
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
                'self' => 'http://api.example.com/translations/1',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '1',
                'type' => 'translations',
                'attributes' => [
                    'status' => 'on',
                    'lang' => 'it-IT',
                    'object_id' => 2,
                    'translated_fields' => [
                        'title' => 'titolo uno',
                        'description' => 'descrizione qui',
                        'body' => 'contenuto qui',
                        'extra' => [
                            'abstract' => 'estratto qui',
                            'list' => ['uno', 'due', 'tre'],
                        ],
                    ],
                ],
                'meta' => [
                    'created' => '2018-01-01T00:00:00+00:00',
                    'modified' => '2018-01-01T00:00:00+00:00',
                    'created_by' => 1,
                    'modified_by' => 1,
                ],
                'relationships' => [
                    'object' => [
                        'links' => [
                            'related' => 'http://api.example.com/translations/1/object',
                            'self' => 'http://api.example.com/translations/1/relationships/object',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/translations/1');
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
            'type' => 'translations',
            'attributes' => [
                'object_id' => 2,
                'lang' => 'sp',
                'status' => 'draft',
                'translated_fields' => [
                    'title' => 'titulo uno',
                ],
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/translations', json_encode(compact('data')));
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayHasKey('data', $result);
        $this->assertHeader('Location', 'http://api.example.com/translations/3');
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
            'id' => '2',
            'type' => 'translations',
            'attributes' => [
                'translated_fields' => [
                    'description' => 'la descripcion',
                ],
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/translations/2', json_encode(compact('data')));

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
        // delete translation 2
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete('/translations/2');
        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');
        $this->assertFalse(TableRegistry::get('Translations')->exists(['id' => 2]));
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
                'self' => 'http://api.example.com/translations/1/object',
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
        $this->get('/translations/1/object');
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
                'self' => 'http://api.example.com/translations/1/relationships/object',
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
        $this->get('/translations/1/relationships/object');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }
}
