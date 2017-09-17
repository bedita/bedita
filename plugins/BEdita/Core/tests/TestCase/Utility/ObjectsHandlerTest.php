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
namespace BEdita\Core\Test\TestCase\Utility;

use BEdita\Core\Utility\LoggedUser;
use BEdita\Core\Utility\ObjectsHandler;
use Cake\Core\Configure;
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
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.property_types',
        'plugin.BEdita/Core.properties',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
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
        $this->assertInternalType('integer', $userId);

        $data = ['title' => 'a pragmatic title', 'description' => 'an agile descriptio'];
        $entity = ObjectsHandler::save('documents', $data, ['id' => $userId]);
        $this->assertNotEmpty($entity);
        $docId = $entity->id;
        $this->assertInternalType('integer', $docId);

        $result = ObjectsHandler::remove($docId);
        $this->assertTrue($result);

        $result = ObjectsHandler::remove($userId);
        $this->assertTrue($result);
    }

    /**
     * Test `save` failure
     *
     * @return void
     * @covers ::save()
     * @expectedException \Cake\Console\Exception\StopException
     */
    public function testSaveException()
    {
        $data = [];
        ObjectsHandler::save('users', $data);
    }

    /**
     * Test `save` existing object
     *
     * @return void
     * @covers ::save()
     */
    public function testSaveExisting()
    {
        $data = ['id' => 5, 'description' => 'a new description'];
        $entity = ObjectsHandler::save('users', $data);
        $this->assertNotEmpty($entity);
        $this->assertEquals(5, $entity->id);
    }

    /**
     * Test `delete` failure
     *
     * @return void
     * @covers ::remove()
     * @expectedException \Cake\Datasource\Exception\RecordNotFoundException
     */
    public function testDeleteException()
    {
        ObjectsHandler::remove(123456);
    }

    /**
     * Test `checkEnvironment'
     *
     * @return void
     * @covers ::checkEnvironment()
     * @expectedException \Cake\Console\Exception\StopException
     */
    public function testEnvironment()
    {
        Configure::write('debug', false);
        ObjectsHandler::save('documents', []);
        Configure::write('debug', true);
    }
}
