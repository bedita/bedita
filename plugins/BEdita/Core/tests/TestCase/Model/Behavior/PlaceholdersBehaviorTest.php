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

namespace BEdita\Core\Test\TestCase\Model\Behavior;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Behavior\PlaceholdersBehavior} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Behavior\PlaceholdersBehavior
 */
class PlaceholdersBehaviorTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
        'plugin.BEdita/Core.History',
    ];

    /**
     * Data provider for `testBeforeSave` test case.
     *
     * @return array
     */
    public function beforeSaveProvider()
    {
        return [
            'without placeholders' => [
                '<p>This is a text</p>',
                'Documents',
                2,
                [
                    'body' => '<p>This is a text</p>',
                ],
                [],
            ],
            'with placeholders' => [
                '<p>This is a text</p><!-- BE-PLACEHOLDER 10 eyAiZm9vIjogImJhciIgfQ== -->',
                'Documents',
                2,
                [
                    'body' => '<p>This is a text</p><!-- BE-PLACEHOLDER 10 eyAiZm9vIjogImJhciIgfQ== -->',
                ],
                [
                    [
                        'id' => 10,
                        'type' => 'media',
                    ],
                ]
            ],
        ];
    }

    /**
     * Test bodies with placeholder `save`.
     *
     * @param array $expected Expected result.
     * @param string $tableName Table.
     * @param int $id Entity ID.
     * @param array $data Data.
     * @return void
     *
     * @dataProvider beforeSaveProvider()
     * @covers ::beforeSave()
     * @covers ::extractPlaceholders()
     * @covers ::getHTMLAttributes()
     * @covers ::isHTMLAttribute()
     */
    public function testBeforeSave(string $expected, string $tableName, int $id, array $data)
    {
        $table = TableRegistry::getTableLocator()->get($tableName);

        $objectType = $table
            ->getAssociation('ObjectTypes')
            ->get($tableName);
        $options = [];
        if (!empty($objectType->get('associations'))) {
            $options = ['contain' => $objectType->get('associations')];
        }
        $entity = $table->get($id, $options);

        $entity = $table->patchEntity($entity, $data);
        $entity = $table->save($entity);

        static::assertEquals($expected, $entity['body']);
    }
}
