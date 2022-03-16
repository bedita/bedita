<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
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
use BEdita\Core\Utility\LoggedUser;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Entity;
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
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.History',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        LoggedUser::setUser(['id' => 1]);
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();

        LoggedUser::resetUser();
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
            'preserveUnderscore' => [
                'Guy_Dude',
                'user-guy_dude',
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
     * @dataProvider uniqueUserProvider
     * @covers ::uniqueName()
     */
    public function testUniqueUser($username, $uname)
    {
        $Users = TableRegistry::getTableLocator()->get('Users');
        $user = $Users->newEntity([]);

        $user = $Users->patchEntity($user, compact('username'));
        $Users->uniqueName($user);
        $user->type = 'users';
        $Users->save($user);

        $this->assertEquals($user['uname'], $uname);
    }

    /**
     * Data provider for `testUniqueName` test case.
     *
     * @return array
     */
    public function uniqueNameProvider()
    {
        return [
            'mix' => [
                'Amazing test! 100% gluten-free!',
                'amazing-test-100-gluten-free',
            ],
            'preserveUnderscore' => [
                '@Guy_Dude made this test',
                'guy_dude-made-this-test',
            ],
            'accents' => [
                'àéì òù',
                'aei-ou',
            ],
        ];
    }

    /**
     * testUniqueName method
     *
     * @param string $value Original uname.
     * @param string $expected Expected sanitized uname.
     * @return void
     * @dataProvider uniqueNameProvider
     * @covers ::uniqueName()
     */
    public function testUniqueName($value, $expected)
    {
        $behavior = TableRegistry::getTableLocator()->get('Objects')->behaviors()->get('UniqueName');
        $entity = new Entity(['uname' => $value]);
        $behavior->uniqueName($entity);

        $this->assertEquals($expected, $entity->get('uname'));
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
                    'hashlength' => 3,
                ],
            ],
            'emptySourceField' => [
                'super_secret',
                '',
                [
                    'sourceField' => 'name',
                ],
            ],
            'generator' => [
                'RandomName',
                'John Doe',
                [
                    'generator' => function ($entity) {
                        return str_shuffle($entity->get('username'));
                    },
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
     * @dataProvider generateUniqueUserProvider
     * @covers ::generateUniqueName()
     */
    public function testGenerateUniqueName($username, $name, $config)
    {
        $Users = TableRegistry::getTableLocator()->get('Users');
        $user = $Users->newEntity([]);
        $Users->patchEntity($user, compact('username', 'name'));
        $behavior = $Users->behaviors()->get('UniqueName');
        $uname1 = $behavior->generateUniqueName($user, false, $config);
        $uname2 = $behavior->generateUniqueName($user, true, $config);

        $this->assertNotEquals($uname1, $uname2);
    }

    /**
     * Data provider for `testRegenerate` test case.
     *
     * @return array
     */
    public function regenerateUniqueNameProvider()
    {
        return [
            'providedUname' => [
                'provided-uname',
                'provided-title',
            ],
            'noUname' => [
                null,
                'my-title',
            ],
        ];
    }

    /**
     * testRegenerate method
     *
     * @param string $uname Uname.
     * @param string $title Title.
     * @return void
     * @dataProvider regenerateUniqueNameProvider
     * @covers ::generateUniqueName()
     */
    public function testRegenerateUniqueName($uname, $title)
    {
        $Folders = TableRegistry::getTableLocator()->get('Folders');
        $folder = $Folders->newEntity([]);
        $Folders->patchEntity($folder, compact('uname', 'title'));
        $behavior = $Folders->behaviors()->get('UniqueName');
        $generated = $behavior->generateUniqueName($folder, true);

        if ($uname !== null) {
            $this->assertTextContains($uname, $generated);
        } else {
            $this->assertTextContains($title, $generated);
        }
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
        $Users = TableRegistry::getTableLocator()->get('Users');
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
                false,
            ],
            'regenerate' => [
                'Romani ite domum!',
                'romani-ite-domum_',
                [
                    'separator' => '_',
                    'hashlength' => 6,
                ],
                true,
            ],
            'underscoreValue' => [
                'test this_value',
                'test-this_value',
                [],
                false,
            ],
        ];
    }

    /**
     * test uniqueNameFromValue()
     *
     * @param string $value Starting value.
     * @param string $expected Expected result.
     * @param array $cfg Configuration.
     * @param bool $regenerate Should unique name be regenerated?
     * @return void
     * @dataProvider uniqueFromValueProvider
     * @covers ::uniqueNameFromValue()
     */
    public function testUniqueNameFromValue($value, $expected, array $cfg, $regenerate)
    {
        $behavior = TableRegistry::getTableLocator()->get('Objects')->behaviors()->get('UniqueName');
        $result = $behavior->uniqueNameFromValue($value, $regenerate, $cfg);

        if ($regenerate) {
            $cfg = array_merge($behavior->getConfig(), $cfg);
            $result = substr($result, 0, strlen($result) - $cfg['hashlength']);
        }
        $this->assertEquals($result, $expected);
    }

    /**
     * test uniqueName() conflicts / missing
     *
     * @return void
     * @covers ::uniqueName()
     */
    public function testUniqueNameMissing()
    {
        $Documents = TableRegistry::getTableLocator()->get('Documents');
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
        static::assertContains('documents-', $document->get('uname'));
    }

    /**
     * Test `uniqueName()` when `uname` is valid an unchanged
     *
     * @return void
     * @covers ::uniqueName()
     */
    public function testUniqueNameUnchanged(): void
    {
        $Documents = TableRegistry::getTableLocator()->get('Documents');
        $behavior = $Documents->behaviors()->get('UniqueName');
        $document = $Documents->get(2);
        $behavior->uniqueName($document);

        static::assertFalse($document->isDirty('uname'));
    }

    /**
     * Test `uniqueName()` when `uname` is unchanged and not in entity
     *
     * @return void
     * @covers ::uniqueName()
     */
    public function testUniqueNameIgnore(): void
    {
        $Documents = TableRegistry::getTableLocator()->get('Documents');
        $document = $Documents->get(2);
        $document->set('title', 'a new title');
        $document->unset('uname');

        $behavior = $Documents->behaviors()->get('UniqueName');
        $behavior->uniqueName($document);

        static::assertFalse($document->isDirty('uname'));
        static::assertFalse($document->has('uname'));
    }

    /**
     * test generate uname before rules
     *
     * @return void
     * @covers ::beforeRules()
     */
    public function testBeforeRules()
    {
        $Documents = TableRegistry::getTableLocator()->get('Documents');
        $entity = $Documents->newEntity([
            'title' => 'uh là la',
        ]);

        $Documents->getEventManager()->on('Model.beforeRules', function (Event $event, EntityInterface $entity) {
            $uname = $entity->get('uname');
            static::assertNotEmpty($uname);
            static::assertEquals('uh-la-la', $uname);

            return false;
        });

        $Documents->save($entity);
    }

    /**
     * Test unique name max lenght
     *
     * @return void
     * @coversNothing
     */
    public function testUniqueNameMaxLen()
    {
        $Documents = TableRegistry::getTableLocator()->get('Documents');
        $behavior = $Documents->behaviors()->get('UniqueName');

        // check internal uname generation lenght
        $data = ['title' => str_repeat('new title', 100)];
        $document = $Documents->newEntity($data);
        $behavior->uniqueName($document);
        $this->assertEquals(strlen($document->get('uname')), UniqueNameBehavior::UNAME_MAX_LENGTH);

        // limit explicit uname set lenght
        $document->set('uname', str_repeat('new-uname', 100));
        $behavior->uniqueName($document);
        $this->assertEquals(strlen($document->get('uname')), UniqueNameBehavior::UNAME_MAX_LENGTH);
    }
}
