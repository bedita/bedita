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

namespace BEdita\Core\Test\TestCase\Model\Behavior;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Behavior\PriorityBehavior} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Behavior\PriorityBehavior
 */
class PriorityBehaviorTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.ObjectRelations',
    ];

    /**
     * Data provider for `testInitialize` test case.
     *
     * @return array
     */
    public function initializeProvider(): array
    {
        return [
            'default' => [
                [],
            ],
            'simple' => [
                [
                    'priority' => [
                        'scope' => false,
                    ],
                ],
                [
                    'fields' => ['priority'],
                ],
            ],
            'advanced' => [
                [
                    'priority' => [
                        'scope' => false,
                    ],
                    'scoped_priority' => [
                        'scope' => ['scoping_field'],
                    ],
                ],
                [
                    'fields' => [
                        '_all' => [
                        ],
                        'priority',
                        'scoped_priority' => [
                            'scope' => ['scoping_field'],
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * Test initialization.
     *
     * @param array $expected Expected result.
     * @param array $config Configuration.
     * @return void
     * @covers ::initialize()
     * @dataProvider initializeProvider()
     */
    public function testInitialize(array $expected, array $config = []): void
    {
        $table = TableRegistry::getTableLocator()->get('MyTable', ['className' => Table::class]);
        $table->addBehavior('BEdita/Core.Priority', $config);

        $behavior = $table->behaviors()->get('Priority');
        $fields = $behavior->getConfig('fields');

        static::assertEquals($expected, $fields);
    }

    /**
     * Test setting of priority before entity is saved using `ObjectRelations` table
     *
     * @return void
     * @covers ::beforeSave()
     * @covers ::updateEntityPriorities()
     * @covers ::maxValue()
     */
    public function testBeforeSave(): void
    {
        $table = TableRegistry::getTableLocator()->get('ObjectRelations');

        $entity = $table->newEntity([]);
        $entity->set([
            'left_id' => 9,
            'relation_id' => 3,
            'right_id' => 10,
        ]);
        $table->dispatchEvent('Model.beforeSave', [$entity]);
        static::assertSame(1, $entity->get('priority'));
        static::assertSame(1, $entity->get('inv_priority'));

        // use explicit priority
        $entity->set('priority', 5);
        $table->dispatchEvent('Model.beforeSave', [$entity]);
        static::assertSame(5, $entity->get('priority'));
        static::assertSame(1, $entity->get('inv_priority'));
    }

    /**
     * Test priorities sorting before entity is saved using `ObjectRelations` table
     *
     * @return void
     * @covers ::_getConditions()
     * @covers ::beforeSave()
     * @covers ::updateEntityPriorities()
     * @covers ::expand()
     */
    public function testExpand(): void
    {
        $table = TableRegistry::getTableLocator()->get('ObjectRelations');

        $entities = $table->find()
            ->where([
                'left_id' => 2,
                'relation_id' => 1,
            ])
            ->order(['priority'])
            ->toList();

        static::assertSame(4, $entities[0]->get('right_id'));
        static::assertSame(1, $entities[0]->get('priority'));
        static::assertSame(3, $entities[1]->get('right_id'));
        static::assertSame(2, $entities[1]->get('priority'));
        static::assertSame(7, $entities[2]->get('right_id'));
        static::assertSame(3, $entities[2]->get('priority'));

        $entities[2]->set(['priority' => 1]);
        $table->save($entities[2]);

        $entities = $table->find()
            ->where([
                'left_id' => 2,
                'relation_id' => 1,
            ])
            ->order(['priority'])
            ->toList();

        static::assertSame(7, $entities[0]->get('right_id'));
        static::assertSame(1, $entities[0]->get('priority'));
        static::assertSame(4, $entities[1]->get('right_id'));
        static::assertSame(2, $entities[1]->get('priority'));
        static::assertSame(3, $entities[2]->get('right_id'));
        static::assertSame(3, $entities[2]->get('priority'));
    }

    /**
     * Test priorities sorting before entity is saved using `ObjectRelations` table
     *
     * @return void
     * @covers ::_getConditions()
     * @covers ::beforeSave()
     * @covers ::updateEntityPriorities()
     * @covers ::compact()
     */
    public function testCompact(): void
    {
        $table = TableRegistry::getTableLocator()->get('ObjectRelations');

        $entities = $table->find()
            ->where([
                'left_id' => 2,
                'relation_id' => 1,
            ])
            ->order(['priority'])
            ->toList();

        static::assertSame(4, $entities[0]->get('right_id'));
        static::assertSame(1, $entities[0]->get('priority'));
        static::assertSame(3, $entities[1]->get('right_id'));
        static::assertSame(2, $entities[1]->get('priority'));
        static::assertSame(7, $entities[2]->get('right_id'));
        static::assertSame(3, $entities[2]->get('priority'));

        $entities[0]->set(['priority' => 3]);
        $table->save($entities[0]);

        $entities = $table->find()
            ->where([
                'left_id' => 2,
                'relation_id' => 1,
            ])
            ->order(['priority'])
            ->toList();

        static::assertSame(3, $entities[0]->get('right_id'));
        static::assertSame(1, $entities[0]->get('priority'));
        static::assertSame(7, $entities[1]->get('right_id'));
        static::assertSame(2, $entities[1]->get('priority'));
        static::assertSame(4, $entities[2]->get('right_id'));
        static::assertSame(3, $entities[2]->get('priority'));
    }

    /**
     * Test priorities compaction before entity is deleted using `ObjectRelations` table
     *
     * @return void
     * @covers ::_getConditions()
     * @covers ::beforeDelete()
     * @covers ::compactEntityField()
     */
    public function testBeforeDelete(): void
    {
        $table = TableRegistry::getTableLocator()->get('ObjectRelations');

        $entities = $table->find()
            ->where([
                'left_id' => 2,
                'relation_id' => 1,
            ])
            ->order(['priority'])
            ->toList();

        static::assertSame(4, $entities[0]->get('right_id'));
        static::assertSame(1, $entities[0]->get('priority'));
        static::assertSame(3, $entities[1]->get('right_id'));
        static::assertSame(2, $entities[1]->get('priority'));
        static::assertSame(7, $entities[2]->get('right_id'));
        static::assertSame(3, $entities[2]->get('priority'));

        $table->delete($entities[1]);

        $entities = $table->find()
            ->where([
                'left_id' => 2,
                'relation_id' => 1,
            ])
            ->order(['priority'])
            ->toList();

        static::assertSame(4, $entities[0]->get('right_id'));
        static::assertSame(1, $entities[0]->get('priority'));
        static::assertSame(7, $entities[1]->get('right_id'));
        static::assertSame(2, $entities[1]->get('priority'));
    }

    /**
     * Data provider for testCompactEntityField
     *
     * @return array
     */
    public function compactEntityFieldProvider(): array
    {
        $table = TableRegistry::getTableLocator()->get('ObjectRelations');

        return [
            'empty scope' => [
                'entity' => $table->newEntity([
                    'left_id' => 2,
                    'relation_id' => 1,
                    'right_id' => 4,
                    'priority' => 1,
                ]),
                'field' => 'priority',
                'config' => [],
                'expected' => false,
            ],
            'empty field' => [
                'entity' => $table->newEntity([
                    'left_id' => 2,
                    'relation_id' => 1,
                    'right_id' => null,
                    'priority' => 1,
                ]),
                'field' => 'right_id',
                'config' => [
                    'scope' => ['whatever'],
                ],
                'expected' => false,
            ],
            'compact data' => [
                'entity' => $table->newEntity([
                    'left_id' => 2,
                    'relation_id' => 1,
                    'right_id' => 4,
                    'priority' => 1,
                ]),
                'field' => 'priority',
                'config' => [
                    'scope' => ['priority'],
                ],
                'expected' => true,
            ],
        ];
    }

    /**
     * Test `compactEntityField()` method
     *
     * @param EntityInterface $entity The entity
     * @param string $field The field
     * @param array $config the config
     * @param bool $expected The expected result
     * @return void
     * @dataProvider compactEntityFieldProvider()
     * @covers ::_getConditions()
     * @covers ::compactEntityField()
     */
    public function testCompactEntityField(EntityInterface $entity, string $field, array $config, bool $expected): void
    {
        $table = TableRegistry::getTableLocator()->get('ObjectRelations');
        $actual = $table->compactEntityField($entity, $field, $config);
        static::assertSame($expected, $actual);
    }

    /**
     * Data provider for testUpdateEntityPriorities
     *
     * @return array
     */
    public function updateEntityPrioritiesProvider(): array
    {
        return [
            'empty scope' => [
                'entity' => [
                    'left_id' => 2,
                    'relation_id' => 1,
                    'right_id' => 4,
                ],
                1,
                1,
                'field' => 'priority',
                'config' => [],
                'expected' => false,
            ],
            'actual value equals previous value' => [
                'entity' => [
                    'left_id' => 2,
                    'relation_id' => 1,
                    'right_id' => 4,
                ],
                1,
                1,
                'field' => 'priority',
                'config' => [
                    'scope' => ['priority'],
                ],
                'expected' => false,
            ],
            'compact' => [
                'entity' => [
                    'left_id' => 2,
                    'relation_id' => 1,
                    'right_id' => 4,
                ],
                2,
                1,
                'field' => 'priority',
                'config' => [
                    'scope' => ['priority'],
                ],
                'expected' => true,
            ],
            'expand' => [
                'entity' => [
                    'left_id' => 2,
                    'relation_id' => 1,
                    'right_id' => 4,
                ],
                1,
                2,
                'field' => 'priority',
                'config' => [
                    'scope' => ['priority'],
                ],
                'expected' => true,
            ],
            'max value' => [
                'entity' => [
                    'left_id' => 2,
                    'relation_id' => 1,
                    'right_id' => 4,
                ],
                null,
                1,
                'field' => 'priority',
                'config' => [
                    'scope' => ['priority'],
                ],
                'expected' => true,
            ],
        ];
    }

    /**
     * Test `updateEntityPriorities()` method
     *
     * @param array|null $entityData The entity
     * @param int|null $actualValue The actual value
     * @param int $previousValue The previous value
     * @param string $field The field
     * @param array $config the config
     * @param bool $expected The expected result
     * @return void
     * @dataProvider updateEntityPrioritiesProvider()
     * @covers ::_getConditions()
     * @covers ::updateEntityPriorities()
     */
    public function testUpdateEntityPriorities(?array $entityData, ?int $actualValue, int $previousValue, string $field, array $config, bool $expected): void
    {
        $table = TableRegistry::getTableLocator()->get('ObjectRelations');
        $entityData[$field] = $previousValue;
        $entity = $table->newEntity($entityData);
        $entity->set($field, $actualValue);
        static::assertSame($expected, $table->updateEntityPriorities($entity, $field, $config));
    }
}
