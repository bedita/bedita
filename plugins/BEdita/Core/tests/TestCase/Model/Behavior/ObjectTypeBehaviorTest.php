<?php
declare(strict_types=1);

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

use BEdita\Core\Model\Behavior\ObjectTypeBehavior;
use BEdita\Core\Model\Entity\ObjectType;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 * {@see \BEdita\Core\Model\Behavior\ObjectTypeBehavior} Test Case
 */
#[CoversClass(ObjectTypeBehavior::class)]
class ObjectTypeBehaviorTest extends TestCase
{
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
    ];

    /**
     * Data provider for `testObjectType` test case.
     *
     * @return array
     */
    public static function objectTypeProvider(): array
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
     */
    #[DataProvider('objectTypeProvider')]
    public function testObjectType($expected, $table, $objectType = null)
    {
        $table = TableRegistry::getTableLocator()->get($table);
        if (!$table->hasBehavior('ObjectType')) {
            $table->addBehavior('BEdita/Core.ObjectType');
        }

        /** @var \BEdita\Core\Model\Behavior\ObjectTypeBehavior $objectTypeBehavior */
        $objectTypeBehavior = $table->getBehavior('ObjectType');
        $objectType = $objectTypeBehavior->objectType($objectType);

        if ($expected === null) {
            static::assertNull($objectType);
        } else {
            static::assertInstanceOf(ObjectType::class, $objectType);
            static::assertSame($expected, $objectType->name);
        }
    }
}
