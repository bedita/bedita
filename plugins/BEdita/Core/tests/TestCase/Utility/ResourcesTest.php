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

namespace BEdita\Core\Test\TestCase\Utility;

use BEdita\Core\Utility\Resources;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * {@see \BEdita\Core\Utility\Resources} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Utility\Resources
 */
class ResourcesTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.Endpoints',
        'plugin.BEdita/Core.EndpointPermissions',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.RolesUsers',
    ];

    /**
     * Data provider for `testCreate`
     *
     * @return array
     */
    public function createProvider(): array
    {
        return [
            'roles' => [
                'roles',
                [
                    [
                        'name' => 'new role',
                    ],
                ],
            ],
            'apps' => [
                'applications',
                [
                    [
                        'name' => 'new app',
                    ],
                ],
            ],
            'objects' => [
                'object_types',
                [
                    [
                        'name' => 'cats',
                        'singular' => 'cat',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `create` method.
     *
     * @param string $type Resource type.
     * @param array $data Resource data.
     * @return void
     *
     * @covers ::create()
     * @covers ::getTable()
     * @dataProvider createProvider
     */
    public function testCreate(string $type, array $data): void
    {
        Resources::create($type, $data);

        $newResources = TableRegistry::getTableLocator()
            ->get(Inflector::camelize($type))
            ->find()
            ->where(['name IN' => Hash::extract($data, '{n}.name')])
            ->toArray();

        static::assertEquals(count($data), count($newResources));
    }

    /**
     * Data provider for `testRemove`
     *
     * @return array
     */
    public function removeProvider(): array
    {
        return [
            'roles' => [
                'roles',
                [
                    [
                        'name' => 'second role',
                    ],
                ],
            ],
            'apps' => [
                'applications',
                [
                    [
                        'name' => 'Disabled app',
                    ],
                ],
            ],
            'objects' => [
                'object_types',
                [
                    [
                        'name' => 'news',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `remove` method.
     *
     * @param string $type Resource type.
     * @param array $data Resource data.
     * @return void
     *
     * @covers ::remove()
     * @dataProvider removeProvider
     */
    public function testRemove(string $type, array $data): void
    {
        Resources::remove($type, $data);

        $resources = TableRegistry::getTableLocator()
            ->get(Inflector::camelize($type))
            ->find()
            ->where(['name IN' => Hash::extract($data, '{n}.name')])
            ->toArray();

        static::assertEmpty($resources);
    }

    /**
     * Data provider for `testUpdate`
     *
     * @return array
     */
    public function updateProvider(): array
    {
        return [
            'roles' => [
                'roles',
                [
                    [
                        'name' => 'second role',
                        'description' => 'new role desc',
                    ],
                ],
            ],
            'apps' => [
                'applications',
                [
                    [
                        'name' => 'Disabled app',
                        'description' => 'A new description',
                    ],
                ],
            ],
            'objects' => [
                'object_types',
                [
                    [
                        'name' => 'news',
                        'hidden' => '["description"]',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test `update` method.
     *
     * @param string $type Resource type.
     * @param array $data Resource data.
     * @return void
     *
     * @covers ::update()
     * @covers ::findCondition()
     * @dataProvider updateProvider
     */
    public function testUpdate(string $type, array $data): void
    {
        Resources::update($type, $data);

        $resources = TableRegistry::getTableLocator()
            ->get(Inflector::camelize($type))
            ->find()
            ->where(['name IN' => Hash::extract($data, '{n}.name')])
            ->toArray();

        static::assertEquals(count($data), count($resources));
        $entity = $resources[0];
        foreach ($data[0] as $name => $val) {
            static::assertEquals($val, $entity->get($name));
        }
    }

    /**
     * Test `getTable` method failure.
     *
     * @covers ::getTable()
     */
    public function testGetTableFail()
    {
        static::expectException(BadRequestException::class);
        static::expectExceptionMessage('Resource type not allowed "cats"');

        Resources::create('cats', []);
    }

    /**
     * Test `findCondition` method failure.
     *
     * @covers ::findCondition()
     */
    public function testFindConditionFail()
    {
        static::expectException(BadRequestException::class);
        static::expectExceptionMessage('Missing mandatory fields "id" or "name"');

        Resources::remove('applications', [['key' => 'value']]);
    }
}
