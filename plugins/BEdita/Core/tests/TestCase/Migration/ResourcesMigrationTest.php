<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Migration;

use BEdita\Core\Test\TestCase\Migration\Migrations\TestAdd;
use BEdita\Core\Test\TestCase\Migration\Migrations\TestColumns;
use BEdita\Core\Test\TestCase\Migration\Migrations\TestMissing;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use RuntimeException;

/**
 * {@see BEdita\Core\State\ResourcesMigration} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Migration\ResourcesMigration
 */
class ResourcesMigrationTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
    ];

    /**
     * Test `up` method.
     *
     * @covers ::up()
     * @covers ::readData()
     * @covers ::executeMigration()
     */
    public function testUp(): void
    {
        $migration = new TestAdd('test', 1);

        $migration->up();

        $objectType = TableRegistry::getTableLocator()->get('ObjectTypes')->get('foos');
        static::assertNotEmpty($objectType);
    }

    /**
     * Test `down` method.
     *
     * @covers ::down()
     * @covers ::readData()
     */
    public function testDown(): void
    {
        $ObjectTypes = TableRegistry::getTableLocator()->get('ObjectTypes');
        $objectType = $ObjectTypes->newEntity(['name' => 'foos', 'singular' => 'foo']);
        $ObjectTypes->saveOrFail($objectType);

        $migration = new TestAdd('test', 1);

        $migration->down();

        $found = TableRegistry::getTableLocator()->get('ObjectTypes')->exists(['name' => 'foos']);
        static::assertFalse($found);
    }

    /**
     * Test missing data file.
     *
     * @covers ::readData()
     */
    public function testMissing(): void
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('YAML file not found');

        $migration = new TestMissing('test', 1);
        $migration->up();
    }

    /**
     * Test columns migration.
     *
     * @covers ::tableColumnsActions()
     * @covers ::updateColumns()
     * @covers ::columnAction()
     * @covers ::migrationTable()
     * @covers ::getColumnType()
     * @covers ::getColumnOptions()
     */
    public function testColumnsUp(): void
    {
        MockMigrationsTable::$calls = [];

        $migration = new TestColumns('test', 1);
        $migration->up();

        $expected = [
            'addColumn' => [
                [
                    'new_prop',
                    'text',
                    [
                        'default' => null,
                        'comment' => 'New prop',
                    ],
                ],
                [
                    'another_prop',
                    'integer',
                    [
                        'comment' => 'Another prop',
                    ],
                ],
                [
                    'json_prop',
                    'text',
                    [
                        'default' => '{}',
                        'comment' => 'Column comment',
                    ],
                ],
                [
                    'enum_prop',
                    'string',
                    [
                        'default' => 'b',
                        'values' => ['a', 'b', 'c'],
                    ],
                ],
            ],
            'changeColumn' => [
                [
                    'new_prop',
                    'text',
                    [
                        'default' => null,
                        'comment' => 'New prop',
                    ],
                ],
            ],
        ];
        static::assertEquals($expected, MockMigrationsTable::$calls);
    }

    /**
     * Test columns rollback.
     *
     * @covers ::tableColumnsActions()
     * @covers ::updateColumns()
     * @covers ::columnAction()
     * @covers ::migrationTable()
     * @covers ::getColumnType()
     * @covers ::getColumnOptions()
     */
    public function testColumnsDown(): void
    {
        MockMigrationsTable::$calls = [];

        $migration = new TestColumns('test', 1);
        $migration->down();

        $expected = [
            'changeColumn' => [
                [
                    'new_prop',
                    'text',
                    [
                        'default' => null,
                        'comment' => 'New prop',
                    ],
                ],
            ],
            'removeColumn' => [
                [
                    'new_prop',
                ],
                [
                    'another_prop',
                ],
                [
                    'json_prop',
                ],
                [
                    'enum_prop',
                ],
            ],
        ];
        static::assertEquals($expected, MockMigrationsTable::$calls);
    }
}
