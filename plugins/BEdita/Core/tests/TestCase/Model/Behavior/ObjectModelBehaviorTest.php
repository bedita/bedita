<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Behavior;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Model\Behavior\ObjectModelBehavior
 */
class ObjectModelBehaviorTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.FakeAnimals',
        'plugin.BEdita/Core.ObjectTypes',
    ];

    /**
     * Test `initialize` method.
     *
     * @covers ::initialize()
     */
    public function testInitialize()
    {
        $table = TableRegistry::get('FakeAnimals');
        $count = $table->behaviors()->count();
        static::assertEquals(0, $count);
        $table->addBehavior('BEdita/Core.ObjectModel');
        $count = $table->behaviors()->count();
        static::assertEquals(9, $count);
    }
}
