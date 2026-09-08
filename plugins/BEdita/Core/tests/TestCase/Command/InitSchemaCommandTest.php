<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Test\TestCase\Command;

use BEdita\Core\Command\InitSchemaCommand;
use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Core\Plugin;
use Cake\Database\Connection;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use RuntimeException;

/**
 * {@see BEdita\Core\Command\InitSchemaCommand} Test Case
 */
#[CoversClass(InitSchemaCommand::class)]
class InitSchemaCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        /** @var \Cake\Database\Connection $connection */
        $connection = ConnectionManager::get('default');
        $connection->transactional(function (Connection $connection) {
            $tables = $connection->getSchemaCollection()->listTables();

            foreach ($tables as $table) {
                /** @var \Cake\Database\Schema\TableSchema $tableSchema */
                $tableSchema = $connection->getSchemaCollection()->describe($table);
                $sql = $tableSchema->dropConstraintSql($connection);
                foreach ($sql as $query) {
                    $connection->updateQuery($query);
                }
            }
            foreach ($tables as $table) {
                /** @var \Cake\Database\Schema\TableSchema $tableSchema */
                $tableSchema = $connection->getSchemaCollection()->describe($table);
                $sql = $tableSchema->dropSql($connection);
                foreach ($sql as $query) {
                    $connection->updateQuery($query);
                }
            }
        });
        parent::tearDown();
    }

    /**
     * Test buildOptionParser method
     *
     * @return void
     */
    public function testBuildOptionParser(): void
    {
        $this->exec('init_schema --help');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('A new database schema is created using current DB connection');
        $this->assertOutputContains('BEWARE: all existing tables will be dropped!');
        $this->assertOutputContains('cake init_schema [options]');
        $this->assertOutputContains('--force, -f');
        $this->assertOutputContains('Automatically drop all existing tables in database');
        $this->assertOutputContains('--no-force');
        $this->assertOutputContains('Do NOT drop any existing table in database');
        $this->assertOutputContains('--seed, -s');
        $this->assertOutputContains('Seed initial set of data');
        $this->assertOutputContains('--no-seed');
        $this->assertOutputContains('Do NOT seed initial set of data');
        $this->assertOutputContains('--connection, -c');
        $this->assertOutputContains('Connection name to use');
    }

    /**
     * Test aborted initialization on not-empty database and `--no-force` argument passed.
     *
     * @return void
     */
    public function testDatabaseNotEmpty(): void
    {
        $connection = ConnectionManager::get('default');
        if (!($connection instanceof Connection)) {
            throw new RuntimeException('Unable to use database connection');
        }

        $table = new TableSchema('foo_bar', ['foo' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null]]);
        foreach ($table->createSql($connection) as $statement) {
            $connection->updateQuery($statement);
        }

        $this->exec('init_schema --no-force --no-seed');

        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains('Database is not empty, no action has been performed');
    }

    /**
     * Test successful initialization on not-empty database and `--force` argument passed.
     *
     * @return void
     */
    public function testDatabaseCleanup(): void
    {
        $connection = ConnectionManager::get('default');
        if (!($connection instanceof Connection)) {
            throw new RuntimeException('Unable to use database connection');
        }

        $table = new TableSchema('foo_bar', ['foo' => ['type' => 'string', 'length' => 255, 'null' => true, 'default' => null]]);
        foreach ($table->createSql($connection) as $statement) {
            $connection->updateQuery($statement);
        }

        $this->exec('init_schema --force --no-seed');

        $schema = unserialize(file_get_contents(Plugin::configPath('BEdita/Core') . DS . 'Migrations' . DS . 'schema-dump-default.lock'));

        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertErrorEmpty();
        $this->checkCounts($schema, $connection->getSchemaCollection()->listTables());
    }

    /**
     * Check counts removing fake tables.
     *
     * @param array $schema Schema.
     * @param array $connectionTables Connection tables.
     * @return void
     */
    private function checkCounts(array $schema, array $connectionTables): void
    {
        $fake = array_filter($connectionTables, function ($table) {
            return strpos($table, 'fake_') === 0;
        });
        $schemaTables = array_keys($schema);
        $expected = count($schemaTables) + 1;
        $actual = count($connectionTables) - count($fake) - 2; // -2 for migrations and seeds tables

        static::assertSame($expected, $actual);
    }
}
