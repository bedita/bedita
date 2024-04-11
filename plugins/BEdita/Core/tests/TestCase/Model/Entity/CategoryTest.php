<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Model\Entity\Category;
use Cake\TestSuite\TestCase;

/**
 *  {@see \BEdita\Core\Model\Entity\Category} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\Category
 */
class CategoryTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
    ];

    /**
     * Test `_getObject` method.
     *
     * @return void
     * @covers ::_getObject()
     */
    public function testGetObject(): void
    {
        $category = $this->fetchTable('Categories')->get(1);
        static::assertEquals('documents', $category->get('object'));
    }

    /**
     * Test `_setObject` method.
     *
     * @return void
     * @covers ::_setObject()
     */
    public function testSetObject(): void
    {
        $category = $this->fetchTable('Categories')->newEmptyEntity();
        $category->set('object', 'documents');
        static::assertEquals(2, $category->get('object_type_id'));
    }

    /**
     * Data provider for `testGetParent` test case.
     *
     * @return array
     */
    public function getParentProvider()
    {
        return [
            'no parent' => [
                null,
                null,
            ],
            'first parent' => [
                'first-cat',
                1,
            ],
            'not found' => [
                null,
                99,
            ],
        ];
    }

    /**
     * Test `_getParent` method.
     *
     * @param string|null $expected Expected parent name.
     * @param int|null $parentId Parent ID.
     * @return void
     * @covers ::_getParent()
     * @dataProvider getParentProvider()
     */
    public function testGetParent(?string $expected, ?int $parentId): void
    {
        $entity = new Category();
        $entity->setSource('Categories');
        $entity->parent_id = $parentId;
        static::assertSame($expected, $entity->get('parent'));
    }

    /**
     * Data provider for `testSetParent` test case.
     *
     * @return array
     */
    public function setParentProvider()
    {
        return [
            'no parent' => [
                null,
                null,
            ],
            'first parent' => [
                1,
                'first-cat',
            ],
            'not found' => [
                null,
                'some-cat',
            ],
        ];
    }

    /**
     * Test `_setParent` method.
     *
     * @param int|null $expected Expected parent ID.
     * @param ?string $parent Parent name.
     * @return void
     * @covers ::_setParent()
     * @dataProvider setParentProvider()
     */
    public function testSetParent(?int $expected, ?string $parent): void
    {
        $entity = new Category();
        $entity->setSource('Categories');
        $entity->object_type_id = 2;
        $entity->set('parent', $parent);
        static::assertSame($expected, $entity->parent_id);
    }

    /**
     * Test `_getLabel` methods.
     *
     * @return void
     * @covers ::_getLabel()
     */
    public function testGetLabel(): void
    {
        $category = $this->fetchTable('Categories')->get(1);
        static::assertEquals('First category', $category->get('label'));
    }

    /**
     * Test `_setLabel` methods.
     *
     * @return void
     * @covers ::_getLabel()
     * @covers ::_setLabel()
     */
    public function testSetLabel(): void
    {
        $category = $this->fetchTable('Categories')->newEmptyEntity();
        $category->set('label', 'New label');
        static::assertEquals('New label', $category->get('label'));
    }
}
