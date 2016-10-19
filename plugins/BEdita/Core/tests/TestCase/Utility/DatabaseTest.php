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
namespace BEdita\Core\Test\TestCase\Utility;

use BEdita\Core\Utility\Database;
use Cake\Core\Plugin;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;

/**
 * \BEdita\Core\Utility\Database Test Case
 *
 * @covers \BEdita\Core\Utility\Database
 */
class DatabaseTest extends TestCase
{

    /**
     * {@inheritDoc}
     */
    public $autoFixtures = false;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BEdita/Core.config',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.objects',
        'plugin.BEdita/Core.profiles',
        'plugin.BEdita/Core.roles',
        'plugin.BEdita/Core.users',
    ];

    /**
     * Test currentSchema method
     *
     * @return void
     */
    public function testCurrentSchema()
    {
        $this->fixtureManager->shutDown();

        $fixtures = ['Config', 'ObjectTypes', 'Objects', 'Profiles'];
        call_user_func_array([$this, 'loadFixtures'], $fixtures);
        $schema = Database::currentSchema();

        $this->assertCount(count($fixtures), $schema);
        foreach ($fixtures as $f) {
            $this->assertArrayHasKey(Inflector::underscore($f), $schema);
        }
    }

    /**
     * Test schemaCompare method
     *
     * @return void
     * @expectedException \Cake\Datasource\Exception\MissingDatasourceConfigException
     */
    public function testMissingDatasourceConfigException()
    {
        Database::currentSchema('zzzzzzzz');
    }

    /**
     * Test schemaCompare method
     *
     * @return void
     */
    public function testSchemaCompare()
    {
        $this->fixtureManager->shutDown();

        $fixtures1 = ['Config', 'ObjectTypes', 'Objects', 'Profiles'];
        call_user_func_array([$this, 'loadFixtures'], $fixtures1);
        $schema1 = Database::currentSchema();

        $fixtures2 = ['Roles', 'Users'];
        call_user_func_array([$this, 'loadFixtures'], $fixtures2);
        $schema2 = Database::currentSchema();

        $fixtures2 = array_merge($fixtures1, $fixtures2);

        $diff1 = Database::schemaCompare($schema1, $schema2);
        $this->assertCount(0, $diff1);

        $diff2 = Database::schemaCompare($schema2, $schema1);
        $this->assertCount(1, $diff2);
        $this->assertArrayHasKey('missing', $diff2);

        $arrayDiff = array_diff($fixtures2, $fixtures1);
        $this->assertCount(count($arrayDiff), $diff2['missing']['tables']);
        foreach ($arrayDiff as $v) {
            $this->assertContains(Inflector::underscore($v), $diff2['missing']['tables']);
        }

        unset($schema2['objects']['indexes']);
        $schema2['objects']['columns']['tttt'] = $schema2['objects']['columns']['title'];
        unset($schema2['objects']['columns']['title']);
        $schema2['objects']['columns']['body'] = $schema1['objects']['columns']['publish_start'];
        $diff1 = Database::schemaCompare($schema1, $schema2);
        $this->assertCount(3, $diff1);
    }

    /**
     * Test basicInfo method
     *
     * @return void
     */
    public function testBasicInfo()
    {
        $info = Database::basicInfo();
        $this->assertNotEmpty($info);
        $this->assertArrayHasKey('database', $info);
        $this->assertStringEndsWith($info['vendor'], strtolower($info['driver']));
        if ($info['vendor'] != 'sqlite') {
            $this->assertArrayHasKey('host', $info);
            $this->assertArrayHasKey('username', $info);
        }
    }

    /**
     * Test connectionTest method
     *
     * @return void
     */
    public function testConnectionTest()
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

    public function sqlExecute()
    {
        return [
            ["SELECT id from users", true, 2, 1],
            ["SELECT id from properties", false, 0, 0],
            ["SELECT id from users", false, 0, 0, 'zzzzzzzzz'],
            ["UPDATE profiles SET name='Germano', surname='Mosconi' WHERE id = 1;\n" .
             "UPDATE profiles SET person_title='Spiritual Guide' WHERE id = 1;", true, 2, 2],
            ["SELECT name from config;\n" . "SELECT name from profiles;", true, 7, 2],
            ["SELECT something", false, 0, 0],
            [["SAY NO TO SQL", " ", "NOSQL NOPARTY"], false, 0, 0],
        ];
    }

    /**
     * Test executeTransaction method
     *
     * @return void
     * @dataProvider sqlExecute
     */
    public function testExecuteTransaction($sql, $success, $rowCount, $queryCount, $dbConfig = 'test')
    {
        $this->loadFixtures('Config', 'ObjectTypes', 'Objects', 'Users', 'Profiles');

        $res = Database::executeTransaction($sql, $dbConfig);
        $this->assertNotEmpty($res);
        $this->assertEquals($success, $res['success']);
        $this->assertEquals($rowCount, $res['rowCount']);
        $this->assertEquals($queryCount, $res['queryCount']);
    }
}
