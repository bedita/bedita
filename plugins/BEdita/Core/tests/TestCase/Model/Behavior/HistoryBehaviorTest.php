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

use BEdita\Core\History\HistoryInterface;
use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use LogicException;

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
        'plugin.BEdita/Core.ObjectHistory',
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
        Configure::write(
            'History.model',
            new class () implements HistoryInterface
            {
                public function addEvent(array $data): void
                {
                }

                public function readEvents($objectId, array $options = []): array
                {
                    return [];
                }

                public function readUserEvents($userId, array $options = []): array
                {
                    return [];
                }
            }
        );
        $Documents = TableRegistry::get('Documents');
        $behavior = $Documents->getBehavior('History');
        static::assertNotEmpty($behavior->historyModel);
        static::assertNotEquals('BEdita\Core\History\DefaultObjectHistory', get_class($behavior->historyModel));
    }

    /**
     * Test `initialize` failure method
     *
     * @covers ::initialize()
     */
    public function testInitializeFailure()
    {
        Configure::write(
            'History.model',
            new class ()
            {
                public function foo(): void
                {
                }
            }
        );
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('History model must implement HistoryInterface');

        $Documents = TableRegistry::get('Documents');
        $behavior = $Documents->getBehavior('History');
        static::assertNotEmpty($behavior->historyModel);
        static::assertEquals('BEdita\Core\History\DefaultObjectHistory', get_class($behavior->historyModel));
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
            'title' => 'hello history'
        ];
        $entity = $Documents->newEntity($data);
        $Documents->save($entity);

        $behavior = $Documents->getBehavior('History');
        static::assertEquals($data, $behavior->changed);
        static::assertNotEmpty($behavior->historyModel);
        static::assertEquals('BEdita\Core\History\DefaultObjectHistory', get_class($behavior->historyModel));
    }

    /**
     * Test `afterSave` method
     *
     * @covers ::afterSave()
     * @covers ::historyData()
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

        $ObjectHistory = TableRegistry::get('ObjectHistory');
        $history = $ObjectHistory->find()
            ->where(['object_id' => 2])
            ->order(['created' => 'DESC'])
            ->first()
            ->toArray();
        static::assertNotEmpty($history);
        $expected = [
            'id' => 3,
            'object_id' => 2,
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
     * Test `afterDelete` method
     *
     * @covers ::afterDelete()
     * @covers ::historyData()
     * @covers ::entityUserAction()
     */
    public function testAfterDelete()
    {
    }
}
