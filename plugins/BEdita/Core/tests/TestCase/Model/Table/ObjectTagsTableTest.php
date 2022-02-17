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

/**
 * {@see \BEdita\Core\Model\Table\ObjectTagsTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\ObjectTagsTable
 */
class ObjectTagsTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\ObjectTagsTable
     */
    public $ObjectTags;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ObjectTags = TableRegistry::getTableLocator()->get('ObjectTags');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ObjectTags);

        parent::tearDown();
    }

    /**
     * Data provider for `testBuildRules` test case.
     *
     * @return array
     */
    public function buildRulesProvider()
    {
        return [
            'inValidObject' => [
                false,
                [
                    'object_id' => 1234,
                    'category_id' => 4,
                ],
            ],
            'inValidTag' => [
                false,
                [
                    'object_id' => 4,
                    'category_id' => 1234,
                ],
            ],
            'valid' => [
                true,
                [
                    'object_id' => 5,
                    'category_id' => 4,
                ],
            ],
        ];
    }

    /**
     * Test build rules validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     *
     * @return void
     * @dataProvider buildRulesProvider
     * @covers \BEdita\Core\Model\Table\ObjectCategoriesTable::buildRules()
     */
    public function testBuildRules($expected, array $data)
    {
        $entity = $this->ObjectTags->newEntity($data, ['validate' => false]);
        $success = $this->ObjectTags->save($entity);
        $this->assertEquals($expected, (bool)$success, print_r($entity->getErrors(), true));
    }
}
