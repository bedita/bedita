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

use BEdita\Core\Exception\BadFilterException;
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
            'object_type_name' => 'documents',
            'name' => 'first-cat',
            'label' => 'First category',
            'parent_id' => null,
            'tree_left' => 1,
            'tree_right' => 2,
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
     * Test find enabled categories
     *
     * @return void
     * @coversNothing
     */
    public function testFindEnabledCategories()
    {
        $categories = $this->Categories->find('enabled')->toArray();
        static::assertEquals([1, 2], Hash::extract($categories, '{n}.id'));
    }

    /**
     * Test find categories by type
     *
     * @return void
     * @covers ::findType()
     */
    public function testFindCategoriesType()
    {
        $order = [
            $this->Categories->aliasField('id') => 'ASC',
        ];
        $categories = $this->Categories->find('type', ['documents'])->order($order)->toArray();
        static::assertEquals([1, 2, 3], Hash::extract($categories, '{n}.id'));

        $categories = $this->Categories->find('type', ['news'])->order($order)->toArray();
        static::assertEquals([], $categories);
    }

    /**
     * Test find categories by type failure
     *
     * @return void
     * @covers ::findType()
     */
    public function testFindCategoriesTypeFail(): void
    {
        $this->expectException(BadFilterException::class);
        $this->expectExceptionMessage('Missing required parameter "type"');

        $this->Categories->find('type')->toArray();
    }

    /**
     * Data provider for `testFindResource()`.
     *
     * @return array
     */
    public function findResourceProvider(): array
    {
        return [
            'category' => [
                1,
                [
                    'name' => 'first-cat',
                    'object_type_name' => 'documents',
                ],
            ],
            'no name' => [
                new BadFilterException('Missing required parameter "name"'),
                [
                    'object_type_name' => 'documents',
                ],
            ],
            'no type' => [
                new BadFilterException('Missing required parameter "object_type_name"'),
                [
                    'name' => 'a-name',
                ],
            ],
        ];
    }

    /**
     * Test custom finder `findResource()`.
     *
     * @param int|\Exception $expected The value expected
     * @param array $options The options for the finder
     * @return void
     *
     * @covers ::findResource()
     * @dataProvider findResourceProvider()
     */
    public function testFindResource($expected, $options): void
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }
        $query = $this->Categories->find('resource', $options);
        $entity = $query->first();

        static::assertEquals(1, $query->count());
        static::assertEquals($expected, $entity->id);
    }
}
