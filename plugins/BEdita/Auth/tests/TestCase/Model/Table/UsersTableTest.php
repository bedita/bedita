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

namespace BEdita\Auth\Test\TestCase\Model\Table;

use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Auth\Model\Table\UsersTable} Test Case
 *
 * @coversDefaultClass \BEdita\Auth\Model\Table\UsersTable
 */
class UsersTableTest extends TestCase
{

    /**
     * Test subject
     *
     * @var \BEdita\Auth\Model\Table\UsersTable
     */
    public $Users;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Auth.users',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->Users = TableRegistry::get(
            'Users',
            TableRegistry::exists('Users') ? [] : ['className' => 'BEdita\Auth\Model\Table\UsersTable']
        );
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
                    'password' => null,
                ],
            ],
            'notUnique' => [
                false,
                [
                    'username' => 'first user',
                    'password' => 'password',
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
            $this->assertEquals($expected, (bool)$success);
        }
    }
}
