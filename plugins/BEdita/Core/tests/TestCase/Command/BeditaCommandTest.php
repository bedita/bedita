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

use Cake\Command\Command;
use Cake\Console\TestSuite\ConsoleIntegrationTestTrait;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\TestCase;
use Cake\Utility\Hash;
use PDOException;

/**
 * {@see BEdita\Core\Command\BeditaCommand} Test Case
 *
 * @coversDefaultClass \BEdita\Core\Command\BeditaCommand
 */
class BeditaCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected array $fixtures = [
        'plugin.BEdita/Core.Applications',
    ];

    /**
     * Name for temporary configuration file.
     *
     * @var string
     */
    public const TEMP_CONNECTION = 'temporary_connection';

    /**
     * Name for temporary configuration file.
     *
     * @var string
     */
    public const TEMP_FILE = TMP . 'app.temp.php';

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();
        if (static::$fixtureManager !== null) {
            static::$fixtureManager->shutDown();
        }
        // Try to avoid "database schema has changed" error on SQLite.
        try {
            ConnectionManager::get('default')->getSchemaCollection()->listTables();
        } catch (PDOException $e) {
            // Do nothing.
        }
        $this->useCommandRunner();
    }

    /**
     * @inheritDoc
     */
    public function tearDown(): void
    {
        parent::tearDown();
        ConnectionManager::alias('test', 'default'); // Restore alias which is dropped by `BeditaShell`.
        ConnectionManager::get('default')->getDriver()->disconnect();
        ConnectionManager::get('default')
            ->transactional(function (Connection $connection) {
                $tables = $connection->getSchemaCollection()->listTables();

                foreach ($tables as $table) {
                    $sql = $connection->getSchemaCollection()->describe($table)->dropConstraintSql($connection);
                    foreach ($sql as $query) {
                        $connection->updateQuery($query);
                    }
                }
                foreach ($tables as $table) {
                    $sql = $connection->getSchemaCollection()->describe($table)->dropSql($connection);
                    foreach ($sql as $query) {
                        $connection->updateQuery($query);
                    }
                }
            });
        if (in_array(static::TEMP_CONNECTION, ConnectionManager::configured())) {
            ConnectionManager::drop(static::TEMP_CONNECTION);
        }
        if (file_exists(static::TEMP_FILE)) {
            unlink(static::TEMP_FILE);
        }
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
        $this->exec('bedita --help');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->helpAsserts();
    }

    /**
     * Test help
     *
     * @return void
     * @covers ::execute()
     */
    public function testHelp(): void
    {
        $this->exec('bedita check_api_key --help');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->helpAsserts();
    }

    /**
     * Test error on missing subcommand
     *
     * @return void
     * @covers ::execute()
     */
    public function testNoSubcommand(): void
    {
        $this->exec('bedita');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->helpAsserts();
    }

    /**
     * Do some asserts.
     *
     * @return void
     */
    private function helpAsserts(): void
    {
        $this->assertOutputContains('BEdita management command. Available subcommands:');
        $this->assertOutputContains('check');
        $this->assertOutputContains('setup');
        $this->assertOutputContains('check_api_key');
        $this->assertOutputContains('check_filesystem');
        $this->assertOutputContains('check_schema');
        $this->assertOutputContains('init_schema');
        $this->assertOutputContains('setup_admin_user');
        $this->assertOutputContains('setup_connection');
        $this->assertOutputContains('cake bedita [options] [<check|setup|check_api_key|check_filesystem|check_schema|init_schema|setup_admin_user|setup_connection>] [<paths>]');
    }

    /**
     * Test subcommand.
     *
     * @return void
     * @covers ::execute()
     * @covers ::executeSubcommand()
     */
    public function testSubcommand(): void
    {
        $this->exec('bedita check_api_key');
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test full setup on a new instance.
     *
     * @return void
     * @covers ::execute()
     * @covers ::executeSubcommand()
     * @covers ::setup()
     */
    public function testSetupNewInteractive(): void
    {
        // Setup configuration file.
        file_put_contents(
            static::TEMP_FILE,
            file_get_contents(CONFIG . 'app_local.example.php'),
            EXTR_OVERWRITE | LOCK_EX,
        );

        // Setup temporary configuration.
        $originalConfig = ConnectionManager::get('default')->config();
        $config = [
            'className' => Connection::class,
            'host' => '__BE4_DB_HOST__',
            'port' => '__BE4_DB_PORT__',
            'database' => '__BE4_DB_DATABASE__',
            'username' => '__BE4_DB_USERNAME__',
            'password' => '__BE4_DB_PASSWORD__',
        ];
        $config += $originalConfig;
        ConnectionManager::setConfig(static::TEMP_CONNECTION, $config);

        $driver = substr($config['driver'], strrpos($config['driver'], '\\') + 1);

        // Mock input values.
        $returnValues = [
            $driver, // Driver
            $originalConfig['database'], // Database path
            'gustavo', // Admin username
            'supporto', // Admin password
        ];
        if ($driver !== 'Sqlite') {
            $returnValues = [
                $driver, // Driver
                $originalConfig['host'], // Hostname
                Hash::get($originalConfig, 'port', ''), // Port
                $originalConfig['database'], // Database name
                $originalConfig['username'], // Username
                Hash::get($originalConfig, 'password', ''), // Password
                'gustavo', // Admin username
                'supporto', // Admin password
            ];
        }

        $this->exec(
            sprintf('bedita setup --connection %s --config-file %s', static::TEMP_CONNECTION, static::TEMP_FILE),
            $returnValues,
        );

        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('Checking connection');
        $this->assertOutputContains('Checking filesystem permissions');
        $this->assertOutputContains('Configuring default administrator user');
        $this->assertOutputContains('Checking API key');
    }

    /**
     * Test full setup on a new instance in a completely non-interactive mode.
     *
     * @return void
     * @covers ::execute()
     * @covers ::executeSubcommand()
     * @covers ::setup()
     */
    public function testSetupNewNonInteractive(): void
    {
        // Setup configuration file.
        file_put_contents(
            static::TEMP_FILE,
            file_get_contents(CONFIG . 'app_local.example.php'),
            EXTR_OVERWRITE | LOCK_EX,
        );

        // Setup temporary configuration.
        $originalConfig = ConnectionManager::get('default')->config();
        $config = [
            'className' => Connection::class,
            'host' => '__BE4_DB_HOST__',
            'port' => '__BE4_DB_PORT__',
            'database' => '__BE4_DB_DATABASE__',
            'username' => '__BE4_DB_USERNAME__',
            'password' => '__BE4_DB_PASSWORD__',
        ];
        $config += $originalConfig;
        ConnectionManager::setConfig(static::TEMP_CONNECTION, $config);

        $driver = substr($config['driver'], strrpos($config['driver'], '\\') + 1);
        $defaultPort = $driver === 'Mysql' ? 3306 : 5432;

        // CLI options.
        $cliOptions = [
            // Driver
            '--connection-driver',
            $driver,

            // Database path
            '--connection-database',
            $originalConfig['database'],

            // Seed
            '--seed',

            // Admin username
            '--admin-username',
            'gustavo',

            // Admin password
            '--admin-password',
            'supporto',
        ];
        if ($driver !== 'Sqlite') {
            $cliOptions = [
                // Driver
                '--connection-driver',
                $driver,

                // Hostname
                '--connection-host',
                $originalConfig['host'],

                // Port
                '--connection-port',
                Hash::get($originalConfig, 'port', $defaultPort),

                // Database name
                '--connection-database',
                $originalConfig['database'],

                // Username
                '--connection-username',
                $originalConfig['username'],

                // Seed
                '--seed',

                // Admin username
                '--admin-username',
                'gustavo',

                // Admin password
                '--admin-password',
                'supporto',
            ];

            // Password
            if (!empty($originalConfig['password'])) {
                $cliOptions[] = '--connection-password';
                $cliOptions[] = $originalConfig['password'];
            } else {
                $cliOptions[] = '--connection-password-empty';
            }
        }

        // Invoke task.
        $this->exec(
            implode(
                ' ',
                array_merge(
                    ['bedita', 'setup', '--connection', static::TEMP_CONNECTION, '--config-file', static::TEMP_FILE],
                    $cliOptions,
                ),
            ),
        );

        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('Checking connection');
        $this->assertOutputContains('Checking filesystem permissions');
        $this->assertOutputContains('Configuring default administrator user');
        $this->assertOutputContains('Checking API key');
    }

    /**
     * Test check.
     *
     * @return void
     * @covers ::execute()
     * @covers ::executeSubcommand()
     * @covers ::check()
     */
    public function testInitSchema(): void
    {
        $this->exec('bedita init_schema --force --seed');
        $this->assertExitCode(Command::CODE_SUCCESS);
    }

    /**
     * Test check.
     *
     * @return void
     * @covers ::execute()
     * @covers ::executeSubcommand()
     * @covers ::check()
     */
    public function testCheck(): void
    {
        $this->exec('bedita check');
        $this->assertExitCode(Command::CODE_SUCCESS);
        $this->assertOutputContains('Checking schema');
        $this->assertOutputContains('Checking filesystem permissions');
        $this->assertErrorEmpty();
    }
}
