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
    public function initializeProvider()
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
    public function testInitialize(array $expected, array $config = [])
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
     * @covers ::maxValue()
     */
    public function testBeforeSave()
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
     * @covers ::beforeSave()
     * @covers ::expand()
     */
    public function testExpand()
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
     * @covers ::beforeSave()
     * @covers ::compact()
     */
    public function testCompact()
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
     * @covers ::beforeDelete()
     */
    public function testBeforeDelete()
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
}
