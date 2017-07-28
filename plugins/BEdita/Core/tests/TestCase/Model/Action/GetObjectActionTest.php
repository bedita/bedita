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

use BEdita\Core\Model\Action\GetObjectAction;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @covers \BEdita\Core\Model\Action\GetObjectAction
 */
class GetObjectActionTest extends TestCase
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
        $action = new GetObjectAction(compact('table'));

        $result = $action(['primaryKey' => 9]);

        static::assertEquals($table->get(9, ['contain' => ['ObjectTypes']]), $result);
    }

    /**
     * Test command execution with filter by object type.
     *
     * @return void
     *
     * @expectedException \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function testExecuteObjectTypeFilter()
    {
        $objectType = TableRegistry::get('ObjectTypes')->get('Events');
        $table = TableRegistry::get('Objects');
        $action = new GetObjectAction(compact('table', 'objectType'));

        $action(['primaryKey' => 8]);
    }

    /**
     * Test command execution with filter by deletion status.
     *
     * @return void
     *
     * @expectedException \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function testExecuteObjectDeleted()
    {
        $table = TableRegistry::get('Objects');
        $action = new GetObjectAction(compact('table'));

        $action(['primaryKey' => 2, 'deleted' => true]);
    }
}
