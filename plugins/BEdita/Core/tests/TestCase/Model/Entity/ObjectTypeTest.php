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

namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Model\Entity\ObjectType;
use BEdita\Core\Model\Table\ObjectTypesTable;
use Cake\Cache\Cache;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Entity\ObjectType} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\ObjectType
 */
class ObjectTypeTest extends TestCase
{

    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\ObjectTypesTable
     */
    public $ObjectTypes;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        Cache::clear(false, ObjectTypesTable::CACHE_CONFIG);

        $this->ObjectTypes = TableRegistry::get('ObjectTypes');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->ObjectTypes);

        parent::tearDown();
    }

    /**
     * Test accessible properties.
     *
     * @return void
     * @coversNothing
     */
    public function testAccessible()
    {
        $objectType = $this->ObjectTypes->get(1);

        $data = [
            'id' => 42,
            'name' => 'patched_name',
        ];
        $objectType = $this->ObjectTypes->patchEntity($objectType, $data);
        if (!($objectType instanceof ObjectType)) {
            throw new \InvalidArgumentException();
        }

        $this->assertEquals(1, $objectType->id);
        $this->assertEquals('patched_name', $objectType->name);
    }

    /**
     * Test virtual properties.
     *
     * @return void
     * @coversNothing
     */
    public function testVirtual()
    {
        $expected = [
            'id' => 1,
            'name' => 'document',
            'pluralized' => 'documents',
            'alias' => 'Documents',
            'description' => null,
            'plugin' => 'BEdita/Core',
            'model' => 'Objects',
            'table' => 'BEdita/Core.Objects',
        ];

        $objectType = $this->ObjectTypes->get(1);
        if (!($objectType instanceof ObjectType)) {
            throw new \InvalidArgumentException();
        }

        $this->assertEquals($expected, $objectType->toArray());
    }

    /**
     * Test setter method for `name`.
     *
     * @return void
     * @covers ::_setName()
     */
    public function testSetName()
    {
        $data = [
            'name' => 'FooBar',
        ];
        $objectType = $this->ObjectTypes->newEntity($data);
        if (!($objectType instanceof ObjectType)) {
            throw new \InvalidArgumentException();
        }

        $this->assertEquals('foo_bar', $objectType->name);
    }

    /**
     * Data provider for `testGetSetPluralized` test case.
     *
     * @return array
     */
    public function getSetPluralizedProvider()
    {
        return [
            'default' => ['foo_bars', 'foo_bar', 'FooBars'],
            'missing' => ['foo_bars', 'foo_bar', null],
        ];
    }

    /**
     * Test getter/setter method for `pluralized`.
     *
     * @param string $expected Expected result.
     * @param string $name Object type name.
     * @param string|null $pluralized Object type pluralized name.
     * @return void
     *
     * @dataProvider getSetPluralizedProvider
     * @covers ::_getPluralized()
     * @covers ::_setPluralized()
     */
    public function testGetSetPluralized($expected, $name, $pluralized)
    {
        $data = compact('name', 'pluralized');
        $objectType = $this->ObjectTypes->newEntity($data);
        if (!($objectType instanceof ObjectType)) {
            throw new \InvalidArgumentException();
        }

        $this->assertEquals($expected, $objectType->pluralized);
    }

    /**
     * Test getter method for `alias`.
     *
     * @return void
     * @covers ::_getAlias()
     */
    public function testGetAlias()
    {
        $data = [
            'name' => 'foo_bar',
        ];
        $objectType = $this->ObjectTypes->newEntity($data);
        if (!($objectType instanceof ObjectType)) {
            throw new \InvalidArgumentException();
        }

        $this->assertEquals('FooBars', $objectType->alias);
    }

    /**
     * Data provider for `testGetSetPluralized` test case.
     *
     * @return array
     */
    public function getSetTableProvider()
    {
        return [
            'pluginSyntax' => ['BEdita/Core.Objects', 'BEdita/Core', 'Objects', 'BEdita/Core.Objects'],
            'noPlugin' => ['Objects', null, 'Objects', 'Objects'],
        ];
    }

    /**
     * Test getter/setter method for `table`.
     *
     * @param string $expected Expected result.
     * @param string|null $expectedPlugin Expected plugin.
     * @param string $expectedModel Expected model.
     * @param string|null $table Object type table.
     * @return void
     *
     * @dataProvider getSetTableProvider
     * @covers ::_getTable()
     * @covers ::_setTable()
     */
    public function testGetSetTable($expected, $expectedPlugin, $expectedModel, $table)
    {
        $data = compact('table');
        $objectType = $this->ObjectTypes->newEntity($data);
        if (!($objectType instanceof ObjectType)) {
            throw new \InvalidArgumentException();
        }

        $this->assertEquals($expectedPlugin, $objectType->plugin);
        $this->assertEquals($expectedModel, $objectType->model);
        $this->assertEquals($expected, $objectType->table);
    }
}
