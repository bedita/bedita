<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Utility;

use BEdita\Core\Utility\ProjectModel;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Utility\ProjectModel} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Utility\ProjectModel
 */
class ProjectModelTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
    ];

    /**
     * Project model data
     *
     * @var array
     */
    public const PROJECT_MODEL = [
        'applications' => [
            [
                'name' => 'First app',
                'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat.',
                'enabled' => true,
            ],
            [
                'name' => 'Disabled app',
                'description' => 'This app has been disabled',
                'enabled' => false,
            ],
        ],
        'roles' => [
            [
                'name' => 'first role',
                'description' => 'this is the very first role',
            ],
            [
                'name' => 'second role',
                'description' => 'this is a second role',
            ],
        ],
        'property_types' => [
            [
                'name' => 'unused property type',
                'params' => [
                    'type' => 'object',
                    'properties' => [
                        'gustavo' => [
                            'const' => 'supporto',
                        ],
                    ],
                    'required' => ['gustavo'],
                ],
            ],
        ],
        'object_types' => [
            [
                'name' => 'objects',
                'is_abstract' => true,
                'singular' => 'object',
                'description' => null,
                'associations' => null,
                'hidden' => null,
                'enabled' => true,
                'table' => 'BEdita/Core.Objects',
                'parent_name' => null,
                'translation_rules' => null,
                'is_translatable' => false,
            ],
            [
                'name' => 'documents',
                'is_abstract' => false,
                'singular' => 'document',
                'description' => null,
                'associations' => ['Categories'],
                'hidden' => null,
                'enabled' => true,
                'table' => 'BEdita/Core.Objects',
                'parent_name' => 'objects',
                'translation_rules' => null,
                'is_translatable' => true,
            ],
            [
                'name' => 'profiles',
                'is_abstract' => false,
                'singular' => 'profile',
                'description' => null,
                'associations' => ['Tags'],
                'hidden' => null,
                'enabled' => true,
                'table' => 'BEdita/Core.Profiles',
                'parent_name' => 'objects',
                'translation_rules' => null,
                'is_translatable' => true,
            ],
            [
                'name' => 'users',
                'is_abstract' => false,
                'singular' => 'user',
                'description' => null,
                'associations' => null,
                'hidden' => null,
                'enabled' => true,
                'table' => 'BEdita/Core.Users',
                'parent_name' => 'objects',
                'translation_rules' => null,
                'is_translatable' => true,
            ],
            [
                'name' => 'news',
                'is_abstract' => false,
                'singular' => 'news_item',
                'description' => null,
                'associations' => null,
                'hidden' => ['body'],
                'enabled' => false,
                'table' => 'BEdita/Core.Objects',
                'parent_name' => 'objects',
                'translation_rules' => null,
                'is_translatable' => true,
            ],
            [
                'name' => 'locations',
                'is_abstract' => false,
                'singular' => 'location',
                'description' => null,
                'associations' => null,
                'hidden' => null,
                'enabled' => true,
                'table' => 'BEdita/Core.Locations',
                'parent_name' => 'objects',
                'translation_rules' => null,
                'is_translatable' => true,
            ],
            [
                'name' => 'events',
                'is_abstract' => false,
                'singular' => 'event',
                'description' => null,
                'associations' => ['DateRanges'],
                'hidden' => null,
                'enabled' => true,
                'table' => 'BEdita/Core.Objects',
                'parent_name' => 'objects',
                'translation_rules' => null,
                'is_translatable' => true,
            ],
            [
                'name' => 'media',
                'is_abstract' => true,
                'singular' => 'media_item',
                'description' => null,
                'associations' => ['Streams'],
                'hidden' => null,
                'enabled' => true,
                'table' => 'BEdita/Core.Media',
                'parent_name' => 'objects',
                'translation_rules' => null,
                'is_translatable' => false,
            ],
            [
                'name' => 'files',
                'is_abstract' => false,
                'singular' => 'file',
                'description' => null,
                'associations' => ['Streams'],
                'hidden' => null,
                'enabled' => true,
                'table' => 'BEdita/Core.Media',
                'parent_name' => 'media',
                'translation_rules' => [
                    'name' => true,
                ],
                'is_translatable' => true,
            ],
            [
                'name' => 'folders',
                'is_abstract' => false,
                'singular' => 'folder',
                'description' => null,
                'associations' => null,
                'hidden' => null,
                'enabled' => true,
                'table' => 'BEdita/Core.Objects',
                'parent_name' => 'objects',
                'translation_rules' => null,
                'is_translatable' => true,
            ],
        ],
        'relations' => [
            [
                'name' => 'test',
                'label' => 'Test relation',
                'inverse_name' => 'inverse_test',
                'inverse_label' => 'Inverse test relation',
                'description' => 'Sample description.',
                'params' => null,
                'right_object_types' => ['documents', 'profiles'],
                'left_object_types' => ['documents'],
            ],
            [
                'name' => 'another_test',
                'label' => 'Another test relation',
                'inverse_name' => 'inverse_another_test',
                'inverse_label' => 'Another inverse test relation',
                'description' => 'Sample description /2.',
                'params' => [
                    'type' => 'object',
                    'properties' => [
                        'name' => [
                            'type' => 'string',
                        ],
                        'age' => [
                            'type' => 'integer',
                            'minimum' => 0,
                        ],
                    ],
                    'required' => ['name'],
                ],
                'right_object_types' => ['locations'],
                'left_object_types' => ['users'],
            ],
            [
                'name' => 'test_abstract',
                'label' => 'Test relation involving abstract types',
                'inverse_name' => 'inverse_test_abstract',
                'inverse_label' => 'Inverse test relation involving abstract types',
                'description' => 'Sample description.',
                'params' => null,
                'right_object_types' => ['media'],
                'left_object_types' => ['events'],
            ],
        ],
        'properties' => [
            [
                'name' => 'another_title',
                'description' => null,
                'is_nullable' => true,
                'property_type_name' => 'string',
                'object_type_name' => 'documents',
            ],
            [
                'name' => 'another_description',
                'description' => null,
                'is_nullable' => true,
                'property_type_name' => 'string',
                'object_type_name' => 'documents',
            ],
            [
                'name' => 'another_username',
                'description' => 'Username, unique string',
                'is_nullable' => true,
                'property_type_name' => 'string',
                'object_type_name' => 'users',
            ],
            [
                'name' => 'another_email',
                'description' => 'User email',
                'is_nullable' => true,
                'property_type_name' => 'email',
                'object_type_name' => 'users',
            ],
            [
                'name' => 'another_birthdate',
                'description' => null,
                'is_nullable' => true,
                'property_type_name' => 'date',
                'object_type_name' => 'profiles',
            ],
            [
                'name' => 'another_surname',
                'description' => null,
                'is_nullable' => true,
                'property_type_name' => 'string',
                'object_type_name' => 'profiles',
            ],
            [
                'name' => 'disabled_property',
                'description' => 'Disabled property example',
                'is_nullable' => true,
                'property_type_name' => 'string',
                'object_type_name' => 'files',
            ],
            [
                'name' => 'media_property',
                'description' => null,
                'is_nullable' => false,
                'property_type_name' => 'boolean',
                'object_type_name' => 'media',
            ],
            [
                'name' => 'files_property',
                'description' => null,
                'is_nullable' => true,
                'property_type_name' => 'json',
                'object_type_name' => 'files',
            ],
            [
                'name' => 'street_address',
                'description' => null,
                'is_nullable' => true,
                'property_type_name' => 'string',
                'object_type_name' => 'profiles',
            ],
            [
                'name' => 'number_of_friends',
                'description' => null,
                'is_nullable' => true,
                'property_type_name' => 'integer',
                'object_type_name' => 'profiles',
            ],
            [
                'name' => 'children_order',
                'description' => null,
                'is_nullable' => true,
                'property_type_name' => 'children_order',
                'object_type_name' => 'folders',
            ],
        ],
    ];

    /**
     * Test `generate()` method
     *
     * @return void
     * @covers ::generate()
     * @covers ::applications()
     * @covers ::roles()
     * @covers ::propertyTypes()
     * @covers ::objectTypes()
     * @covers ::relations()
     * @covers ::properties()
     */
    public function testGenerate(): void
    {
        $result = ProjectModel::generate();
        $result = json_decode(json_encode($result), true);
        static::assertNotEmpty($result);
        static::assertEquals(self::PROJECT_MODEL, $result);
    }

    /**
     * Test `diff()` method adding items
     *
     * @return void
     * @covers ::diff()
     * @covers ::itemsToUpdate()
     */
    public function testDiffAdd(): void
    {
        $data = self::PROJECT_MODEL;
        $propType = [
            'name' => 'test property type',
            'params' => [
                'type' => 'number',
            ],
        ];
        $data['property_types'][] = $propType;

        $result = ProjectModel::diff($data);
        $create = [
            'property_types' => [$propType],
        ];
        static::assertEquals(compact('create'), $result);
    }

    /**
     * Test `diff()` method removing items
     *
     * @return void
     * @covers ::diff()
     */
    public function testDiffRemove(): void
    {
        $data = self::PROJECT_MODEL;
        unset($data['properties'][0], $data['relations'][0]);
        $result = ProjectModel::diff($data);
        $remove = [
            'properties' => [
                [
                    'name' => 'another_title',
                    'description' => null,
                    'is_nullable' => true,
                    'property_type_name' => 'string',
                    'object_type_name' => 'documents',
                ],
            ],
            'relations' => [
                [
                    'name' => 'test',
                    'label' => 'Test relation',
                    'inverse_name' => 'inverse_test',
                    'inverse_label' => 'Inverse test relation',
                    'description' => 'Sample description.',
                    'params' => null,
                    'right_object_types' => ['documents', 'profiles'],
                    'left_object_types' => ['documents'],
                ],
            ],
        ];
        static::assertEquals(compact('remove'), $result);
    }

    /**
     * Test `diff()` method updating items
     *
     * @return void
     * @covers ::diff()
     * @covers ::itemsToUpdate()
     */
    public function testDiffUpdate(): void
    {
        $data = self::PROJECT_MODEL;
        $rel = [
            'name' => 'test',
            'label' => 'Test test',
            'inverse_name' => 'inverse_test',
            'inverse_label' => 'Inverse test test',
            'description' => 'Another description',
            'params' => null,
            'right_object_types' => ['documents', 'profiles'],
            'left_object_types' => ['events'],
        ];
        $data['relations'][0] = $rel;

        $result = ProjectModel::diff($data);
        $update = [
            'relations' => [$rel],
        ];
        static::assertEquals(compact('update'), $result);
    }
}
