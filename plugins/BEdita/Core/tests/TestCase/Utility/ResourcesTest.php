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
        'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.Endpoints',
        'plugin.BEdita/Core.EndpointPermissions',
        'plugin.BEdita/Core.Config',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.ExternalAuth',
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
            'auth prov' => [
                'auth_providers',
                [
                    [
                        'name' => 'oauthsome',
                        'auth_class' => 'BEdita/API.OAuth2',
                        'url' => 'https://some.example.com/oauth2',
                        'params' => [
                            'provider_username_field' => 'owner_id',
                        ],
                        'enabled' => true,
                    ],
                ],
            ],
            'config' => [
                'config',
                [
                    [
                        'name' => 'Status',
                        'context' => 'core',
                        'content' => '{"level":"on"}',
                        'application' => 'First app',
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
            'prop types' => [
                'property_types',
                [
                    [
                        'name' => 'my_type',
                        'params' => [
                            'type' => 'string',
                            'enum' => ['A', 'B'],
                        ],
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
        $result = Resources::create($type, $data);
        static::assertEquals(count($data), count($result));
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
            'auth prov' => [
                'auth_providers',
                [
                    [
                        'name' => 'linkedout',
                    ],
                ],
            ],
            'config' => [
                'config',
                [
                    [
                        'name' => 'appVal',
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
            'prop types' => [
                'property_types',
                [
                    [
                        'name' => 'unused property type',
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
            'auth prov' => [
                'auth_providers',
                [
                    [
                        'name' => 'linkedout',
                        'params' => [
                            'provider_username_field' => 'another_id',
                        ],
                    ],
                ],
            ],
            'config' => [
                'config',
                [
                    [
                        'name' => 'appVal',
                        'content' => '{"val": 50}',
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
            'prop types' => [
                'property_types',
                [
                    [
                        'name' => 'unused property type',
                        'params' => ['type' => 'object'],
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
     * @covers ::loadEntity()
     * @covers ::findCondition()
     * @dataProvider updateProvider
     */
    public function testUpdate(string $type, array $data): void
    {
        $result = Resources::update($type, $data);
        static::assertEquals(count($data), count($result));

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

    /**
     * Data provider for `testSave`
     *
     * @return array
     */
    public function saveProvider(): array
    {
        return [
            'simple' => [
                [
                    'create' => [
                        'roles' => [
                            [
                                'name' => 'supporter',
                                'description' => 'some text here...',
                            ],
                        ],
                    ],
                ],
            ],
            'remove simple' => [
                [
                    'remove' => [
                        'relations' => [
                            [
                                'name' => 'test_abstract',
                                'left' => ['events'],
                                'right' => ['media'],
                            ],
                        ],
                    ],
                ],
            ],
            'update relation' => [
                [
                    'update' => [
                        'relations' => [
                            [
                                'name' => 'test_abstract',
                                'left' => ['documents'],
                                'right' => ['files'],
                            ],
                        ],
                    ],
                ],
            ],
            'bad action' => [
                [
                    'assign' => [
                        'roles' => []
                    ],
                ],
                new BadRequestException('Save action "assign" not allowed'),
            ],
            'bad type' => [
                [
                    'remove' => [
                        'questions' => [
                            []
                        ],
                    ],
                ],
                new BadRequestException('Resource type "questions" not supported'),
            ],
        ];
    }

    /**
     * Test `save` method.
     *
     * @param array $resources Resource save input data.
     * @param \Exception|null $exception Expected expection.
     * @return void
     *
     * @covers ::save()
     * @covers ::saveType()
     * @dataProvider saveProvider
     */
    public function testSave(array $resources, ?\Exception $exception = null): void
    {
        if ($exception) {
            static::expectException(get_class($exception));
            static::expectExceptionMessage($exception->getMessage());
        }

        Resources::save($resources);

        foreach ($resources as $action => $data) {
            foreach ($data as $type => $details) {
                $entities = TableRegistry::getTableLocator()
                    ->get(Inflector::camelize($type))
                    ->find()
                    ->where(['name IN' => Hash::extract($details, '{n}.name')])
                    ->toArray();

                if ($action === 'remove') {
                    static::assertEmpty($entities);
                } else {
                    $entity = $entities[0];
                    foreach ($details[0] as $name => $val) {
                        if ($type != 'relations' || !in_array($name, ['left', 'right'])) {
                            static::assertEquals($val, $entity->get($name));
                        }
                    }
                }
            }
        }
    }
}
