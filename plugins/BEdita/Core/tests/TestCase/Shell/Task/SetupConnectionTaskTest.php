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

namespace BEdita\Core\Test\TestCase\Shell\Task;

use BEdita\Core\Shell\Task\SetupConnectionTask;
use Cake\Console\Shell;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionInterface;
use Cake\Datasource\ConnectionManager;
use Cake\TestSuite\ConsoleIntegrationTestCase;
use Cake\Utility\Hash;
use Cake\Utility\Text;

/**
 * @coversDefaultClass \BEdita\Core\Shell\Task\SetupConnectionTask
 */
class SetupConnectionTaskTest extends ConsoleIntegrationTestCase
{
    /**
     * Name for temporary connection.
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
    public function tearDown(): void
    {
        if (in_array(static::TEMP_CONNECTION, ConnectionManager::configured())) {
            ConnectionManager::drop(static::TEMP_CONNECTION);
        }
        if (file_exists(static::TEMP_FILE)) {
            unlink(static::TEMP_FILE);
        }

        parent::tearDown();
    }

    /**
     * Test execution when specified connection is **NOT** a valid connection object.
     *
     * @return void
     * @covers ::main()
     */
    public function testExecuteUnknownConnectionType()
    {
        // Setup temporary configuration.
        $connection = $this->getMockBuilder(ConnectionInterface::class)
            ->getMock();
        ConnectionManager::setConfig(static::TEMP_CONNECTION, $connection);

        $this->exec(sprintf('%s --connection %s', SetupConnectionTask::class, static::TEMP_CONNECTION));

        $this->assertExitCode(Shell::CODE_ERROR);
        $this->assertErrorContains('Invalid connection object');
    }

    /**
     * Test execution when connection is already configured and we're **NOT** able to connect.
     *
     * @return void
     * @covers ::main()
     * @covers ::isConnectionConfigured()
     * @covers ::checkCanConnect()
     */
    public function testExecuteConfiguredFail()
    {
        // Setup temporary configuration.
        $config = [
            'className' => Connection::class,
            'database' => Text::uuid(),
        ];
        $config += ConnectionManager::get('default')->config();
        $driver = substr($config['driver'], strrpos($config['driver'], '\\') + 1);
        if ($driver === 'Sqlite') {
            // Must use a non-writable path, or SQLite will create the database.
            $config['database'] = '/thispathdoesnotexist/bedita.sqlite';
        }
        ConnectionManager::setConfig(static::TEMP_CONNECTION, $config);

        $this->exec(sprintf('%s --connection %s', SetupConnectionTask::class, static::TEMP_CONNECTION));

        $this->assertExitCode(Shell::CODE_ERROR);
        $this->assertOutputContains('Connection failed');
        $this->assertErrorContains('Connection to database could not be established');
    }

    /**
     * Test execution when connection is already configured and we're able to connect.
     *
     * @return void
     * @covers ::main()
     * @covers ::isConnectionConfigured()
     * @covers ::checkCanConnect()
     */
    public function testExecuteConfiguredOk()
    {
        $this->exec(SetupConnectionTask::class);

        $this->assertExitCode(Shell::CODE_SUCCESS);
        $this->assertOutputContains('Connection is still ok. Relax...');
        $this->assertErrorEmpty();
    }

