<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
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
use Cake\Utility\Hash;

/**
 * Test tags & categories
 */
class TagsCategoriesTest extends IntegrationTestCase
{
    /**
     * Data provider for `testCreate`
     *
     * @return array
     */
    public function createProvider(): array
    {
        return [
            'simple tag' => [
                [
                    'tags' => [
                        [
                            'name' => 'first-tag',
                            'labels' => ['default' => 'First tag'],
                            'label' => 'First tag',
                        ],
                    ],
                ],
                'profiles',
                [
                    'tags' => [
                        ['name' => 'first-tag'],
                    ],
                ],
            ],
            'no tag' => [
                [
                    'categories' => [],
                ],
                'documents',
                [
                    'tags' => [
                        ['name' => 'first-tag'],
                    ],
                ],
            ],
            'second cat' => [
                [
                    'categories' => [
                        [
                            'name' => 'second-cat',
                            'labels' => ['default' => 'Second category'],
                            'params' => null,
                            'label' => 'Second category',
                        ],
                    ],
                ],
                'documents',
                [
                    'categories' => [
                        ['name' => 'second-cat'],
                    ],
                ],
            ],
            'nothing' => [
                [
                ],
                'locations',
                [
                    'tags' => [
                        ['name' => 'first-tag'],
                    ],
                    'categories' => [
                        ['name' => 'first-cat'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test new object creation with tags/categories
     *
     * @param array $expected Expected result
     * @param string $type Object type
     * @param array $attributes New object attributes
     * @return void
     * @dataProvider createProvider
     * @coversNothing
     */
    public function testCreate(array $expected, string $type, array $attributes): void
    {
        // Create object
        $data = compact('type', 'attributes');
        $this->configRequestHeaders('POST', $this->getUserAuthHeader());
        $this->post("/$type", json_encode(compact('data')));
        $this->assertResponseCode(201);
        $this->assertContentType('application/vnd.api+json');

        $locationHeader = $this->_response->getHeaderLine('Location');
        $this->assertNotEmpty($locationHeader);
        $objId = substr($locationHeader, strrpos($locationHeader, '/') + 1);

        // View object
        $this->configRequestHeaders();
        $this->get("/$type/$objId");
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        foreach (['tags', 'categories'] as $item) {
            if (!isset($expected[$item])) {
                static::assertArrayNotHasKey($item, $result['data']['attributes']);
            } else {
                static::assertArrayHasKey($item, $result['data']['attributes']);
                static::assertEquals($expected[$item], $result['data']['attributes'][$item]);
            }
        }
    }

    /**
     * Data provider for `testUpdate`
     *
     * @return array
     */
    public function updateProvider(): array
    {
        return [
            'no cat' => [
                [
                    'tags' => [
                        [
                            'name' => 'first-tag',
                            'labels' => ['default' => 'First tag'],
                            'label' => 'First tag',
                        ],
                    ],
                ],
                'profiles',
                '4',
                [
                    'categories' => [
                        ['name' => 'first-cat'],
                    ],
                ],
            ],

            'no tag' => [
                [
                    'categories' => [
                        [
                            'name' => 'first-cat',
                            'labels' => ['default' => 'First category'],
                            'params' => '100',
                            'label' => 'First category',
                        ],
                        [
                            'name' => 'second-cat',
                            'labels' => ['default' => 'Second category'],
                            'params' => null,
                            'label' => 'Second category',
                        ],
                    ],
                ],
                'documents',
                '2',
                [
                    'tags' => [
                        ['name' => 'first-tag'],
                    ],
                ],
            ],

            'less cat, no tag' => [
                [
                    'categories' => [
                        [
                            'name' => 'first-cat',
                            'labels' => ['default' => 'First category'],
                            'params' => '100',
                            'label' => 'First category',
                        ],
                    ],
                ],
                'documents',
                '2',
                [
                    'categories' => [
                        ['name' => 'first-cat'],
                    ],
                    'tags' => [
                        ['name' => 'first-tag'],
                    ],
                ],
            ],

            'no cat, no tag' => [
                [
                ],
                'locations',
                '8',
                [
                    'categories' => [
                        ['name' => 'first-cat'],
                    ],
                    'tags' => [
                        ['name' => 'first-tag'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test object update with tags/categories
     *
     * @param array $expected Expected result
     * @param string $type Object type
     * @param array $attributes Object update attributes
     * @return void
     * @dataProvider updateProvider
     * @coversNothing
     */
    public function testUpdate(array $expected, string $type, string $id, array $attributes): void
    {
        // Patch object
        $data = compact('type', 'id', 'attributes');
        $this->configRequestHeaders('PATCH', $this->getUserAuthHeader());
        $this->patch("/$type/$id", json_encode(compact('data')));
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $result = json_decode((string)$this->_response->getBody(), true);

        // View object
        $this->configRequestHeaders();
        $this->get("/$type/$id");
        $result = json_decode((string)$this->_response->getBody(), true);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');

        foreach (['tags', 'categories'] as $item) {
            if (!isset($expected[$item])) {
                static::assertArrayNotHasKey($item, $result['data']['attributes']);
            } else {
                static::assertArrayHasKey($item, $result['data']['attributes']);
                static::assertEquals($expected[$item], $result['data']['attributes'][$item]);
            }
        }
    }

    /**
     * Data provider for `testModelEndpoints`
     *
     * @return array
     */
    public function modelEndpointsProvider(): array
    {
        return [
            'categories' => [
                [1, 2, 3, 4],
                '/model/categories',
            ],
            'tags' => [
                [1],
                '/model/tags',
            ],
            'single cat' => [
                [2],
                '/model/categories/2',
            ],
            'single tag' => [
                [1],
                '/model/tags/1',
            ],
            'obj categories' => [
                [2],
                '/model/categories/2/object_categories',
            ],
            'obj tags' => [
                [1],
                '/model/tags/1/object_tags',
            ],
        ];
    }

    /**
     * Test model endpoints of tags/categories
     *
     * @param array $expected Expected result
     * @param string $url Endpoint url
     * @return void
     * @dataProvider modelEndpointsProvider
     * @coversNothing
     */
    public function testModelEndpoints(array $expected, string $url): void
    {
        $this->configRequestHeaders();
        $this->get($url);
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $result = json_decode((string)$this->_response->getBody(), true);
        $data = Hash::extract($result, 'data');
        if (!empty($data['id'])) {
            static::assertEquals($expected[0], $data['id']);

            return;
        }
        $ids = Hash::extract($data, '{n}.id');
        sort($ids);
        static::assertEquals($expected, $ids);
    }

    /**
     * Test `/model/categories` endpoint
     *
     * @return void
     * @coversNothing
     */
    public function testCategoriesModel(): void
    {
        $this->configRequestHeaders();
        $this->get('/model/categories');
        $this->assertResponseCode(200);
        $this->assertContentType('application/vnd.api+json');
        $result = json_decode((string)$this->_response->getBody(), true);
        $data = Hash::extract($result, 'data');
        $expected = [
            [
                'id' => '1',
                'type' => 'categories',
                'attributes' => [
                    'name' => 'first-cat',
                    'labels' => ['default' => 'First category'],
                    'parent_id' => null,
                    'tree_left' => 1,
                    'tree_right' => 2,
                    'enabled' => true,
                    'object_type_name' => 'documents',
                    'label' => 'First category',
                ],
            ],
            [
                'id' => '2',
                'type' => 'categories',
                'attributes' => [
                    'name' => 'second-cat',
                    'labels' => ['default' => 'Second category'],
                    'parent_id' => null,
                    'tree_left' => 3,
                    'tree_right' => 6,
                    'enabled' => true,
                    'object_type_name' => 'documents',
                    'label' => 'Second category',
                ],
            ],
            [
                'id' => '3',
                'type' => 'categories',
                'attributes' => [
                    'name' => 'disabled-cat',
                    'labels' => ['default' => 'Disabled category'],
                    'parent_id' => null,
                    'tree_left' => 7,
                    'tree_right' => 8,
                    'enabled' => false,
                    'object_type_name' => 'documents',
                    'label' => 'Disabled category',
                ],
            ],
            [
                'id' => '4',
                'type' => 'categories',
                'attributes' => [
                    'name' => 'child-cat-1',
                    'labels' => ['default' => 'Child category'],
                    'parent_id' => 2,
                    'tree_left' => 4,
                    'tree_right' => 5,
                    'enabled' => true,
                    'object_type_name' => 'documents',
                    'label' => 'Child category',
                ],
            ],
        ];
        $data = Hash::remove($data, '{n}.relationships');
        $data = Hash::remove($data, '{n}.meta');
        $data = Hash::remove($data, '{n}.links');

        static::assertEquals($expected, $data);
    }
}
