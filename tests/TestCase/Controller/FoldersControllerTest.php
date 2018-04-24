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
use Cake\Utility\Hash;

/**
 * @coversDefaultClass \BEdita\API\Controller\FoldersController
 */
class FoldersControllerTest extends IntegrationTestCase
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
                'self' => 'http://api.example.com/folders',
                'first' => 'http://api.example.com/folders',
                'last' => 'http://api.example.com/folders',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'pagination' => [
                    'count' => 3,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 3,
                    'page_size' => 20,
                ],
                'schema' => [
                    'folders' => [
                        '$id' => 'http://api.example.com/model/schema/folders',
                        'revision' => TestConstants::SCHEMA_REVISIONS['folders'],
                    ],
                ],
            ],
            'data' => [
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
                        'lang' => 'eng',
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
                        'children' => [
                            'links' => [
                                'related' => 'http://api.example.com/folders/11/children',
                                'self' => 'http://api.example.com/folders/11/relationships/children',
                            ],
                        ],
                        'parent' => [
                            'links' => [
                                'related' => 'http://api.example.com/folders/11/parent',
                                'self' => 'http://api.example.com/folders/11/relationships/parent',
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
                        'lang' => 'eng',
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
                        'children' => [
                            'links' => [
                                'related' => 'http://api.example.com/folders/12/children',
                                'self' => 'http://api.example.com/folders/12/relationships/children',
                            ],
                        ],
                        'parent' => [
                            'links' => [
                                'related' => 'http://api.example.com/folders/12/parent',
                                'self' => 'http://api.example.com/folders/12/relationships/parent',
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
                        'lang' => 'eng',
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
                        'children' => [
                            'links' => [
                                'related' => 'http://api.example.com/folders/13/children',
                                'self' => 'http://api.example.com/folders/13/relationships/children',
                            ],
                        ],
                        'parent' => [
                            'links' => [
                                'related' => 'http://api.example.com/folders/13/parent',
                                'self' => 'http://api.example.com/folders/13/relationships/parent',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/folders');
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
     * @coversNothing
     */
    public function testEmpty()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/folders',
                'first' => 'http://api.example.com/folders',
                'last' => 'http://api.example.com/folders',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
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

        TableRegistry::get('folders')->deleteAll([]);

        $this->configRequestHeaders();
        $this->get('/folders');
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
     * @coversNothing
     */
    public function testSingle()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/folders/11',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '11',
                'type' => 'folders',
                'attributes' => [
                    'status' => 'on',
                    'uname' => 'root-folder',
                    'title' => 'Root Folder',
                    'description' => 'first root folder',
                    'body' => null,
                    'extra' => null,
                    'lang' => 'eng',
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
                'relationships' => [
                    'children' => [
                        'links' => [
                            'related' => 'http://api.example.com/folders/11/children',
                            'self' => 'http://api.example.com/folders/11/relationships/children',
                        ],
                    ],
                    'parent' => [
                        'links' => [
                            'related' => 'http://api.example.com/folders/11/parent',
                            'self' => 'http://api.example.com/folders/11/relationships/parent',
                        ],
                    ],
                ],
            ],
            'meta' => [
                'schema' => [
                    'folders' => [
                        '$id' => 'http://api.example.com/model/schema/folders',
                        'revision' => TestConstants::SCHEMA_REVISIONS['folders'],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/folders/11');
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
     * @coversNothing
     */
    public function testMissing()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/folders/99',
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '404',
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/folders/99');
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
     * @coversNothing
     */
    public function testAdd()
    {
        $data = [
            'type' => 'folders',
            'attributes' => [
                'title' => 'A new folder',
                'description' => 'Here I am',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/folders', json_encode(compact('data')));
        $folderId = $this->lastObjectId();

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        $this->assertHeader('Location', 'http://api.example.com/folders/' . $folderId);
        static::assertTrue(TableRegistry::get('Folders')->exists(['id' => $folderId]));
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
            'id' => '11',
            'type' => 'folders',
            'attributes' => [
                'title' => 'Radice',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/folders/11', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals('Radice', TableRegistry::get('Folders')->get(11)->get('title'));
        static::assertEquals('folders', TableRegistry::get('Folders')->get(11)->get('type'));

        $result = json_decode((string)$this->_response->getBody(), true);
        static::assertEquals($data['id'], $result['data']['id']);
        static::assertEquals($data['type'], $result['data']['type']);
        static::assertEquals($data['attributes']['title'], $result['data']['attributes']['title']);
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
        // TODO: how should we behave on folders delete?
        // a. delete only folders that doesn't have another folder as child
        // b. delete folder anyway
        $this->markTestIncomplete();
    }

    /**
     * Test related method to get `parent` folder.
     *
     * @return void
     *
     * @covers ::findAssociation()
     * @covers ::getAvailableUrl()
     * @covers ::getAssociatedAction()
     */
    public function testRelatedParent()
    {
        $this->configRequestHeaders();
        $this->get('/folders/12/parent');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertSame('11', $result['data']['id']);
        static::assertSame('folders', $result['data']['type']);
    }

    /**
     * Test that an error is returned getting `parents` folder.
     *
     * @return void
     *
     * @covers ::findAssociation()
     * @covers ::getAvailableUrl()
     * @covers ::getAssociatedAction()
     */
    public function testErrorRelatedParents()
    {
        $this->configRequestHeaders();
        $this->get('/folders/12/parents');
        $result = json_decode((string)$this->_response->getBody(), true);

        $expected = [
            'status' => '404',
            'title' => 'Relationship "parents" does not exist',
        ];
        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayHasKey('error', $result);
        static::assertArraySubset($expected, $result['error']);
    }

    /**
     * Test related method to get `children` objects.
     *
     * @return void
     *
     * @covers ::findAssociation()
     * @covers ::getAvailableUrl()
     * @covers ::getAssociatedAction()
     */
    public function testRelatedChildren()
    {
        $this->configRequestHeaders();
        $this->get('/folders/11/children');
        $result = json_decode((string)$this->_response->getBody(), true);

        $treesTable = TableRegistry::get('Trees');
        $node = $treesTable->find()->where(['object_id' => 11])->first();
        $children = $treesTable
            ->find('children', ['for' => $node->id, 'direct' => true])
            ->toArray();

        // build array of ids casted to string
        $expected = Hash::map($children, '{n}.object_id', function ($id) {
            return (string)$id;
        });
        sort($expected);

        $actual = Hash::extract($result, 'data.{n}.id');
        sort($actual);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for `testSetRelationshipsAllowedMethods()`
     *
     * @return array
     */
    public function setRelationshipsAllowedMethodsProvider()
    {
        return [
            'get' => [200, 'get'],
            'patch' => [
                204,
                'patch',
                [
                    'type' => 'folders',
                    'id' => 11,
                ]
            ],
            'post' => [405, 'post'],
            'delete' => [405, 'delete'],
        ];
    }

    /**
     * Test for setRelationshipsAllowedMethods() method.
     *
     * @param int $expected The expected HTTP status code.
     * @param string $method The http method.
     * @param array|null $data Payload to use for the request.
     * @return void
     *
     * @dataProvider setRelationshipsAllowedMethodsProvider
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testSetRelationshipsAllowedMethods($expected, $method, $data = null)
    {
        $authHeader = $this->getUserAuthHeader();
        $this->configRequestHeaders($method, $authHeader);
        $this->{$method}('/folders/12/relationships/parent', $data);
        $this->assertResponseCode($expected);
    }

    /**
     * Test deleted objects as `children`
     *
     * @return void
     *
     * @coversNothing
     */
    public function testDeletedChildren()
    {
        // add a deleted object to folder and verify it's not listed in `childrens`
        $treesTable = TableRegistry::get('Trees');
        $entity = $treesTable->newEntity([
                'object_id' => 6,
            ]);
        $entity->parent_id = 12;
        $treesTable->saveOrFail($entity);

        $this->configRequestHeaders();
        $this->get('/folders/12/children');
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $result = json_decode((string)$this->_response->getBody(), true);

        $ids = Hash::extract($result, 'data.{n}.id');
        static::assertSame(['4'], $ids);
    }
}
