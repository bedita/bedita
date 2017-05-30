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

use BEdita\Core\Model\Action\SaveEntityAction;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Validation\Validator;

/**
 * @covers \BEdita\Core\Model\Action\SaveEntityAction
 */
class SaveEntityActionTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.fake_animals',
    ];

    /**
     * Test command execution.
     *
     * @return void
     */
    public function testExecute()
    {
        $table = TableRegistry::get('FakeAnimals');
        $action = new SaveEntityAction(compact('table'));

        $entity = $table->newEntity();
        $data = [
            'name' => 'monkey',
            'legs' => 2,
        ];

        $result = $action(compact('entity', 'data'));

        static::assertInstanceOf(get_class($entity), $result);
        static::assertTrue($table->exists(['id' => $entity->id]));
    }

    /**
     * Test command execution with validation errors.
     *
     * @return void
     *
     * @expectedException \Cake\Network\Exception\BadRequestException
     */
    public function testExecuteErrors()
    {
        $table = TableRegistry::get('FakeAnimals');
        $table->validator(
            $table::DEFAULT_VALIDATOR,
            (new Validator())
                ->requirePresence('name')
                ->greaterThan('legs', 2)
        );
        $action = new SaveEntityAction(compact('table'));

        $entity = $table->get(1);
        $data = [
            'legs' => 1,
        ];

        $action(compact('entity', 'data'));
    }
}
