<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Action;

use BEdita\Core\Model\Action\ListAssociated;
use BEdita\Core\Model\Action\ListRelatedObjects;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;

/**
 * @coversDefaultClass \BEdita\Core\Model\Action\ListRelatedObjects
 */
class ListRelatedObjectsTest extends TestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.object_relations',
        'plugin.BEdita/Core.profiles',
    ];

    /**
     * Test constructor with a table that does not have the required behavior.
     *
     * @return void
     *
     * @covers ::__construct()
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Table "ObjectTypes" does not implement relations
     */
    public function testConstructInvalidTable()
    {
        new ListRelatedObjects(TableRegistry::get('ObjectTypes')->association('Properties'));
    }

    /**
     * Test constructor with valid arguments.
     *
     * @return void
     *
     * @covers ::__construct()
     */
    public function testConstruct()
    {
        $Action = new ListRelatedObjects(TableRegistry::get('Documents')->association('Test'), 'test');

        static::assertAttributeInstanceOf(BelongsToMany::class, 'Association', $Action);
        static::assertAttributeInstanceOf(ListAssociated::class, 'Action', $Action);
    }

    /**
     * Data provider for `testInvocation` test case.
     *
     * @return array
     */
    public function invocationProvider()
    {
        return [
            [
                [
                    [
                        'id' => 4,
                        'type' => 'profiles',
                    ],
                    [
                        'id' => 3,
                        'type' => 'documents',
                    ],
                ],
                'Documents',
                'test',
                2,
            ],
            [
                [
                    [
                        'id' => 4,
                        'type' => 'profiles',
                    ],
                ],
                'Documents',
                'test',
                3,
            ],
            [
                [
                    [
                        'id' => 3,
                        'type' => 'documents',
                    ],
                    [
                        'id' => 2,
                        'type' => 'documents',
                    ],
                ],
                'Profiles',
                'inverse_test',
                4,
            ],
        ];
    }

    /**
     * Test command invocation.
     *
     * @param array $expected Expected result.
     * @param string $objectType Object type name.
     * @param string $relation Relation name.
     * @param int $id ID.
     * @return void
     *
     * @covers ::__invoke()
     * @dataProvider invocationProvider()
     */
    public function testInvocation($expected, $objectType, $relation, $id)
    {
        $alias = Inflector::camelize(Inflector::underscore($relation));
        $Action = new ListRelatedObjects(TableRegistry::get($objectType)->association($alias));

        $result = json_decode(json_encode($Action($id)->toArray()), true);

        static::assertEquals($expected, $result);
    }
}
