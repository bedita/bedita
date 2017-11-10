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
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

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

        static::assertEquals(1, $objectType->id);
        static::assertEquals('patched_name', $objectType->name);
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
            'alias' => 'Documents',
            'table' => 'BEdita/Core.Objects',
            'relations' => [
                'test',
                'inverse_test',
            ],
            'parent_name' => 'objects',
        ];

        $objectType = $this->ObjectTypes->get(2);

        static::assertEquals($expected, $objectType->extract($objectType->getVirtual()));
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

        static::assertEquals('foo_bar', $objectType->name);
    }

    /**
     * Data provider for `testGetSetSingular` test case.
     *
     * @return array
     */
    public function getSetSingularProvider()
    {
        return [
            'default' => ['foo_bar', 'foo_bars', 'FooBar'],
            'missing' => ['foo_bar', 'foo_bars', null],
        ];
    }

    /**
     * Test getter/setter method for `singular`.
     *
     * @param string $expected Expected result.
     * @param string $name Object type name.
     * @param string|null $singular Object type singular name.
     * @return void
     *
     * @dataProvider getSetSingularProvider
     * @covers ::_getSingular()
     * @covers ::_setSingular()
     */
    public function testGetSetSingular($expected, $name, $singular)
    {
        $data = compact('name', 'singular');
        $objectType = $this->ObjectTypes->newEntity($data);

        static::assertEquals($expected, $objectType->singular);
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
            'name' => 'foo_bars',
        ];
        $objectType = $this->ObjectTypes->newEntity($data);

        static::assertEquals('FooBars', $objectType->alias);
    }

    /**
     * Data provider for `testGetSetTable` test case.
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

        static::assertEquals($expectedPlugin, $objectType->plugin);
        static::assertEquals($expectedModel, $objectType->model);
        static::assertEquals($expected, $objectType->table);
    }

    /**
     * Test getter for relations.
     *
     * @return void
     *
     * @covers ::_getRelations()
     */
    public function testGetRelations()
    {
        $expected = [
            'inverse_test',
        ];
        $objectType = $this->ObjectTypes->get(3);

        static::assertEquals($expected, $objectType->relations, '', 0, 10, true);
    }

    /**
     * Test getter for relations when associations haven't been loaded.
     *
     * @return void
     *
     * @covers ::_getRelations()
     */
    public function testGetRelationsAssociationsNotLoaded()
    {
        $objectType = $this->ObjectTypes->find()
            ->contain(['LeftRelations'], true)
            ->firstOrFail();

        static::assertInstanceOf($this->ObjectTypes->getEntityClass(), $objectType);
        static::assertNull($objectType->relations);
    }

    /**
     * Test that `relations` association was removed serializing entity
     *
     * @return void
     *
     * @covers ::listAssociations()
     */
    public function testListAssociations()
    {
        $objectType = $this->ObjectTypes->get(1);

        $result = $objectType->jsonApiSerialize();

        static::assertArrayHasKey('relationships', $result);
        static::assertArrayHasKey('left_relations', $result['relationships']);
        static::assertArrayHasKey('right_relations', $result['relationships']);
        static::assertArrayNotHasKey('relations', $result['relationships']);
    }

    /**
     * Data provider for `testGetSetParentName` test case.
     *
     * @return array
     */
    public function getSetParentNameProvider()
    {
        return [
            'objects' => [
                'objects',
                null,
                'not_found',
                null,
            ],
            'documents' => [
                'documents',
                'objects',
                'objects',
                'objects',
            ],
            'profilesBadParent' => [
                'profiles',
                'objects',
                'documents',
                'objects',
            ],
        ];
    }

    /**
     * Test getter/setter method for `parent_name`.
     *
     * @param string $name Object type name.
     * @param string|null $getExpected Expected parent name result.
     * @param string $newParent New parent name to set.
     * @param string|null $setExpected Parent name set expected result.
     * @return void
     *
     * @dataProvider getSetParentNameProvider
     * @covers ::_getParentName()
     * @covers ::_setParentName()
     */
    public function testGetSetParentName($name, $getExpected, $newParent, $setExpected)
    {
        $objectType = $this->ObjectTypes->get($name);

        static::assertEquals($getExpected, $objectType->parent_name);
        $objectType->set('parent_name', $newParent);
        static::assertEquals($setExpected, $objectType->parent_name);
    }
}
