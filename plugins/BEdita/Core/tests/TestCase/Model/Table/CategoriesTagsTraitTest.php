<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
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
 * {@see \BEdita\Core\Model\Table\CategoriesTagsTrait} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\CategoriesTagsTrait
 */
class CategoriesTagsTraitTest extends TestCase
{
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
     * Test `removeFields` method
     *
     * @return void
     * @covers ::removeFields()
     */
    public function testRemoveFields()
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
     * Test `testFindEnabled` method
     *
     * @return void
     * @covers ::testFindEnabled()
     */
    public function testFindEnabled()
    {
        $categories = TableRegistry::getTableLocator()
            ->get('Categories')
            ->find('enabled')
            ->toArray();
        static::assertEquals([1, 2], Hash::extract($categories, '{n}.id'));
    }

    /**
     * Test `idsByNames` method
     *
     * @return void
     * @covers ::idsByNames()
     */
    public function testIdsByNames()
    {
        $table = TableRegistry::getTableLocator()->get('Tags');
        $tags = $table->find('ids', ['names' => ['first-tag']])->toArray();
        static::assertEquals(1, count($tags));
        static::assertEquals(4, $tags[0]['id']);

        $tags = $table->find('ids', ['names' => ['tag-1', 'tag-2']])->toArray();
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
        TableRegistry::getTableLocator()->get('Tags')->find('ids', ['names' => 42])->toArray();
    }
}
