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
use Cake\ORM\TableRegistry;

/**
 * @covers \BEdita\Core\Shell\Task\InitSchemaTask
 */
class InitSchemaTaskTest extends ShellTestCase
{

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->fixtureManager->shutDown();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();

        ConnectionManager::get('default')
            ->disableConstraints(function (Connection $connection) {
                $tables = $connection->getSchemaCollection()->listTables();

                foreach ($tables as $table) {
                    $sql = $connection->getSchemaCollection()->describe($table)->dropSql($connection);
                    foreach ($sql as $query) {
                        $connection->query($query);
                    }
                }
            });
    }

    /**
     * Test aborted initialization on not-empty database and `--no-force` argument passed.
     *
     * @return void
     */
    public function testDatabaseNotEmpty()
    {
        $connection = ConnectionManager::get('default');
        if (!($connection instanceof Connection)) {
            throw new \RuntimeException('Unable to use database connection');
        }

        $table = new Table('foo_bar', ['foo' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null]]);
        foreach ($table->createSql($connection) as $statement) {
            $connection->query($statement);
        }

        $this->invoke(['db_admin', 'init', '--no-force', '--no-seed']);

        $this->assertAborted();
    }

    /**
     * Test successful initialization on empty database.
     *
     * @return int
     */
    public function testDatabaseEmpty()
    {
        $connection = ConnectionManager::get('default');
        if (!($connection instanceof Connection)) {
            throw new \RuntimeException('Unable to use database connection');
        }

        $this->invoke(['db_admin', 'init', '--no-force', '--no-seed']);

        $schema = unserialize(file_get_contents(Plugin::configPath('BEdita/Core') . DS . 'Migrations' . DS . 'schema-dump-default.lock'));

        $this->assertNotAborted();
        $this->assertCount(count($schema) + 1, $connection->getSchemaCollection()->listTables());

        return TableRegistry::get('ObjectTypes')->find()->count();
    }

    /**
     * Test successful initialization on not-empty database and `--force` argument passed.
     *
     * @return void
     */
    public function testDatabaseCleanup()
    {
        $connection = ConnectionManager::get('default');
        if (!($connection instanceof Connection)) {
            throw new \RuntimeException('Unable to use database connection');
        }

        $table = new Table('foo_bar', ['foo' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null]]);
        foreach ($table->createSql($connection) as $statement) {
            $connection->query($statement);
        }

        $this->invoke(['db_admin', 'init', '--force', '--no-seed']);

        $schema = unserialize(file_get_contents(Plugin::configPath('BEdita/Core') . DS . 'Migrations' . DS . 'schema-dump-default.lock'));

        $this->assertNotAborted();
        $this->assertCount(count($schema) + 1, $connection->getSchemaCollection()->listTables());
    }

    /**
     * Test successful initialization on empty database and `--seed` argument passed.
     *
     * @return void
     *
     * @depends testDatabaseEmpty
     */
    public function testDatabaseSeed()
    {
        $connection = ConnectionManager::get('default');
        if (!($connection instanceof Connection)) {
            throw new \RuntimeException('Unable to use database connection');
        }

        $this->invoke(['db_admin', 'init', '--no-force', '--seed']);

        $schema = unserialize(file_get_contents(Plugin::configPath('BEdita/Core') . DS . 'Migrations' . DS . 'schema-dump-default.lock'));

        $this->assertNotAborted();
        $this->assertCount(count($schema) + 1, $connection->getSchemaCollection()->listTables());
    }

    /**
     * Test successful initialization on not-empty database and no arguments passed.
     *
     * @param int $notSeededCount Count of object types in a not-seeded database.
     * @return void
     *
     * @depends testDatabaseEmpty
     */
    public function testInteractive($notSeededCount)
    {
        $connection = ConnectionManager::get('default');
        if (!($connection instanceof Connection)) {
            throw new \RuntimeException('Unable to use database connection');
        }

        $table = new Table('foo_bar', ['foo' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null]]);
        foreach ($table->createSql($connection) as $statement) {
            $connection->query($statement);
        }

        $io = $this->getMockBuilder('Cake\Console\ConsoleIo')->getMock();
        $io->method('askChoice')
            ->will(
                $this->returnValueMap([
                    ['Do you really want to proceed?', ['y', 'n'], 'n', 'y'],
                    ['Would you like to populate your database with an optional set of data?', ['y', 'n'], 'y', 'n'],
                ])
            );

        $this->invoke(['db_admin', 'init'], [], $io);

        $schema = unserialize(file_get_contents(Plugin::configPath('BEdita/Core') . DS . 'Migrations' . DS . 'schema-dump-default.lock'));

        $this->assertNotAborted();
        $this->assertCount(count($schema) + 1, $connection->getSchemaCollection()->listTables());

        $this->assertEquals($notSeededCount, TableRegistry::get('ObjectTypes')->find()->count());
    }
}
