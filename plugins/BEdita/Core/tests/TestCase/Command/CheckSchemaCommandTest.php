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

use BEdita\Core\Utility\Database;
use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Core\Plugin;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\ConnectionHelper;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use TestApp\Application;

/**
 * {@see BEdita\Core\Command\CheckSchemaCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\CheckSchemaCommand
 */
class CheckSchemaCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BEdita/Core.FakeAnimals',
    ];

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->useCommandRunner();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test buildOptionParser method
     *
     * @return void
     * @covers ::buildOptionParser()
     * @covers ::getDescription()
     */
    public function testBuildOptionParser(): void
    {
        $this->exec('check_schema --help');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('Current schema is compared with versioned schema dump');
        $this->assertOutputContains('cake check_schema [-c default|test] [-h] [--ignore-migration-status] [-q] [-v]');
        $this->assertOutputContains('--connection, -c');
        $this->assertOutputContains('Connection name to use');
        $this->assertOutputContains('--ignore-migration-status');
        $this->assertOutputContains('Skip checks on migration status');
    }

    /**
     * Check whether or not perform a check on a given $connection
     *
     * @param ConnectionInterface $connection
     * @return bool
     */
    protected function checkAvailable($connection): bool
    {
        if (!($connection->getDriver() instanceof Mysql)) {
            return false;
        }
        // Real vendor must not be defined, otherwise we are dealing
        // with MariaDB, Aurora or other MySQL compatible DB (including Mysql 5.7)
        // where some checks involving `Migrations.MigrationDiff` are failing
        $realVendor = Hash::get((array)$connection->config(), 'realVendor');

        return empty($realVendor);
    }

    /**
     * Test `checkSymbol` method.
     *
     * @return void
     * @covers ::checkSymbol()
     */
    public function testCheckSymbol(): void
    {
        $cmd = new class extends \BEdita\Core\Command\CheckSchemaCommand {
            public function checkSymbol($symbol, array $context = []): array
            {
                return parent::checkSymbol($symbol, $context);
            }
        };

        $actual = $cmd->checkSymbol('fake_animals');
        static::assertEmpty($actual);

        $connection = ConnectionManager::get('default');
        $allColumns = [];
        $table = $this->fetchTable('fake_animals');
        $schema = $connection->getSchemaCollection()->describe('fake_animals');
        $columns = $schema->columns();
        foreach ($columns as $column) {
            $actual = $cmd->checkSymbol($column, compact('table', 'allColumns'));
            static::assertEmpty($actual);
            $allColumns[$column] = $table;
        }
        foreach ($schema->indexes() as $index) {
            $actual = $cmd->checkSymbol($index, $schema->getIndex($index) + compact('table'));
            static::assertEmpty($actual);
        }
        foreach ($schema->constraints() as $constraint) {
            $actual = $cmd->checkSymbol($constraint, $schema->getConstraint($constraint) + compact('table'));
            static::assertEmpty($actual);
        }
    }

    /**
     * Test `checkConventions`.
     *
     * @return void
     * @covers ::checkConventions()
     */
    public function testCheckConventions(): void
    {
        if (Database::basicInfo()['vendor'] === 'sqlite') {
            $this->markTestSkipped('Test skipped on SQLite');
        }
        $cmd = new class extends \BEdita\Core\Command\CheckSchemaCommand {
            public function __construct()
            {
                $this->args = ['cake', 'check_schema'];
                $this->io = new \Cake\Console\ConsoleIo();
                parent::__construct();
            }

            public function checkConventions(Connection $connection): void
            {
                parent::checkConventions($connection);
            }

            public function getMessages(): array
            {
                return $this->messages;
            }
        };
        $connection = ConnectionManager::get('default');
        $cmd->checkConventions($connection);
        $actual = $cmd->getMessages();
        $this->assertTreeEmptyNaming($actual['captions']['table']);
        $this->assertTreeEmptyNaming($actual['captions']['column']);
    }

    /**
     * Assert that the tree of messages is empty.
     *
     * @param array $messages Messages tree.
     * @return void
     */
    private function assertTreeEmptyNaming(array $messages): void
    {
        foreach ($messages as $key => $value) {
            if ($key === 'naming') {
                static::assertEmpty($value);

                continue;
            }
            $this->assertTreeEmptyNaming($value);
        }
    }

    /**
     * Test `formatMessages`.
     *
     * @return void
     * @covers ::checkConventions()
     * @covers ::formatMessages()
     * @covers ::errorMessage()
     */
    public function testCheckFormatMessages(): void
    {
        $cmd = new class extends \BEdita\Core\Command\CheckSchemaCommand {
            public function __construct()
            {
                $this->args = ['cake', 'check_schema'];
                $this->io = new \Cake\Console\ConsoleIo();
                parent::__construct();
            }

            public function checkConventions(Connection $connection): void
            {
                parent::checkConventions($connection);
            }

            public function formatMessages(): bool
            {
                return parent::formatMessages();
            }

            public function addMessages(string $table, array $messages): void
            {
                $this->messages[$table] = $messages;
            }

            public function getMessages(): array
            {
                return $this->messages;
            }
        };
        $connection = ConnectionManager::get('default');
        $cmd->checkConventions($connection);
        $cmd->addMessages('phinxlog', ['foo' => 'bar']);
        $actual = $cmd->formatMessages();
        $expected = true;
        $info = Database::basicInfo();
        if ($info['vendor'] === 'sqlite') {
            $expected = false;
        }
        static::assertSame($expected, $actual);
        $actual = array_keys($cmd->getMessages());
        $messages = $cmd->getMessages();
        unset($messages['phinxlog']);
        ksort($messages);
        $expected = array_keys($messages);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test `errorMessage`.
     *
     * @return void
     * @covers ::errorMessage()
     */
    public function testErrorMessage(): void
    {
        $cmd = new class extends \BEdita\Core\Command\CheckSchemaCommand {
            public function __construct()
            {
                $this->args = ['cake', 'check_schema'];
                $this->io = new \Cake\Console\ConsoleIo();
                parent::__construct();
            }

            public function errorMessage(string $type, string $symbol, string $errorType, array $details): string
            {
                return parent::errorMessage($type, $symbol, $errorType, $details);
            }
        };
        $actual = $cmd->errorMessage('dummy', 'foo', 'naming', ['baz', 'qux']);
        $expected = 'dummy name "foo" is not valid (baz, qux)';
        static::assertEquals($expected, $actual);
        $actual = $cmd->errorMessage('dummy', 'foo', 'add', []);
        $expected = 'dummy "foo" has been added';
        static::assertEquals($expected, $actual);
        $actual = $cmd->errorMessage('dummy', 'foo', 'remove', []);
        $expected = 'dummy "foo" has been removed';
        static::assertEquals($expected, $actual);
        $actual = $cmd->errorMessage('dummy', 'foo', 'changed', []);
        $expected = 'dummy "foo" has been changed';
        static::assertEquals($expected, $actual);
        $actual = $cmd->errorMessage('dummy', 'foo', 'whatever', []);
        $expected = '';
        static::assertEquals($expected, $actual);
    }

    /**
     * Test check on Phinxlog tables.
     *
     * @return void
     * @covers ::filterPhinxlogTables()
     */
    public function testFilterPhinxlogTables(): void
    {
        $cmd = new class extends \BEdita\Core\Command\CheckSchemaCommand {
            public function filterPhinxlogTables($tables): array
            {
                return parent::filterPhinxlogTables($tables);
            }
        };
        $tables = [
            'documents',
            'phinxlog',
            'documents_phinxlog',
        ];
        $expected = [
            'documents',
        ];
        $actual = $cmd->filterPhinxlogTables($tables);
        static::assertEquals($expected, $actual);
    }

    /**
     * Test check on unknown connection type.
     *
     * @return void
     * @covers ::execute()
     */
    public function testUnkwownConnectionType(): void
    {
        ConnectionManager::setConfig('dummy', new \stdClass());
        $this->exec('check_schema -c dummy');
        ConnectionManager::drop('dummy');

        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains('Unknown connection type');
    }

    /**
     * Test controlled failure on missing "Migrations" plugin.
     *
     * @return void
     * @covers ::execute()
     */
    public function testMissingMigrationsPlugin(): void
    {
        $pluginCollection = Plugin::getCollection();
        $migrationPlugin = $pluginCollection->get('Migrations');
        $pluginCollection->remove('Migrations');
        $this->configApplication(Application::class, []);

        $this->exec('check_schema');

        $this->assertExitCode(Command::CODE_ERROR);
        $this->assertErrorContains('Plugin "Migrations" must be loaded');
        // restore plugin
        $pluginCollection->add($migrationPlugin);
    }

    /**
     * Test check on offended SQL conventions.
     *
     * @return void
     * @covers ::execute()
     * @covers ::checkConventions()
     * @covers ::filterPhinxlogTables()
     * @covers ::checkMigrationsStatus()
     * @covers ::checkSymbol()
     * @covers ::formatMessages()
     */
    public function testOffendedConventions(): void
    {
        /** @var \Cake\Database\Connection $connection */
        $connection = ConnectionManager::get('default');

        $table = new TableSchema('foo_bar');
        $table
            ->addColumn('foo_bar', [
                'type' => 'string',
                'length' => 255,
                'null' => true,
                'default' => null,
            ])
            ->addColumn('password', [
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
                'type' => TableSchema::INDEX_INDEX,
                'columns' => ['foo_bar'],
            ])
            ->addConstraint('foobar_uq', [
                'type' => TableSchema::CONSTRAINT_UNIQUE,
                'columns' => ['foo_bar'],
            ]);
        foreach ($table->createSql($connection) as $statement) {
            $connection->execute($statement);
        }

        $this->exec('check_schema');

        // drop table
        $connectionHelper = new ConnectionHelper();
        $connectionHelper->dropTables('default', ['foo_bar']);

        if ($this->checkAvailable($connection)) {
            static::assertExitCode(Command::CODE_ERROR);
            $this->assertOutputContains('Column name "foo_bar" is not valid (same name as table)');
            $this->assertOutputContains('Column name "password" is not valid (reserved word)');
            $this->assertOutputContains('Column name "42gustavo__suppOrto_" is not valid');
            $this->assertOutputContains('Index name "mytestindex" is not valid');
            $this->assertOutputRegExp('/Constraint name "[a-zA-Z0-9_]+" is not valid/');
        } else {
            static::assertExitCode(Command::CODE_SUCCESS);
            $this->assertOutputContains('SQL conventions and schema differences can only be checked on MySQL');
        }
        $this->assertErrorEmpty();
    }

    /**
     * Test successful schema check.
     *
     * @return void
     * @covers ::execute()
     */
    public function testCheckSchema(): void
    {
        /** @var \Cake\Database\Connection $connection */
        $connection = ConnectionManager::get('default');

        $this->exec('check_schema');

        $this->assertExitCode(Command::CODE_SUCCESS);
        if (!$this->checkAvailable($connection)) {
            $this->assertOutputContains('SQL conventions and schema differences can only be checked on MySQL');
        }
        $this->assertErrorEmpty();
    }
}
