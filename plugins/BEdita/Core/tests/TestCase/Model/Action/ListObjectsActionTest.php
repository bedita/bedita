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

namespace BEdita\Core\Test\TestCase\Model\Action;

use BEdita\Core\Model\Action\ListObjectsAction;
use Cake\Database\Query;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @covers \BEdita\Core\Model\Action\ListObjectsAction
 */
class ListObjectsActionTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.date_ranges',
        'plugin.BEdita/Core.objects',
    ];

    /**
     * Test command execution.
     *
     * @return void
     */
    public function testExecute()
    {
        $table = TableRegistry::get('Objects');
        $action = new ListObjectsAction(compact('table'));

        $result = $action();

        static::assertInstanceOf(Query::class, $result);
        static::assertSame(11, $result->count());
    }

    /**
     * Test command execution with filter by object type.
     *
     * @return void
     */
    public function testExecuteObjectTypeFilter()
    {
        $objectType = TableRegistry::get('ObjectTypes')->get('Events');
        $table = TableRegistry::get('Objects');
        $action = new ListObjectsAction(compact('table', 'objectType'));

        $result = $action();

        static::assertInstanceOf(Query::class, $result);
        static::assertSame(1, $result->count());
    }

    /**
     * Test command execution with filter by deletions status.
     *
     * @return void
     */
    public function testExecuteDeleted()
    {
        $table = TableRegistry::get('Objects');
        $action = new ListObjectsAction(compact('table'));

        $result = $action(['deleted' => true]);

        static::assertInstanceOf(Query::class, $result);
        static::assertSame(2, $result->count());
    }

    /**
     * Test command execution with a custom filter.
     *
     * @return void
     */
    public function testExecuteCustomFilter()
    {
        $table = TableRegistry::get('Objects');
        $action = new ListObjectsAction(compact('table'));

        $result = $action([
            'filter' => 'published=null',
        ]);

        static::assertInstanceOf(Query::class, $result);
        static::assertSame(9, $result->count());
    }
}
