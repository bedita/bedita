<?php
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

/**
 * Test tags & categories save
 */
class TagsCategoriesTest extends IntegrationTestCase
{
    /**
     * Data provider for `testCreate`
     */
    public function createProvider()
    {
        return [
            'simple tag' => [
                [
                    'tags' => [
                        [
                            'name' => 'first-tag',
                            'label' => 'First tag',
                            'params' => null,
                        ]
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
                            'label' => 'Second category',
                            'params' => null,
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
     * @dataProvider createProvider
     * @coversNothing
     */
    public function testCreate(array $expected, string $type, array $attributes)
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
     */
    public function updateProvider()
    {
        return [
            'no cat' => [
                [
                    'tags' => [
                        [
                            'name' => 'first-tag',
                            'label' => 'First tag',
                            'params' => null,
                        ]
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
                            'label' => 'First category',
                            'params' => '100',
                        ],
                        [
                            'name' => 'second-cat',
                            'label' => 'Second category',
                            'params' => null,
                        ]
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
                            'label' => 'First category',
                            'params' => '100',
                        ]
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
     * @dataProvider updateProvider
     * @coversNothing
     */
    public function testUpdate(array $expected, string $type, string $id, array $attributes)
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
}
