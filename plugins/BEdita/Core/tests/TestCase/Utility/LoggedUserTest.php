<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Utility;

use BEdita\Core\Utility\LoggedUser;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Utility\LoggedUser} Test Case
 *
 * @covers \BEdita\Core\Utility\LoggedUser
 */
class LoggedUserTest extends TestCase
{
    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        LoggedUser::resetUser();
    }

    /**
     * Test user data
     *
     * @return void
     */
    public function testUserData()
    {
        $this->assertEquals([], LoggedUser::getUser());

        $userData = ['id' => 10, 'somefield' => 'somevalue'];
        LoggedUser::setUser($userData);
        $this->assertEquals(10, LoggedUser::id());

        LoggedUser::setUser([]);
        $this->assertEquals($userData['id'], LoggedUser::id());

        $this->assertEquals($userData, LoggedUser::getUser());

        LoggedUser::setUserAdmin();
        $expected = LoggedUser::getUserAdmin();
        $actual = LoggedUser::getUser();
        $this->assertEquals($expected, $actual);
    }
}
