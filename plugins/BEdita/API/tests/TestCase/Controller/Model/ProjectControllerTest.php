<?php
declare(strict_types=1);

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

namespace BEdita\API\Test\TestCase\Controller\Model;

use BEdita\API\TestSuite\IntegrationTestCase;

/**
 * {@see \BEdita\API\Controller\Model\ProjectController} Test Case
 *
 * @coversDefaultClass \BEdita\API\Controller\Model\ProjectController
 */
class ProjectControllerTest extends IntegrationTestCase
{
    /**
     * Test `index()` method.
     *
     * @return void
     * @covers ::index()
     * @covers ::initialize()
     */
    public function testIndex(): void
    {
        $expected = [
            'applications' => [
                [
                    'name' => 'Disabled app',
                    'description' => 'This app has been disabled',
                    'enabled' => false,
                ],
                [
                    'name' => 'First app',
                    'description' => 'Lorem ipsum dolor sit amet, aliquet feugiat.',
                    'enabled' => true,
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
                [
                    'name' => 'images',
                    'is_abstract' => false,
                    'singular' => 'image',
                    'description' => null,
                    'associations' => ['Streams'],
                    'hidden' => null,
                    'enabled' => true,
                    'table' => 'BEdita/Core.Media',
                    'parent_name' => 'media',
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
                    'name' => 'videos',
                    'is_abstract' => false,
                    'singular' => 'video',
                    'description' => null,
                    'associations' => ['Streams', 'Captions'],
                    'hidden' => null,
                    'enabled' => true,
                    'table' => 'BEdita/Core.Media',
                    'parent_name' => 'media',
                    'translation_rules' => null,
                    'is_translatable' => true,
                ],
            ],
            'relations' => [
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
                    'right' => ['locations'],
                    'left' => ['users'],
                ],
                [
                    'name' => 'test',
                    'label' => 'Test relation',
                    'inverse_name' => 'inverse_test',
                    'inverse_label' => 'Inverse test relation',
                    'description' => 'Sample description.',
                    'right' => ['documents', 'profiles'],
                    'left' => ['documents'],
                ],
                [
                    'name' => 'test_abstract',
                    'label' => 'Test relation involving abstract types',
                    'inverse_name' => 'inverse_test_abstract',
                    'inverse_label' => 'Inverse test relation involving abstract types',
                    'description' => 'Sample description.',
                    'right' => ['media'],
                    'left' => ['events'],
                ],
                [
                    'name' => 'test_defaults',
                    'label' => 'Test relation involving default parameters',
                    'inverse_name' => 'inverse_test_defaults',
                    'inverse_label' => 'Inverse test relation involving default parameters',
                    'description' => 'Sample description.',
                    'params' => [
                        'type' => 'object',
                        'properties' => [
                            'size' => [
                                'type' => ['integer', 'null'],
                                'default' => 5,
                            ],
                            'street' => [
                                'type' => 'string',
                                'default' => 'fighter',
                            ],
                            'color' => [
                                'type' => ['string', 'null'],
                                'default' => null,
                            ],
                        ],
                    ],
                    'right' => ['documents', 'profiles'],
                    'left' => ['documents'],
                ],
                [
                    'name' => 'test_simple',
                    'label' => 'Test relation involving simple parameters',
                    'inverse_name' => 'inverse_test_simple',
                    'inverse_label' => 'Inverse test relation involving simple parameters',
                    'description' => 'Sample description.',
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
                    ],
                    'right' => ['documents', 'profiles'],
                    'left' => ['documents'],
                ],
            ],
            'properties' => [
                [
                    'name' => 'another_birthdate',
                    'description' => null,
                    'is_nullable' => true,
                    'property' => 'date',
                    'object' => 'profiles',
                    'read_only' => true,
                    'default_value' => null,
                ],
                [
                    'name' => 'another_description',
                    'description' => null,
                    'is_nullable' => true,
                    'property' => 'string',
                    'object' => 'documents',
                    'read_only' => false,
                    'default_value' => null,
                ],
                [
                    'name' => 'another_email',
                    'description' => 'User email',
                    'is_nullable' => true,
                    'property' => 'email',
                    'object' => 'users',
                    'read_only' => false,
                    'default_value' => null,
                ],
                [
                    'name' => 'another_surname',
                    'description' => null,
                    'is_nullable' => true,
                    'property' => 'string',
                    'object' => 'profiles',
                    'read_only' => false,
                    'default_value' => null,
                ],
                [
                    'name' => 'another_title',
                    'description' => null,
                    'is_nullable' => true,
                    'property' => 'string',
                    'object' => 'documents',
                    'read_only' => false,
                    'default_value' => null,
                ],
                [
                    'name' => 'another_username',
                    'description' => 'Username, unique string',
                    'is_nullable' => true,
                    'property' => 'string',
                    'object' => 'users',
                    'read_only' => false,
                    'default_value' => null,
                ],
                [
                    'name' => 'children_order',
                    'description' => null,
                    'is_nullable' => true,
                    'property' => 'children_order',
                    'object' => 'folders',
                    'read_only' => false,
                    'default_value' => null,
                ],
                [
                    'name' => 'default_val_property',
                    'description' => 'Default val property example',
                    'is_nullable' => true,
                    'property' => 'string',
                    'object' => 'files',
                    'read_only' => false,
                    'default_value' => 'my default value',
                ],
                [
                    'name' => 'disabled_property',
                    'description' => 'Disabled property example',
                    'is_nullable' => true,
                    'property' => 'string',
                    'object' => 'files',
                    'read_only' => false,
                    'default_value' => null,
                ],
                [
                    'name' => 'files_property',
                    'description' => null,
                    'is_nullable' => true,
                    'property' => 'json',
                    'object' => 'files',
                    'read_only' => false,
                    'default_value' => null,
                ],
                [
                    'name' => 'media_property',
                    'description' => null,
                    'is_nullable' => false,
                    'property' => 'boolean',
                    'object' => 'media',
                    'read_only' => false,
                    'default_value' => null,
                ],
                [
                    'name' => 'number_of_friends',
                    'description' => null,
                    'is_nullable' => true,
                    'property' => 'integer',
                    'object' => 'profiles',
                    'read_only' => false,
                    'default_value' => null,
                ],
                [
                    'name' => 'street_address',
                    'description' => null,
                    'is_nullable' => true,
                    'property' => 'string',
                    'object' => 'profiles',
                    'read_only' => false,
                    'default_value' => null,
                ],
            ],
            'categories' => [
                [
                    'name' => 'first-cat',
                    'labels' => ['default' => 'First category'],
                    'parent' => null,
                    'enabled' => true,
                    'object' => 'documents',
                    'label' => 'First category',
                ],
                [
                    'name' => 'second-cat',
                    'labels' => ['default' => 'Second category'],
                    'parent' => null,
                    'enabled' => true,
                    'object' => 'documents',
                    'label' => 'Second category',
                ],
                [
                    'name' => 'child-cat-1',
                    'labels' => ['default' => 'Child category'],
                    'parent' => 'second-cat',
                    'enabled' => true,
                    'object' => 'documents',
                    'label' => 'Child category',
                ],
                [
                    'name' => 'disabled-cat',
                    'labels' => ['default' => 'Disabled category'],
                    'parent' => null,
                    'enabled' => false,
                    'object' => 'documents',
                    'label' => 'Disabled category',
                ],
            ],
            'config' => [
                [
                    'name' => 'appVal',
                    'context' => 'app',
                    'content' => '{"val": 42}',
                    'application' => 'First app',
                ],
                [
                    'name' => 'someVal',
                    'context' => 'core',
                    'content' => '42',
                    'application' => 'First app',
                ],
                [
                    'name' => 'myVal',
                    'context' => 'somecontext',
                    'content' => '42',
                    'application' => 'First app',
                ],
            ],
        ];

        $this->configRequestHeaders('GET', ['Accept' => 'application/json']);
        $this->get('/model/project');
        $result = json_decode((string)$this->_response->getBody(), true);

        $this->assertResponseCode(200);
        $this->assertContentType('application/json');
        static::assertEquals($expected, $result);
    }

    /**
     * Test `checkAcceptable()` method.
     *
     * @return void
     * @covers ::checkAcceptable()
     */
    public function testCheckAcceptable(): void
    {
        $this->configRequestHeaders();
        $this->get('/model/project');
        $this->assertResponseCode(406);

        $this->configRequestHeaders('GET', ['Accept' => 'application/json']);
        $this->get('/model/project');
        $this->assertResponseCode(200);
    }
}
