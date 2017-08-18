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
use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

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
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Users = TableRegistry::get('Users');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Users);

        parent::tearDown();
    }

    /**
     * Test accessible properties.
     *
     * @return void
     * @coversNothing
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
     * @coversNothing
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

        $data = [
            'password_hash' => 'myPassword',
        ];
        $user = $this->Users->patchEntity($user, $data);
        if (!($user instanceof User)) {
            throw new \InvalidArgumentException();
        }

        static::assertNotEquals('myPassword', $user->password_hash);
        static::assertTrue((new DefaultPasswordHasher())->check('myPassword', $user->password_hash));
    }
}
