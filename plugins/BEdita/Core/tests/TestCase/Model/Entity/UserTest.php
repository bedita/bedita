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

namespace BEdita\Core\Test\TestCase\Model\Entity;

use BEdita\Core\Model\Entity\User;
use BEdita\Core\Utility\JsonApiSerializable;
use Cake\Auth\DefaultPasswordHasher;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;

/**
 * {@see \BEdita\Core\Model\Entity\User} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Entity\User
 */
class UserTest extends TestCase
{
    /**
     * Test subject's table
     *
     * @var \BEdita\Core\Model\Table\UsersTable
     */
    public $Users;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.ExternalAuth',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Users = TableRegistry::getTableLocator()->get('Users');
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        unset($this->Users);

        parent::tearDown();
    }

    /**
     * Test accessible properties.
     *
     * @return void
     * @covers ::__construct()
     */
    public function testAccessible()
    {
        $user = $this->Users->get(1);

        $data = [
            'id' => 42,
            'blocked' => true,
            'username' => 'patched_username',
        ];
        $user = $this->Users->patchEntity($user, $data);
        if (!($user instanceof User)) {
            throw new \InvalidArgumentException();
        }

        static::assertEquals(1, $user->id);
        static::assertFalse($user->blocked);
        static::assertEquals('patched_username', $user->username);
    }

    /**
     * Test hidden properties.
     *
     * @return void
     * @covers ::__construct()
     */
    public function testHidden()
    {
        $user = $this->Users->get(1);
        if (!($user instanceof User)) {
            throw new \InvalidArgumentException();
        }

        static::assertNotEmpty($user->password_hash);
        static::assertArrayNotHasKey('password_hash', $user->toArray());
    }

    /**
     * Test setter method for `password`.
     *
     * @return void
     * @covers ::_setPassword()
     */
    public function testSetPassword()
    {
        $user = $this->Users->get(1);

        $data = [
            'password' => 'myPassword',
        ];
        $user = $this->Users->patchEntity($user, $data);
        if (!($user instanceof User)) {
            throw new \InvalidArgumentException();
        }

        static::assertNull($user->password);
        static::assertNotEquals('myPassword', $user->password_hash);
        static::assertTrue((new DefaultPasswordHasher())->check('myPassword', $user->password_hash));
    }

    /**
     * Test setter method for `password_hash`.
     *
     * @return void
     * @covers ::_setPasswordHash()
     */
    public function testSetPasswordHash()
    {
        $user = $this->Users->get(1);
        $now = FrozenTime::now();

        $data = [
            'password_hash' => 'myPassword',
        ];
        $user = $this->Users->patchEntity($user, $data);
        if (!($user instanceof User)) {
            throw new \InvalidArgumentException();
        }

        static::assertNotEquals('myPassword', $user->password_hash);
        static::assertTrue((new DefaultPasswordHasher())->check('myPassword', $user->password_hash));
        static::assertNotEmpty($user->password_modified);
        static::assertGreaterThanOrEqual($user->password_modified->timestamp, $now->timestamp);
    }

    /**
     * Test getter for JSON API meta fields.
     *
     * @return void
     * @covers ::getMeta()
     * @covers ::getExternalAuthMeta()
     */
    public function testGetMeta(): void
    {
        $user = $this->Users->get(5);
        $user = $user->jsonApiSerialize(
            JsonApiSerializable::JSONAPIOPT_EXCLUDE_LINKS |
            JsonApiSerializable::JSONAPIOPT_EXCLUDE_RELATIONSHIPS
        );

        static::assertArrayHasKey('external_auth', $user['meta']);
        static::assertEquals('uuid', Hash::get($user, 'meta.external_auth.0.provider'));
    }

    /**
     * Test that external_auth is null for entity withoud id.
     *
     * @return void
     * @covers ::getMeta()
     * @covers ::getExternalAuthMeta()
     */
    public function testGetMetaMissingUserId(): void
    {
        $user = new User();
        $user->type = 'users';
        $user->created_by = 1;
        $user = $user->jsonApiSerialize(
            JsonApiSerializable::JSONAPIOPT_EXCLUDE_LINKS |
            JsonApiSerializable::JSONAPIOPT_EXCLUDE_RELATIONSHIPS
        );

        static::assertArrayHasKey('external_auth', $user['meta']);
        static::assertEquals(null, Hash::get($user, 'meta.external_auth'));
    }
}
