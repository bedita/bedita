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
     * Test `beforeFilter()` method.
     *
     * @return void
     * @covers ::beforeFilter()
     */
    public function testBeforeFilter(): void
    {
        $this->configRequestHeaders();
        $this->get('/model/project');
        $this->assertResponseCode(406);

        $this->configRequestHeaders('GET', ['Accept' => 'application/json']);
        $this->get('/model/project');
        $this->assertResponseCode(200);
    }
}
