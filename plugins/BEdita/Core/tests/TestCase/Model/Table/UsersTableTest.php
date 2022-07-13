<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Table;

use Authentication\Identity;
use BEdita\Core\Model\Table\UsersTable;
use BEdita\Core\Utility\LoggedUser;
use Cake\Auth\WeakPasswordHasher;
use Cake\Core\Configure;
use Cake\Http\Exception\BadRequestException;
use Cake\I18n\FrozenTime;
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
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.AuthProviders',
        'plugin.BEdita/Core.ExternalAuth',
        'plugin.BEdita/Core.Roles',
        'plugin.BEdita/Core.RolesUsers',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
        'plugin.BEdita/Core.History',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Users = TableRegistry::getTableLocator()->get('Users');
        LoggedUser::setUser(['id' => 1]);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
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
     * Data provider for `testSave` test case.
     *
     * @return array
     */
    public function saveProvider()
    {
        return [
            'valid' => [
                false,
                [
                    'username' => 'globetrotter user',
                    'password_hash' => (new WeakPasswordHasher(['hashType' => 'md5']))->hash('hunter1'),
                    'blocked' => 0,
                    'last_login' => null,
                    'last_login_err' => null,
                    'num_login_err' => 1,
                    'verified' => '2017-05-29 11:36:00',
                ],
            ],
            'notUniqueUname' => [
                true,
                [
                    'username' => 'support user',
                    'password_hash' => (new WeakPasswordHasher(['hashType' => 'md5']))->hash('hunter2'),
                    'blocked' => 0,
                    'last_login' => null,
                    'last_login_err' => null,
                    'num_login_err' => 1,
                    'verified' => '2017-05-29 11:36:00',
                    'uname' => 'gustavo-supporto',
                ],
            ],
        ];
    }

    /**
     * Test entity save.
     *
     * @param bool $changed
     * @param array $data
     * @return void
     * @dataProvider saveProvider
     * @coversNothing
     */
    public function testSave(bool $changed, array $data)
    {
        $entity = $this->Users->newEntity($data);
        $success = (bool)$this->Users->save($entity);

        $this->assertTrue($success);

        if ($changed) {
            $this->assertNotEquals($data['uname'], $entity->uname);
        } elseif (isset($data['uname'])) {
            $this->assertEquals($data['uname'], $entity->uname);
        }
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
     * @return void
     * @dataProvider validationProvider
     * @coversNothing
     */
    public function testValidation($expected, array $data)
    {
        $user = $this->Users->newEntity([]);
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
     * @covers ::login()
     */
    public function testLogin()
    {
        $identity = new Identity($this->Users->get(1));
        $expected = new FrozenTime();
        $this->Users->dispatchEvent('Authentication.afterIdentify', compact('identity'));

        $lastLogin = $this->Users->get(1)->last_login;
        static::assertNotNull($lastLogin);
        static::assertLessThanOrEqual(2, $expected->diffInSeconds($lastLogin));
    }

    /**
     * Test login with no data.
     *
     * @return void
     * @covers ::login()
     */
    public function testLoginNoData()
    {
        $result = $this->Users->dispatchEvent('Authentication.afterIdentify', []);
        static::assertEmpty($result->getData());
        static::assertNull($result->getResult());
    }

    /**
     * Test `login` finder.
     *
     * @return void
     * @covers ::findLogin()
     */
    public function testFindLogin()
    {
        $user = $this->Users->find('login')->where(['username' => 'second user'])->first();
        static::assertNotEmpty($user);
        static::assertEquals('second user', $user['username']);
    }

    /**
     * Test `loginRoles` finder.
     *
     * @return void
     * @covers ::findLoginRoles()
     */
    public function testFindLoginRoles()
    {
        $user = $this->Users->find('loginRoles')->where(['username' => 'second user'])->first();
        static::assertNotEmpty($user);
        static::assertEquals('second user', $user['username']);
        static::assertNotEmpty($user['roles']);
        static::assertEquals(1, count($user['roles']));
        static::assertEquals('second role', $user['roles'][0]['name']);
    }

    /**
     * Test `login` finder fail.
     *
     * @return void
     * @covers ::findLogin()
     */
    public function testFailFindLogin()
    {
        $user = $this->Users->get(5);
        $user->blocked = true;
        $this->Users->saveOrFail($user);

        $user = $this->Users->find('login')->where(['username' => 'second user'])->first();
        static::assertNull($user);
    }

    /**
     * Test handling of external auth login event.
     *
     * @return void
     * @covers ::externalAuthLogin()
     */
    public function testExternalAuthLogin()
    {
        //1. Add external auth and create new user
        $authProvider = TableRegistry::getTableLocator()->get('AuthProviders')->get(2);
        $providerUsername = 'gustavo';
        $params = ['job' => 'head of technical support'];
        $userId = null;

        $event = $this->Users->dispatchEvent('Auth.externalAuth', compact('authProvider', 'providerUsername', 'userId', 'params'));

        /** @var \BEdita\Core\Model\Entity\ExternalAuth $externalAuth */
        $externalAuth = $event->getResult();
        static::assertInstanceOf($this->Users->ExternalAuth->getEntityClass(), $externalAuth);
        static::assertFalse($externalAuth->isNew());
        static::assertNotNull($externalAuth->id);
        static::assertEquals(16, $externalAuth->user_id);

        // 2. Add external auth to current user
        $authProvider = TableRegistry::getTableLocator()->get('AuthProviders')->get(1);
        $providerUsername = 'friend of gustavo';
        $params = ['job' => 'support of technical support'];
        $userId = 5;

        $event = $this->Users->dispatchEvent('Auth.externalAuth', compact('authProvider', 'providerUsername', 'userId', 'params'));

        /** @var \BEdita\Core\Model\Entity\ExternalAuth $externalAuth */
        $externalAuth = $event->getResult();
        static::assertInstanceOf($this->Users->ExternalAuth->getEntityClass(), $externalAuth);
        static::assertFalse($externalAuth->isNew());
        static::assertNotNull($externalAuth->id);
        static::assertEquals(5, $externalAuth->user_id);
    }

    /**
     * Test deleted field on user deleted.
     *
     * @return void
     * @coversNothing
     */
    public function testDeleted()
    {
        $user = $this->Users->get(5);
        $user->deleted = true;
        $success = $this->Users->save($user);
        $this->assertTrue((bool)$success);
        $deleted = $this->Users->get(5)->deleted;
        $this->assertEquals(true, $deleted);
    }

    /**
     * Test soft delete admin user
     *
     * @return void
     * @covers ::beforeSave
     */
    public function testSoftDeleteAdminUser()
    {
        $this->expectException(\BEdita\Core\Exception\ImmutableResourceException::class);
        $this->expectExceptionCode('403');
        $this->expectExceptionMessage('Could not delete "User" 1');
        $user = $this->Users->get(UsersTable::ADMIN_USER);
        $user->deleted = true;
        $this->Users->save($user);
    }

    /**
     * Test soft delete logged user
     *
     * @return void
     * @covers ::beforeSave
     */
    public function testSoftDeleteLoggedUser()
    {
        $this->expectException(\Cake\Http\Exception\BadRequestException::class);
        $this->expectExceptionCode('400');
        $this->expectExceptionMessage('Logged users cannot delete their own account');
        LoggedUser::setUser(['id' => 5]);
        $user = $this->Users->get(5);
        $user->deleted = true;
        $this->Users->save($user);
    }

    /**
     * Test soft delete second user
     *
     * @return void
     * @covers ::beforeSave
     */
    public function testSoftDeleteSecondUser()
    {
        $user = $this->Users->get(5);
        $user->deleted = true;
        static::assertTrue((bool)$this->Users->save($user));
    }

    /**
     * Test delete admin user
     *
     * @return void
     * @covers ::beforeDelete
     */
    public function testHardDeleteAdminUser()
    {
        $this->expectException(\BEdita\Core\Exception\ImmutableResourceException::class);
        $this->expectExceptionCode('403');
        $this->expectExceptionMessage('Could not delete "User" 1');
        $user = $this->Users->get(UsersTable::ADMIN_USER);
        $this->Users->delete($user);
    }

    /**
     * Test hard delete second user
     *
     * @return void
     * @covers ::beforeDelete
     */
    public function testHardDeleteSecondUser()
    {
        $user = $this->Users->get(5);
        static::assertTrue((bool)$this->Users->delete($user));
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

    /**
     * Data provider for `testValidationSignup` test case.
     *
     * @return array
     */
    public function validationSignupProvider()
    {
        return [
            'valid' => [
                true,
                [
                    'username' => 'some_unique_value',
                    'password_hash' => 'password',
                    'email' => 'my@email.com',
                    'status' => 'draft',
                ],
            ],
            'missing password' => [
                false,
                [
                    'username' => 'some_unique_value',
                    'email' => 'my@email.com',
                ],
            ],
            'missing email' => [
                false,
                [
                    'username' => 'some_unique_value',
                    'password_hash' => 'a great password',
                ],
            ],
            'username only' => [
                true,
                [
                    'username' => 'some_unique_value',
                ],
                [
                    'requireEmail' => false,
                    'requirePassword' => false,
                ],
            ],
        ];
    }

    /**
     * Test validation signup.
     *
     * @param bool $expected Expected result.
     * @param array $data Data to be validated.
     * @param array $config Signup configuration.
     * @return void
     * @covers ::validationSignup()
     * @dataProvider validationSignupProvider
     */
    public function testValidationSignup($expected, array $data, array $config = [])
    {
        Configure::write('Signup', $config);

        $user = $this->Users->newEntity([]);
        $this->Users->patchEntity($user, $data, ['validate' => 'signup']);
        $user->type = 'users';

        $error = (bool)$user->getErrors();
        $this->assertEquals($expected, !$error);

        if ($expected) {
            $success = $this->Users->save($user);
            $this->assertTrue((bool)$success);
        }
    }

    /**
     * Test validation signup.
     *
     * @return void
     * @covers ::validationSignupExternal()
     */
    public function testValidationSignupExternal()
    {
        $data = [
            'username' => 'test',
            'email' => 'test@email.com',
        ];

        $user = $this->Users->newEntity([]);
        $this->Users->patchEntity($user, $data, ['validate' => 'signupExternal']);

        $error = (bool)$user->getErrors();
        static::assertEmpty($error);
    }

    /**
     * Test finder for my objects.
     *
     * @return void
     * @covers ::findMine()
     */
    public function testFindMine()
    {
        $expected = [
            1 => 1,
        ];

        $result = $this->Users->find('mine')
            ->find('list', ['keyField' => 'id', 'valueField' => 'id'])
            ->toArray();

        static::assertEquals($expected, $result);
    }

    /**
     * Test `findRoles` method.
     *
     * @return void
     * @covers ::findRoles()
     * @covers ::rolesNamesIds()
     */
    public function testFindRoles()
    {
        $expected = [
            1 => 1,
            5 => 5,
        ];

        $result = $this->Users->find('roles', [1, 'second role'])
            ->find('list', ['keyField' => 'id', 'valueField' => 'id'])
            ->toArray();

        static::assertEquals($expected, $result);
    }

    /**
     * Test `findRoles` failure method.
     *
     * @return void
     * @covers ::findRoles()
     */
    public function testFindRolesFail()
    {
        $this->expectException(\BEdita\Core\Exception\BadFilterException::class);
        $this->expectExceptionMessage('Missing required parameter "roles"');
        $this->Users->find('roles', [])
            ->toArray();
    }

    /**
     * Data provider for `beforeMarshal`
     *
     * @return array
     */
    public function beforeMarshalProvider()
    {
        return [
            'ok' => [
                [
                    'username' => 'gustavo2',
                    'password' => 'password2',
                ],
                '',
                '',
                true,
            ],
            'failSimple' => [
                [
                    'username' => 'gustavo2',
                    'password' => 'pp',
                ],
                '/\w{3,}/',
                'Password must contain at least 3 valid alphanumeric characters',
                new BadRequestException('Password must contain at least 3 valid alphanumeric characters'),
            ],
        ];
    }

    /**
     * Test `beforeMarshal` method
     *
     * @param array $data User data to save
     * @param string $passwdRule Password regexp rule
     * @param string $passwdMessage Password validation error message
     * @param bool|Exception $expected Save result or exception
     * @return void
     * @dataProvider beforeMarshalProvider
     * @covers ::beforeMarshal()
     */
    public function testBeforeMarshal($data, $passwdRule, $passwdMessage, $expected)
    {
        Configure::write('Auth.passwordPolicy.rule', $passwdRule);
        Configure::write('Auth.passwordPolicy.message', $passwdMessage);
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionMessage($expected->getMessage());
        }

        $user = $this->Users->newEntity([]);
        $user = $this->Users->patchEntity($user, $data);
        $success = $this->Users->save($user);

        $this->assertEquals($expected, (bool)$success);
    }

    /**
     * Data provider for `testCustomPropsCreate`
     *
     * @return array
     */
    public function customPropsCreateProvider()
    {
        return [
            'users custom prop' => [
                [
                    'username' => 'gustavo_supporto',
                    'another_username' => 'supporto_gustavo',
                ],
            ],
            'profiles custom prop' => [
                [
                    'username' => 'gustavo_supporto',
                    'another_surname' => 'aiuto',
                ],
            ],
            'both custom prop' => [
                [
                    'username' => 'gustavo_supporto',
                    'another_email' => 'supporto@gusta.vo',
                    'another_surname' => 'helpus',
                ],
            ],
        ];
    }

    /**
     * Test create new user with custom properties
     *
     * @param array $data User data
     * @return void
     * @dataProvider customPropsCreateProvider
     * @coversNothing
     */
    public function testCustomPropsCreate(array $data)
    {
        $user = $this->Users->newEntity([]);
        $user = $this->Users->patchEntity($user, $data);
        $user->type = 'users';
        $success = $this->Users->save($user);

        $user = $this->Users->get($success['id']);
        foreach ($data as $key => $value) {
            static::assertEquals($value, $user[$key]);
        }
    }

    /**
     * Test users unique email via application rules
     *
     * @return void
     * @coversNothing
     */
    public function testValidateUniqueEmail()
    {
        // with a user email in use -> save is not allowed
        $user = $this->Users->newEntity([
            'username' => 'gustavosupporto',
        ]);
        $user->email = 'first.user@example.com';
        $result = $this->Users->save($user);
        static::assertFalse($result);

        // with a profile email in use -> save is allowed
        $user = $this->Users->newEntity([
            'username' => 'gustavosupporto',
        ]);
        $user->email = 'gustavo.supporto@channelweb.it';
        $result = $this->Users->save($user);
        static::assertNotEmpty($result);
        static::assertEquals(16, $result->get('id'));
    }

    /**
     * Test delete method with anonymization
     *
     * @return void
     * @covers ::delete()
     * @covers ::anonymizeUser()
     * @covers ::notNullableColumns()
     */
    public function testAnonymousDelete()
    {
        $user = $this->Users->get(5);
        $result = $this->Users->delete($user);
        static::assertTrue($result);
        // verify user entity is anonymized
        $result = $this->Users->get(5);
        static::assertEquals('__deleted-5', $result->get('username'));
        static::assertEquals('__deleted-5', $result->get('uname'));
        static::assertEquals(true, $result->get('locked'));
        static::assertNull($result->get('last_login'));
        // verify external_auth records have been removed
        static::assertFalse($this->Users->ExternalAuth->exists(['user_id' => 5]));
    }

    /**
     * Test delete method with anonymization when
     * created_by/modified_by fk is set on other objects
     *
     * @return void
     * @covers ::delete()
     * @covers ::anonymizeUser()
     * @covers ::notNullableColumns()
     */
    public function testAnonymousDeleteOther()
    {
        $user = $this->Users->get(5);
        $user->created_by = 1;
        $user->modified_by = 1;
        $user = $this->Users->saveOrFail($user);

        $result = $this->Users->delete($user);
        static::assertTrue($result);
        $result = $this->Users->get(5);
        static::assertEquals('__deleted-5', $result->get('username'));
        static::assertEquals('__deleted-5', $result->get('uname'));
        static::assertEquals(true, $result->get('locked'));
        static::assertNull($result->get('last_login'));
        // verify external_auth records have been removed
        static::assertFalse($this->Users->ExternalAuth->exists(['user_id' => 5]));
    }

    /**
     * Test delete method without anonymization
     *
     * @return void
     * @covers ::delete()
     */
    public function testDelete()
    {
        $user = $this->Users->get(5);
        $user->created_by = 1;
        $user->modified_by = 1;
        $user = $this->Users->saveOrFail($user);

        $table = TableRegistry::getTableLocator()->get('Objects');
        $doc = $table->get(3);
        $doc->modified_by = 1;
        $doc = $table->saveOrFail($doc);

        $result = $this->Users->delete($user);
        static::assertTrue($result);
        static::assertFalse($this->Users->exists(['id' => 5]));
    }

    /**
     * Data provider for `testPrefix` test case.
     *
     * @return array
     */
    public function prefixProvider()
    {
        return [
            'valid' => [
                true,
                [
                    'username' => 'some_unique_value',
                ],
            ],
            'bad username' => [
                new BadRequestException('"username" cannot start with reserved word "__deleted-"'),
                [
                    'username' => '__deleted-something',
                    'uname' => 'a-valid-uname',
                ],
            ],
            'bad uname' => [
                new BadRequestException('"uname" cannot start with reserved word "__deleted-"'),
                [
                    'username' => 'validusername',
                    'uname' => '__deleted-uname',
                ],
            ],
        ];
    }

    /**
     * Test avoid reserved `__deleted-` prefix
     *
     * @param bool|\Exception $expected Expected result.
     * @param array $data Data to be validated.
     * @return void
     * @dataProvider prefixProvider
     * @covers ::beforeSave()
     */
    public function testPrefix($expected, array $data)
    {
        if ($expected instanceof \Exception) {
            $this->expectException(get_class($expected));
            $this->expectExceptionCode($expected->getCode());
            $this->expectExceptionMessage($expected->getMessage());
        }

        $user = $this->Users->newEntity([]);
        $this->Users->patchEntity($user, $data);

        $success = $this->Users->save($user);
        static::assertEquals($expected, (bool)$success);
    }
}
