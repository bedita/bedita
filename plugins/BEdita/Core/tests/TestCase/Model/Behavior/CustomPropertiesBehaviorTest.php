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

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Behavior\CustomPropertiesBehavior} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Behavior\CustomPropertiesBehavior
 */
class CustomPropertiesBehaviorTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.properties',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
    ];

    /**
     * Data provider for testGetAvailable()
     *
     * @return array
     */
    public function getAvailableProvider()
    {
        return [
            'noProps' => [
                [],
                'Locations',
            ],
            'userProp' => [
                ['another_username', 'another_email'],
                'Users'
            ]
        ];
    }

    /**
     * Test get available properties
     *
     * @param array $expected Expected result.
     * @param string $tableName Table name.
     * @return void
     *
     * @covers ::getAvailable()
     * @dataProvider getAvailableProvider
     */
    public function testGetAvailable(array $expected, $tableName)
    {
        $table = TableRegistry::get($tableName);
        $behavior = $table->behaviors()->get('CustomProperties');
        $result = $behavior->getAvailable();
        $result = array_keys($result);
        sort($result);
        sort($expected);
        static::assertEquals($expected, $result);

        // cover use of internal `available` array
        $result = $behavior->getAvailable();
        $result = array_keys($result);
        sort($result);
        static::assertEquals($expected, $result);
    }

    /**
     * Test get available when no object type is found
     *
     * @return void
     *
     * @covers ::getAvailable()
     */
    public function testGetAvailableTypeNotfound()
    {
        // test try/catch failure on `objectType` load
        $Relations = TableRegistry::get('Relations');
        $Relations->addBehavior('BEdita/Core.CustomProperties', ['field' => 'description']);
        $rel = $Relations->get(1);
        $result = $rel->toArray();
        static::assertNotEmpty($result);
    }

    /**
     * Test empty custom properties
     *
     * @return void
     *
     * @covers ::getAvailable()
     */
    public function testEmpty()
    {
        $table = TableRegistry::get('Locations');
        $result = $table->behaviors()->get('CustomProperties')->getDefaultValues();
        static::assertEmpty($result);
    }

    /**
     * Test get available properties
     *
     * @return void
     *
     * @covers ::getDefaultValues()
     */
    public function testDefaultValues()
    {
        $expected = [
            'another_username' => null,
            'another_email' => null,
        ];
        $user = TableRegistry::get('Users');
        $result = $user->behaviors()->get('CustomProperties')->getDefaultValues();
        static::assertEquals($expected, $result);
    }

    /**
     * Test setting of priority before entity is saved.
     *
     * @return void
     *
     * @covers ::beforeFind()
     * @covers ::promoteProperties()
     * @covers ::isFieldSet()
     */
    public function testBeforeFind()
    {
        $table = TableRegistry::get('Users');
        $user = $table->get(1);

        static::assertFalse($user->isDirty());

        $result = $user->toArray();
        static::assertArrayHasKey('another_username', $result);
        static::assertArrayHasKey('another_email', $result);
        static::assertArrayNotHasKey('custom_props', $result);

        // no hydration
        $result = $table->find()
            ->where(['id' => 1])
            ->enableHydration(false)
            ->first();

        static::assertArrayHasKey('another_username', $result);
        static::assertArrayHasKey('another_email', $result);
        static::assertArrayNotHasKey('custom_props', $result);

        // test empty `custom_props`
        $table = TableRegistry::get('Events');
        $event = $table->get(9);
        $result = $event->toArray();
        static::assertNotEmpty($result);
        static::assertArrayNotHasKey('custom_props', $result);

        // test `promoteProperties` case `$entity` is not an entity ora array
        $result = TableRegistry::get('Objects')->find('list')->find('type', ['documents'])->toArray();
        static::assertNotEmpty($result);
    }
}
