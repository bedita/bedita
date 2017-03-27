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
use Cake\Datasource\EntityInterface;
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
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
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
     * @covers ::uniqueName()
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
                [],
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
            'emptySourceField' => [
                'super_secret',
                '',
                [
                    'sourceField' => 'name',
                ],
            ]
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
     * @covers ::generateUniqueName()
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
     * @covers ::uniqueNameExists()
     */
    public function testUniqueNameExists($uname, $id, $expected)
    {
        $Users = TableRegistry::get('Users');
        $behavior = $Users->behaviors()->get('UniqueName');
        $result = $behavior->uniqueNameExists($uname, $id);

        $this->assertEquals($expected, $result);
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
     * test uniqueName() conflicts / missing
     *
     * @return void
     *
     * @covers ::uniqueName()
     */
    public function testUniqueNameMissing()
    {
        $Documents = TableRegistry::get('Documents');
        $behavior = $Documents->behaviors()->get('UniqueName');

        $data = ['title' => 'Some data', 'uname' => 'some-data'];
        $document = $Documents->newEntity($data);

        $document->set('uname', '');
        $behavior->uniqueName($document);
        $this->assertEquals($document->get('uname'), 'some-data');

        $document->set('uname', 'first-user');
        $behavior->uniqueName($document);
        $this->assertNotEquals($document->get('uname'), 'first-user');

        $document->set('uname', '');
        $document->set('title', '');
        $behavior->uniqueName($document);
        static::assertContains('documents_', $document->get('uname'));
    }

    /**
     * test generate uname before save
     *
     * @return void
     * @covers ::beforeSave()
     */
    public function testBeforeSave()
    {
        $Documents = TableRegistry::get('Documents');
        $entity = $Documents->newEntity([
            'title' => 'uh là la'
        ]);

        $Documents->eventManager()->on('Model.beforeSave', function (Event $event, EntityInterface $entity) {
            $uname = $entity->get('uname');
            static::assertNotEmpty($uname);
            static::assertEquals('uh-la-la', $uname);

            return false;
        });

        $Documents->save($entity);
    }
}
