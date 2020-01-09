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

namespace BEdita\Core\Test\TestCase\Model\Behavior;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Behavior\CategoriesBehavior} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Behavior\CategoriesBehavior
 */
class CategoriesBehaviorTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
        'plugin.BEdita/Core.History',
    ];

    /**
     * Data provider for `testBeforeSave` test case.
     *
     * @return array
     */
    public function beforeSaveProvider()
    {
        return [
            'ok' => [
                [
                    'categories' => [
                        [
                            'name' => 'second-cat',
                            'id' => 2,
                        ]
                    ]
                ],
                [
                    'categories' => [
                        ['name' => 'second-cat'],
                        ['name' => 'disabled-cat'],
                    ]
                ],
                2,
                'Documents',
            ],
            'profile tags' => [
                [
                    'tags' => [
                        [
                            'name' => 'first-tag',
                            'id' => 4,
                        ]
                    ]
                ],
                [
                    'tags' => [
                        ['name' => 'first-tag'],
                    ]
                ],
                4,
                'Profiles',
            ],
            'missing categories' => [
                [
                    'categories' => []
                ],
                [
                    'categories' => [
                        ['name' => 'some-category'],
                        ['name' => 'other-cat'],
                    ]
                ],
                2,
                'Documents',
            ],
            'no categories' => [
                [
                    'categories' => []
                ],
                [
                    'description' => 'some description',
                ],
                2,
                'Documents',
            ],
            'no tags allowed' => [
                [
                    'tags' => [],
                ],
                [
                    'tags' => [
                        ['name' => 'first-tag'],
                    ]
                ],
                2,
                'Documents',
            ],
            'no categories allowed' => [
                [
                    'categories' => [],
                ],
                [
                    'categories' => [
                        ['name' => 'second-cat'],
                    ]
                ],
                4,
                'Profiles',
            ],
            'missing tags' => [
                [
                    'tags' => []
                ],
                [
                    'tags' => [
                        ['name' => 'some-tag'],
                        ['name' => 'other-tag'],
                    ]
                ],
                4,
                'Profiles',
            ],
        ];
    }

    /**
     * Test tags and categories `save`.
     *
     * @param array $expected Expected result.
     * @param array $data Data.
     * @param int $id Entity ID.
     * @param string $tableName Table.
     * @return void
     *
     * @dataProvider beforeSaveProvider()
     * @covers ::beforeSave()
     * @covers ::prepareData()
     * @covers ::retrieveIds()
     * @covers ::updateData()
     */
    public function testBeforeSave(array $expected, array $data, $id, $tableName)
    {
        $table = TableRegistry::getTableLocator()->get($tableName);

        $objectType = $table
            ->getAssociation('ObjectTypes')
            ->get($tableName);
        $options = [];
        if (!empty($objectType->get('associations'))) {
            $options = ['contain' => $objectType->get('associations')];
        }
        $entity = $table->get($id, $options);

        $entity = $table->patchEntity($entity, $data);
        $entity = $table->save($entity);
        static::assertNotFalse($entity);

        foreach (array_keys($expected) as $key) {
            $result = (array)$entity->get($key);
            static::assertSame(count($expected[$key]), count($result));
            foreach ($result as $k => $res) {
                ksort($expected[$key][$k]);
                if (!is_array($res)) {
                    $res = $res->toArray();
                }
                ksort($res);
                static::assertSame($expected[$key][$k], $res);
            }
        }
    }
}
