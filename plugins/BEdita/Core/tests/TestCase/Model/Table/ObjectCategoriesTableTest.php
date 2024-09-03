<?php
declare(strict_types=1);

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
 * {@see \BEdita\Core\Model\Table\ObjectCategoriesTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\ObjectCategoriesTable
 */
class ObjectCategoriesTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\ObjectCategoriesTable
     */
    public $ObjectCategories;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
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
        $this->ObjectCategories = TableRegistry::getTableLocator()->get('ObjectCategories');
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ObjectCategories);

        parent::tearDown();
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'ok' => [
                [],
                [
                    'category_id' => 2,
                    'object_id' => 6,
                ],
            ],
            'invalid' => [
                [
                    'object_id._required',
                ],
                [
                    'category_id' => 2,
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param string[] $expected Expected errors.
     * @param array $data Data.
     * @return void
     * @dataProvider validationProvider
     * @covers ::validationDefault()
     */
    public function testValidation(array $expected, array $data)
    {
        $entity = $this->ObjectCategories->newEntity([]);
        $entity = $this->ObjectCategories->patchEntity($entity, $data);
        $errors = array_keys(Hash::flatten($entity->getErrors()));

        static::assertEquals($expected, $errors);
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
                    'category_id' => 1,
                ],
            ],
            'inValidCategory' => [
                false,
                [
                    'object_id' => 4,
                    'category_id' => 1234,
                ],
            ],
        ];
    }

    /**
     * Test build rules validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     * @return void
     * @dataProvider buildRulesProvider
     * @covers ::buildRules()
     */
    public function testBuildRules($expected, array $data)
    {
        $entity = $this->ObjectCategories->newEntity($data, ['validate' => false]);
        $success = $this->ObjectCategories->save($entity);
        $this->assertEquals($expected, (bool)$success, print_r($entity->getErrors(), true));
    }
}
