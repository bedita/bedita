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

use Cake\Event\Event;
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

        $this->Users = TableRegistry::get('BEdita/Core.Users');
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->Users);

        TableRegistry::clear();

        parent::tearDown();
    }

    /**
     * Test initialization.
     *
     * @return void
     * @covers ::initialize()
     */
    public function testInitialization()
    {
        $this->Users->initialize([]);
        $this->assertEquals('users', $this->Users->table());
        $this->assertEquals('id', $this->Users->primaryKey());
        $this->assertEquals('username', $this->Users->displayField());

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
                    'object_type_id' => 3,
                    'status' => 'draft',
                    'uname' => 'some-unique-value',
                    'lang' => 'eng',
                    'created_by' => 1,
                    'modified_by' => 1,
                    'company' => false,
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
     * @covers ::validationDefault
     * @covers ::buildRules
     */
    public function testValidation($expected, array $data)
    {
        $user = $this->Users->newEntity();
        $this->Users->patchEntity($user, $data);

        $error = (bool)$user->errors();
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
        $this->Users->login(new Event('Auth.afterIdentify', null, [['id' => 1]]));

        $lastLogin = $this->Users->get(1)->get('last_login');
        $this->assertNotNull($lastLogin);
        $this->assertEquals(time(), $lastLogin->timestamp, '', 1);
    }
}
