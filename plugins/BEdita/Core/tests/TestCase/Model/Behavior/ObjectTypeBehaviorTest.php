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

namespace BEdita\Core\Test\TestCase\Model\Behavior;

use BEdita\Core\Model\Entity\ObjectType;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Model\Behavior\ObjectTypeBehavior
 */
class ObjectTypeBehaviorTest extends TestCase
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
    ];

    /**
     * Data provider for `testObjectType` test case.
     *
     * @return array
     */
    public function objectTypeProvider()
    {
        return [
            'getter' => [
                'documents',
                'Documents',
            ],
            'setter' => [
                'documents',
                'Documents',
                2,
            ],
            'setter by name' => [
                'documents',
                'Documents',
                'document',
            ],
        ];
    }

    /**
     * Test `objectType` getter/setter.
     *
     * @param string|string $expected Expected result.
     * @param string $table Table.
     * @param int|string|null $objectType Object type being set.
     * @return void
     *
     * @dataProvider objectTypeProvider()
     * @covers ::objectType()
     */
    public function testObjectType($expected, $table, $objectType = null)
    {
        $table = TableRegistry::get($table);
        if (!$table->hasBehavior('ObjectType')) {
            $table->addBehavior('BEdita/Core.ObjectType');
        }
        $behavior = $table->behaviors()->get('ObjectType');

        static::assertTrue($table->behaviors()->hasMethod('objectType'));

        $objectType = $table->behaviors()->call('objectType', [$objectType]);

        if ($expected === null) {
            static::assertNull($objectType);
            static::assertAttributeSame(null, 'objectType', $behavior);
        } else {
            static::assertInstanceOf(ObjectType::class, $objectType);
            static::assertAttributeInstanceOf(ObjectType::class, 'objectType', $behavior);
            static::assertSame($expected, $objectType->name);
        }
    }
}
