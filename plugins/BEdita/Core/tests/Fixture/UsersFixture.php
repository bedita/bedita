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

use BEdita\Core\TestSuite\Fixture\TestFixture;
use Cake\Auth\DefaultPasswordHasher;
use Cake\Auth\WeakPasswordHasher;

/**
 * Fixture for `users` table.
 */
class UsersFixture extends TestFixture
{

    /**
     * {@inheritDoc}
     */
    public function init()
    {
        $this->records = [
            [
                'id' => 1,
                'username' => 'first user',
                'password_hash' => (new WeakPasswordHasher(['hashType' => 'md5']))->hash('password1'),
                'blocked' => 0,
                'last_login' => null,
                'last_login_err' => null,
                'num_login_err' => 1,
            ],
            [
                'id' => 5,
                'username' => 'second user',
                'password_hash' => (new DefaultPasswordHasher())->hash('password2'),
                'blocked' => 0,
                'last_login' => '2016-03-15 09:57:38',
                'last_login_err' => '2016-03-15 09:57:38',
                'num_login_err' => 0,
            ],
        ];

        parent::init();
    }
}
