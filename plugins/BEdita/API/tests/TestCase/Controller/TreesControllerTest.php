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

namespace BEdita\API\Test\TestCase\Controller;

use BEdita\API\TestSuite\IntegrationTestCase;
use BEdita\API\Test\TestConstants;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * @coversDefaultClass \BEdita\API\Controller\TreesController
 */
class TreesControllerTest extends IntegrationTestCase
{
    /**
     * Test `index` method.
     *
     * @return void
     * @covers ::index()
     * @covers ::initialize()
     * @covers ::loadObject()
     * @covers ::getContain()
     */
    public function testIndex(): void
    {
        $this->configRequestHeaders();
        $this->get('/trees/root-folder/sub-folder');

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
                    'menu' => true,
                    'children_order' => null,
                ],
                'meta' => [
                    'locked' => false,
                    'created' => '2018-01-31T07:09:23+00:00',
                    'modified' => '2018-01-31T08:30:00+00:00',
                    'published' => null,
                    'created_by' => 1,
                    'modified_by' => 1,
                    'path' => '/11/12',
                    'extra' => [
                        'uname_path' => '/root-folder/sub-folder',
                    ],
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
            'links' => [
                'self' => 'http://api.example.com/trees',
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
     * Data provider for `testTrees` method
     *
     * @return void
     */
    public function treesProvider(): array
    {
        $error = [
            'status' => '404',
            'title' => 'Invalid path',
        ];

        return [
            'simple id' => [
                '/root-folder/title-one',
                '/11/2',
            ],
            'simple uname' => [
                '/root-folder/sub-folder/gustavo-supporto',
                '/root-folder/sub-folder/gustavo-supporto',
            ],
            'simple folder' => [
                '/root-folder/sub-folder',
                '/root-folder/12',
            ],
            'invalid 1' => [
                compact('error'),
                '/4',
            ],
            'invalid 2' => [
                compact('error'),
                '/12',
            ],
            'invalid 3' => [
                compact('error'),
                '/sub-folder/gustavo-supporto',
            ],
            'invalid 4' => [
                compact('error'),
                '/12/4',
            ],
            'invalid 5' => [
                compact('error'),
                '/11/4',
            ],
            'root' => [
                '/root-folder',
                '/11',
            ],
        ];
    }

    /**
     * Test trees path check methods.
     *
     * @return void
     * @dataProvider treesProvider()
     * @covers ::checkPath()
     * @covers ::pathDetails()
     * @covers ::objectDetails()
     * @covers ::parents()
     * @covers ::loadTreesNode()
     */
    public function testTrees($expected, $path): void
    {
        $this->configRequestHeaders();
        $this->get(sprintf('/trees%s', $path));

        $status = (int)Hash::get((array)$expected, 'error.status', 200);
        $this->assertResponseCode($status);
        $this->assertContentType('application/vnd.api+json');

        $response = json_decode((string)$this->_response->getBody(), true);

        if (is_array($expected)) {
            unset($response['error']['meta']);
            unset($response['links']);
            static::assertEquals($expected, $response);
        } else {
            $unamePath = Hash::get($response, 'data.meta.extra.uname_path');
            static::assertEquals($expected, $unamePath);
        }
    }

    /**
     * Test `include` query string.
     *
     * @return void
     * @covers ::getContain()
     * @covers ::findAssociation()
     */
    public function testInclude(): void
    {
        $this->configRequestHeaders();
        $this->get('/trees/root-folder/title-one?include=test');

        $this->assertResponseCode(200);

        $response = json_decode((string)$this->_response->getBody(), true);

        $included = Hash::get($response, 'included');
        static::assertNotEmpty($included);
        static::assertEquals(2, count($included));

        $relData = Hash::get($response, 'data.relationships.test.data');
        static::assertNotEmpty($relData);
        static::assertEquals(2, count($relData));
    }

    /**
     * Test `include` failure.
     *
     * @return void
     * @covers ::findAssociation()
     */
    public function testIncludeFail(): void
    {
        $this->configRequestHeaders();
        $this->get('/trees/root-folder?include=has_gustavo');

        $this->assertResponseCode(400);

        $response = json_decode((string)$this->_response->getBody(), true);
        unset($response['error']['meta']);
        unset($response['links']);

        $error = [
            'status' => '400',
            'title' => 'Invalid "include" query parameter (Relationship "has_gustavo" does not exist)',
        ];
        static::assertEquals(compact('error'), $response);
    }

    /**
     * Test failure when parent folder is in trashcan.
     *
     * @return void
     * @covers ::pathDetails()
     */
    public function testParentDeleted(): void
    {
        $table = TableRegistry::getTableLocator()->get('Folders');
        $folder = $table->get(12);
        $folder->set('deleted', true);
        $table->saveOrFail($folder);

        $this->configRequestHeaders();
        $this->get('/trees/root-folder/sub-folder/gustavo-supporto');
        $this->assertResponseCode(404);

        $response = json_decode((string)$this->_response->getBody(), true);
        unset($response['error']['meta'], $response['links']);
        $error = [
            'status' => '404',
            'title' => 'Invalid path',
        ];
        static::assertEquals(compact('error'), $response);
    }

    /**
     * Test failure when parent folder has unavailable status.
     *
     * @return void
     * @covers ::pathDetails()
     */
    public function testParentStatus(): void
    {
        $table = TableRegistry::getTableLocator()->get('Folders');
        $folder = $table->get(12);
        $folder->set('status', 'draft');
        $table->saveOrFail($folder);

        Configure::write('Status.level', 'on');

        $this->configRequestHeaders();
        $this->get('/trees/root-folder/sub-folder/gustavo-supporto');
        $this->assertResponseCode(404);

        $response = json_decode((string)$this->_response->getBody(), true);
        unset($response['error']['meta'], $response['links']);
        $error = [
            'status' => '404',
            'title' => 'Invalid path',
        ];
        static::assertEquals(compact('error'), $response);
    }

    /**
     * Test check path failure on folders.
     *
     * @return void
     * @covers ::checkPath()
     */
    public function testcheckPathFolderFail(): void
    {
        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch(
            '/folders/13/relationships/parent',
            json_encode([
                'data' => [
                    'type' => 'folders',
                    'id' => '12',
                ],
            ])
        );
        $this->assertResponseCode(200);

        $this->configRequestHeaders();
        $this->get('/trees/sub-folder/another-root-folder');
        $this->assertResponseCode(404);

        $response = json_decode((string)$this->_response->getBody(), true);
        unset($response['error']['meta'], $response['links']);
        $error = [
            'status' => '404',
            'title' => 'Invalid path',
        ];
        static::assertEquals(compact('error'), $response);
    }
}
