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

use ArrayObject;
use BEdita\Core\Model\Behavior\UniqueNameBehavior;
use Cake\Event\Event;
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
        $user->type = 'users';
        $this->Users->save($user);

        $this->assertEquals($user['uname'], $input[1]);
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
                [
                    'Dummy Person',
                    'John Doe',
                    [
                    ]
                ]
            ],
            'customConfig' => [
                [
                    'Another Dummy Person',
                    'Julia Doe',
                    [
                        'sourceField' => 'name',
                        'prefix' => 'u_',
                        'replacement' => ':',
                        'separator' => '|',
                        'hashlength' => 3
                    ]
                ]
            ]
        ];
    }

    /**
     * testGenerate method
     *
     * @return void
     *
     * @dataProvider generateUniqueUserProvider
     * @covers ::generateUniqueName()
     */
    public function testGenerateUniqueName($input)
    {
        $this->Users = TableRegistry::get('Users');
        $user = $this->Users->newEntity();
        $data['username'] = $input[0];
        $data['name'] = $input[1];
        $this->Users->patchEntity($user, $data);
        $config = $input[2];
        $behavior = $this->Users->behaviors()->get('UniqueName');
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
                [
                    'first-user',
                    null,
                    true
                ]
            ],
            'uname exists, no collision' => [
                [
                    'first-user',
                    1,
                    false
                ]
            ],
            'uname exists, collision' => [
                [
                    'first-user',
                    2,
                    true
                ]
            ],
            'uname does not exist, id null' => [
                [
                    'aaaaa-bbbbb-ccccc',
                    null,
                    false
                ]
            ],
            'uname does not exist, id not null' => [
                [
                    'aaaaa-bbbbb-ccccc',
                    1,
                    false
                ]
            ]
        ];
    }

    /**
     * testNameExists method
     *
     * @return void
     *
     * @dataProvider uniqueNameExistsProvider
     */
    public function testUniqueNameExists($input)
    {
        $this->Users = TableRegistry::get('Users');
        $behavior = $this->Users->behaviors()->get('UniqueName');
        $result = $behavior->uniqueNameExists($input[0], $input[1]);

        $this->assertEquals($result, $input[2]);
    }

    /**
     * Data provider for `testUniqueNameFromValue` test case.
     *
     * @return array
     */
    public function uniqueFromValueProvider()
    {
        return [
            'simpleNoConf' => [
               'Dummy expressions: olè, ça va',
               'dummy-expressions-ole-ca-va',
               [],
               false,
            ],
            'customConfig' => [
                'ROMANES EUNT DOMUS!',
                'pre_romanes_eunt_domus',
                [
                    'prefix' => 'pre_',
                    'replacement' => '_',
                ],
                false
            ],
            'regenerate' => [
                'Romani ite domum!',
                'romani-ite-domum_',
                [
                    'separator' => '_',
                    'hashlength' => 6,
                ],
                true
            ],
        ];
    }

    /**
     * test uniqueNameFromValue()
     *
     * @return void
     *
     * @dataProvider uniqueFromValueProvider
     * @covers ::uniqueNameFromValue()
     */
    public function testUniqueNameFromValue($value, $expected, $cfg, $regenerate)
    {
        $behavior = TableRegistry::get('Objects')->behaviors()->get('UniqueName');
        $result = $behavior->uniqueNameFromValue($value, $cfg, $regenerate);

        if ($regenerate) {
            $cfg = array_merge($behavior->config(), $cfg);
            $result = substr($result, 0, strlen($result) - $cfg['hashlength']);
        }
        $this->assertEquals($result, $expected);
    }


    /**
     * test uniqueNameFromValue()
     *
     * @return void
     *
     * @dataProvider uniqueFromValueProvider
     * @covers ::beforeMarshal()
     */
    public function testBeforeMarshal()
    {
        $behavior = TableRegistry::get('Objects')->behaviors()->get('UniqueName');
        $data = [
            'title' => '',
            'type' => 'documents'
        ];
        $dataObj = new ArrayObject($data);
        $behavior->beforeMarshal(new Event('Dummy'), $dataObj, new ArrayObject());
        $this->assertEquals('documents', $dataObj['uname']);
    }
}
