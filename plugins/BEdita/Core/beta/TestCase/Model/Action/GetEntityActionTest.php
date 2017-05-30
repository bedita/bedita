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

use BEdita\Core\Model\Action\GetEntityAction;
use BEdita\Core\ORM\Inheritance\Table;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @covers \BEdita\Core\Model\Action\GetEntityAction
 */
class GetEntityActionTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.fake_animals',
        'plugin.BEdita/Core.fake_articles',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        TableRegistry::get('FakeAnimals', ['className' => Table::class])
            ->hasMany('FakeArticles');
    }

    /**
     * Test command execution.
     *
     * @return void
     */
    public function testExecute()
    {
        $table = TableRegistry::get('FakeAnimals');
        $action = new GetEntityAction(compact('table'));

        $result = $action(['primaryKey' => 1]);

        static::assertEquals($table->get(1), $result);
    }

    /**
     * Test command execution with contained entities.
     *
     * @return void
     */
    public function testExecuteContain()
    {
        $table = TableRegistry::get('FakeAnimals');
        $action = new GetEntityAction(compact('table'));

        $result = $action(['primaryKey' => 1, 'contain' => ['FakeArticles']]);

        static::assertEquals($table->get(1, ['contain' => ['FakeArticles']]), $result);
    }
}
