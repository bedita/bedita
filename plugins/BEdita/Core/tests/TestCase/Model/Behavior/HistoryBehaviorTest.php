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
    public $fixtures = [
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Properties',
        'plugin.BEdita/Core.PropertyTypes',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.ObjectRelations',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.History',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();
        LoggedUser::setUser(['id' => 1]);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
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
        $Documents = TableRegistry::get('Documents');
        $behavior = $Documents->getBehavior('History');
        static::assertNotEmpty($behavior->Table);
        static::assertEquals('BEdita\Core\Model\Table\HistoryTable', get_class($behavior->Table));
    }

    /**
     * Test `initialize` failure method
     *
     * @covers ::initialize()
     */
    public function testInitializeFailure()
    {
        Configure::write('History.table', 'Roles');
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('History table must implement "history" and "activity" finders');

        $Documents = TableRegistry::get('Documents');
    }

    /**
     * Test `beforeMarshal` method
     *
     * @covers ::beforeMarshal()
     */
    public function testBeforeMarshal()
    {
        $Documents = TableRegistry::get('Documents');
        $data = [
            'type' => 'Documents',
            'title' => 'hello history',
        ];
        $entity = $Documents->newEntity($data);
        $Documents->save($entity);

        $behavior = $Documents->getBehavior('History');
        unset($data['type']);
        static::assertEquals($data, $behavior->changed);
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
        $Documents = TableRegistry::get('Documents');
        $doc = $Documents->get(2);
        $data = [
            'description' => 'new history desc'
        ];
        $entity = $Documents->patchEntity($doc, $data);
        $Documents->save($entity);

        $behavior = $Documents->getBehavior('History');
        static::assertEquals($data, $behavior->changed);

        $History = TableRegistry::get('History');
        $history = $History->find('history', [2])->last()->toArray();
        static::assertNotEmpty($history);
        $expected = [
            'id' => 3,
            'resource_id' => '2',
            'resource_type' => 'objects',
            'user_id' => 1,
            'application_id' => null,
            'user_action' => 'update',
            'changed' => $data,
        ];
        static::assertNotEmpty($history['created']);
        static::assertEquals('Cake\I18n\Time', get_class($history['created']));
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
        $Documents = TableRegistry::get('Documents');
        $entity = $Documents->get(2);
        $entity->deleted = true;
        $entity = $Documents->saveOrFail($entity);

        $History = TableRegistry::get('History');
        $history = $History->find('history', [2])->last();
        static::assertEquals('trash', $history->get('user_action'));

        $entity->deleted = false;
        $Documents->saveOrFail($entity);
        $history = $History->find('history', [2])->last();
        static::assertEquals('restore', $history->get('user_action'));
    }

    /**
     * Test `create` user action
     *
     * @covers ::entityUserAction()
     */
    public function testCreate()
    {
        $Documents = TableRegistry::get('Documents');
        $entity = $Documents->newEntity();
        $Documents->patchEntity($entity, ['title' => 'new doc']);
        $entity = $Documents->saveOrFail($entity);

        $history = TableRegistry::get('History')->find('history', [$entity->get('id')])->last();
        static::assertEquals('create', $history->get('user_action'));
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
        $Documents = TableRegistry::get('Documents');
        $entity = $Documents->get(2);
        $Documents->delete($entity);

        $History = TableRegistry::get('History');
        $history = $History->find('history', [2])->last()->toArray();
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
        static::assertEquals('Cake\I18n\Time', get_class($history['created']));
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
        $Documents = TableRegistry::get('Documents');
        $entity = $Documents->get(2);
        $Documents->delete($entity);

        $History = TableRegistry::get('History');
        $history = $History->find('history', [2])->toArray();
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
        $Documents = TableRegistry::get('Documents');
        $entity = $Documents->get(2);
        $Documents->patchEntity($entity, ['title' => 'new title']);
        $Documents->saveOrFail($entity);

        $History = TableRegistry::get('History');
        $history = $History->find('history', [2])->toArray();
        static::assertEquals(2, count($history));
    }
}
