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

namespace BEdita\Core\Test\TestCase\Shell\Task;

use BEdita\Core\TestSuite\ShellTestCase;
use Cake\Core\Plugin;
use Cake\Database\Connection;
use Cake\Database\Schema\Table;
use Cake\Datasource\ConnectionManager;

/**
 * @coversDefaultClass \BEdita\Core\Shell\Task\CheckSchemaTask
 */
class CheckSchemaTaskTest extends ShellTestCase
{

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->fixtureManager->shutDown();

        $this->invoke(['db_admin', 'init', '-fs']);
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        Plugin::load('Migrations');

        ConnectionManager::get('default')
            ->disableConstraints(function (Connection $connection) {
                $tables = $connection->schemaCollection()->listTables();

                foreach ($tables as $table) {
                    $sql = $connection->schemaCollection()->describe($table)->dropSql($connection);
                    foreach ($sql as $query) {
                        $connection->query($query);
                    }
                }
            });
    }

    /**
     * Test controlled failure on missing "Migrations" plugin.
     *
     * @return void
     * @covers ::main()
     */
    public function testMissingMigrationsPlugin()
    {
        Plugin::unload('Migrations');

        $this->invoke(['db_admin', 'check_schema']);
        $this->assertErrorContains('Plugin "Migrations" must be loaded');

        $this->assertAborted();
    }

    /**
     * Test successful schema check.
     *
     * @return void
     * @covers \BEdita\Core\Shell\Task\CheckSchemaTask
     */
    public function testCheckSchema()
    {
        $info = ConnectionManager::get('default')->config();
        if (strstr($info['driver'], 'Mysql') === false) {
            $this->markTestSkipped('Successful schema checks happens only on default driver (currently MySQL)');
        }

        $result = $this->invoke(['db_admin', 'check_schema']);

        $this->assertNotAborted();
        $this->assertTrue($result);
    }

    /**
     * Test check on new table.
     *
     * @return void
     * @covers ::checkDiff()
     * @covers ::formatMessages()
     */
    public function testAddTable()
    {
        $connection = ConnectionManager::get('default');
        if (!($connection instanceof Connection)) {
            throw new \RuntimeException('Unable to use database connection');
        }

        $table = new Table('foo_bar', ['foo' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null]]);
        foreach ($table->createSql($connection) as $statement) {
            $connection->query($statement);
        }

        $result = $this->invoke(['db_admin', 'check_schema']);

        $this->assertNotAborted();
        $this->assertFalse($result);
        $this->assertOutputContains('Table "foo_bar" has been added');
    }

    /**
     * Test check on removed table.
     *
     * @return void
     * @covers ::checkDiff()
     * @covers ::formatMessages()
     */
    public function testRemoveTable()
    {
        $connection = ConnectionManager::get('default');
        if (!($connection instanceof Connection)) {
            throw new \RuntimeException('Unable to use database connection');
        }

        $table = $connection->schemaCollection()->describe('config');
        foreach ($table->dropSql($connection) as $statement) {
            $connection->query($statement);
        }

        $result = $this->invoke(['db_admin', 'check_schema']);

        $this->assertNotAborted();
        $this->assertFalse($result);
        $this->assertOutputContains('Table "config" has been removed');
    }

    /**
     * Test check on removed constraint.
     *
     * @return void
     * @covers ::checkDiff()
     * @covers ::formatMessages()
     */
    public function testUpdateConstraints()
    {
        $connection = ConnectionManager::get('default');
        if (!($connection instanceof Connection)) {
            throw new \RuntimeException('Unable to use database connection');
        }

        $table = $connection->schemaCollection()->describe('objects');
        $constraints = $table->constraints();
        foreach ($table->dropConstraintSql($connection) as $statement) {
            $connection->query($statement);
        }

        $result = $this->invoke(['db_admin', 'check_schema']);

        $this->assertNotAborted();
        $this->assertFalse($result);
        foreach ($constraints as $constraint) {
            $info = $table->constraint($constraint);
            if ($info && isset($info['type']) && $info['type'] !== Table::CONSTRAINT_FOREIGN) {
                continue;
            }

            $this->assertOutputContains(sprintf('Constraint "%s" has been removed', $constraint));
        }
    }

    /**
     * Test check on offended SQL conventions.
     *
     * @return void
     * @covers ::checkConventions()
     * @covers ::checkSymbol()
     * @covers ::formatMessages()
     */
    public function testOffendedConventions()
    {
        $connection = ConnectionManager::get('default');
        if (!($connection instanceof Connection)) {
            throw new \RuntimeException('Unable to use database connection');
        }

        $table = new Table('foo_bar');
        $table
            ->addColumn('foo_bar', [
                'type' => 'string',
                'length' => 255,
                'null' => true,
                'default' => null,
            ])
            ->addColumn('42gustavo__suppOrto_', [
                'type' => 'string',
                'length' => 255,
                'null' => true,
                'default' => null,
            ])
            ->addIndex('mytestindex', [
                'type' => Table::INDEX_INDEX,
                'columns' => ['foo_bar'],
            ])
            ->addConstraint('foobar_uq', [
                'type' => Table::CONSTRAINT_UNIQUE,
                'columns' => ['foo_bar'],
            ]);
        foreach ($table->createSql($connection) as $statement) {
            $connection->query($statement);
        }

        $result = $this->invoke(['db_admin', 'check_schema']);

        $this->assertNotAborted();
        $this->assertFalse($result);
        $this->assertOutputContains('Column name "foo_bar" is not valid (same name as table)');
        $this->assertOutputContains('Column name "42gustavo__suppOrto_" is not valid');
        $this->assertOutputContains('Index name "mytestindex" is not valid');
        $this->assertRegExp('/Constraint name "[a-zA-Z0-9_]+" is not valid/', $this->getOutput());
    }
}
