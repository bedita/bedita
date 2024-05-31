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
use Cake\Database\Connection;
use Cake\Database\DriverInterface;
use Cake\Database\Schema\Collection;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;

/**
 * \BEdita\Core\Utility\Database Test Case
 *
 * @coversDefaultClass \BEdita\Core\Utility\Database
 */
class DatabaseTest extends TestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.Applications',
        'plugin.BEdita/Core.Config',
        'plugin.BEdita/Core.AsyncJobs',
        'plugin.BEdita/Core.ObjectTypes',
        'plugin.BEdita/Core.Roles',
    ];

    /**
     * Test currentSchema method
     *
     * @return void
     * @covers ::currentSchema()
     */
    public function testCurrentSchema()
    {
        $expected = ConnectionManager::get('test')->getSchemaCollection()->listTables();
        $schema = Database::currentSchema();
        $this->assertCount(count($expected), $schema);
        foreach ($expected as $table) {
            $this->assertArrayHasKey($table, $schema);
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
     * @covers ::currentSchema()
     */
    public function testMissingDatasourceConfigException()
    {
        $this->expectException(\Cake\Datasource\Exception\MissingDatasourceConfigException::class);
        Database::currentSchema('zzzzzzzz');
    }

    /**
     * Test schemaCompare method
     *
     * @return void
     * @covers ::schemaCompare()
     * @covers ::compareSchemaItems()
     */
    public function testSchemaCompare()
    {
        $schemaTables1 = ['applications', 'config', 'object_types'];
        $schemaTables2 = ['applications', 'config', 'object_types', 'async_jobs', 'roles'];

        $describeCallback = function ($table) {
            $schemaCol = ConnectionManager::get('test')->getSchemaCollection();

            return $schemaCol->describe($table);
        };

        $mockSchemaCollection = $this->getMockBuilder(Collection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['listTables', 'describe'])
            ->getMock();

        $mockSchemaCollection
            ->method('listTables')
            ->willReturnOnConsecutiveCalls($schemaTables1, $schemaTables2);

        $mockSchemaCollection
            ->method('describe')
            ->willReturnCallback($describeCallback);

        $mockConnection = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getSchemaCollection'])
            ->getMock();

        $mockConnection->method('getSchemaCollection')
            ->willReturn($mockSchemaCollection);

        ConnectionManager::setConfig('__tmp1Connection', ['className' => $mockConnection]);

        $schema1 = Database::currentSchema('__tmp1Connection');
        $schema2 = Database::currentSchema('__tmp1Connection');

        ConnectionManager::drop('__tmp1Connection');

        $diff1 = Database::schemaCompare($schema1, $schema2);
        $this->assertCount(0, $diff1);

        $diff2 = Database::schemaCompare($schema2, $schema1);
        $this->assertCount(1, $diff2);
        $this->assertArrayHasKey('missing', $diff2);

        $arrayDiff = array_diff($schemaTables2, $schemaTables1);
        $this->assertCount(count($arrayDiff), $diff2['missing']['tables']);
        foreach ($arrayDiff as $v) {
            $this->assertContains($v, $diff2['missing']['tables']);
        }

        unset($schema2['object_types']['indexes']);
        $schema2['object_types']['columns']['tttt'] = $schema2['object_types']['columns']['name'];
        unset($schema2['object_types']['columns']['name']);
        $schema2['object_types']['columns']['description'] = $schema1['applications']['columns']['created'];
        $diff1 = Database::schemaCompare($schema1, $schema2);
        $this->assertCount(3, $diff1);
    }

    /**
     * Test basicInfo method
     *
     * @return void
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
     * @covers ::supportedVersion()
     */
    public function testSupportedVersion()
    {
        $info = Database::basicInfo();
        /* @phpstan-ignore-next-line */
        $result = Database::supportedVersion(['vendor' => $info['vendor'], 'version' => $info['version']]);
        static::assertTrue($result);
        /* @phpstan-ignore-next-line */
        $result = Database::supportedVersion(['vendor' => $info['vendor'], 'version' => 'zzzzzzzzz']);
        static::assertFalse($result);
        /* @phpstan-ignore-next-line */
        $result = Database::supportedVersion(['vendor' => 'mongodb']);
        static::assertFalse($result);
    }

    /**
     * Test connectionTest method
     *
     * @return void
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
            ["UPDATE roles SET name='gustavo' WHERE id = 1;\n" .
             "UPDATE applications SET name='Gustano' WHERE id = 1;", true, 2, 2],
            ['SELECT id from applications', true, 2, 1],
            ['SELECT id from not_existing_table', false, 0, 0],
            ['SELECT id from roles', false, 0, 0, 'zzzzzzzzz'],
            ["SELECT name from config;\n" . 'SELECT name from roles;', true, 15, 2],
            ['SELECT something', false, 0, 0],
            [[' ', 'SAY NO TO SQL', 'NOSQL NOPARTY'], false, 0, 0],
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
     * @dataProvider sqlExecute
     * @covers ::splitSqlQueries()
     * @covers ::executeTransaction()
     */
    public function testExecuteTransaction($sql, $success, $rowCount, $queryCount, $dbConfig = 'test')
    {
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
                ['execute' => false],
            ],
            'errorCodeTrue' => [
                [
                    'execute' => true,
                    'errorCode' => true,
                ],
            ],
            'errorCodeDefined' => [
                [
                    'execute' => true,
                    'errorCode' => '00001',
                ],
            ],
        ];
    }

    /**
     * Test `executeTransaction()` simulating errors with database statement
     *
     * @param array $statementMethods An array of methods (array keys) and return values (array values) to mock on `\Cake\Database\StatementInterface`.
     * @return void
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

        $mockDriver = $this->createMock(DriverInterface::class);

        $mockConnection = $this->getMockBuilder('\Cake\Database\Connection')
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->onlyMethods(['prepare', 'begin', 'commit', 'rollback', 'getDriver', '__debugInfo'])
            ->getMock();

        $mockConnection->method('prepare')
            ->willReturn($mockStatement);
        $mockConnection->method('getDriver')
            ->willReturn($mockDriver);

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
