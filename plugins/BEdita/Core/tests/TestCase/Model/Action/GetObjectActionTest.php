<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
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
use Cake\Core\Configure;
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
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.properties',
        'plugin.BEdita/Core.date_ranges',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.translations',
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

    /**
     * Test command execution with conditions on objects status.
     *
     * @return void
     *
     * @expectedException \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function testExecuteObjectStatusNotAvailable()
    {
        Configure::write('Status.level', 'on');

        $table = TableRegistry::get('Objects');
        $action = new GetObjectAction(compact('table'));

        $action(['primaryKey' => 3]);
    }

    /**
     * Test command execution with an invalid primary key.
     *
     * @return void
     *
     * @expectedException \Cake\Datasource\Exception\InvalidPrimaryKeyException
     * @expectedExceptionMessage Record not found in table "objects" with primary key [1, 2]
     */
    public function testExecuteInvalidPrimaryKey()
    {
        $table = TableRegistry::get('Objects');
        $action = new GetObjectAction(compact('table'));

        $action(['primaryKey' => [1, 2]]);
    }

    /**
     * Test command execution with lang query string.
     *
     * @return void
     */
    public function testLang()
    {
        $objectType = TableRegistry::get('ObjectTypes')->get('Documents');
        $table = TableRegistry::get('Objects');
        $action = new GetObjectAction(compact('table', 'objectType'));

        $result = $action(['primaryKey' => 2, 'lang' => 'fr']);

        static::assertNotEmpty($result);
        static::assertNotEmpty($result['translations']);
        static::assertEquals(1, count($result['translations']));
        static::assertEquals(2, $result['translations'][0]['id']);
    }
}
