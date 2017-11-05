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
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use Cake\Utility\Inflector;

/**
 * \BEdita\Core\Utility\Database Test Case
 *
 * @coversDefaultClass \BEdita\Core\Utility\Database
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
        'plugin.BEdita/Core.applications',
        'plugin.BEdita/Core.async_jobs',
        'plugin.BEdita/Core.object_types',
        'plugin.BEdita/Core.roles',
    ];

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->fixtureManager->shutDown();
    }

    /**
     * Test currentSchema method
     *
     * @return void
     *
     * @covers ::currentSchema()
     */
    public function testCurrentSchema()
    {
        $fixtures = ['Config', 'ObjectTypes', 'Roles', 'Applications'];
        call_user_func_array([$this, 'loadFixtures'], $fixtures);
        $schema = Database::currentSchema();

        $this->assertCount(count($fixtures), $schema);
        foreach ($fixtures as $f) {
            $this->assertArrayHasKey(Inflector::underscore($f), $schema);
        }

        // test not valid Connection object
        $mockConnection = $this->createMock('\Cake\Datasource\ConnectionInterface');
        ConnectionManager::setConfig('__wrongConnection', $mockConnection);

        $schema = Database::currentSchema('__wrongConnection');
        $this->assertEquals([], $schema);

        ConnectionManager::drop('__wrongConnection');
    }

    /**
     * Test schemaCompare method
     *
     * @return void
     *
     * @expectedException \Cake\Datasource\Exception\MissingDatasourceConfigException
     * @covers ::currentSchema()
     */
    public function testMissingDatasourceConfigException()
    {
        Database::currentSchema('zzzzzzzz');
    }

    /**
     * Test schemaCompare method
     *
     * @return void
     *
     * @covers ::schemaCompare()
     * @covers ::compareSchemaItems()
     */
    public function testSchemaCompare()
    {
        $fixtures1 = ['Config', 'ObjectTypes', 'Applications'];
        call_user_func_array([$this, 'loadFixtures'], $fixtures1);
        $schema1 = Database::currentSchema();

        $fixtures2 = ['AsyncJobs', 'Roles'];
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

        unset($schema2['roles']['indexes']);
        $schema2['roles']['columns']['tttt'] = $schema2['roles']['columns']['name'];
        unset($schema2['roles']['columns']['name']);
        $schema2['roles']['columns']['description'] = $schema1['applications']['columns']['description'];
        $diff1 = Database::schemaCompare($schema1, $schema2);
        $this->assertCount(0, $diff1);
    }

    /**
     * Test basicInfo method
     *
     * @return void
     *
     * @covers ::basicInfo()
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
            $this->assertArrayHasKey('version', $info);
        }
    }

    /**
     * Test supportedVersion method
     *
     * @return void
     *
     * @covers ::supportedVersion()
     */
    public function testSupportedVersion()
    {
        $info = Database::basicInfo();
        $result = Database::supportedVersion(['vendor' => $info['vendor'], 'version' => $info['version']]);
        static::assertTrue($result);
        $result = Database::supportedVersion(['vendor' => $info['vendor'], 'version' => 'ZZZZ']);
        static::assertFalse(($info['vendor'] !== 'sqlite') ? $result : !$result);
        $result = Database::supportedVersion(['vendor' => 'mongodb']);
        static::assertFalse($result);
    }

    /**
     * Test connectionTest method
     *
     * @return void
     *
     * @covers ::connectionTest()
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

    /**
     * Data provider for `testExecuteTransaction` test case.
     *
     * @return array
     */
    public function sqlExecute()
    {
        return [
            ["SELECT id from applications", true, 2, 1],
            ["SELECT id from properties", false, 0, 0],
            ["SELECT id from roles", false, 0, 0, 'zzzzzzzzz'],
            ["UPDATE roles SET name='gustavo' WHERE id = 1;\n" .
             "UPDATE applications SET name='Gustano' WHERE id = 1;", true, 2, 2],
            ["SELECT name from config;\n" . "SELECT name from roles;", true, 12, 2],
            ["SELECT something", false, 0, 0],
            [[" ", "SAY NO TO SQL", "NOSQL NOPARTY"], false, 0, 0],
        ];
    }

    /**
     * Test executeTransaction method
     *
     * @param string $sql SQL to be executed.
     * @param bool $success Expected success.
     * @param int $rowCount Expected amount of affected rows.
     * @param int $queryCount Expected amount of returned rows.
     * @param string $dbConfig Connection name.
     * @return void
     *
     * @dataProvider sqlExecute
     * @covers ::splitSqlQueries()
     * @covers ::executeTransaction()
     */
    public function testExecuteTransaction($sql, $success, $rowCount, $queryCount, $dbConfig = 'test')
    {
        $this->loadFixtures('Config', 'ObjectTypes', 'Roles', 'Applications');

        $res = Database::executeTransaction($sql, $dbConfig);
        $this->assertNotEmpty($res);
        $this->assertEquals($success, $res['success']);
        $this->assertEquals($rowCount, $res['rowCount']);
        $this->assertEquals($queryCount, $res['queryCount']);
    }

    /**
     * Data provider for `testExecuteTransactionStatementError` test case.
     *
     * @return array
     */
    public function connectionErrorProvider()
    {
        return [
            'errorExecute' => [
                ['execute' => false]
            ],
            'errorCodeTrue' => [
                [
                    'execute' => true,
                    'errorCode' => true
                ]
            ],
            'errorCodeDefined' => [
                [
                    'execute' => true,
                    'errorCode' => '00001'
                ]
            ]
        ];
    }

    /**
     * Test `executeTransaction()` simulating errors with database statement
     *
     * @param array $statementMethods An array of methods (array keys) and return values (array values) to mock on `\Cake\Database\StatementInterface`.
     * @return void
     *
     * @dataProvider connectionErrorProvider
     * @covers ::executeTransaction()
     */
    public function testExecuteTransactionStatementError($statementMethods)
    {
        $mockStatement = $this->createMock('\Cake\Database\StatementInterface');
        foreach ($statementMethods as $name => $value) {
            $mockStatement->method($name)
                ->willReturn($value);
        }

        $mockConnection = $this->getMockBuilder('\Cake\Database\Connection')
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->setMethods(['prepare', 'begin', 'commit', 'rollback', '__debugInfo'])
            ->getMock();

        $mockConnection->method('prepare')
            ->willReturn($mockStatement);

        $dbConfig = '__mockConnectionError';

        ConnectionManager::setConfig($dbConfig, $mockConnection);

        $res = Database::executeTransaction(['SELECT nothing'], $dbConfig);
        $this->assertNotEmpty($res);
        $this->assertEquals('Could not execute statement', $res['error']);
        $this->assertEquals(false, $res['success']);
        $this->assertEquals(0, $res['rowCount']);
        $this->assertEquals(0, $res['queryCount']);

        ConnectionManager::drop($dbConfig);
    }
}
