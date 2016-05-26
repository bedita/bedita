<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\ORM\Inheritance;

use BEdita\Core\ORM\Inheritance\EntityPatcher;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\ORM\Inheritance\EntityPatcher} Test Case
 *
 * @coversDefaultClass \BEdita\Core\ORM\Inheritance\EntityPatcher
 */
class EntityPatcherTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.fake_animals',
        'plugin.BEdita/Core.fake_mammals',
        'plugin.BEdita/Core.fake_felines',
        'plugin.BEdita/Core.fake_articles',
    ];

    /**
     * Table FakeAnimals
     *
     * @var \Cake\ORM\Table
     */
    public $fakeAnimals;

    /**
     * Table FakeMammals
     *
     * @var \Cake\ORM\Table
     */
    public $fakeMammals;

    /**
     * Table FakeFelines
     *
     * @var \Cake\ORM\Table
     */
    public $fakeFelines;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->fakeAnimals = TableRegistry::get('FakeAnimals');
        $this->fakeAnimals->hasMany('FakeArticles');

        $this->fakeMammals = TableRegistry::get('FakeMammals');
        $this->fakeMammals->addBehavior('BEdita/Core.ClassTableInheritance', [
            'table' => [
                'tableName' => 'FakeAnimals'
            ]
        ]);

        $this->fakeFelines = TableRegistry::get('FakeFelines');
        $this->fakeFelines->addBehavior('BEdita/Core.ClassTableInheritance', [
            'table' => [
                'tableName' => 'FakeMammals'
            ]
        ]);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown()
    {
        TableRegistry::clear();
        unset($this->fakeFelines);
        unset($this->fakeMammals);
        parent::tearDown();
    }

    /**
     * testNewEntityPatcherWithWrongTable method
     *
     * @return void
     * @covers ::__construct()
     */
    public function testNewEntityPatcherWithWrongTable()
    {
        $this->setExpectedException('\InvalidArgumentException');
        new EntityPatcher($this->fakeAnimals);
    }

    /**
     * testFlatten method
     *
     * @param bool $hydrate If entity must be hydrated from $data
     * @param array $data The data to flatten
     * @return void
     *
     * @covers ::flatten()
     * @covers ::flattenEntityProperty()
     * @covers ::flattenArrayProperty()
     */
    public function testFlatten()
    {
        $data = [
            'family' => 'roaring cats',
            'fake_mammal' => [
                'subclass' => 'Eutheria',
                'fake_animal' => [
                    'legs' => 4,
                    'name' => 'lion',
                ]
            ]
        ];

        $expected = [
            'name' => 'lion',
            'legs' => 4,
            'subclass' => 'Eutheria',
            'family' => 'roaring cats'
        ];
        ksort($expected);

        $entityPatcher = new EntityPatcher($this->fakeFelines);

        // flatten entity
        $entity = $this->fakeFelines->newEntity($data, [
            'associated' => ['FakeMammals.FakeAnimals']
        ]);

        // clean dirty
        $entity->clean();
        $entity->fake_mammal->clean();
        $entity->fake_mammal->fake_animal->clean();

        $entity = $entityPatcher->flatten($entity);
        $result = $entity->extract($entity->visibleProperties());
        ksort($result);

        $this->assertEquals($expected, $result);
        $this->assertFalse($entity->dirty());

        // flatten array
        $result = $entityPatcher->flatten($data);
        ksort($result);
        $this->assertEquals($expected, $result);
    }
}