    /**
     * Test execution when connection is not yet configured and we're **NOT** able to connect using
     * provided credentials.
     *
     * @return void
     * @covers ::main()
     * @covers ::isConnectionConfigured()
     * @covers ::readConnectionParams()
     * @covers ::checkCanConnect()
     */
    public function testExecuteInteractiveFail()
    {
        // Setup temporary configuration.
        $config = [
            'className' => Connection::class,
            'host' => '__BE4_DB_HOST__',
            'port' => '__BE4_DB_PORT__',
            'database' => '__BE4_DB_DATABASE__',
            'username' => '__BE4_DB_USERNAME__',
            'password' => '__BE4_DB_PASSWORD__',
        ];
        $config += ConnectionManager::get('default')->config();
        ConnectionManager::setConfig(static::TEMP_CONNECTION, $config);

        $driver = substr($config['driver'], strrpos($config['driver'], '\\') + 1);
        $defaultPort = $driver === 'Mysql' ? '3306' : '5432';

        // Mock input values.
        $returnValues = [
            $driver, // Driver
            '/thispathdoesnotexist/bedita.sqlite', // Database path
        ];
        if ($driver !== 'Sqlite') {
            $returnValues = [
                $driver, // Driver
                'localhost', // Hostname
                $defaultPort, // Port
                'bedita', // Database name
                'bedita', // Username
                Text::uuid(), // Password
            ];
        }

        // Invoke task.
        $this->exec(sprintf('%s --connection %s', SetupConnectionTask::class, static::TEMP_CONNECTION), $returnValues);

        $this->assertExitCode(Shell::CODE_ERROR);
        $this->assertOutputContains('Connection failed');
        $this->assertErrorContains('Connection to database could not be established');
    }

    /**
     * Test execution when connection is not yet configured and the provided configuration file is **NOT** valid.
     *
     * @return void
     * @covers ::main()
     * @covers ::isConnectionConfigured()
     * @covers ::readConnectionParams()
     * @covers ::checkCanConnect()
     * @covers ::saveConnectionConfig()
     */
    public function testExecuteInteractiveInvalidFile()
    {
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
        ];
        if ($driver !== 'Sqlite') {
            $returnValues = [
                $driver, // Driver
                $originalConfig['host'], // Hostname
                Hash::get($originalConfig, 'port', ''), // Port
                $originalConfig['database'], // Database name
                $originalConfig['username'], // Username
                Hash::get($originalConfig, 'password', ''), // Password
            ];
        }

        // Invoke task.
        $this->exec(
            sprintf('%s --connection %s --config-file %s', SetupConnectionTask::class, static::TEMP_CONNECTION, TMP . Text::uuid()),
            $returnValues
        );

