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

use Cake\ORM\Table;
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
     * Test initialization.
     *
     * @return void
     *
     * @covers ::initialize()
     */
    public function testInitialize()
    {
        $table = TableRegistry::get('FakeObjects', [
            'className' => Table::class,
        ]);
        static::assertFalse($table->hasBehavior('BEdita/Core.ObjectType'));

        $table->addBehavior('BEdita/Core.CustomProperties');
        static::assertTrue($table->hasBehavior('ObjectType'));
    }

    /**
     * Data provider for testGetAvailable()
     *
     * @return array
     */
    public function getAvailableProvider()
    {
        return [
            'locations' => [
                [],
                'Locations',
            ],
            'profiles' => [
                [
                    'another_surname',
                    'another_birthdate',
                ],
                'Profiles',
            ],
            'users' => [
                [
                    'another_username',
                    'another_email',
                    'another_surname',
                    'another_birthdate',
                ],
                'Users',
            ],
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
     * @covers ::objectType()
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
    public function testGetAvailableTypeNotFound()
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
            'another_surname' => null,
            'another_birthdate' => null,
        ];
        $user = TableRegistry::get('Users');
        $result = $user->behaviors()->get('CustomProperties')->getDefaultValues();
        static::assertEquals($expected, $result);
    }

    /**
     * Data provider for `testBeforeFind` test case.
     *
     * @return array
     */
    public function beforeFindProvider()
    {
        return [
            'simple' => [
                ['another_username', 'another_email'],
                1,
                'Users',
            ],
            'no hydration' => [
                ['another_username', 'another_email'],
                1,
                'Users',
                false,
            ],
            'empty' => [
                [],
                9,
                'Events',
            ],
        ];
    }
    /**
     * Test setting of priority before entity is saved.
     *
     * @param string[] $expectedProperties List of expected properties.
     * @param int $id Entity ID.
     * @param string $table Table.
     * @param bool $hydrate Should hydration be enabled?
     * @return void
     *
     * @dataProvider beforeFindProvider()
     * @covers ::beforeFind()
     * @covers ::promoteProperties()
     * @covers ::isFieldSet()
     */
    public function testBeforeFind(array $expectedProperties, $id, $table, $hydrate = true)
    {
        $result = TableRegistry::get($table)->find()
            ->where(compact('id'))
            ->enableHydration($hydrate)
            ->first();
        if ($hydrate) {
            $result = $result->toArray();
        }

        static::assertArrayNotHasKey('custom_props', $result);
        foreach ($expectedProperties as $property) {
            static::assertArrayHasKey($property, $result);
        }
    }

    /**
     * Test that no errors are triggered if results aren't neither entities nor arrays.
     *
     * @return void
     *
     * @covers ::beforeFind()
     * @covers ::promoteProperties()
     * @covers ::isFieldSet()
     */
    public function testBeforeFindOtherType()
    {
        $result = TableRegistry::get('Objects')
            ->find('list')
            ->find('type', ['documents'])
            ->toArray();

        static::assertNotEmpty($result);
    }

    /**
     * Data provider for `testBeforeSave` test case.
     *
     * @return array
     */
    public function beforeSaveProvider()
    {
        return [
            'simple' => [
                [
                    'another_username' => 'gustavo',
                    'another_email' => null,
                    'another_surname' => 'Support',
                    'another_birthdate' => null,
                ],
                [
                    'another_username' => 'gustavo',
                    'another_surname' => 'Support',
                ],
                1,
                'Users',
            ],
            'overwrite' => [
                [
                    'another_username' => 'synapse',
                    'another_email' => 'gustavo@example.org',
                    'another_surname' => null,
                    'another_birthdate' => null,
                ],
                [
                    'another_email' => 'gustavo@example.org',
                ],
                5,
                'Users',
            ],
            'empty' => [
                [
                    'another_username' => null,
                    'another_email' => null,
                    'another_surname' => null,
                    'another_birthdate' => null,
                ],
                [
                    'password' => 'hohoho',
                ],
                1,
                'Users',
            ],
            'disabledProperty' => [
                [
                    'another_username' => 'gustavo',
                    'another_email' => null,
                    'another_surname' => null,
                    'another_birthdate' => null,
                ],
                [
                    'another_username' => 'gustavo',
                    'disabled_property' => 'do not write it!',
                ],
                1,
                'Users',
            ],
        ];
    }

    /**
     * Test correct save of custom properties.
     *
     * @param array $expected Expected result.
     * @param array $data Data.
     * @param int $id Entity ID.
     * @param string $table Table.
     * @return void
     *
     * @dataProvider beforeSaveProvider()
     * @covers ::beforeSave()
     * @covers ::demoteProperties()
     */
    public function testBeforeSave(array $expected, array $data, $id, $table)
    {
        $table = TableRegistry::get($table);
        $entity = $table->get($id);

        $table->patchEntity($entity, $data);
        $table->save($entity);

        $result = $entity->get('custom_props');

        ksort($expected);
        ksort($result);

        static::assertSame($expected, $result);
    }
}
