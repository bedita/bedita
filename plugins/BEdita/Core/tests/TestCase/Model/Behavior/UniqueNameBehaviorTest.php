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

namespace BEdita\Core\Test\TestCase\Model\Behavior;

use BEdita\Core\Model\Behavior\UniqueNameBehavior;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Model\Behavior\UniqueNameBehavior} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Model\Behavior\UniqueNameBehavior
 */
class UniqueNameBehaviorTest extends TestCase
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
     * setUp method
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Data provider for `testUnique` test case.
     *
     * @return array
     */
    public function uniqueUserProvider()
    {
        return [
            'simple' => [
                [
                    'Great Lion',
                    'user-great-lion'
                ]
            ],
            'accents' => [
                [
                    'Oèù yahìì',
                    'user-oeu-yahii'
                ]
            ],
            'others' => [
                [
                    '¬5654@-BIG STRING',
                    'user-5654-big-string'
                ]
            ],
        ];
    }

    /**
     * testUnique method
     *
     * @return void
     *
     * @dataProvider uniqueUserProvider
     */
    public function testUniqueUser($input)
    {
        $this->Users = TableRegistry::get('Users');
        $user = $this->Users->newEntity();

        $data['username'] = $input[0];
        $this->Users->patchEntity($user, $data);
        $this->Users->uniqueName($user);

        $this->assertEquals($user['uname'], $input[1]);
    }
}
