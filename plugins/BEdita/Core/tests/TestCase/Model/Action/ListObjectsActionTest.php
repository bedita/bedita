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

use BEdita\Core\Model\Action\ListObjectsAction;
use Cake\Core\Configure;
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
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.DateRanges',
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
        $action = new ListObjectsAction(compact('table'));

        $result = $action();

        static::assertInstanceOf(Query::class, $result);
        static::assertSame(13, $result->count());
    }

    /**
     * Test command execution with filter by object type.
     *
     * @return void
     */
    public function testExecuteObjectTypeFilter()
    {
        $objectType = TableRegistry::getTableLocator()->get('ObjectTypes')->get('Events');
        $table = TableRegistry::getTableLocator()->get('Objects');
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
        $table = TableRegistry::getTableLocator()->get('Objects');
        $action = new ListObjectsAction(compact('table'));

        $result = $action(['deleted' => true]);

        static::assertInstanceOf(Query::class, $result);
        static::assertSame(3, $result->count());
    }

    /**
     * Test command execution with a custom filter.
     *
     * @return void
     */
    public function testExecuteCustomFilter()
    {
        $table = TableRegistry::getTableLocator()->get('Objects');
        $action = new ListObjectsAction(compact('table'));

        $result = $action([
            'filter' => 'published=null',
        ]);

        static::assertInstanceOf(Query::class, $result);
        static::assertSame(11, $result->count());
    }

    /**
     * Test command execution with constraints on objects status.
     *
     * @return void
     */
    public function testExecuteStatus()
    {
        Configure::write('Status.level', 'on');

        $table = TableRegistry::getTableLocator()->get('Objects');
        $action = new ListObjectsAction(compact('table'));

        $result = $action();

        static::assertInstanceOf(Query::class, $result);
        static::assertSame(12, $result->count());
    }

    /**
     * Test command execution with a `lang` query string.
     *
     * @return void
     */
    public function testLang()
    {
        $objectType = TableRegistry::getTableLocator()->get('ObjectTypes')->get('Documents');
        $table = TableRegistry::getTableLocator()->get('Objects');
        $action = new ListObjectsAction(compact('table', 'objectType'));

        $result = $action([
            'lang' => 'fr',
        ]);

        static::assertInstanceOf(Query::class, $result);
        static::assertSame(2, $result->count());
        $result->order(['Objects.id' => 'ASC']);
        $result = $result->toArray();

        static::assertNotEmpty($result[0]['translations']);
        static::assertEquals(1, count($result[0]['translations']));
        static::assertEquals(2, $result[0]['translations'][0]['id']);
    }
}
