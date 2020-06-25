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

use BEdita\API\Test\TestConstants;
use BEdita\API\TestSuite\IntegrationTestCase;
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
     *
     * @covers ::index()
     * @covers ::initialize()
     * @covers ::loadObject()
     */
    public function testIndex()
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
                        'uname_path' => '/root-folder/sub-folder'
                    ]
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
            ]
    ];

        static::assertEquals($expected, $response);
    }

    /**
     * Data provider for `testTrees` method
     *
     * @return void
     */
    public function treesProvider()
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
            'invalid 4' => [
                compact('error'),
                '/11/4',
            ],
        ];
    }

    /**
     * Test trees path check methods.
     *
     * @return void
     *
     * @dataProvider treesProvider()
     * @covers ::checkPath()
     * @covers ::pathDetails()
     * @covers ::objectUname()
     * @covers ::parents()
     */
    public function testTrees($expected, $path)
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
}
