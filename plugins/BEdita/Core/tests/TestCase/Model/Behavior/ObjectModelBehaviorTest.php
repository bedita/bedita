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

use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Model\Behavior\ObjectModelBehavior} Test Case
 *
 * @property \BEdita\Core\Model\Table\ObjectsTable $Documents
 * @coversDefaultClass \BEdita\Core\Model\Behavior\ObjectModelBehavior
 */
#[\AllowDynamicProperties]
class ObjectModelBehaviorTest extends TestCase
{
    use LocatorAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.FakeAnimals',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
    ];

    /**
     * Test `initialize` method.
     *
     * @covers ::initialize()
     */
    public function testInitialize(): void
    {
        $table = $this->fetchTable('FakeAnimals');
        $count = $table->behaviors()->count();
        static::assertEquals(0, $count);
        $table->addBehavior('BEdita/Core.ObjectModel');
        $count = $table->behaviors()->count();
        static::assertEquals(11, $count);
    }

    /**
     * Test `addRelated` method.
     *
     * @covers ::addRelated()
     */
    public function testAddRelated(): void
    {
        $this->Documents = $this->fetchTable('Documents');
        $entity = $this->Documents->get(3);
        $related = $this->Documents->get(2);
        $this->Documents->addRelated($entity, 'test', [$related]);

        $entity = $this->Documents->get(3, ['contain' => 'Test']);
        $ids = Hash::extract($entity->get('test'), '{n}.id');
        sort($ids);
        static::assertEquals([2, 4], $ids);
    }

    /**
     * Test `replaceRelated` method.
     *
     * @covers ::replaceRelated()
     */
    public function testReplaceRelated(): void
    {
        $this->Documents = $this->fetchTable('Documents');
        $entity = $this->Documents->get(3);
        $related = $this->Documents->get(2);
        $this->Documents->replaceRelated($entity, 'test', [$related]);

        $entity = $this->Documents->get(3, ['contain' => 'Test']);
        $ids = Hash::extract($entity->get('test'), '{n}.id');
        static::assertEquals([2], $ids);
    }

    /**
     * Test `removeRelated` method.
     *
     * @covers ::removeRelated()
     */
    public function testRemoveRelated(): void
    {
        $this->Documents = $this->fetchTable('Documents');
        $entity = $this->Documents->get(3);
        $related = $this->Documents->get(4);
        $this->Documents->removeRelated($entity, 'test', [$related]);

        $entity = $this->Documents->get(3, ['contain' => 'Test']);
        $ids = Hash::extract($entity->get('test'), '{n}.id');
        static::assertEquals([], $ids);
    }
}
