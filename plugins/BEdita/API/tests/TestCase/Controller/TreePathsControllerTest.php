<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Test\TestCase\Controller;

use BEdita\API\Controller\TreePathsController;
use BEdita\API\Test\TestConstants;
use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see \BEdita\API\Controller\TreePathsController} Test Case
 */
#[CoversClass(TreePathsController::class)]
class TreePathsControllerTest extends IntegrationTestCase
{
    /**
     * Test `index` with a valid slug.
     *
     * @return void
     */
    public function testIndexValidSlugPath(): void
    {
        $this->configRequestHeaders();
        $this->get('/tree_paths/root-folder-11/sub-folder-12');

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $response = json_decode((string)$this->_response->getBody(), true);

        $expected = [
            'data' => [
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
                    'children_order' => null,
                    'menu' => true,
                ],
                'meta' => [
                    'locked' => false,
                    'path' => '/11/12',
                    'slug_path' => [
                        [
                            'id' => 11,
                            'menu' => false,
                            'params' => null,
                            'slug' => 'root-folder-11',
                        ],
                        [
                            'id' => 12,
                            'menu' => true,
                            'params' => null,
                            'slug' => 'sub-folder-12',
                        ],
                    ],
                    'published' => null,
                    'created_by' => 1,
                    'modified_by' => 1,
                    'created' => '2018-01-31T07:09:23+00:00',
                    'modified' => '2018-01-31T08:30:00+00:00',
                ],
                'relationships' => [
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
                    'parent' => [
                        'links' => [
                            'related' => 'http://api.example.com/folders/12/parent',
                            'self' => 'http://api.example.com/folders/12/relationships/parent',
                        ],
                    ],
                ],
            ],
            'links' => [
                'self' => 'http://api.example.com/tree_paths',
                'home' => 'http://api.example.com/home',
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

        static::assertEquals($expected, $response);
    }

    /**
     * Test `index` with an invalid slug.
     *
     * @return void
     */
    public function testIndexInvalidSlugPath(): void
    {
        $this->configRequestHeaders();
        $this->get('/tree_paths/pippo/pluto');

        $this->assertResponseCode(404);
        $this->assertContentType('application/vnd.api+json');

        $response = json_decode((string)$this->_response->getBody(), true);

        static::assertEquals($response['error']['title'], 'Invalid path');
    }

    /**
     * Verify that two object with the same slug are reachable with different paths.
     *
     * @return void
     */
    public function testObjectsWithSameSlugDifferentPaths(): void
    {
        $treesTable = TableRegistry::getTableLocator()->get('Trees');

        // Create a new object with the same slug but different paths
        $newTreeNode = $treesTable->newEntity([
            'object_id' => 14,
            'parent_id' => 13, // Another-root-folder-13
            'root_id' => 13,
            'parent_node_id' => 5,
            'tree_left' => 10,
            'tree_right' => 11,
            'depth_level' => 1,
            'menu' => 1,
            'canonical' => 1,
            'slug' => 'gustavo-supporto-profile-4',
        ]);

        $treesTable->saveOrFail($newTreeNode);

        $this->configRequestHeaders();
        // First object in "another-root-folder-13/gustavo-supporto-profile-4"
        $this->get('/tree_paths/another-root-folder-13/gustavo-supporto-profile-4');
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $response1 = json_decode((string)$this->_response->getBody(), true);

        $this->configRequestHeaders();
        // Second object in "root-folder-11/sub-folder-12/gustavo-supporto-profile-4"
        $this->get('/tree_paths/root-folder-11/sub-folder-12/gustavo-supporto-profile-4');
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $response2 = json_decode((string)$this->_response->getBody(), true);

        static::assertNotEquals(
            $response1['data']['id'],
            $response2['data']['id'],
            'Object IDS must be different even if they have the same slug',
        );

        static::assertNotEquals(
            $response1['data']['meta']['extra']['slug_path'],
            $response2['data']['meta']['extra']['slug_path'],
            'Paths must me different for the same slug.',
        );
    }
}