        $this->assertExitCode(Shell::CODE_ERROR);
        $this->assertErrorContains('Unable to read from or write to configuration file');
    }

    /**
     * Test execution when connection is not yet configured and everything goes alright.
     *
     * @return void
     * @covers ::main()
     * @covers ::isConnectionConfigured()
     * @covers ::readConnectionParams()
     * @covers ::checkCanConnect()
     * @covers ::saveConnectionConfig()
     */
    public function testExecuteInteractiveOk()
    {
        // Setup configuration file.
        file_put_contents(
            static::TEMP_FILE,
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
        ConnectionManager::setConfig(static::TEMP_CONNECTION, $config);
        $connection = ConnectionManager::get(static::TEMP_CONNECTION);

        $driver = substr($config['driver'], strrpos($config['driver'], '\\') + 1);

        // Mock input values.
        $relevantKeys = ['className', 'driver', 'database'];
        $returnValues = [
            $driver, // Driver
            $originalConfig['database'], // Database path
        ];
        if ($driver !== 'Sqlite') {
            $relevantKeys = ['className', 'driver', 'host', 'port', 'database', 'username', 'password'];
            $returnValues = [
                $driver, // Driver
                $originalConfig['host'], // Hostname
                Hash::get($originalConfig, 'port', ''), // Port
                $originalConfig['database'], // Database name
                $originalConfig['username'], // Username
                Hash::get($originalConfig, 'password', ''), // Password
            ];
        }

        // Invoke task.
        $this->exec(
            sprintf('%s --connection %s --config-file %s', SetupConnectionTask::class, static::TEMP_CONNECTION, static::TEMP_FILE),
            $returnValues
        );

        $this->assertExitCode(Shell::CODE_SUCCESS);
        $this->assertErrorEmpty();
        $this->assertOutputContains('Configuration saved');
        $this->assertOutputContains('Connection is ok. It\'s time to start using BEdita!');

        // Perform additional assertions on connection.
        $newConnection = ConnectionManager::get(static::TEMP_CONNECTION);
        $newConfig = $newConnection->config() + ['className' => Connection::class];
        $newConfig = array_intersect_key($newConfig, array_flip($relevantKeys));
        static::assertNotSame($connection, $newConnection);
        static::assertArraySubset(array_intersect_key($originalConfig, array_flip($relevantKeys)), $newConfig);

        // Perform additional assertions on configuration.
        $fileContents = include static::TEMP_FILE;
        static::assertTrue(is_array($fileContents));
        $config = Hash::get($fileContents, 'Datasources.default');
        static::assertEquals($newConfig, array_intersect_key($config, array_flip($relevantKeys)));
    }

    /**
     * Test execution when connection is not yet configured and everything goes alright with an unattended run.
     *
     * @return void
     * @covers ::main()
     * @covers ::isConnectionConfigured()
     * @covers ::readConnectionParams()
     * @covers ::checkCanConnect()
     * @covers ::saveConnectionConfig()
     */
    public function testExecuteNonInteractiveOk()
    {
        // Setup configuration file.
        file_put_contents(
            static::TEMP_FILE,
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
        ConnectionManager::setConfig(static::TEMP_CONNECTION, $config);
        $connection = ConnectionManager::get(static::TEMP_CONNECTION);

        $driver = substr($config['driver'], strrpos($config['driver'], '\\') + 1);
        $defaultPort = $driver === 'Mysql' ? 3306 : 5432;

        $relevantKeys = ['className', 'driver', 'database'];

        // CLI options.
        $cliOptions = [
            // Driver
            '--connection-driver',
            $driver,

            // Database path
            '--connection-database',
            $originalConfig['database'],
        ];
        if ($driver !== 'Sqlite') {
            $relevantKeys = ['className', 'driver', 'host', 'port', 'database', 'username', 'password'];
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
                    [SetupConnectionTask::class, '--connection', static::TEMP_CONNECTION, '--config-file', static::TEMP_FILE],
                    $cliOptions
                )
            )
        );

        $this->assertExitCode(Shell::CODE_SUCCESS);
        $this->assertErrorEmpty();
        $this->assertOutputContains('Configuration saved');
        $this->assertOutputContains('Connection is ok. It\'s time to start using BEdita!');

        // Perform additional assertions on connection.
        $newConnection = ConnectionManager::get(static::TEMP_CONNECTION);
        $newConfig = $newConnection->config() + ['className' => Connection::class];
        $newConfig = array_intersect_key($newConfig, array_flip($relevantKeys));
        static::assertNotSame($connection, $newConnection);
        static::assertArraySubset(array_intersect_key($originalConfig, array_flip($relevantKeys)), $newConfig);

        // Perform additional assertions on configuration.
        $fileContents = include static::TEMP_FILE;
        static::assertTrue(is_array($fileContents));
        $config = Hash::get($fileContents, 'Datasources.default');
        static::assertEquals($newConfig, array_intersect_key($config, array_flip($relevantKeys)));
    }

    /**
     * Test execution when connection is not yet configured and everything goes alright with an unattended run.
     *
     * @return void
     * @covers ::main()
     * @covers ::isConnectionConfigured()
     * @covers ::readConnectionParams()
     * @covers ::checkCanConnect()
     * @covers ::saveConnectionConfig()
     */
    public function testExecuteSyntaxError()
    {
        // Setup configuration file.
        $fileContents = file_get_contents(CONFIG . 'app.default.php') . '?><?php }}{{$YNTAX]][[ERROR))((;;!:?';
        file_put_contents(
            static::TEMP_FILE,
            $fileContents,
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
        ConnectionManager::setConfig(static::TEMP_CONNECTION, $config);
        $connection = ConnectionManager::get(static::TEMP_CONNECTION);

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
                    [SetupConnectionTask::class, '--connection', static::TEMP_CONNECTION, '--config-file', static::TEMP_FILE],
                    $cliOptions
                )
            )
        );

        $this->assertExitCode(Shell::CODE_ERROR);
        $this->assertErrorContains('Updated configuration file has invalid syntax');

        // Perform additional assertions on connection.
        $newConnection = ConnectionManager::get(static::TEMP_CONNECTION);
        static::assertSame($connection, $newConnection);

        // Perform additional assertions on configuration.
        $newFileContents = file_get_contents(static::TEMP_FILE);
        static::assertSame($fileContents, $newFileContents);
    }
}
