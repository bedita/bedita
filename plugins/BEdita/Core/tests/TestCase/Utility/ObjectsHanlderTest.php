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

use BEdita\Core\Utility\ObjectsHandler;
use Cake\ORM\TableRegistry;
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
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.users',
        'plugin.BEdita/Core.relations',
        'plugin.BEdita/Core.relation_types',
    ];

    /**
     * Test `create` and `remove` method
     *
     * @return void
     * @covers ::create()
     * @covers ::remove()
     */
    public function testCreateRemove()
    {
        $data = ['username' => 'somenewusername', 'password' => 'somepassword'];

        $entity = ObjectsHandler::create('users', $data);
        $this->assertNotEmpty($entity);
        $userId = $entity->id;
        $this->assertInternalType('integer', $userId);

        $data = ['title' => 'a pragmatic title', 'description' => 'an agile descriptio'];
        $entity = ObjectsHandler::create('documents', $data, ['id' => $userId]);
        $this->assertNotEmpty($entity);
        $docId = $entity->id;
        $this->assertInternalType('integer', $docId);

        $result = ObjectsHandler::remove($docId);
        $this->assertTrue($result);

        $result = ObjectsHandler::remove($userId);
        $this->assertTrue($result);
    }

    /**
     * Test `create` failure
     *
     * @return void
     * @covers ::create()
     * @expectedException Cake\Console\Exception\StopException
     */
    public function testCreateException()
    {
        $data = [];
        ObjectsHandler::create('users', $data);
    }

    /**
     * Test `delete` failure
     *
     * @return void
     * @covers ::create()
     * @expectedException Cake\Datasource\Exception\RecordNotFoundException
     */
    public function testDeleteException()
    {
        ObjectsHandler::remove(123456);
    }
}
