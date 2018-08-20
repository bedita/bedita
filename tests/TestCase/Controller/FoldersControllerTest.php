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
     * Folders table.
     *
     * @var \BEdita\Core\Model\Table\FoldersTable
     */
    public $Folders;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Folders = TableRegistry::get('Folders');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Folders);

        parent::tearDown();
    }

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
                        'path' => '/11',
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
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/folders/11/translations',
                                'self' => 'http://api.example.com/folders/11/relationships/translations',
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
                        'path' => '/11/12',
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
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/folders/12/translations',
                                'self' => 'http://api.example.com/folders/12/relationships/translations',
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
                        'path' => '/13',
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
                        'translations' => [
                            'links' => [
                                'related' => 'http://api.example.com/folders/13/translations',
                                'self' => 'http://api.example.com/folders/13/relationships/translations',
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
        static::assertEquals($expected, $result);
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

        TableRegistry::get('Translations')->deleteAll([]);
        TableRegistry::get('Folders')->deleteAll([]);

        $this->configRequestHeaders();
        $this->get('/folders');
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
                    'path' => '/11',
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
                    'translations' => [
                        'links' => [
                            'related' => 'http://api.example.com/folders/11/translations',
                            'self' => 'http://api.example.com/folders/11/relationships/translations',
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
        static::assertEquals($expected, $result);
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
        static::assertArrayNotHasKey('data', $result);
        static::assertArrayHasKey('links', $result);
        static::assertArrayHasKey('error', $result);
        static::assertEquals($expected['links'], $result['links']);
        static::assertArraySubset($expected['error'], $result['error']);
        static::assertArrayHasKey('title', $result['error']);
        static::assertNotEmpty($result['error']['title']);
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
        $foldersTable = TableRegistry::get('Folders');
        $folderId = 11;
        $children = $foldersTable
            ->find('ancestor', [$folderId])
            ->toArray();

        $authHeader = $this->getUserAuthHeader();

        $this->configRequestHeaders('DELETE', $authHeader);
        $endpoint = sprintf('/folders/%s', $folderId);
        $this->delete($endpoint);

        $this->assertResponseCode(204);
        $this->assertContentType('application/vnd.api+json');

        $this->configRequestHeaders();
        $this->get($endpoint);
        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');

        foreach ($children as $child) {
            $this->configRequestHeaders();
            $this->get(sprintf('/%s/%s', $child->type, $child->id));
            if ($child->type === 'folders') {
                $this->assertResponseCode(404);
                $this->assertContentType('application/vnd.api+json');
            } else {
                $this->assertResponseCode(200);
                $this->assertContentType('application/vnd.api+json');
            }
        }
    }

    /**
     * Test related method to get `parent` folder.
     *
     * @return void
     *
     * @covers ::findAssociation()
     * @covers ::getAvailableTypes()
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
     * @covers ::getAvailableTypes()
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
     * @covers ::getAvailableTypes()
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
            'get' => [
                200,
                'GET',
            ],
            'patch' => [
                204,
                'PATCH',
                [
                    'type' => 'folders',
                    'id' => 11,
                ],
            ],
            'patch conflict' => [
                409,
                'PATCH',
                [
                    'type' => 'documents',
                    'id' => 2,
                ],
            ],
            'post' => [
                405,
                'POST',
                [
                    'type' => 'folders',
                    'id' => 11,
                ],
            ],
            'delete' => [
                405,
                'DELETE',
                [
                    'type' => 'folders',
                    'id' => 11,
                ],
            ],
        ];
    }

    /**
     * Test for setRelationshipsAllowedMethods() method.
     *
     * @param int $expected The expected HTTP status code.
     * @param string $method The http method.
     * @param array $data Payload to use for the request.
     * @return void
     *
     * @dataProvider setRelationshipsAllowedMethodsProvider
     * @covers ::setRelationshipsAllowedMethods()
     */
    public function testSetRelationshipsAllowedMethods($expected, $method, array $data = [])
    {
        $authHeader = $this->getUserAuthHeader();
        $this->configRequestHeaders($method, $authHeader);
        if (!empty($data)) {
            $data = json_encode(compact('data'));
        }
        $this->_sendRequest('/folders/12/relationships/parent', $method, $data);
        $this->assertResponseCode($expected);
    }

    /**
     * Test set `parent` folder relationship
     *
     * @return void
     *
     * @coversNothing
     */
    public function testSetParent()
    {
        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $data = [
            'type' => 'folders',
            'id' => 13,
        ];
        $this->patch('/folders/12/relationships/parent', json_encode(compact('data')));
        $this->assertResponseCode(200);
        $result = json_decode((string)$this->_response->getBody(), true);

        $expected = [
            'links' => [
                'self' => 'http://api.example.com/folders/12/relationships/parent',
                'home' => 'http://api.example.com/home',
            ],
        ];
        static::assertEquals($expected, $result);

        $this->configRequestHeaders();
        $this->get('/folders/12');
        $this->assertResponseCode(200);
        $result = json_decode((string)$this->_response->getBody(), true);

        $path = Hash::get($result, 'data.meta.path');
        static::assertEquals('/13/12', $path);
    }

    /**
     * Test set children relationship
     *
     * @return void
     *
     * @coversNothing
     */
    public function testSetChildren()
    {
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $data = [
            [
                'type' => 'documents',
                'id' => 2,
            ],
            [
                'type' => 'users',
                'id' => 5,
            ],
        ];
        $this->post('/folders/12/relationships/children', json_encode(compact('data')));
        $this->assertResponseCode(200);

        $this->configRequestHeaders();
        $this->get('/folders/12/children');
        $this->assertResponseCode(200);
        $result = json_decode((string)$this->_response->getBody(), true);

        $childrenIds = Hash::extract($result, 'data.{n}.id');
        static::assertEquals(['4', '2', '5'], $childrenIds);
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

    /**
     * Test `?include=children` query
     *
     * @return void
     *
     * @coversNothing
     */
    public function testIncludeChildren()
    {
        $this->configRequestHeaders();
        $this->get('/folders/11?include=children');
        $this->assertResponseCode(200);
        $result = json_decode((string)$this->_response->getBody(), true);

        $includedIds = Hash::extract($result, 'included.{n}.id');
        static::assertEquals(['12', '2'], $includedIds);
    }

    /**
     * Test `?include=parent` query
     *
     * @return void
     *
     * @coversNothing
     */
    public function testIncludeParent()
    {
        $this->configRequestHeaders();
        $this->get('/folders/12?include=parent');
        $result = json_decode((string)$this->_response->getBody(), true);

        $includedIds = Hash::extract($result, 'included.{n}.id');
        static::assertEquals(['11'], $includedIds);
    }

    /**
     * Data provider for `testGetOrphanFolder()`
     *
     * @return array
     */
    public function getOrphanFolderProvider()
    {
        return [
            'folders/:id' => ['12'],
            '/folders' => [],
        ];
    }

    /**
     * Test that getting orphan folders return a 500 error.
     *
     * @param int|null $id Folder ID to get.
     * @return void
     *
     * @dataProvider getOrphanFolderProvider
     * @coversNothing
     */
    public function testGetOrphanFolder($id = null)
    {
        TableRegistry::get('Trees')->deleteAll(['object_id' => 12]);
        TableRegistry::get('Trees')->recover();

        $endpoint = '/folders';
        if ($id) {
            $endpoint .= "/$id";
        }
        $this->configRequestHeaders();
        $this->get($endpoint);
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(500);
        $this->assertContentType('application/vnd.api+json');
        static::assertArrayHasKey('error', $result);
        static::assertArrayHasKey('title', $result['error']);
        static::assertEquals('Folder "12" is not on the tree.', $result['error']['title']);
    }

    /**
     * Data provider for `testMoveFolder()`
     *
     * @return array
     */
    public function moveFolderProvider()
    {
        return [
            'becomeRoot' => [
                12,
                null,
            ],
            'moveToAnotherPosition' => [
                12,
                13,
            ],
            'rootBecomeSubfolder' => [
                11,
                13,
            ],
        ];
    }

    /**
     * Test that moving folder all descendants remain at their place.
     *
     * @param int $folderId The folder to move
     * @param int|null $parentId the new parent
     * @return void
     *
     * @dataProvider moveFolderProvider
     * @coversNothing
     */
    public function testMoveFolder($folderId, $parentId)
    {
        $foldersTable = TableRegistry::get('Folders');

        $getDescendants = function () use ($folderId, $foldersTable) {
            return $foldersTable
                ->find('ancestor', [$folderId])
                ->find('list')
                ->toArray();
        };

        $treesTable = TableRegistry::get('Trees');
        $folderTreeNode = $treesTable->find()->where(['object_id' => $folderId])->first();

        $getTreeList = function () use ($treesTable, $folderTreeNode) {
            return $treesTable
                ->find('children', ['for' => $folderTreeNode->id])
                ->find('treeList')
                ->toArray();
        };

        $expectedDescendants = $getDescendants();
        $expectedTreeList = $getTreeList();

        $data = null;
        if ($parentId !== null) {
            $data = [
                'type' => 'folders',
                'id' => (string)$parentId,
            ];
        }
        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch("/folders/$folderId/relationships/parent", json_encode(compact('data')));
        $this->assertResponseCode(200);

        $actualDescendants = $getDescendants();
        $actualTreeList = $getTreeList();

        static::assertSame($expectedDescendants, $actualDescendants);
        static::assertSame($expectedTreeList, $actualTreeList);

        // get parent
        $this->configRequestHeaders();
        $this->get("/folders/$folderId/parent");
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        if ($parentId === null) {
            static::assertSame(null, $result['data']);
        } else {
            static::assertSame((string)$parentId, Hash::get($result, 'data.id'));
        }
    }

    /**
     * Test setting a folder's children with position.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testSetChildrenPosition()
    {
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $data = [
            [
                'id' => '2',
                'type' => 'documents',
                'meta' => [
                    'relation' => [
                        'position' => 3,
                    ],
                ],
            ],
            [
                'id' => '5',
                'type' => 'users',
                'meta' => [
                    'relation' => [
                        'position' => 'first',
                    ],
                ],
            ],
        ];
        $this->post('/folders/12/relationships/children', json_encode(compact('data')));
        $this->assertResponseCode(200);

        $folder = $this->Folders->get(12, ['contain' => ['Children']]);

        $childrenIds = Hash::extract($folder->children, '{n}.id');
        static::assertEquals(['5', '4', '2'], $childrenIds);
    }

    /**
     * Data provider for `testSetChildrenPositionInvalid` test case.
     *
     * @return array
     */
    public function setChildrenPositionInvalidProvider()
    {
        return [
            'zero' => [
                '[position.notEquals]: The provided value is invalid',
                0,
            ],
            'invalid string' => [
                '[position.inList]: The provided value is invalid',
                'gustavo',
            ],
            'empty' => [
                '[position._empty]: This field cannot be left empty',
                '',
            ],
        ];
    }

    /**
     * Test setting a folder's children with an invalid position.
     *
     * @param string $expected Expected error.
     * @param int|string $position Desired position.
     * @return void
     *
     * @dataProvider setChildrenPositionInvalidProvider()
     * @coversNothing
     */
    public function testSetChildrenPositionInvalid($expected, $position)
    {
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $data = [
            [
                'id' => '2',
                'type' => 'documents',
                'meta' => [
                    'relation' => compact('position'),
                ],
            ],
        ];
        $this->post('/folders/12/relationships/children', json_encode(compact('data')));
        $this->assertResponseCode(400);

        $result = json_decode((string)$this->_response->getBody(), true);

        static::assertEquals('Invalid data', $result['error']['title']);
        static::assertEquals($expected, $result['error']['detail']);

        $folder = $this->Folders->get(12, ['contain' => ['Children']]);

        $childrenIds = Hash::extract($folder->children, '{n}.id');
        static::assertEquals(['4'], $childrenIds);
    }

    /**
     * Test updating an object's position within its parent using `POST`.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testUpdateChildPosition()
    {
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $data = [
            [
                'id' => '2',
                'type' => 'documents',
                'meta' => [
                    'relation' => [
                        'position' => 'first',
                    ],
                ],
            ],
        ];
        $this->post('/folders/11/relationships/children', json_encode(compact('data')));
        $this->assertResponseCode(200);

        $folder = $this->Folders->get(11, ['contain' => ['Children']]);
        $childrenIds = Hash::extract($folder->children, '{n}.id');

        static::assertEquals([2, 12], $childrenIds);
    }
}
