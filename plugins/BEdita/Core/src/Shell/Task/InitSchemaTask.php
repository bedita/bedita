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
namespace BEdita\Core\Shell\Task;

use Cake\Console\Shell;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionInterface;
use Cake\Datasource\ConnectionManager;
use Migrations\Migrations;

/**
 * Task to initialize database.
 *
 * @since 4.0.0
 */
class InitSchemaTask extends Shell
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
                'A new database schema is created using current DB connection.',
                'BEWARE: all existing tables will be dropped!',
            ])
            ->addOption('force', [
                'help' => 'Automatically drop all existing tables in database, if any. Useful for unattended runs.',
                'short' => 'f',
                'boolean' => true,
            ])
            ->addOption('no-force', [
                'help' => 'Do NOT drop any existing table in database. Useful for unattended runs.',
                'boolean' => true,
            ])
            ->addOption('seed', [
                'help' => 'Seed initial set of data. Useful for unattended runs.',
                'short' => 's',
                'boolean' => true,
            ])
            ->addOption('no-seed', [
                'help' => 'Do NOT seed initial set of data. Useful for unattended runs.',
                'boolean' => true,
            ])
            ->addOption('connection', [
                'help' => 'Connection name to use',
                'short' => 'c',
                'required' => false,
                'default' => 'default',
                'choices' => ConnectionManager::configured(),
            ]);

        return $parser;
    }

    /**
     * Initialize database.
     *
     * @return void
     */
    public function main()
    {
        $connection = ConnectionManager::get($this->param('connection'));

        $this->verbose('<comment>You are about to initialize your instance.</comment>');

        $this->cleanup($connection);
        $this->migrate($connection);
        $this->seed($connection);
    }

    /**
     * Remove all tables in database.
     *
     * @param \Cake\Datasource\ConnectionInterface $connection Connection instance.
     * @return void
     */
    protected function cleanup(ConnectionInterface $connection)
    {
        if (!($connection instanceof Connection) || count($connection->getSchemaCollection()->listTables()) === 0) {
            return;
        }

        if ($this->param('no-force')) {
            $this->params['force'] = false;
        } elseif (!$this->param('force')) {
            $this->_io->styles('blink', ['text' => 'red', 'blink' => true, 'bold' => true]);
            $this->quiet('<blink>CAREFUL!</blink> <warning>ALL CURRENT TABLES WILL BE DROPPED!</warning>');

            $this->params['force'] = ($this->in('Do you really want to proceed?', ['y', 'n'], 'n') === 'y');
        }
        if (!$this->param('force')) {
            // Exiting with exit code 74 (see http://tldp.org/LDP/abs/html/exitcodes.html and /usr/include/sysexits.h)
            $this->abort('Database is not empty, no action has been performed', 74);
        }

        $this->out('Dropping all tables in database...');
        $connection
            ->disableConstraints(function (Connection $connection) {
                $tables = $connection->getSchemaCollection()->listTables();

                foreach ($tables as $table) {
                    $this->verbose(sprintf(' - Dropping table <comment>%s</comment>... ', $table), 0);

                    $sql = $connection->getSchemaCollection()->describe($table)->dropSql($connection);
                    foreach ($sql as $query) {
                        $connection->query($query);
                    }

                    $this->verbose('<info>DONE</info>');
                }
            });
    }

    /**
     * Run schema migrations.
     *
     * @param \Cake\Datasource\ConnectionInterface $connection Connection instance.
     * @return void
     */
    protected function migrate(ConnectionInterface $connection)
    {
        $this->out('Running migrations... ', 0);
        $migrations = new Migrations([
            'connection' => $connection->configName(),
            'plugin' => 'BEdita/Core',
        ]);
        if (!$migrations->migrate()) {
            $this->out('<error>FAIL</error>');

            $this->abort('Could not migrate database, aborting');
        }
        $this->out('<info>DONE</info>');
    }

    /**
     * Seed initial set of data.
     *
     * @param \Cake\Datasource\ConnectionInterface $connection Connection instance.
     * @return void
     */
    protected function seed(ConnectionInterface $connection)
    {
        if ($this->param('no-seed')) {
            $this->params['seed'] = false;
        } elseif (!$this->param('seed')) {
            $question = 'Would you like to populate your database with an optional set of data?';
            $this->params['seed'] = ($this->in($question, ['y', 'n'], 'y') === 'y');
        }
        if (!$this->param('seed')) {
            return;
        }

        $this->out('Seeding data... ', 0);
        $migrations = new Migrations([
            'connection' => $connection->configName(),
            'plugin' => 'BEdita/Core',
        ]);
        if (!$migrations->seed(['plugin' => 'BEdita/Core', 'seed' => 'InitialSeed'])) {
            $this->out('<error>FAIL</error>');

            $this->abort('Could not seed initial data set');
        }
        $this->out('<info>DONE</info>');
    }
}
