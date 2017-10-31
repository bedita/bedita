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

namespace BEdita\Core\Test\TestCase\Shell;

use BEdita\Core\Shell\Task\InitSchemaTask;
use BEdita\Core\Test\TestCase\Shell\Task\SetupConnectionTaskTest;
use BEdita\Core\TestSuite\ShellTestCase;
use Cake\Console\ConsoleInput;
use Cake\Console\ConsoleIo;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;

/**
 * @coversDefaultClass \BEdita\Core\Shell\BeditaShell
 */
class BeditaShellTest extends ShellTestCase
{

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->fixtureManager->shutDown();

        // Try to avoid "database schema has changed" error on SQLite.
        try {
            ConnectionManager::get('default')->schemaCollection()->listTables();
        } catch (\PDOException $e) {
            // Do nothing.
        }
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        if (in_array(SetupConnectionTaskTest::TEMP_CONNECTION, ConnectionManager::configured())) {
            ConnectionManager::get(SetupConnectionTaskTest::TEMP_CONNECTION)
                ->transactional(function (Connection $connection) {
                    $tables = $connection->getSchemaCollection()->listTables();

                    foreach ($tables as $table) {
                        $sql = $connection->getSchemaCollection()->describe($table)->dropConstraintSql($connection);
                        foreach ($sql as $query) {
                            $connection->query($query);
                        }
                    }
                    foreach ($tables as $table) {
                        $sql = $connection->getSchemaCollection()->describe($table)->dropSql($connection);
                        foreach ($sql as $query) {
                            $connection->query($query);
                        }
                    }
                });
            ConnectionManager::drop(SetupConnectionTaskTest::TEMP_CONNECTION);
            ConnectionManager::alias('test', 'default');
        }
        if (file_exists(SetupConnectionTaskTest::TEMP_FILE)) {
            unlink(SetupConnectionTaskTest::TEMP_FILE);
        }

        parent::tearDown();
    }

    /**
     * {@inheritDoc}
     */
    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        ConnectionManager::alias('test', 'default');
    }

    /**
     * Test full setup on a new instance.
     *
     * @covers ::setup()
     */
    public function testSetupNewInteractive()
    {
        // Setup configuration file.
        file_put_contents(
            SetupConnectionTaskTest::TEMP_FILE,
            file_get_contents(CONFIG . 'app.default.php'),
            EXTR_OVERWRITE | LOCK_EX
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
        ConnectionManager::setConfig(SetupConnectionTaskTest::TEMP_CONNECTION, $config);

        $driver = substr($config['driver'], strrpos($config['driver'], '\\') + 1);

        // Mock input values.
        $returnValues = [
            $driver, // Driver
            $originalConfig['database'], // Database path
            'y', // Seed
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
                $originalConfig['password'], // Password
                'y', // Seed
                'gustavo', // Admin username
                'supporto', // Admin password
            ];
        }
        $stdin = $this->getMockBuilder(ConsoleInput::class)
            ->getMock();
        $stdin->method('read')
            ->willReturnOnConsecutiveCalls(...$returnValues);
        $io = new ConsoleIo($this->_out, $this->_err, $stdin);

        $this->invoke(
            ['bedita', 'setup', '--connection', SetupConnectionTaskTest::TEMP_CONNECTION, '--config-file', SetupConnectionTaskTest::TEMP_FILE],
            [],
            $io
        );

        $this->assertNotAborted();
        $this->assertOutputContains('Checking connection');
        $this->assertOutputContains('Initializing schema');
        $this->assertOutputContains('Checking filesystem permissions');
        $this->assertOutputContains('Configuring default administrator user');
    }

    /**
     * Test full setup on a new instance in a completely non-interactive mode.
     *
     * @covers ::setup()
     */
    public function testSetupNewNonInteractive()
    {
        // Setup configuration file.
        file_put_contents(
            SetupConnectionTaskTest::TEMP_FILE,
            file_get_contents(CONFIG . 'app.default.php'),
            EXTR_OVERWRITE | LOCK_EX
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
        ConnectionManager::setConfig(SetupConnectionTaskTest::TEMP_CONNECTION, $config);

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

                // Password
                '--connection-password',
                $originalConfig['password'],

                // Seed
                '--seed',

                // Admin username
                '--admin-username',
                'gustavo',

                // Admin password
                '--admin-password',
                'supporto',
            ];
        }

        // Invoke task.
        $this->invoke(
            array_merge(
                ['bedita', 'setup', '--connection', SetupConnectionTaskTest::TEMP_CONNECTION, '--config-file', SetupConnectionTaskTest::TEMP_FILE],
                $cliOptions
            )
        );

        $this->assertNotAborted();
        $this->assertOutputContains('Checking connection');
        $this->assertOutputContains('Initializing schema');
        $this->assertOutputContains('Checking filesystem permissions');
        $this->assertOutputContains('Configuring default administrator user');
    }

    /**
     * Test full setup on an already initialized instance in a completely non-interactive mode.
     *
     * @covers ::setup()
     */
    public function testSetupExistingNonInteractive()
    {
        $this->invoke([InitSchemaTask::class, '--seed', '--force']);

        // Invoke task.
        $this->invoke(['bedita', 'setup', '--admin-overwrite', '--admin-username', 'gustavo', '--admin-password', 'supporto']);

        $this->assertNotAborted();
        $this->assertOutputContains('Checking connection');
        $this->assertOutputContains('Checking schema');
        $this->assertOutputContains('Checking filesystem permissions');
        $this->assertOutputContains('Configuring default administrator user');
    }

    /**
     * Test full setup on an already initialized instance in a completely non-interactive mode.
     *
     * @covers ::setup()
     */
    public function testCheck()
    {
        $this->invoke([InitSchemaTask::class, '--seed', '--force']);

        // Invoke task.
        $this->invoke(['bedita', 'check']);

        $this->assertNotAborted();
        $this->assertOutputContains('Checking schema');
        $this->assertOutputContains('Checking filesystem permissions');
    }
}
