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
use BEdita\Core\TestSuite\ShellTestCase;
use Cake\Console\ConsoleInput;
use Cake\Console\ConsoleIo;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;
use Cake\Utility\Text;

/**
 * @covers \BEdita\Core\Shell\Task\SetupConnectionTask
 */
class SetupConnectionTaskTest extends ShellTestCase
{

    /**
     * Name for temporary connection.
     *
     * @var string
     */
    const TEMP_CONNECTION = 'temporary_connection';

    /**
     * Name for temporary configuration file.
     *
     * @var string
     */
    const TEMP_FILE = TMP . 'app.temp.php';

    /**
     * {@inheritDoc}
     */
    public function tearDown()
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
     * Test execution when connection is already configured and we're **NOT** able to connect.
     *
     * @return void
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

        $this->invoke([SetupConnectionTask::class, '--connection', static::TEMP_CONNECTION]);

        $this->assertAborted();
        $this->assertOutputContains('Connection failed');
        $this->assertErrorContains('Connection to database could not be established');
    }

    /**
     * Test execution when connection is already configured and we're able to connect.
     *
     * @return void
     */
    public function testExecuteConfiguredOk()
    {
        $this->invoke([SetupConnectionTask::class]);

        $this->assertNotAborted();
        $this->assertOutputContains('Connection is still ok. Relax...');
    }

    /**
     * Test execution when connection is not yet configured and we're **NOT** able to connect using
     * provided credentials.
     *
     * @return void
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
        $defaultPort = $driver === 'Mysql' ? 3306 : 5432;

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
        $stdin = $this->getMockBuilder(ConsoleInput::class)
            ->getMock();
        $stdin->method('read')
            ->willReturnOnConsecutiveCalls(...$returnValues);
        $io = new ConsoleIo($this->_out, $this->_err, $stdin);

        // Invoke task.
        $this->invoke([SetupConnectionTask::class, '--connection', static::TEMP_CONNECTION], [], $io);

        $this->assertAborted();
        $this->assertOutputContains('Connection failed');
        $this->assertErrorContains('Connection to database could not be established');
    }

    /**
     * Test execution when connection is not yet configured and the provided configuration file is **NOT** valid.
     *
     * @return void
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
                $originalConfig['password'], // Password
            ];
        }
        $stdin = $this->getMockBuilder(ConsoleInput::class)
            ->getMock();
        $stdin->method('read')
            ->willReturnOnConsecutiveCalls(...$returnValues);
        $io = new ConsoleIo($this->_out, $this->_err, $stdin);

        // Invoke task.
        $this->invoke(
            [SetupConnectionTask::class, '--connection', static::TEMP_CONNECTION, '--config-file', TMP . Text::uuid()],
            [],
            $io
        );

        $this->assertAborted();
        $this->assertErrorContains('Unable to read from or write to configuration file');
    }

    /**
     * Test execution when connection is not yet configured and everything goes alright.
     *
     * @return void
     */
    public function testExecuteInteractiveOk()
    {
        static $relevantKeys = ['className', 'driver', 'host', 'port', 'database', 'username', 'password'];

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
                $originalConfig['password'], // Password
            ];
        }
        $stdin = $this->getMockBuilder(ConsoleInput::class)
            ->getMock();
        $stdin->method('read')
            ->willReturnOnConsecutiveCalls(...$returnValues);
        $io = new ConsoleIo($this->_out, $this->_err, $stdin);

        // Invoke task.
        $this->invoke(
            [SetupConnectionTask::class, '--connection', static::TEMP_CONNECTION, '--config-file', static::TEMP_FILE],
            [],
            $io
        );

        $this->assertNotAborted($this->getError());
        $this->assertErrorEquals('');
        $this->assertOutputContains('Configuration saved');
        $this->assertOutputContains('Connection is ok. It\'s time to start using BEdita!');

        // Perform additional assertions on connection.
        $newConnection = ConnectionManager::get(static::TEMP_CONNECTION);
        $newConfig = $newConnection->config() + ['className' => Connection::class];
        $newConfig = array_intersect_key($newConfig, array_flip($relevantKeys));
        static::assertNotSame($connection, $newConnection);
        static::assertArraySubset(array_intersect_key($originalConfig, array_flip($relevantKeys)), $newConfig);

        // Perform additional assertions on connection.
        $fileContents = include static::TEMP_FILE;
        static::assertTrue(is_array($fileContents));
        $config = Hash::get($fileContents, 'Datasources.default');
        static::assertEquals($newConfig, array_intersect_key($config, array_flip($relevantKeys)));
    }
}
