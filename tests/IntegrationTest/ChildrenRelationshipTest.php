<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
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
 * Test operations on `children` relationships.
 *
 * @coversNothing
 */
class ChildrenRelationshipTest extends IntegrationTestCase
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
     * Test that moving children already on tree is consistent.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testMoveChildrenToCustomPosition()
    {
        $authHeader = $this->getUserAuthHeader();

        $parentFolderId = 13;

        $objectsData = [
            [
                'type' => 'documents',
                'attributes' => [
                    'title' => 'Doc one here',
                    'description' => 'Document one',
                ],
            ],
            [
                'type' => 'documents',
                'attributes' => [
                    'title' => 'Doc two here',
                    'description' => 'Document two',
                ],
            ],
            [
                'type' => 'documents',
                'attributes' => [
                    'title' => 'Doc three here',
                    'description' => 'Document three',
                ],
            ],
        ];

        $childrenData = [];

        // create documents
        foreach ($objectsData as &$data) {
            $this->configRequestHeaders('POST', $authHeader);
            $this->post('/documents', json_encode(compact('data')));
            $this->assertResponseCode(201);
            $this->assertContentType('application/vnd.api+json');
            $data['id'] = $this->lastObjectId();

            $childrenData[] = [
                'type' => $data['type'],
                'id' => $data['id'],
            ];
        }
        unset($data);

        // put on tree
        $relationshipsEndpoint = sprintf('/folders/%s/relationships/children', $parentFolderId);
        $this->configRequestHeaders('POST', $authHeader);
        $this->post($relationshipsEndpoint, json_encode(['data' => $childrenData]));
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        // check current positions
        $childrenIds = $this->getChildrenIds($parentFolderId);
        $expected = Hash::extract($objectsData, '{n}.id');
        static::assertEquals($expected, $childrenIds);

        // move positions
        $newPositions = [3, 1, 2];
        $expected = [];
        $childrenData = [];
        foreach ($objectsData as $key => $data) {
            $position = $newPositions[$key];
            $childrenData[] = [
                'type' => $data['type'],
                'id' => $data['id'],
                'meta' => [
                    'relation' => [
                        'position' => $position,
                    ],
                ],
            ];

            $expected[$position - 1] = $data['id'];
        }

        ksort($expected);

        $relationshipsEndpoint = sprintf('/folders/%s/relationships/children', $parentFolderId);
        $this->configRequestHeaders('PATCH', $authHeader);
        $this->patch($relationshipsEndpoint, json_encode(['data' => $childrenData]));
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $childrenIds = $this->getChildrenIds($parentFolderId);
        static::assertEquals($expected, $childrenIds);
    }

    /**
     * Given a parent id return a list of ordered children
     *
     * @param int $parentId The parent id.
     * @return array
     */
    protected function getChildrenIds($parentId)
    {
        return $this->Trees->find('list', ['valueField' => 'object_id'])
            ->where(['parent_id' => $parentId])
            ->order(['tree_left' => 'ASC'])
            ->toList();
    }

    /**
     * Test `meta.relation` content in GET `children` response
     *
     * @return void
     *
     * @coversNothing
     */
    public function testChildrenMeta()
    {
        $this->configRequestHeaders();
        $this->get(sprintf('/folders/12/children'));
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $result = json_decode((string)$this->_response->getBody(), true);
        static::assertEquals(1, count($result['data']));

        $expected = [
            'depth_level' => 2,
            'menu' => true,
            'canonical' => true,
        ];
        static::assertEquals($expected, Hash::get($result, 'data.0.meta.relation'));
    }
}
