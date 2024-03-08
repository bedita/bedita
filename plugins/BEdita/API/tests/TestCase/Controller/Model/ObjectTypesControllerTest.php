<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Test\TestCase\Controller\Model;

use BEdita\API\TestSuite\IntegrationTestCase;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use DMS\PHPUnitExtensions\ArraySubset\ArraySubsetAsserts;

/**
 * @coversDefaultClass \BEdita\API\Controller\Model\ObjectTypesController
 */
class ObjectTypesControllerTest extends IntegrationTestCase
{
    use ArraySubsetAsserts;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
    ];

    /**
     * Test index method.
     *
     * @return void
     * @covers ::index()
     * @covers ::initialize()
     * @covers ::prepareInclude()
     */
    public function testIndex()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/model/object_types',
                'first' => 'http://api.example.com/model/object_types',
                'last' => 'http://api.example.com/model/object_types',
                'prev' => null,
                'next' => null,
                'home' => 'http://api.example.com/home',
            ],
            'meta' => [
                'pagination' => [
                    'count' => 11,
                    'page' => 1,
                    'page_count' => 1,
                    'page_items' => 11,
                    'page_size' => 20,
                ],
            ],
            'data' => [
                [
                    'id' => '1',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'object',
                        'name' => 'objects',
                        'description' => null,
                        'table' => 'BEdita/Core.Objects',
                        'associations' => null,
                        'hidden' => null,
                        'is_abstract' => true,
                        'parent_name' => null,
                        'enabled' => true,
                        'translation_rules' => null,
                        'is_translatable' => false,
                    ],
                    'meta' => [
                        'alias' => 'Objects',
                        'relations' => [],
                        'created' => '2017-11-10T09:27:23+00:00',
                        'modified' => '2017-11-10T09:27:23+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/object_types/1',
                    ],
                    'relationships' => [
                        'left_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/1/relationships/left_relations',
                                'related' => 'http://api.example.com/model/object_types/1/left_relations',
                            ],
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/1/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/1/right_relations',
                            ],
                        ],
                        'parent' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/1/relationships/parent',
                                'related' => 'http://api.example.com/model/object_types/1/parent',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '2',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'document',
                        'name' => 'documents',
                        'description' => null,
                        'table' => 'BEdita/Core.Objects',
                        'associations' => ['Categories'],
                        'hidden' => null,
                        'is_abstract' => false,
                        'parent_name' => 'objects',
                        'enabled' => true,
                        'translation_rules' => null,
                        'is_translatable' => true,
                    ],
                    'meta' => [
                        'alias' => 'Documents',
                        'relations' => [
                            'test',
                            'inverse_test',
                        ],
                        'created' => '2017-11-10T09:27:23+00:00',
                        'modified' => '2017-11-10T09:27:23+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/object_types/2',
                    ],
                    'relationships' => [
                        'left_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/2/relationships/left_relations',
                                'related' => 'http://api.example.com/model/object_types/2/left_relations',
                            ],
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/2/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/2/right_relations',
                            ],
                        ],
                        'parent' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/2/relationships/parent',
                                'related' => 'http://api.example.com/model/object_types/2/parent',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '3',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'profile',
                        'name' => 'profiles',
                        'description' => null,
                        'table' => 'BEdita/Core.Profiles',
                        'associations' => ['Tags'],
                        'hidden' => null,
                        'is_abstract' => false,
                        'parent_name' => 'objects',
                        'enabled' => true,
                        'translation_rules' => null,
                        'is_translatable' => true,
                    ],
                    'meta' => [
                        'alias' => 'Profiles',
                        'relations' => [
                            'inverse_test',
                        ],
                        'created' => '2017-11-10T09:27:23+00:00',
                        'modified' => '2017-11-10T09:27:23+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/object_types/3',
                    ],
                    'relationships' => [
                        'left_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/3/relationships/left_relations',
                                'related' => 'http://api.example.com/model/object_types/3/left_relations',
                            ],
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/3/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/3/right_relations',
                            ],
                        ],
                        'parent' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/3/relationships/parent',
                                'related' => 'http://api.example.com/model/object_types/3/parent',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '4',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'user',
                        'name' => 'users',
                        'description' => null,
                        'table' => 'BEdita/Core.Users',
                        'associations' => null,
                        'hidden' => null,
                        'is_abstract' => false,
                        'parent_name' => 'objects',
                        'enabled' => true,
                        'translation_rules' => null,
                        'is_translatable' => true,
                    ],
                    'meta' => [
                        'alias' => 'Users',
                        'relations' => [
                            'another_test',
                        ],
                        'created' => '2017-11-10T09:27:23+00:00',
                        'modified' => '2017-11-10T09:27:23+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/object_types/4',
                    ],
                    'relationships' => [
                        'left_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/4/relationships/left_relations',
                                'related' => 'http://api.example.com/model/object_types/4/left_relations',
                            ],
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/4/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/4/right_relations',
                            ],
                        ],
                        'parent' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/4/relationships/parent',
                                'related' => 'http://api.example.com/model/object_types/4/parent',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '5',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'news_item',
                        'name' => 'news',
                        'description' => null,
                        'table' => 'BEdita/Core.Objects',
                        'associations' => null,
                        'hidden' => ['body'],
                        'is_abstract' => false,
                        'parent_name' => 'objects',
                        'enabled' => false,
                        'translation_rules' => null,
                        'is_translatable' => true,
                    ],
                    'meta' => [
                        'alias' => 'News',
                        'relations' => [],
                        'created' => '2017-11-10T09:27:23+00:00',
                        'modified' => '2017-11-10T09:27:23+00:00',
                        'core_type' => false,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/object_types/5',
                    ],
                    'relationships' => [
                        'left_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/5/relationships/left_relations',
                                'related' => 'http://api.example.com/model/object_types/5/left_relations',
                            ],
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/5/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/5/right_relations',
                            ],
                        ],
                        'parent' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/5/relationships/parent',
                                'related' => 'http://api.example.com/model/object_types/5/parent',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '6',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'location',
                        'name' => 'locations',
                        'description' => null,
                        'table' => 'BEdita/Core.Locations',
                        'associations' => null,
                        'hidden' => null,
                        'is_abstract' => false,
                        'parent_name' => 'objects',
                        'enabled' => true,
                        'translation_rules' => null,
                        'is_translatable' => true,
                    ],
                    'meta' => [
                        'alias' => 'Locations',
                        'relations' => [
                            'inverse_another_test',
                        ],
                        'created' => '2017-11-10T09:27:23+00:00',
                        'modified' => '2017-11-10T09:27:23+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/object_types/6',
                    ],
                    'relationships' => [
                        'left_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/6/relationships/left_relations',
                                'related' => 'http://api.example.com/model/object_types/6/left_relations',
                            ],
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/6/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/6/right_relations',
                            ],
                        ],
                        'parent' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/6/relationships/parent',
                                'related' => 'http://api.example.com/model/object_types/6/parent',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '7',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'event',
                        'name' => 'events',
                        'description' => null,
                        'table' => 'BEdita/Core.Objects',
                        'associations' => ['DateRanges'],
                        'hidden' => null,
                        'is_abstract' => false,
                        'parent_name' => 'objects',
                        'enabled' => true,
                        'translation_rules' => null,
                        'is_translatable' => true,
                    ],
                    'meta' => [
                        'alias' => 'Events',
                        'relations' => [
                           'test_abstract',
                        ],
                        'created' => '2017-11-10T09:27:23+00:00',
                        'modified' => '2017-11-10T09:27:23+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/object_types/7',
                    ],
                    'relationships' => [
                        'left_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/7/relationships/left_relations',
                                'related' => 'http://api.example.com/model/object_types/7/left_relations',
                            ],
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/7/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/7/right_relations',
                            ],
                        ],
                        'parent' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/7/relationships/parent',
                                'related' => 'http://api.example.com/model/object_types/7/parent',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '8',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'media_item',
                        'name' => 'media',
                        'description' => null,
                        'table' => 'BEdita/Core.Media',
                        'associations' => ['Streams'],
                        'hidden' => null,
                        'is_abstract' => true,
                        'parent_name' => 'objects',
                        'enabled' => true,
                        'translation_rules' => null,
                        'is_translatable' => false,
                    ],
                    'meta' => [
                        'alias' => 'Media',
                        'relations' => [
                            'inverse_test_abstract',
                        ],
                        'created' => '2017-11-10T09:27:23+00:00',
                        'modified' => '2017-11-10T09:27:23+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/object_types/8',
                    ],
                    'relationships' => [
                        'left_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/8/relationships/left_relations',
                                'related' => 'http://api.example.com/model/object_types/8/left_relations',
                            ],
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/8/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/8/right_relations',
                            ],
                        ],
                        'parent' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/8/relationships/parent',
                                'related' => 'http://api.example.com/model/object_types/8/parent',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '9',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'file',
                        'name' => 'files',
                        'description' => null,
                        'table' => 'BEdita/Core.Media',
                        'associations' => ['Streams'],
                        'hidden' => null,
                        'is_abstract' => false,
                        'parent_name' => 'media',
                        'enabled' => true,
                        'translation_rules' => [
                            'name' => true,
                        ],
                        'is_translatable' => true,
                    ],
                    'meta' => [
                        'alias' => 'Files',
                        'relations' => [
                            'inverse_test_abstract',
                        ],
                        'created' => '2017-11-10T09:27:23+00:00',
                        'modified' => '2017-11-10T09:27:23+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/object_types/9',
                    ],
                    'relationships' => [
                        'left_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/9/relationships/left_relations',
                                'related' => 'http://api.example.com/model/object_types/9/left_relations',
                            ],
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/9/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/9/right_relations',
                            ],
                        ],
                        'parent' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/9/relationships/parent',
                                'related' => 'http://api.example.com/model/object_types/9/parent',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '10',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'folder',
                        'name' => 'folders',
                        'description' => null,
                        'table' => 'BEdita/Core.Objects',
                        'associations' => null,
                        'hidden' => null,
                        'is_abstract' => false,
                        'parent_name' => 'objects',
                        'enabled' => true,
                        'translation_rules' => null,
                        'is_translatable' => true,
                    ],
                    'meta' => [
                        'alias' => 'Folders',
                        'relations' => [],
                        'created' => '2018-01-29T08:47:29+00:00',
                        'modified' => '2018-01-29T08:47:29+00:00',
                        'core_type' => true,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/object_types/10',
                    ],
                    'relationships' => [
                        'left_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/10/relationships/left_relations',
                                'related' => 'http://api.example.com/model/object_types/10/left_relations',
                            ],
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/10/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/10/right_relations',
                            ],
                        ],
                        'parent' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/10/relationships/parent',
                                'related' => 'http://api.example.com/model/object_types/10/parent',
                            ],
                        ],
                    ],
                ],
                [
                    'id' => '11',
                    'type' => 'object_types',
                    'attributes' => [
                        'singular' => 'image',
                        'name' => 'images',
                        'description' => null,
                        'table' => 'BEdita/Core.Media',
                        'associations' => ['Streams'],
                        'hidden' => null,
                        'is_abstract' => false,
                        'parent_name' => 'media',
                        'enabled' => true,
                        'translation_rules' => null,
                        'is_translatable' => true,
                    ],
                    'meta' => [
                        'alias' => 'Images',
                        'relations' => [
                            'inverse_test_abstract',
                        ],
                        'created' => '2024-03-08T11:21:51+00:00',
                        'modified' => '2024-03-08T11:21:51+00:00',
                        'core_type' => false,
                    ],
                    'links' => [
                        'self' => 'http://api.example.com/model/object_types/11',
                    ],
                    'relationships' => [
                        'left_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/11/relationships/left_relations',
                                'related' => 'http://api.example.com/model/object_types/11/left_relations',
                            ],
                        ],
                        'right_relations' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/11/relationships/right_relations',
                                'related' => 'http://api.example.com/model/object_types/11/right_relations',
                            ],
                        ],
                        'parent' => [
                            'links' => [
                                'self' => 'http://api.example.com/model/object_types/11/relationships/parent',
                                'related' => 'http://api.example.com/model/object_types/11/parent',
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/model/object_types');
        $result = json_decode((string)$this->_response->getBody(), true);

        /*
         * @todo To remove when fquffio :) resolves the inconsistency response.
         *       According with other endpoint responses "included" and "data" of "relationships"
         *       should be present only when the query string "include" is present
         */
        $result = Hash::remove($result, 'included');
        $result = Hash::remove($result, 'data.{n}.relationships.left_relations.data');
        $result = Hash::remove($result, 'data.{n}.relationships.right_relations.data');

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test index method.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEmpty()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/model/object_types',
                'first' => 'http://api.example.com/model/object_types',
                'last' => 'http://api.example.com/model/object_types',
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

        $this->fetchTable('Objects')
            ->getConnection()
            ->disableConstraints(function () {
                $this->fetchTable('Properties')->deleteAll([]);
                $this->fetchTable('Translations')->deleteAll([]);
                $this->fetchTable('Annotations')->deleteAll([]);
                $this->fetchTable('Objects')->deleteAll([]);
                $this->fetchTable('ObjectTypes')->deleteAll([]);
            });

        $this->configRequestHeaders();
        $this->get('/model/object_types');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test view method.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testSingle()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/model/object_types/2',
                'home' => 'http://api.example.com/home',
            ],
            'data' => [
                'id' => '2',
                'type' => 'object_types',
                'attributes' => [
                    'singular' => 'document',
                    'name' => 'documents',
                    'description' => null,
                    'table' => 'BEdita/Core.Objects',
                    'associations' => ['Categories'],
                    'hidden' => null,
                    'is_abstract' => false,
                    'parent_name' => 'objects',
                    'enabled' => true,
                    'translation_rules' => null,
                    'is_translatable' => true,
                ],
                'meta' => [
                    'alias' => 'Documents',
                    'relations' => [
                        'test',
                        'inverse_test',
                    ],
                    'created' => '2017-11-10T09:27:23+00:00',
                    'modified' => '2017-11-10T09:27:23+00:00',
                    'core_type' => true,
                ],
                'relationships' => [
                    'left_relations' => [
                        'links' => [
                            'self' => 'http://api.example.com/model/object_types/2/relationships/left_relations',
                            'related' => 'http://api.example.com/model/object_types/2/left_relations',
                        ],
                    ],
                    'right_relations' => [
                        'links' => [
                            'self' => 'http://api.example.com/model/object_types/2/relationships/right_relations',
                            'related' => 'http://api.example.com/model/object_types/2/right_relations',
                        ],
                    ],
                    'parent' => [
                        'links' => [
                            'self' => 'http://api.example.com/model/object_types/2/relationships/parent',
                            'related' => 'http://api.example.com/model/object_types/2/parent',
                        ],
                    ],
                ],
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/model/object_types/2');
        $result = json_decode((string)$this->_response->getBody(), true);

        /*
         * @todo To remove when fquffio :) resolves the inconsistency response.
         *       According with other endpoint responses "included" and "data" of "relationships"
         *       should be present only when the query string "include" is present
         */
        $result = Hash::remove($result, 'included');
        $result = Hash::remove($result, 'data.relationships.left_relations.data');
        $result = Hash::remove($result, 'data.relationships.right_relations.data');

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($expected, $result);
    }

    /**
     * Test view method.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testMissing()
    {
        $expected = [
            'links' => [
                'self' => 'http://api.example.com/model/object_types/99',
                'home' => 'http://api.example.com/home',
            ],
            'error' => [
                'status' => '404',
            ],
        ];

        $this->configRequestHeaders();
        $this->get('/model/object_types/99');
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
     * @covers ::index()
     * @covers ::initialize()
     * @covers ::resourceUrl()
     */
    public function testAdd()
    {
        $data = [
            'type' => 'object_types',
            'attributes' => [
                'singular' => 'my_object_type',
                'name' => 'my_object_types',
                'alias' => 'My Object Type',
                'description' => null,
                'table' => 'BEdita/Core.Objects',
            ],
        ];

        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/model/object_types', json_encode(compact('data')));

        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');
        $this->assertHeader('Location', 'http://api.example.com/model/object_types/12');
        $this->assertTrue(TableRegistry::getTableLocator()->get('ObjectTypes')->exists(['singular' => 'my_object_type']));
    }

    /**
     * Test add method with invalid data.
     *
     * @return void
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testAddMissing()
    {
        // missing mandatory `name`, `singular`
        $data = [
            'type' => 'object_types',
            'attributes' => [
                'description' => 'Anonymous object_type.',
            ],
        ];

        $count = TableRegistry::getTableLocator()->get('ObjectTypes')->find()->count();
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/model/object_types', json_encode(compact('data')));
        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($count, TableRegistry::getTableLocator()->get('ObjectTypes')->find()->count());
    }

    /**
     * Test with reserve words.
     *
     * @return void
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testReserved()
    {
        // add reserved word failure
        $data = [
            'type' => 'object_types',
            'attributes' => [
                'name' => 'applications',
                'singular' => 'application',
            ],
        ];

        $count = TableRegistry::getTableLocator()->get('ObjectTypes')->find()->count();
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/model/object_types', json_encode(compact('data')));
        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($count, TableRegistry::getTableLocator()->get('ObjectTypes')->find()->count());
    }

    /**
     * Test failure with same `name` and `singular`.
     *
     * @return void
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testNameSingular()
    {
        // add same `name`, `singular`
        $data = [
            'type' => 'object_types',
            'attributes' => [
                'name' => 'gustavo',
                'singular' => 'gustavo',
            ],
        ];

        $count = TableRegistry::getTableLocator()->get('ObjectTypes')->find()->count();
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post('/model/object_types', json_encode(compact('data')));
        $this->assertResponseCode(400);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals($count, TableRegistry::getTableLocator()->get('ObjectTypes')->find()->count());
    }

    /**
     * Test edit method.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEdit()
    {
        $data = [
            'id' => '2',
            'type' => 'object_types',
            'attributes' => [
                'singular' => 'document_new',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/model/object_types/2', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        $ObjectTypes = TableRegistry::getTableLocator()->get('ObjectTypes');
        $entity = $ObjectTypes->get(2);
        $this->assertEquals('document_new', $entity->get('singular'));

        // restore previous values
        $entity = $ObjectTypes->patchEntity($entity, ['singular' => 'document']);
        $success = $ObjectTypes->save($entity);
        $this->assertTrue((bool)$success);
    }

    /**
     * Test edit method with no change data.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEditNoChange()
    {
        $data = [
            'id' => '2',
            'type' => 'object_types',
            'attributes' => [
                'is_abstract' => false,
                'parent_name' => 'objects',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/model/object_types/2', json_encode(compact('data')));

        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
    }

    /**
     * Test edit method with ID conflict.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testEditConflict()
    {
        $data = [
            'id' => '2',
            'type' => 'object_types',
            'attributes' => [
                'singular' => 'profile new',
            ],
        ];

        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch('/model/object_types/3', json_encode(compact('data')));

        $this->assertResponseCode(409);
        $this->assertContentType('application/vnd.api+json');
        $this->assertEquals('document', TableRegistry::getTableLocator()->get('ObjectTypes')->get(2)->get('singular'));
        $this->assertEquals('profile', TableRegistry::getTableLocator()->get('ObjectTypes')->get(3)->get('singular'));
    }

    /**
     * Test delete method.
     *
     * @return void
     * @covers ::resource()
     * @covers ::initialize()
     */
    public function testDelete()
    {
        $this->configRequestHeaders('DELETE', $this->getUserAuthHeader());
        $this->delete('/model/object_types/5');

        $this->assertResponseCode(204);
        $this->assertResponseEmpty();
        $this->assertFalse(TableRegistry::getTableLocator()->get('ObjectTypes')->exists(['id' => 5]));
    }
}
