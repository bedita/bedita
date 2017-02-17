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

namespace BEdita\Core\Test\TestCase\Model\Behavior;

use BEdita\Core\Model\Behavior\UserModifiedBehavior;
use BEdita\Core\Utility\LoggedUser;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Behavior\UserModifiedBehavior} Test Case
 *
 * @covers \BEdita\Core\Model\Behavior\UserModifiedBehavior
 */
class UserModifiedBehaviorTest extends TestCase
{

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
     * testUserFields method
     *
     * @return void
     */
    public function testUserFields()
    {
        $this->Users = TableRegistry::get('Users');

        $user = $this->Users->newEntity();
        $data['username'] = 'testusername';
        $user->created_by = LoggedUser::id() + 1;
        $this->Users->patchEntity($user, $data);
        $this->Users->setupUserFields($user);

        $this->assertEquals($user['created_by'], LoggedUser::id());

        $user = $this->Users->get(1);
        $user->modified_by = LoggedUser::id() + 1;
        $this->Users->patchEntity($user, $data);
        $this->Users->setupUserFields($user);

        $this->assertEquals($user['modified_by'], LoggedUser::id());
    }
}
