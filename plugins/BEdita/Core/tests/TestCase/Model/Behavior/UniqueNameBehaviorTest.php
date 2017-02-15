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
                'Great Lion',
                'user-great-lion',
            ],
            'accents' => [
                'Oèù yahìì',
                'user-oeu-yahii',
            ],
            'others' => [
                '¬5654@-BIG STRING',
                'user-5654-big-string',
            ],
        ];
    }

    /**
     * testUnique method
     *
     * @param string $username Username.
     * @param string $uname Expected unique name.
     * @return void
     *
     * @dataProvider uniqueUserProvider
     */
    public function testUniqueUser($username, $uname)
    {
        $Users = TableRegistry::get('Users');
        $user = $Users->newEntity();

        $Users->patchEntity($user, compact('username'));
        $Users->uniqueName($user);
        $user->type = 'users';
        $Users->save($user);

        $this->assertEquals($user['uname'], $uname);
    }

    /**
     * Data provider for `testGenerate` test case.
     *
     * @return array
     */
    public function generateUniqueUserProvider()
    {
        return [
            'defaultConfig' => [
                'Dummy Person',
                'John Doe',
                [
                ],
            ],
            'customConfig' => [
                'Another Dummy Person',
                'Julia Doe',
                [
                    'sourceField' => 'name',
                    'prefix' => 'u_',
                    'replacement' => ':',
                    'separator' => '|',
                    'hashlength' => 3
                ],
            ],
        ];
    }

    /**
     * testGenerate method
     *
     * @param string $username Username.
     * @param string $name Full name.
     * @param array $config Configuration.
     * @return void
     *
     * @dataProvider generateUniqueUserProvider
     */
    public function testGenerateUniqueName($username, $name, $config)
    {
        $Users = TableRegistry::get('Users');
        $user = $Users->newEntity();
        $Users->patchEntity($user, compact('username', 'name'));
        $behavior = $Users->behaviors()->get('UniqueName');
        $uname1 = $behavior->generateUniqueName($user, $config);
        $uname2 = $behavior->generateUniqueName($user, $config, true);

        $this->assertNotEquals($uname1, $uname2);
    }

    /**
     * Data provider for `testNameExists` test case.
     *
     * @return array
     */
    public function uniqueNameExistsProvider()
    {
        return [
            'uname exists, id null' => [
                'first-user',
                null,
                true,
            ],
            'uname exists, no collision' => [
                'first-user',
                1,
                false,
            ],
            'uname exists, collision' => [
                'first-user',
                2,
                true,
            ],
            'uname does not exist, id null' => [
                'aaaaa-bbbbb-ccccc',
                null,
                false,
            ],
            'uname does not exist, id not null' => [
                'aaaaa-bbbbb-ccccc',
                1,
                false,
            ],
        ];
    }

    /**
     * testNameExists method
     *
     * @param string $uname Unique name to check.
     * @param int|null $id ID to exclude.
     * @param bool $expected Expected result.
     * @return void
     * @dataProvider uniqueNameExistsProvider
     */
    public function testUniqueNameExists($uname, $id, $expected)
    {
        $Users = TableRegistry::get('Users');
        $behavior = $Users->behaviors()->get('UniqueName');
        $result = $behavior->uniqueNameExists($uname, $id);

        $this->assertEquals($expected, $result);
    }
}
