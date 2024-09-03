<?php
declare(strict_types=1);

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

namespace BEdita\Core\Test\TestCase\Model\Action;

use BEdita\Core\Model\Action\ListRelatedObjectsAction;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;

/**
 * @covers \BEdita\Core\Model\Action\ListRelatedObjectsAction
 */
class ListRelatedObjectsActionTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Streams',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
        'plugin.BEdita/Core.History',
    ];

    /**
     * Data provider for `testInvocation` test case.
     *
     * @return array
     */
    public function invocationProvider()
    {
        return [
            [
                [
                    [
                        'id' => 4,
                        'type' => 'profiles',
                        '_joinData' => [
                            'priority' => 1,
                            'inv_priority' => 2,
                            'params' => null,
                        ],
                    ],
                    [
                        'id' => 3,
                        'type' => 'documents',
                        '_joinData' => [
                            'priority' => 2,
                            'inv_priority' => 1,
                            'params' => null,
                        ],
                    ],
                ],
                'Documents',
                'test',
                2,
            ],
            [
                [
                    [
                        'id' => 4,
                        'type' => 'profiles',
                        '_joinData' => [
                            'priority' => 1,
                            'inv_priority' => 1,
                            'params' => null,
                        ],
                    ],
                ],
                'Documents',
                'test',
                3,
            ],
            [
                [
                    [
                        'id' => 3,
                        'type' => 'documents',
                        '_joinData' => [
                            'priority' => 1,
                            'inv_priority' => 1,
                            'params' => null,
                        ],
                        'categories' => [],
                    ],
                    [
                        'id' => 2,
                        'type' => 'documents',
                        '_joinData' => [
                            'priority' => 2,
                            'inv_priority' => 1,
                            'params' => null,
                        ],
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
                ],
                'Profiles',
                'inverse_test',
                4,
            ],
            [
                [
                    [
                        'id' => 8,
                        'type' => 'locations',
                        'coords' => 'POINT(11.3464055 44.4944183)',
                        'address' => 'Piazza di Porta Ravegnana',
                        'locality' => 'Bologna',
                        'postal_code' => '40126',
                        'country_name' => 'Italy',
                        'region' => 'Emilia-romagna',
                        'status' => 'on',
                        'uname' => 'the-two-towers',
                        'locked' => false,
                        'created' => '2017-02-20T07:09:23+00:00',
                        'modified' => '2017-02-20T07:09:23+00:00',
                        'published' => '2017-02-20T07:09:23+00:00',
                        'title' => 'The Two Towers',
                        'description' => null,
                        'body' => null,
                        'extra' => null,
                        'lang' => 'en',
                        'created_by' => 1,
                        'modified_by' => 1,
                        'publish_start' => null,
                        'publish_end' => null,
                        '_joinData' => [
                            'priority' => 1,
                            'inv_priority' => 1,
                            'params' => null,
                        ],
                    ],
                ],
                'Users',
                'another_test',
                1,
                false,
            ],
            [
                [
                    [
                        'id' => 2,
                        'type' => 'documents',
                        '_joinData' => [
                            'priority' => 2,
                            'inv_priority' => 1,
                            'params' => null,
                        ],
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
                ],
                'Profiles',
                'inverse_test',
                4,
                true,
                [2],
            ],
            [
                new RecordNotFoundException('Record not found in table "locations"'),
                'Locations',
                'inverse_another_test',
                -1,
            ],
            [
                [
                    [
                        'id' => 4,
                        'type' => 'profiles',
                        '_joinData' => [
                            'priority' => 1,
                            'inv_priority' => 2,
                            'params' => null,
                        ],
                    ],
                ],
                'Documents',
                'test',
                2,
                true,
                null,
                'on',
            ],
            [
                [
                    [
                        'uuid' => '6aceb0eb-bd30-4f60-ac74-273083b921b6',
                        'url' => null,
                    ],
                ],
                'Files',
                'streams',
                14,
                true,
                null,
                'on',
            ],
        ];
    }

    /**
     * Test command invocation.
     *
     * @param array|\Exception $expected Expected result.
     * @param string $objectType Object type name.
     * @param string $relation Relation name.
     * @param int $id ID.
     * @param bool $list Should results be presented in a list format?
     * @param array|null $only Filter related entities by ID.
     * @param string|null $statusLevel Status level.
     * @return void
     * @dataProvider invocationProvider()
     */
    public function testInvocation($expected, $objectType, $relation, $id, $list = true, ?array $only = null, $statusLevel = null)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        Configure::write('Status.level', $statusLevel);

        $alias = Inflector::camelize(Inflector::underscore($relation));
        $association = TableRegistry::getTableLocator()->get($objectType)->getAssociation($alias);
        $action = new ListRelatedObjectsAction(compact('association'));

        $result = $action(['primaryKey' => $id] + compact('list', 'only'));
        $result = json_decode(json_encode($result->toArray()), true);

        static::assertEquals($expected, $result);
    }

    /**
     * Test that deleted objects will not show as related
     *
     * @return void
     * @coversNothing
     */
    public function testDeleted(): void
    {
        // set Document 3 `deleted`
        // must not appear on right side of Document 2 `test` relation
        $table = TableRegistry::getTableLocator()->get('Documents');
        $entity = $table->get(3);
        $entity->set('deleted', true);
        $table->saveOrFail($entity);

        $association = $table->getAssociation('Test');
        $action = new ListRelatedObjectsAction(compact('association'));

        $result = $action(['primaryKey' => 2, 'list' => true]);
        $result = json_decode(json_encode($result->toArray()), true);

        $expected = [
            [
                'id' => 4,
                'type' => 'profiles',
                '_joinData' => [
                    'priority' => 1,
                    'inv_priority' => 2,
                    'params' => null,
                ],
            ],
        ];
        static::assertEquals($expected, $result);
    }
}
