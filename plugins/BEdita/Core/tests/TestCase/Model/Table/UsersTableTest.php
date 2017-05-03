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

use BEdita\Core\Utility\LoggedUser;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Table\UsersTable} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Table\UsersTable
 */
class UsersTableTest extends TestCase
{

    /**
     * Test subject
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
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.auth_providers',
        'plugin.BEdita/Core.external_auth',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Users = TableRegistry::get('Users');
        LoggedUser::setUser(['id' => 1]);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Users);
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
        $this->Users->associations()->removeAll();
        $this->Users->initialize([]);
        $this->assertEquals('users', $this->Users->getTable());
        $this->assertEquals('id', $this->Users->getPrimaryKey());
        $this->assertEquals('username', $this->Users->getDisplayField());

        $this->assertInstanceOf('\Cake\ORM\Association\HasMany', $this->Users->ExternalAuth);
        $this->assertInstanceOf('\Cake\ORM\Association\BelongsToMany', $this->Users->Roles);
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
                    'username' => 'some_unique_value',
                    'password_hash' => null,
                ],
            ],
            'notUnique' => [
                false,
                [
                    'username' => 'first user',
                    'password_hash' => 'password',
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
        $user = $this->Users->newEntity();
        $this->Users->patchEntity($user, $data);
        $user->type = 'users';

        $error = (bool)$user->getErrors();
        $this->assertEquals($expected, !$error);

        if ($expected) {
            $success = $this->Users->save($user);
            $this->assertTrue((bool)$success);
        }
    }

    /**
     * Test handling of login event.
     *
     * @return void
     *
     * @covers ::login()
     */
    public function testLogin()
    {
        $data = $this->Users->get(1)->toArray();
        $expected = new Time();
        $this->Users->dispatchEvent('Auth.afterIdentify', [$data]);

        $lastLogin = $this->Users->get(1)->last_login;
        static::assertNotNull($lastLogin);
        static::assertLessThanOrEqual(2, $expected->diffInSeconds($lastLogin));
    }

    /**
     * Test handling of external auth login event.
     *
     * @return void
     *
     * @covers ::externalAuthLogin()
     */
    public function testExternalAuthLogin()
    {
        $authProvider = TableRegistry::get('AuthProviders')->get(2);
        $username = 'gustavo';
        $params = ['job' => 'head of technical support'];

        $event = $this->Users->dispatchEvent('Auth.externalAuth', compact('authProvider', 'username', 'params'));

        /* @var \BEdita\Core\Model\Entity\ExternalAuth $externalAuth */
        $externalAuth = $event->result;
        static::assertInstanceOf($this->Users->ExternalAuth->getEntityClass(), $externalAuth);
        static::assertFalse($externalAuth->isNew());
        static::assertNotNull($externalAuth->id);
    }

    /**
     * Test deleted field on user deleted.
     *
     * @return void
     *
     * @coversNothing
     */
    public function testDeleted()
    {
        $user = $this->Users->get(1);
        $user->deleted = true;
        $success = $this->Users->save($user);
        $this->assertTrue((bool)$success);
        $deleted = $this->Users->get(1)->deleted;
        $this->assertEquals(true, $deleted);
    }

    /**
     * Data provider for `testFindExternalAuth` test case.
     *
     * @return array
     */
    public function findExternalAuthProvider()
    {
        return [
            'generic' => [
                [
                    1 => 'first user',
                ],
                [
                    'auth_provider' => 'example',
                ],
            ],
            'specific' => [
                [
                    1 => 'first user',
                ],
                [
                    'auth_provider' => 'example',
                    'username' => 'first_user',
                ],
            ],
            'not fount' => [
                [],
                [
                    'auth_provider' => 'example',
                    'username' => 'not_found',
                ],
            ],
        ];
    }

    /**
     * Test finder by external auth.
     *
     * @param array $expected Expected results.
     * @param array $options Finder options.
     * @return void
     *
     * @covers ::findExternalAuth()
     * @dataProvider findExternalAuthProvider()
     */
    public function testFindExternalAuth($expected, $options)
    {
        $actual = $this->Users
            ->find('externalAuth', $options)
            ->find('list')
            ->toArray();

        static::assertSame($expected, $actual);
    }
}
