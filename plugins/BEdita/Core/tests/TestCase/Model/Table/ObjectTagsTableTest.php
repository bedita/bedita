<?php
declare(strict_types=1);

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

use BEdita\Core\Model\Table\ObjectCategoriesTable;
use BEdita\Core\Model\Table\ObjectTagsTable;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \BEdita\Core\Model\Table\ObjectTagsTable} Test Case
 */
#[CoversClass(ObjectTagsTable::class)]
#[CoversMethod(ObjectCategoriesTable::class, 'buildRules')]
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
    protected array $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Tags',
        'plugin.BEdita/Core.ObjectTags',
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
    public static function buildRulesProvider(): array
    {
        return [
            'inValidObject' => [
                false,
                [
                    'object_id' => 1234,
                    'tag_id' => 1,
                ],
            ],
            'inValidTag' => [
                false,
                [
                    'object_id' => 4,
                    'tag_id' => 1234,
                ],
            ],
            'valid' => [
                true,
                [
                    'object_id' => 5,
                    'tag_id' => 1,
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
     */
    #[DataProvider('buildRulesProvider')]
    public function testBuildRules($expected, array $data)
    {
        $entity = $this->ObjectTags->newEntity($data, ['validate' => false]);
        $success = $this->ObjectTags->save($entity);
        $this->assertEquals($expected, (bool)$success, print_r($entity->getErrors(), true));
    }
}
