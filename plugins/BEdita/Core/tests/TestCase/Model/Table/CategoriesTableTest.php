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

use BEdita\Core\Model\Table\CategoriesTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

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
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
    ];

    /**
     * Test `beforeFind` method
     *
     * @return void
     */
    public function testBeforeFindPrimary()
    {
        $category = TableRegistry::getTableLocator()->get('Categories')->get(1)->toArray();
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
}
