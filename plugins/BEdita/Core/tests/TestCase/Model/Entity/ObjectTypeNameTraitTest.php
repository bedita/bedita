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

namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Model\Entity\Property;
use Cake\TestSuite\TestCase;

/**
 *  {@see \BEdita\Core\Model\Entity\ObjectTypeNameTrait} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\ObjectTypeNameTrait
 */
class ObjectTypeNameTraitTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
    ];

    /**
     * Data provider for `testGetObjectTypeName` test case.
     *
     * @return array
     */
    public function getObjectTypeNameProvider()
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
     *
     * @covers ::_getObjectTypeName()
     * @dataProvider getObjectTypeNameProvider()
     */
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
    public function setObjectTypeNameProvider()
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
     *
     * @covers ::_setObjectTypeName()
     * @dataProvider setObjectTypeNameProvider()
     */
    public function testSetObjectTypeName($expected, $objectTypeName)
    {
        $entity = new Property();
        $entity->object_type_name = $objectTypeName;

        $objectTypeId = $entity->object_type_id;

        static::assertSame($expected, $objectTypeId);
    }
}
