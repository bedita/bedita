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
namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Model\Entity\ObjectTypeNameTrait;
use BEdita\Core\Model\Entity\Property;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;

/**
 *  {@see \BEdita\Core\Model\Entity\ObjectTypeNameTrait} Test Case
 */
#[CoversTrait(ObjectTypeNameTrait::class)]
class ObjectTypeNameTraitTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
    ];

    /**
     * Data provider for `testGetObjectTypeName` test case.
     *
     * @return array
     */
    public static function getObjectTypeNameProvider(): array
    {
        return [
            'document' => [
                'documents',
                2,
            ],
            'non existent' => [
                null,
                -1,
            ],
            'invalid' => [
                null,
                null,
            ],
        ];
    }

    /**
     * Test magic getter for object type name property.
     *
     * @param string|null $expected Expected object type name.
     * @param mixed $objectTypeId Object type ID.
     * @return void
     */
    #[DataProvider('getObjectTypeNameProvider')]
    public function testGetObjectTypeName($expected, $objectTypeId)
    {
        $entity = new Property();
        $entity->object_type_id = $objectTypeId;

        $objectTypeName = $entity->object_type_name;

        static::assertSame($expected, $objectTypeName);
    }

    /**
     * Data provider for `testSetObjectTypeName` test case.
     *
     * @return array
     */
    public static function setObjectTypeNameProvider(): array
    {
        return [
            'document' => [
                2,
                'documents',
            ],
            'non existent' => [
                null,
                'this type does not exist',
            ],
        ];
    }

    /**
     * Test magic setter for object type name property.
     *
     * @param string|null $expected Expected object type ID.
     * @param mixed $objectTypeName Object type name.
     * @return void
     */
    #[DataProvider('setObjectTypeNameProvider')]
    public function testSetObjectTypeName($expected, $objectTypeName)
    {
        $entity = new Property();
        $entity->object_type_name = $objectTypeName;

        $objectTypeId = $entity->object_type_id;

        static::assertSame($expected, $objectTypeId);
    }
}
