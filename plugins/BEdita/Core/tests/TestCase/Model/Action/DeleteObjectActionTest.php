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

use BEdita\Core\Model\Action\DeleteObjectAction;
use BEdita\Core\Utility\LoggedUser;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @covers \BEdita\Core\Model\Action\DeleteObjectAction
 */
class DeleteObjectActionTest extends TestCase
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
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
        'plugin.BEdita/Core.History',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        LoggedUser::setUser(['id' => 1]);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        LoggedUser::resetUser();
    }

    /**
     * Test command execution.
     *
     * @return void
     */
    public function testExecute()
    {
        $table = TableRegistry::getTableLocator()->get('Documents');
        $action = new DeleteObjectAction(compact('table'));

        $entity = $table->get(3);

        $result = $action(compact('entity'));

        static::assertTrue($result);
        static::assertTrue($table->exists(['id' => 3]));
        static::assertTrue($table->get(3)->get('deleted'));
    }

    /**
     * Test command execution with hard delete.
     *
     * @return void
     */
    public function testExecuteHardDelete()
    {
        $table = TableRegistry::getTableLocator()->get('Documents');
        $action = new DeleteObjectAction(compact('table'));

        $entity = $table->get(3);

        $result = $action(compact('entity') + ['hard' => true]);

        static::assertTrue($result);
        static::assertFalse($table->exists(['id' => 3]));
    }
}
