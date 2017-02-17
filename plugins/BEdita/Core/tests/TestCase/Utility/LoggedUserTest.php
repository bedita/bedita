<?php
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
 * \BEdita\Core\Utility\LoggedUser Test Case
 *
 * @covers \BEdita\Core\Utility\LoggedUser
 */
class LoggedUserTest extends TestCase
{

    /**
     * Test singleton
     */
    public function testFail()
    {
        $reflection = new \ReflectionClass('\BEdita\Core\Utility\LoggedUser');
        $this->assertFalse($reflection->isCloneable());
        $this->assertFalse($reflection->getConstructor()->isPublic());
    }

    /**
     * Test user data
     *
     * @return void
     */
    public function testUserData()
    {
        $userData = LoggedUser::getUser();

        LoggedUser::setUser(false);
        $this->assertEquals($userData['id'], LoggedUser::id());

        LoggedUser::setUser(['id' => 10, 'somefield' => 'somevalue']);
        $this->assertEquals(10, LoggedUser::id());

        LoggedUser::setUser($userData);
        $this->assertEquals($userData, LoggedUser::getUser());
    }
}
