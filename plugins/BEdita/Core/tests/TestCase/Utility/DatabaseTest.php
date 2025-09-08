<?php
declare(strict_types=1);

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
namespace BEdita\Core\Test\TestCase\Utility;

use BEdita\Core\Utility\Database;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * {@see \BEdita\Core\Utility\Database} Test Case
 */
#[CoversClass(Database::class)]
class DatabaseTest extends TestCase
{
    /**
     * Test basicInfo method
     *
     * @return void
     */
    public function testBasicInfo(): void
    {
        $info = Database::basicInfo();
        $this->assertNotEmpty($info);
        $this->assertArrayHasKey('database', $info);
        $this->assertStringEndsWith($info['vendor'], strtolower($info['driver']));
        if ($info['vendor'] != 'sqlite') {
            $this->assertArrayHasKey('host', $info);
            $this->assertArrayHasKey('username', $info);
            $this->assertArrayHasKey('version', $info);
        }

        // test not valid Connection object
        $mockConnection = $this->createMock('\Cake\Datasource\ConnectionInterface');
        ConnectionManager::setConfig('__wrongConnection', $mockConnection);
        $info = Database::basicInfo('__wrongConnection');
        static::assertEquals([], $info);
        ConnectionManager::drop('__wrongConnection');
    }

    /**
     * Test connectionTest method
     *
     * @return void
     */
    public function testConnectionTest(): void
    {
        $res = Database::connectionTest();
        $this->assertNotEmpty($res);
        $this->assertTrue($res['success']);
        $this->assertEmpty($res['error']);

        $res = Database::connectionTest('zzzzzzzzzzz');
        $this->assertNotEmpty($res);
        $this->assertFalse($res['success']);
        $this->assertNotEmpty($res['error']);
    }
}
