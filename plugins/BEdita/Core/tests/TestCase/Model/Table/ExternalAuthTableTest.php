<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Table;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Model\Table\ExternalAuthTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\ExternalAuthTable
 */
class ExternalAuthTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Core\Model\Table\ExternalAuthTable
     */
    public $ExternalAuth;

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
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.roles_users',
        'plugin.BEdita/Core.auth_providers',
        'plugin.BEdita/Core.external_auth',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->ExternalAuth = TableRegistry::get('ExternalAuth');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->ExternalAuth);
        LoggedUser::resetUser();

        parent::tearDown();
    }

    /**
     * Test initialization.
     *
     * @return void
     * @coversNothing
     */
    public function testInitialization()
    {
        $this->ExternalAuth->initialize([]);
        $schema = $this->ExternalAuth->getSchema();

        static::assertEquals('external_auth', $this->ExternalAuth->getTable());
        static::assertEquals('id', $this->ExternalAuth->getPrimaryKey());
        static::assertEquals('provider_username', $this->ExternalAuth->getDisplayField());

        static::assertInstanceOf(BelongsTo::class, $this->ExternalAuth->AuthProviders);
        static::assertInstanceOf(BelongsTo::class, $this->ExternalAuth->Users);

        static::assertEquals('json', $schema->getColumnType('params'));
    }

    /**
     * Data provider for `testValidation` test case.
     *
     * @return array
     */
    public function validationProvider()
    {
        return [
            'valid' => [
                true,
                [
                    'user_id' => 1,
                    'auth_provider_id' => 2,
                    'provider_username' => 'unique_username',
                ],
            ],
            'notUnique' => [
                false,
                [
                    'user_id' => 2,
                    'auth_provider_id' => 1,
                    'provider_username' => 'first_user',
                    'params' => [
                        'someParam' => 'someValue',
                    ],
                ],
            ],
            'notUnique2' => [
                false,
                [
                    'user_id' => 1,
                    'auth_provider_id' => 1,
                    'provider_username' => 'some_username',
                ],
            ],
        ];
    }

    /**
     * Test validation.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     *
     * @return void
     * @dataProvider validationProvider
     * @coversNothing
     */
    public function testValidation($expected, array $data)
    {
        $externalAuth = $this->ExternalAuth->newEntity();
        $this->ExternalAuth->patchEntity($externalAuth, $data);

        $success = $this->ExternalAuth->save($externalAuth);
        static::assertEquals($expected, (bool)$success);
    }

    /**
     * Test before save callback when everything is already ok.
     *
     * @return void
     *
     * @covers ::beforeSave()
     */
    public function testBeforeSaveNothingToDo()
    {
        $count = $this->ExternalAuth->Users->find()->count();
        $entity = $this->ExternalAuth->newEntity([
            'auth_provider_id' => 2,
            'user_id' => 1,
            'provider_username' => 'gustavo',
        ]);

        $result = $this->ExternalAuth->save($entity);
        $countAfter = $this->ExternalAuth->Users->find()->count();

        static::assertInstanceOf($this->ExternalAuth->getEntityClass(), $result);
        static::assertSame($count, $countAfter);
    }

    /**
     * Test before save callback that creates a new user.
     *
     * @return void
     *
     * @covers ::beforeSave()
     */
    public function testBeforeSaveCreateUser()
    {
        LoggedUser::setUser(['id' => 1]);
        Configure::write('Roles.BEdita/API.Uuid', ['first role']);
        $count = $this->ExternalAuth->Users->find()->count();
        $entity = $this->ExternalAuth->newEntity([
            'auth_provider_id' => 2,
            'provider_username' => 'gustavo',
        ]);

        $result = $this->ExternalAuth->save($entity);
        $countAfter = $this->ExternalAuth->Users->find()->count();

        static::assertInstanceOf($this->ExternalAuth->getEntityClass(), $result);
        static::assertSame($count + 1, $countAfter);
        static::assertNotNull($entity->get('user_id'));

        $newUser = $this->ExternalAuth->Users->get($entity->get('user_id'), ['contain' => 'Roles']);

        static::assertSame(1, $newUser->get('created_by'));
        static::assertSame(1, $newUser->get('modified_by'));
        static::assertSame([1 => 'first role'], Hash::combine($newUser->get('roles'), '{n}.id', '{n}.name'));
    }

    /**
     * Test before save callback that creates a new user and sets `created_by` to its own ID.
     *
     * @return void
     *
     * @covers ::beforeSave()
     */
    public function testBeforeSaveCreateUserCreatedByThemselves()
    {
        $count = $this->ExternalAuth->Users->find()->count();
        $entity = $this->ExternalAuth->newEntity([
            'auth_provider_id' => 2,
            'provider_username' => 'gustavo',
        ]);

        $result = $this->ExternalAuth->save($entity);
        $countAfter = $this->ExternalAuth->Users->find()->count();

        static::assertInstanceOf($this->ExternalAuth->getEntityClass(), $result);
        static::assertSame($count + 1, $countAfter);
        static::assertNotNull($entity->get('user_id'));

        $newUser = $this->ExternalAuth->Users->get($entity->get('user_id'));

        static::assertSame($newUser->id, $newUser->get('created_by'));
        static::assertSame($newUser->id, $newUser->get('modified_by'));
    }

    /**
     * Data provider for `testFindAuthProvider` test case.
     *
     * @return array
     */
    public function findAuthProviderProvider() // Nice name, huh!?
    {
        return [
            'missing parameter' => [
                new BadFilterException([
                    'title' => 'Invalid data',
                    'detail' => '"auth_provider" parameter missing',
                ]),
                null,
            ],
            'name' => [
                [
                    [
                        'id' => 1,
                        'user_id' => 1,
                        'auth_provider_id' => 1,
                        'params' => null,
                        'provider_username' => 'first_user',
                    ],
                ],
                'example',
            ],
            'auth provider data' => [
                [
                    [
                        'id' => 1,
                        'user_id' => 1,
                        'auth_provider_id' => 1,
                        'params' => null,
                        'provider_username' => 'first_user',
                    ],
                ],
                [
                    'id' => 1,
                ],
            ],
            'id' => [
                [
                    [
                        'id' => 1,
                        'user_id' => 1,
                        'auth_provider_id' => 1,
                        'params' => null,
                        'provider_username' => 'first_user',
                    ],
                ],
                1,
            ],
        ];
    }

    /**
     * Test finder by auth provider.
     *
     * @param mixed $expected Expected result.
     * @param $authProvider
     * @return void
     *
     * @covers ::findAuthProvider()
     * @dataProvider findAuthProviderProvider()
     */
    public function testFindAuthProvider($expected, $authProvider)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $options = [
            'auth_provider' => $authProvider,
        ];
        $result = $this->ExternalAuth
            ->find('authProvider', $options)
            ->enableHydration(false)
            ->toArray();

        static::assertSame($expected, $result);
    }
}
