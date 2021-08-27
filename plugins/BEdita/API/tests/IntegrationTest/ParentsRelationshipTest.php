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
namespace BEdita\API\Test\IntegrationTest;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Test operations on `parents` relationships.
 *
 * @coversNothing
 */
class ParentsRelationshipTest extends IntegrationTestCase
{

    /**
     * Keep the TreesTable instance
     *
     * @var \BEdita\Core\Model\Table\TreesTable
     */
    protected $Trees = null;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Trees = TableRegistry::getTableLocator()->get('Trees');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        $this->Trees = null;
    }

    /**
     * Test setting a parent to an object.
     *
     * @return void
     */
    public function testParents()
    {
        $authHeader = $this->getUserAuthHeader();

        // create documents
        $data = [
            'type' => 'documents',
            'attributes' => [
                'title' => 'Doc here',
                'description' => 'Please put me on tree',
            ],
        ];
        $this->configRequestHeaders('POST', $authHeader);
        $this->post('/documents', json_encode(compact('data')));
        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $docId = $this->lastObjectId();
        static::assertEquals(0, $this->countTrees($docId));

        // POST: add 3 folders as parents relationships
        $foldersTable = TableRegistry::getTableLocator()->get('Folders');
        $folders = $foldersTable
            ->find('list', [
                'keyField' => 'uname',
                'valueField' => 'id'
            ])
            ->where(['object_type_id' => $foldersTable->objectType()->id])
            ->order(['id' => 'ASC'])
            ->limit(3)
            ->toArray();

        $data = [];
        foreach ($folders as $folderId) {
            $data[] = [
                'type' => 'folders',
                'id' => "$folderId",
            ];
        }

        $relationshipsEndpoint = sprintf('/documents/%s/relationships/parents', $docId);
        $relatedEndpoint = sprintf('/documents/%s/parents', $docId);

        $this->configRequestHeaders('POST', $authHeader);
        $this->post($relationshipsEndpoint, json_encode(compact('data')));
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals(3, $this->countTrees($docId));

        // GET: get parents of doc created
        $this->configRequestHeaders();
        $this->get($relatedEndpoint);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $body = json_decode((string)$this->_response->getBody(), true);
        static::assertCount(3, $body['data']);
        $parentIds = Hash::extract($body['data'], '{n}.id');
        sort($parentIds);
        static::assertEquals(array_values($folders), $parentIds);

        // PATCH: patch parents relationships removing one item
        $this->configRequestHeaders('PATCH', $authHeader);
        array_pop($data);
        $this->patch($relationshipsEndpoint, json_encode(compact('data')));
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals(2, $this->countTrees($docId));

        $this->configRequestHeaders();
        $this->get($relatedEndpoint);
        $body = json_decode((string)$this->_response->getBody(), true);
        static::assertCount(2, $body['data']);
        $parentIds = Hash::extract($body['data'], '{n}.id');
        sort($parentIds);
        static::assertEquals(Hash::extract($data, '{n}.id'), $parentIds);

        // DELETE: delete all remaining parents relationships
        $this->configRequestHeaders('DELETE', $authHeader);
        // Cannot use `IntegrationTestCase::delete()`, as it does not allow sending payload with the request.
        $this->_sendRequest($relationshipsEndpoint, 'DELETE', json_encode(compact('data')));
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals(0, $this->countTrees($docId));

        $this->configRequestHeaders();
        $this->get($relatedEndpoint);
        $body = json_decode((string)$this->_response->getBody(), true);
        static::assertCount(0, $body['data']);
    }

    /**
     * Test not valid type for parents relationship
     *
     * @return void
     */
    public function testNotAllowedResourceType()
    {
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $data = [
            [
                'type' => 'profiles',
                'id' => '4', // <= he is Gustavo!
            ],
        ];
        $this->post('/documents/2/relationships/parents', json_encode(compact('data')));
        $this->assertResponseCode(409);
        $body = json_decode((string)$this->_response->getBody(), true);
        static::assertEquals('Unsupported resource type', $body['error']['title']);
    }

    /**
     * Return the count of an object on tree
     *
     * @param int $objectId The object id to count
     * @return int
     */
    private function countTrees($objectId)
    {
        return $this->Trees
            ->find()
            ->where(['object_id' => $objectId])
            ->count();
    }

    /**
     * Test deleted objects as `parent`
     *
     * @return void
     *
     * @coversNothing
     */
    public function testDeletedParent()
    {
        // a deleted folder must not be listed in `parents`
        $foldersTable = TableRegistry::getTableLocator()->get('Folders');
        $folder = $foldersTable->get(12);
        $folder->deleted = true;
        $foldersTable->saveOrFail($folder);

        $this->configRequestHeaders();
        $this->get('/profiles/4/parents');
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $result = json_decode((string)$this->_response->getBody(), true);
        $ids = Hash::extract($result, 'data.{n}.id');
        static::assertEmpty($ids);
    }

    /**
     * Test `?include=parents` query string
     *
     * @return void
     */
    public function testIncludeParents()
    {
        $this->configRequestHeaders('GET');
        $this->get('/documents/2?include=parents');
        $this->assertResponseCode(200);
        $result = json_decode((string)$this->_response->getBody(), true);

        $includedIds = Hash::extract($result, 'included.{n}.id');
        static::assertEquals(['11'], $includedIds);
    }

    /**
     * Test setting an object's parent with position.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testSetParentPosition()
    {
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $data = [
            [
                'id' => '12',
                'type' => 'folders',
                'meta' => [
                    'relation' => [
                        'position' => 1,
                    ],
                ],
            ],
        ];
        $this->post('/documents/2/relationships/parents', json_encode(compact('data')));
        $this->assertResponseCode(200);

        $childrenIds = $this->Trees->find('list', ['valueField' => 'object_id'])
            ->where(['parent_id' => 12])
            ->order(['tree_left' => 'ASC'])
            ->toList();

        static::assertEquals([2, 4], $childrenIds);
    }

    /**
     * Data provider for `testSetParentPositionInvalid` test case.
     *
     * @return array
     */
    public function setParentPositionInvalidProvider()
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
     * Test setting an object's parent with an invalid position.
     *
     * @param string $expected Expected error.
     * @param int|string $position Desired position.
     * @return void
     *
     * @dataProvider setParentPositionInvalidProvider()
     * @coversNothing
     */
    public function testSetParentPositionInvalid($expected, $position)
    {
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $data = [
            [
                'id' => '12',
                'type' => 'folders',
                'meta' => [
                    'relation' => compact('position'),
                ],
            ],
        ];
        $this->post('/documents/2/relationships/parents', json_encode(compact('data')));
        $this->assertResponseCode(400);

        $result = json_decode((string)$this->_response->getBody(), true);

        static::assertEquals('Invalid data', $result['error']['title']);
        static::assertEquals($expected, $result['error']['detail']);

        $childrenIds = $this->Trees->find('list', ['valueField' => 'object_id'])
            ->where(['parent_id' => 12])
            ->order(['tree_left' => 'ASC'])
            ->toList();

        static::assertEquals([4], $childrenIds);
    }

    /**
     * Test `meta.relation` content in GET `parents` and GET `parent` response
     *
     * @return void
     *
     * @coversNothing
     */
    public function testParentsMeta()
    {
        $this->configRequestHeaders();
        $this->get('/profiles/4/parents');
        $this->assertResponseCode(200);
        $result = json_decode((string)$this->_response->getBody(), true);
        static::assertEquals(1, count($result['data']));

        $expected = [
            'depth_level' => 2,
            'menu' => true,
            'canonical' => true,
        ];
        static::assertEquals($expected, Hash::get($result, 'data.0.meta.relation'));

        $this->configRequestHeaders();
        $this->get('/folders/12/parent');
        $this->assertResponseCode(200);
        $result = json_decode((string)$this->_response->getBody(), true);
        static::assertEquals('11', $result['data']['id']);

        $expected = [
            'depth_level' => 1,
            'menu' => true,
            'canonical' => true,
        ];
        static::assertEquals($expected, Hash::get($result, 'data.meta.relation'));
    }
}
