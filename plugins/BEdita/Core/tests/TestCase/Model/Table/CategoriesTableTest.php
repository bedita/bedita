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

namespace BEdita\Core\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Model\Table\CategoriesTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\CategoriesTable
 */
class CategoriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\CategoriesTable
     */
    public $Categories;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->Categories = TableRegistry::getTableLocator()->get('Categories');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Categories);
        parent::tearDown();
    }

    /**
     * Test `beforeFind` method
     *
     * @return void
     * @covers ::beforeFind()
     */
    public function testBeforeFindPrimary()
    {
        $category = $this->Categories->get(1)->toArray();
        $expected = [
            'id' => 1,
            'object_type_id' => 2,
            'name' => 'first-cat',
            'label' => 'First category',
            'parent_id' => null,
            'tree_left' => null,
            'tree_right' => null,
            'enabled' => true,
        ];
        unset($category['created'], $category['modified']);
        static::assertEquals($expected, $category);
    }

    /**
     * Test `beforeFind` method in case of association
     *
     * @return void
     * @covers ::beforeFind()
     */
    public function testBeforeFindAssoc()
    {
        $document = TableRegistry::getTableLocator()->get('Documents')
            ->get(2, ['contain' => ['Categories']])
            ->toArray();
        $expected = [
            [
                'name' => 'first-cat',
                'label' => 'First category',
                'params' => '100',
            ],
            [
                'name' => 'second-cat',
                'label' => 'Second category',
                'params' => null,
            ],
        ];
        static::assertEquals($expected, $document['categories']);
    }

    /**
     * Test `findEnabledCategories` method
     *
     * @return void
     * @covers ::findEnabledCategories()
     */
    public function testFindEnabledCategories()
    {
        $categories = $this->Categories->find('enabledCategories')->toArray();
        static::assertEquals([1, 2], Hash::extract($categories, '{n}.id'));
    }

    /**
     * Test `findEnabledTags` method
     *
     * @return void
     * @covers ::findEnabledCategories()
     */
    public function testFindEnabledTags()
    {
        $categories = $this->Categories->find('enabledTags')->toArray();
        static::assertEquals([4], Hash::extract($categories, '{n}.id'));
    }

    /**
     * Test `findTagsIds` method
     *
     * @return void
     * @covers ::findTagsIds()
     */
    public function testFindTagsIds()
    {
        $tags = $this->Categories->find('tagsIds', ['names' => ['first-tag']])->toArray();
        static::assertEquals(1, count($tags));
        static::assertEquals(4, $tags[0]['id']);

        $tags = $this->Categories->find('tagsIds', ['names' => ['tag-1', 'tag-2']])->toArray();
        static::assertEmpty($tags);
    }

    /**
     * Test `findTagsIds` failure
     *
     * @return void
     * @covers ::findTagsIds()
     */
    public function testFindTagsIdsFail()
    {
        static::expectException('\Cake\Http\Exception\BadRequestException');
        static::expectExceptionMessage('Missing or wrong required parameter "names"');
        $this->Categories->find('tagsIds', ['names' => 42])->toArray();
    }

    /**
     * Test `findCategoriesIds` method
     *
     * @return void
     * @covers ::findCategoriesIds()
     */
    public function testFindCategoriesIds()
    {
        $categories = $this->Categories
            ->find('categoriesIds', ['names' => ['second-cat'], 'typeId' => 2])
            ->toArray();
        static::assertEquals(1, count($categories));
        static::assertEquals(2, $categories[0]['id']);

        $categories = $this->Categories
            ->find('categoriesIds', ['names' => ['first-cat', 'second-cat'], 'typeId' => 4])
            ->toArray();
        static::assertEmpty($categories);
    }

    /**
     * Test `findCategoriesIds` failure
     *
     * @return void
     * @covers ::findCategoriesIds()
     */
    public function testFindCategoriesIdsFail()
    {
        static::expectException('\Cake\Http\Exception\BadRequestException');
        static::expectExceptionMessage('Missing required parameter "typeId"');
        $this->Categories->find('categoriesIds', ['names' => ['second-cat']])->toArray();
    }

    /**
     * Test `findCategoriesIds` failure
     *
     * @return void
     * @covers ::findCategoriesIds()
     */
    public function testFindCategoriesIdsFail2()
    {
        static::expectException('\Cake\Http\Exception\BadRequestException');
        static::expectExceptionMessage('Missing or wrong required parameter "names"');
        $this->Categories->find('categoriesIds', ['typeId' => 2, 'names' => 'unnamed'])->toArray();
    }
}
