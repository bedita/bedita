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

namespace BEdita\Core\Test\Fixture;

use BEdita\Core\Auth\LegacyMd5PasswordHasher;
use BEdita\Core\TestSuite\Fixture\TestFixture;
use Cake\Auth\DefaultPasswordHasher;

/**
 * Fixture for `users` table.
 */
class UsersFixture extends TestFixture
{

    /**
     * {@inheritDoc}
     *
     * @codingStandardsIgnore
     */
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 10, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'username' => ['type' => 'string', 'length' => 100, 'null' => false, 'default' => null, 'comment' => 'login user name', 'precision' => null, 'fixed' => null],
        'password' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'comment' => 'login password, if empty external auth is used', 'precision' => null],
        'blocked' => ['type' => 'boolean', 'length' => null, 'null' => false, 'default' => '0', 'comment' => 'user blocked flag', 'precision' => null],
        'last_login' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => 'last succcessful login datetime', 'precision' => null],
        'last_login_err' => ['type' => 'datetime', 'length' => null, 'null' => true, 'default' => null, 'comment' => 'last login filaure datetime', 'precision' => null],
        'num_login_err' => ['type' => 'integer', 'length' => 4, 'unsigned' => false, 'null' => false, 'default' => '0', 'comment' => 'number of consecutive login failures', 'precision' => null, 'autoIncrement' => null],
        'created' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => 'record creation date', 'precision' => null],
        'modified' => ['type' => 'datetime', 'length' => null, 'null' => false, 'default' => null, 'comment' => 'record last modification date', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
            'username' => ['type' => 'unique', 'columns' => ['username'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci',
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->records = [
            [
                'username' => 'first user',
                'password' => (new LegacyMd5PasswordHasher())->hash('password1'),
                'blocked' => 0,
                'last_login' => null,
                'last_login_err' => null,
                'num_login_err' => 1,
                'created' => '2016-03-15 09:57:38',
                'modified' => '2016-03-15 09:57:38',
            ],
            [
                'username' => 'second user',
                'password' => (new DefaultPasswordHasher())->hash('password2'),
                'blocked' => 0,
                'last_login' => '2016-03-15 09:57:38',
                'last_login_err' => '2016-03-15 09:57:38',
                'num_login_err' => 0,
                'created' => '2016-03-15 09:57:38',
                'modified' => '2016-03-15 09:57:38',
            ],
        ];

        parent::init();
    }
}
