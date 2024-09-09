<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Action;

use BEdita\Core\Model\Action\DeleteEntitiesAction;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Action\DeleteEntitiesAction} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Action\DeleteEntitiesAction
 */
class DeleteEntitiesActionTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.FakeAnimals',
    ];

    /**
     * Test command execution.
     *
     * @return void
     * @covers ::initialize()
     * @covers ::execute()
     */
    public function testExecute()
    {
        $table = TableRegistry::getTableLocator()->get('FakeAnimals');
        $action = new DeleteEntitiesAction(compact('table'));
        $entities = [$table->get(1)];
        $actual = $action(compact('entities'));
        static::assertIsArray($actual);
        static::assertCount(1, $actual);
        static::assertFalse($table->exists(['id' => 1]));
    }
}
