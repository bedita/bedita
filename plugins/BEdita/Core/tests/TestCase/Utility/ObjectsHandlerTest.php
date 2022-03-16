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
namespace BEdita\Core\Test\TestCase\Utility;

use BEdita\Core\Utility\LoggedUser;
use BEdita\Core\Utility\ObjectsHandler;
use Cake\TestSuite\TestCase;

/**
 * {@see \BEdita\Core\Utility\ObjectsHandler} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Utility\ObjectsHandler
 */
class ObjectsHandlerTest extends TestCase
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
        'plugin.BEdita/Core.Objects',
        'plugin.BEdita/Core.Profiles',
        'plugin.BEdita/Core.Users',
        'plugin.BEdita/Core.Relations',
        'plugin.BEdita/Core.RelationTypes',
        'plugin.BEdita/Core.Trees',
        'plugin.BEdita/Core.Categories',
        'plugin.BEdita/Core.ObjectCategories',
        'plugin.BEdita/Core.History',
    ];

    /**
     * @inheritDoc
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
     * Test `create` and `remove` method
     *
     * @return void
     * @covers ::save()
     * @covers ::remove()
     * @covers ::checkEnvironment()
     */
    public function testCreateRemove()
    {
        $data = ['username' => 'somenewusername', 'password' => 'somepassword'];

        $entity = ObjectsHandler::save('users', $data);
        $this->assertNotEmpty($entity);
        $userId = $entity->id;
        $this->assertIsInt($userId);

        $data = [
            'title' => 'a pragmatic title',
            'description' => 'an agile description',
            'uname' => 'agile-uname',
        ];
        $entity = ObjectsHandler::save('documents', $data, ['id' => $userId]);
        $this->assertNotEmpty($entity);
        $docId = $entity->id;
        $this->assertIsInt($docId);

        $result = ObjectsHandler::remove('agile-uname');
        $this->assertTrue($result);

        $result = ObjectsHandler::remove($userId);
        $this->assertTrue($result);
    }

    /**
     * Test `save` failure
     *
     * @return void
     * @covers ::save()
     */
    public function testSaveException()
    {
        $this->expectException(\Cake\ORM\Exception\PersistenceFailedException::class);
        $data = [];
        ObjectsHandler::save('users', $data);
    }

    /**
     * Test `save` existing object
     *
     * @return void
     * @covers ::save()
     * @covers ::isCli()
     */
    public function testSaveExisting()
    {
        $data = ['id' => 5, 'description' => 'a new description'];
        $entity = ObjectsHandler::save('users', $data);
        $this->assertNotEmpty($entity);
        $this->assertEquals(5, $entity->id);
    }

    /**
     * Test `save` with `locked` attribute
     *
     * @return void
     * @covers ::save()
     */
    public function testSaveLocked()
    {
        $data = ['id' => 5, 'locked' => true];
        $entity = ObjectsHandler::save('users', $data);
        $this->assertNotEmpty($entity);
        $this->assertTrue($entity->locked);
    }

    /**
     * Test `delete` failure
     *
     * @return void
     * @covers ::remove()
     */
    public function testDeleteException()
    {
        $this->expectException(\Cake\Datasource\Exception\RecordNotFoundException::class);
        ObjectsHandler::remove(123456);
    }

    /**
     * Test `checkEnvironment'
     *
     * @return void
     * @covers ::checkEnvironment()
     */
    public function testEnvironment()
    {
        $this->expectException(\Cake\Console\Exception\StopException::class);
        $this->expectExceptionMessage('Operation avilable only in CLI environment');
        $testClass = new class extends ObjectsHandler {
            protected static function isCli(): bool
            {
                return false;
            }
        };
        $testClass::save('documents', []);
    }
}
