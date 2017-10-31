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

namespace BEdita\Core\Shell\Task;

use Cake\Console\Shell;
use Cake\Database\Connection;
use Cake\Database\Exception\MissingConnectionException;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Task to setup connection.
 *
 * @since 4.0.0
 */
class SetupConnectionTask extends Shell
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser
            ->setDescription([
                'Setup database connection.',
            ])
            ->addOption('config-file', [
                'help' => 'Configuration file where updated connection config will be saved.',
                'required' => false,
                'default' => CONFIG . 'app.php',
            ])
            ->addOption('connection', [
                'help' => 'Connection name to use.',
                'short' => 'c',
                'required' => false,
                'default' => 'default',
                'choices' => ConnectionManager::configured(),
            ])
            ->addOption('connection-driver', [
                'help' => 'Driver to use for new connection. Useful for unattended runs.',
                'required' => false,
                'choices' => ['Mysql', 'Postgres', 'Sqlite'],
            ])
            ->addOption('connection-host', [
                'help' => 'Database host for new connection. Useful for unattended runs.',
                'required' => false,
            ])
            ->addOption('connection-port', [
                'help' => 'Database port for new connection. Useful for unattended runs.',
                'required' => false,
            ])
            ->addOption('connection-database', [
                'help' => 'Database name (or path for SQLite) for new connection. Useful for unattended runs.',
                'required' => false,
            ])
            ->addOption('connection-username', [
                'help' => 'Database username for new connection. Useful for unattended runs.',
                'required' => false,
            ])
            ->addOption('connection-password', [
                'help' => 'Database password for new connection. Useful for unattended runs.',
                'required' => false,
            ]);

        return $parser;
    }

    /**
     * Configure connection.
     *
     * @return void
     */
    public function main()
    {
        $connectionName = $this->param('connection');
        $connection = ConnectionManager::get($connectionName);
        if (!($connection instanceof Connection)) {
            $this->abort('Invalid connection object');
        }

        // Check if connection has already been configured, and assert we're able to connect.
        if ($this->isConnectionConfigured($connection)) {
            $this->verbose('=====> <info>Connection has already been configured</info>');

            $this->checkCanConnect($connection);

            $this->out('=====> <success>Connection is still ok. Relax...</success>');

            return;
        }
        $this->verbose('=====> <info>Connection hasn\'t been configured yet</info>');

        // Ask user for connection params, check ability to connect, and save config to file.
        $newConnection = $this->readConnectionParams($connection);
        $this->checkCanConnect($newConnection);
        $this->saveConnectionConfig($newConnection);

        // Clean up things.
        $this->verbose('=====> Replacing old connection and flushing models');
        ConnectionManager::drop($connectionName);
        ConnectionManager::setConfig($connectionName, ['className' => Connection::class] + $newConnection->config());
        TableRegistry::clear();

        $this->out('=====> <success>Connection is ok. It\'s time to start using BEdita!</success>');
    }

    /**
     * Check if a connection has been properly configured.
     *
     * @param \Cake\Database\Connection $connection Connection instance.
     * @return bool
     */
    protected function isConnectionConfigured(Connection $connection)
    {
        static $original = [
            'host' => '__BE4_DB_HOST__',
            'port' => '__BE4_DB_PORT__',
            'database' => '__BE4_DB_DATABASE__',
            'username' => '__BE4_DB_USERNAME__',
            'password' => '__BE4_DB_PASSWORD__',
        ];

        $config = $connection->config();
        foreach ($original as $field => $originalValue) {
            if (isset($config[$field]) && $config[$field] === $originalValue) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if a connection is able to connect.
     *
     * @param \Cake\Database\Connection $connection Connection instance.
     * @return void
     */
    protected function checkCanConnect(Connection $connection)
    {
        $this->verbose('=====> Checking ability to connect... ', 0);
        try {
            $connection->connect();

            $this->verbose('<info>DONE</info>');
        } catch (MissingConnectionException $e) {
            $this->verbose('<error>FAIL</error>');
            $this->out('=====> <error>Connection failed</error>');
            $this->abort($e->getMessage());
        }
    }

    /**
     * Read connection parameters from interactive input.
     *
     * @param \Cake\Database\Connection $connection Connection instance.
     * @return \Cake\Database\Connection
     */
    protected function readConnectionParams(Connection $connection)
    {
        $config = array_fill_keys(['host', 'port', 'database', 'username', 'password'], '');
        $config += $connection->config();

        // Database driver.
        if (!$this->param('connection-driver')) {
            $this->params['connection-driver'] = $this->in('Enter database driver:', ['Mysql', 'Postgres', 'Sqlite'], 'Mysql');
        }
        $driver = $this->param('connection-driver');
        $config['driver'] = sprintf('Cake\Database\Driver\%s', $driver);

        if ($driver === 'Sqlite') {
            // Database path.
            if (!$this->param('connection-database')) {
                $this->params['connection-database'] = $this->in('Enter database path:', null, TMP . 'bedita.sqlite');
            }
            $config['database'] = $this->param('connection-database');

            return new Connection($config);
        }

        // Database host.
        if (!$this->param('connection-host')) {
            $this->params['connection-host'] = $this->in('Enter database host:', null, 'localhost');
        }
        $config['host'] = $this->param('connection-host');

        // Database port.
        if (!$this->param('connection-port')) {
            $this->params['connection-port'] = $this->in('Enter database port:', null, $driver === 'Mysql' ? 3306 : 5432);
        }
        $config['port'] = $this->param('connection-port');

        // Database name.
        if (!$this->param('connection-database')) {
            $this->params['connection-database'] = $this->in('Enter database name:', null, 'bedita');
        }
        $config['database'] = $this->param('connection-database');

        // Database username.
        if (!$this->param('connection-username')) {
            $this->params['connection-username'] = $this->in('Enter username to connect to database:');
        }
        $config['username'] = $this->param('connection-username');

        // Database password.
        if (!$this->param('connection-password')) {
            $this->quiet('=====> <warning>Typing will NOT be hidden!</warning> Please do not enter really sensitive data here.');
            $this->params['connection-password'] = $this->in('Enter password to connect to database:');
        }
        $config['password'] = $this->param('connection-password');

        return new Connection($config);
    }

    /**
     * Save new connection configuration to file.
     *
     * @param \Cake\Database\Connection $connection Connection instance.
     * @return void
     */
    protected function saveConnectionConfig(Connection $connection)
    {
        $file = new File($this->param('config-file'));
        if (!$file->exists() || !$file->readable() || !$file->writable()) {
            $this->abort('Unable to read from or write to configuration file');
        }

        $config = $connection->config();
        $replace = [
            'Cake\Database\Driver\Mysql' => Hash::get($config, 'driver', ''),
            '__BE4_DB_HOST__' => Hash::get($config, 'host', ''),
            '__BE4_DB_PORT__' => Hash::get($config, 'port', ''),
            '__BE4_DB_DATABASE__' => Hash::get($config, 'database', ''),
            '__BE4_DB_USERNAME__' => Hash::get($config, 'username', ''),

            // Escape special characters in password. Hopefully, other values will never contain
            // single quotes or backslashes, and leaving this little bug open lets us easily test
            // validation of PHP syntax.
            '__BE4_DB_PASSWORD__' => str_replace(['\'', '\\'], ['\\\'', '\\\\'], Hash::get($config, 'password', '')),
        ];

        // Replace placeholders in current file's content.
        $contents = str_replace(
            array_keys($replace),
            array_values($replace),
            $file->read()
        );

        // Open process to validate PHP syntax, and attach pipes to stdin, stdout and stderr.
        $this->verbose('=====> Validating updated configuration syntax before persisting changes... ', 0);
        $process = proc_open(
            '/usr/bin/env php -l',
            [
                0 => ['pipe', 'r'], // stdin (read-end on the process' side)
                1 => ['pipe', 'w'], // stdout (write-end on the process' side)
                2 => ['pipe', 'w'], // stderr (write-end on the process' side)
            ],
            $pipes // This array will contain the pipes as asked.
        );
        if (!is_resource($process)) {
            $this->verbose('<error>FAIL</error>');
            $this->abort('Could not validate configuration syntax');
        }

        // Write the file contents to the pipe that is connected to the process' stdin.
        fwrite($pipes[0], $contents);
        fclose($pipes[0]);

        // Check exit code (should be 0 if syntax is valid).
        $exitCode = proc_close($process);
        if ($exitCode !== 0) {
            $this->verbose('<error>FAIL</error>');
            $this->abort('Updated configuration file has invalid syntax');
        }
        $this->verbose('<info>DONE</info>');

        // Write changes to disk.
        $success = $file->write($contents);
        if (!$success) {
            $this->abort('Could not update configuration file');
        }
        $this->out('=====> <success>Configuration saved</success>');

        $file->close();
    }
}
