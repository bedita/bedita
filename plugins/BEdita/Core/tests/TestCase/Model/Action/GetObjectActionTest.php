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
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.DateRanges',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Translations',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
    ];

    /**
     * Test command execution.
     *
     * @return void
     */
    public function testExecute()
    {
        $table = TableRegistry::getTableLocator()->get('Objects');
        $action = new GetObjectAction(compact('table'));

        $result = $action(['primaryKey' => 9]);

        static::assertEquals($table->get(9, ['contain' => ['ObjectTypes']]), $result);
    }

    /**
     * Test command execution with filter by object type.
     *
     * @return void
     */
    public function testExecuteObjectTypeFilter()
    {
        $this->expectException(\Cake\Datasource\Exception\RecordNotFoundException::class);
        $objectType = TableRegistry::getTableLocator()->get('ObjectTypes')->get('Events');
        $table = TableRegistry::getTableLocator()->get('Objects');
        $action = new GetObjectAction(compact('table', 'objectType'));

        $action(['primaryKey' => 8]);
    }

    /**
     * Test command execution with filter by deletion status.
     *
     * @return void
     */
    public function testExecuteObjectDeleted()
    {
        $this->expectException(\Cake\Datasource\Exception\RecordNotFoundException::class);
        $table = TableRegistry::getTableLocator()->get('Objects');
        $action = new GetObjectAction(compact('table'));

        $action(['primaryKey' => 2, 'deleted' => true]);
    }

    /**
     * Test command execution filter with deleted and locked filter.
     *
     * @return void
     */
    public function testExecuteObjectDeletedLocked()
    {
        $this->expectException(\Cake\Datasource\Exception\RecordNotFoundException::class);
        $table = TableRegistry::getTableLocator()->get('Objects');
        $action = new GetObjectAction(compact('table'));

        $action(['primaryKey' => 15, 'deleted' => true, 'locked' => false]);
    }

    /**
     * Test command execution with conditions on objects status.
     *
     * @return void
     */
    public function testExecuteObjectStatusNotAvailable()
    {
        $this->expectException(\Cake\Datasource\Exception\RecordNotFoundException::class);
        Configure::write('Status.level', 'on');

        $table = TableRegistry::getTableLocator()->get('Objects');
        $action = new GetObjectAction(compact('table'));

        $action(['primaryKey' => 3]);
    }

    /**
     * Test command execution with an invalid primary key.
     *
     * @return void
     */
    public function testExecuteInvalidPrimaryKey()
    {
        $this->expectException(\Cake\Datasource\Exception\InvalidPrimaryKeyException::class);
        $this->expectExceptionMessage('Record not found in table "objects" with primary key [1, 2]');
        $table = TableRegistry::getTableLocator()->get('Objects');
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
        $objectType = TableRegistry::getTableLocator()->get('ObjectTypes')->get('Documents');
        $table = TableRegistry::getTableLocator()->get('Objects');
        $action = new GetObjectAction(compact('table', 'objectType'));

        $result = $action(['primaryKey' => 2, 'lang' => 'fr']);

        static::assertNotEmpty($result);
        static::assertNotEmpty($result['translations']);
        static::assertEquals(1, count($result['translations']));
        static::assertEquals(2, $result['translations'][0]['id']);
    }
}
