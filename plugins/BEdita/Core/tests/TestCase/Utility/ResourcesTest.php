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
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Locations',
        'plugin.BEdita/Core.Media',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.Endpoints',
        'plugin.BEdita/Core.EndpointPermissions',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.Config',
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
            'categories' => [
                'categories',
                [
                    [
                        'name' => 'third-cat',
                        'label' => 'Third category',
                        'object_type_name' => 'documents',
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
            'endpoints' => [
                'endpoints',
                [
                    [
                        'name' => 'pets',
                        'description' => 'handle pets with care',
                    ],
                ],
                'endpoints with object type',
                [
                    [
                        'name' => 'pets',
                        'description' => 'handle pets with care',
                        'object_type_name' => 'documents',
                    ],
                ],
            ],
            'endpoint_permissions' => [
                'endpoint_permissions',
                [
                    [
                        'endpoint' => 'home',
                        'application' => 'First app',
                        'role' => 'first role',
                        'permission' => 12,
                    ],
                ],
            ],
            'endpoint_permissions with app null' => [
                'endpoint_permissions',
                [
                    [
                        'endpoint' => 'home',
                        'application' => null,
                        'role' => 'first role',
                        'permission' => 12,
                    ],
                ],
            ],
            'endpoint_permissions with role null' => [
                'endpoint_permissions',
                [
                    [
                        'endpoint' => 'home',
                        'application' => 'First app',
                        'role' => null,
                        'permission' => 12,
                    ],
                ],
            ],
            'endpoint_permissions with endpoint null' => [
                'endpoint_permissions',
                [
                    [
                        'endpoint' => null,
                        'application' => 'First app',
                        'role' => 'first role',
                        'permission' => 12,
                    ],
                ],
            ],
            'properties' => [
                'properties',
                [
                    [
                        'name' => 'extra_data',
                        'description' => 'Document extra data',
                        'object' => 'documents',
                        'property' => 'json',
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
     * @covers ::create()
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
            'categories' => [
                'categories',
                [
                    [
                        'name' => 'second-cat',
                        'object_type_name' => 'documents',
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
            'endpoints' => [
                'endpoints',
                [
                    [
                        'name' => 'disabled',
                    ],
                ],
            ],
            'endpoint_permissions' => [
                'endpoint_permissions',
                [
                    [
                        'application_name' => 'Disabled app',
                        'endpoint_name' => 'home',
                        'role_name' => 'second role',
                    ],
                    [
                        'application_name' => 'First app',
                        'endpoint_name' => null,
                        'role_name' => null,
                    ],
                ],
            ],
            'properties' => [
                'properties',
                [
                    [
                        'name' => 'another_description',
                        'object' => 'documents',
                        'property' => 'string',
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
     * @covers ::remove()
     * @dataProvider removeProvider
     */
    public function testRemove(string $type, array $data): void
    {
        Resources::remove($type, $data);

        $Table = TableRegistry::getTableLocator()->get(Inflector::camelize($type));
        if (!$Table->hasFinder('resource')) {
            $resources = $Table
                ->find()
                ->where(['name IN' => Hash::extract($data, '{n}.name')])
                ->toArray();

            static::assertEmpty($resources);

            return;
        }

        foreach ($data as $options) {
            static::assertCount(0, $Table->find('resource', $options));
        }
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
            'categories' => [
                'categories',
                [
                    [
                        'name' => 'second-cat',
                        'object_type_name' => 'documents',
                        'label' => 'Another category',
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
            'endpoints' => [
                'endpoints',
                [
                    [
                        'name' => 'disabled',
                        'enabled' => 1,
                    ],
                ],
                'endpoints with object type',
                [
                    [
                        'name' => 'disabled',
                        'object_type_name' => 'documents',
                    ],
                ],
            ],
            'endpoint_permissions' => [
                'endpoint_permissions',
                [
                    [
                        'application_name' => 'Disabled app',
                        'endpoint_name' => 'home',
                        'role_name' => 'first role',
                        'permission' => 0b1111,
                    ],
                ],
            ],
            'properties' => [
                'properties',
                [
                    [
                        'name' => 'another_title',
                        'description' => 'Another Title',
                        'object' => 'documents',
                        'property' => 'string',
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
     * @covers ::update()
     * @covers ::loadEntity()
     * @covers ::findCondition()
     * @dataProvider updateProvider
     */
    public function testUpdate(string $type, array $data): void
    {
        $result = Resources::update($type, $data);
        static::assertEquals(count($data), count($result));

        $Table = TableRegistry::getTableLocator()->get(Inflector::camelize($type));
        if (!$Table->hasFinder('resource')) {
            $resources = $Table
                ->find()
                ->where(['name IN' => Hash::extract($data, '{n}.name')])
                ->toArray();
        } else {
            $resources = $Table->find('resource', $data[0])->toArray();
        }

        static::assertEquals(count($data), count($resources));
        /** @var \Cake\ORM\Entity $entity */
        $entity = $resources[0];
        $properties = array_merge($entity->getVisible(), $entity->getHidden());
        foreach ($data[0] as $name => $val) {
            if (in_array($name, $properties)) { // check against real entity properties
                static::assertEquals($val, $entity->get($name));
            }
        }
    }

    /**
     * Test `findCondition` method failure.
     *
     * @covers ::findCondition()
     */
    public function testFindConditionFail()
    {
        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Missing mandatory fields "id" or "name"');

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
                        'roles' => [],
                    ],
                ],
                new BadRequestException('Save action "assign" not allowed'),
            ],
            'bad type' => [
                [
                    'remove' => [
                        'questions' => [
                            [],
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
     * @covers ::save()
     * @covers ::saveType()
     * @dataProvider saveProvider
     */
    public function testSave(array $resources, ?\Exception $exception = null): void
    {
        if ($exception) {
            $this->expectException(get_class($exception));
            $this->expectExceptionMessage($exception->getMessage());
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
