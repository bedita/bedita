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
    public $fixtures = [
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
     */
    public function testUp()
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
    public function testDown()
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
    public function testMissing()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('YAML file not found');

        $migration = new TestMissing('test', 1);
        $migration->up();
    }
}
