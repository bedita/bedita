<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\TestCase\Model\Behavior;

use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\I18n\FrozenTime;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;

/**
 * @coversDefaultClass \BEdita\Core\Model\Behavior\HistoryBehavior
 */
class HistoryBehaviorTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.History',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
        'plugin.BEdita/Core.Tags',
        'plugin.BEdita/Core.ObjectTags',
    ];

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();
        LoggedUser::setUserAdmin();
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
     * Test `initialize` method
     *
     * @covers ::initialize()
     */
    public function testInitialize()
    {
        $prevConf = Configure::read('History');

        // pass config via `Configure`
        Configure::write('History.exclude', ['id']);
        $Documents = TableRegistry::getTableLocator()->get('Documents');
        $behavior = $Documents->getBehavior('History');
        static::assertEquals(['id'], $behavior->getConfig('exclude'));
        static::assertNotEmpty($behavior->Table);
        static::assertEquals('BEdita\Core\Model\Table\HistoryTable', get_class($behavior->Table));

        // pass config via addBehavior argument
        $Documents->removeBehavior('History');
        $Documents->addBehavior('BEdita/Core.History', ['exclude' => ['type']]);
        $behavior = $Documents->getBehavior('History');
        static::assertEquals(['type'], $behavior->getConfig('exclude'));

        Configure::write('History', $prevConf);
    }

    /**
     * Test `beforeMarshal` method
     *
     * @covers ::beforeMarshal()
     */
    public function testBeforeMarshal()
    {
        $Documents = TableRegistry::getTableLocator()->get('Documents');
        $data = [
            'type' => 'Documents',
            'title' => 'hello history',
        ];
        $entity = $Documents->newEntity($data);
        $Documents->save($entity);

        $behavior = $Documents->getBehavior('History');
        unset($data['type']);
        static::assertEquals($data, $behavior->getChanged());
    }

    /**
     * Test `beforeSave` method
     *
     * @covers ::beforeSave()
     */
    public function testBeforeSave()
    {
        $Users = TableRegistry::getTableLocator()->get('Users');
        $doc = $Users->get(5);
        $data = [
            'username' => 'second user',
            'description' => 'user desc',
            'password' => 'gustavogustavo',
        ];
        $entity = $Users->patchEntity($doc, $data);
        $Users->save($entity);

        $history = TableRegistry::getTableLocator()->get('History')->find()
                ->where(['resource_id' => '5', 'resource_type' => 'objects'])
                ->all()
                ->last();
        static::assertNotEmpty($history);
        unset($data['username']);
        $data['password'] = '*****';
        // verify that `username` is not in `changed` array
        // and `password` is obfuscated
        static::assertEquals($data, $history->get('changed'));
    }

    /**
     * Test `afterSave` method
     *
     * @covers ::afterSave()
     * @covers ::historyEntity()
     * @covers ::entityUserAction()
     */
    public function testAfterSave()
    {
        $Documents = TableRegistry::getTableLocator()->get('Documents');
        $doc = $Documents->get(3);
        $data = [
            'description' => 'new history desc',
        ];
        $entity = $Documents->patchEntity($doc, $data);
        $Documents->save($entity);

        $behavior = $Documents->getBehavior('History');
        static::assertEquals($data, $behavior->getChanged());

        $history = TableRegistry::getTableLocator()->get('History')->find()
                ->where(['resource_id' => '3', 'resource_type' => 'objects'])
                ->order(['id' => 'ASC'])
                ->all()
                ->last()
                ->toArray();
        static::assertNotEmpty($history);
        $expected = [
            'id' => 3,
            'resource_id' => '3',
            'resource_type' => 'objects',
            'user_id' => 1,
            'application_id' => null,
            'user_action' => 'update',
            'changed' => $data,
        ];
        static::assertNotEmpty($history['created']);
        static::assertEquals(FrozenTime::class, get_class($history['created']));
        unset($history['created']);
        $history['changed'] = (array)$history['changed'];
        static::assertEquals($expected, $history);
    }

    /**
     * Test `trash` and `restore` user actions
     *
     * @covers ::entityUserAction()
     */
    public function testTrashRestore()
    {
        $Documents = TableRegistry::getTableLocator()->get('Documents');
        $entity = $Documents->get(3);
        $entity->deleted = true;
        $entity = $Documents->saveOrFail($entity);

        $History = TableRegistry::getTableLocator()->get('History');
        $history = $History->find()
                ->where(['resource_id' => '3', 'resource_type' => 'objects'])
                ->order(['id' => 'ASC'])
                ->all()
                ->last();
        static::assertEquals('trash', $history->get('user_action'));

        $entity->deleted = false;
        $Documents->saveOrFail($entity);
        $history = $History->find()
                ->where(['resource_id' => '3', 'resource_type' => 'objects'])
                ->order(['id' => 'ASC'])
                ->all()
                ->last();
        static::assertNotEmpty($history);
        static::assertEquals('restore', $history->get('user_action'));
    }

    /**
     * Test `create` user action
     *
     * @covers ::entityUserAction()
     */
    public function testCreate()
    {
        $Users = TableRegistry::getTableLocator()->get('Users');
        $entity = $Users->newEntity([]);
        $data = [
            'username' => 'aurelio',
            'name' => 'Aurelio',
            'surname' => 'Supporto',
        ];
        $Users->patchEntity($entity, $data);
        $entity = $Users->saveOrFail($entity);

        $history = TableRegistry::getTableLocator()->get('History')->find()
                ->where(['resource_id' => $entity->get('id'), 'resource_type' => 'objects'])
                ->toArray();
        static::assertNotEmpty($history);
        static::assertEquals(1, count($history));
        static::assertEquals('create', $history[0]->get('user_action'));
        static::assertEquals($data, $history[0]->get('changed'));
    }

    /**
     * Test `afterDelete` method
     *
     * @covers ::afterDelete()
     * @covers ::historyEntity()
     * @covers ::entityUserAction()
     */
    public function testAfterDelete()
    {
        $Documents = TableRegistry::getTableLocator()->get('Documents');
        $entity = $Documents->get(2);
        $Documents->delete($entity);

        $history = TableRegistry::getTableLocator()->get('History')->find()
                ->where(['resource_id' => '2', 'resource_type' => 'objects'])
                ->order(['id' => 'ASC'])
                ->all()
                ->last()
                ->toArray();
        static::assertNotEmpty($history);
        $expected = [
            'id' => 3,
            'resource_id' => '2',
            'resource_type' => 'objects',
            'user_id' => 1,
            'application_id' => null,
            'user_action' => 'remove',
            'changed' => [],
        ];
        static::assertNotEmpty($history['created']);
        static::assertEquals(FrozenTime::class, get_class($history['created']));
        unset($history['created']);
        static::assertEquals($expected, $history);
    }

    /**
     * Test `afterDelete` with empty table
     *
     * @covers ::afterDelete()
     */
    public function testAfterDeleteEmpty()
    {
        Configure::write('History', ['table' => null]);
        $Documents = TableRegistry::getTableLocator()->get('Documents');
        $entity = $Documents->get(2);
        $Documents->delete($entity);

        $history = TableRegistry::getTableLocator()->get('History')->find()
                ->where(['resource_id' => '2', 'resource_type' => 'objects'])
                ->toArray();
        static::assertNotEmpty($history);
        static::assertEquals(2, count($history));
    }

    /**
     * Test `afterSave` with empty table
     *
     * @covers ::afterSave()
     */
    public function testAfterSaveEmpty()
    {
        Configure::write('History', ['table' => null]);
        $Documents = TableRegistry::getTableLocator()->get('Documents');
        $entity = $Documents->get(2);
        $Documents->patchEntity($entity, ['title' => 'new title', 'id' => 2]);
        $Documents->saveOrFail($entity);

        $history = TableRegistry::getTableLocator()->get('History')->find()
                ->where(['resource_id' => '2', 'resource_type' => 'objects'])
                ->toArray();
        static::assertNotEmpty($history);
        static::assertEquals(2, count($history));
    }

    /**
     * Data provider for `testFindHistoryEditor()`
     *
     * @return array
     */
    public function findHistoryEditorProvider(): array
    {
        return [
            'logged' => [
                [2],
                [],
            ],
            'options' => [
                [2],
                [5],
            ],
        ];
    }

    /**
     * Test `findHistoryEditor` finder.
     *
     * @param array $expected Expected result
     * @param array $options Filter options
     * @return void
     * @dataProvider findHistoryEditorProvider
     * @covers ::findHistoryEditor()
     */
    public function testFindHistoryEditor(array $expected, array $options): void
    {
        LoggedUser::setUserAdmin();

        $result = TableRegistry::getTableLocator()->get('Documents')
            ->find('historyEditor', $options)
            ->find('list', ['keyField' => 'id', 'valueField' => 'id'])
            ->toArray();

        LoggedUser::resetUser();

        static::assertEquals($expected, array_values($result));
    }
}
