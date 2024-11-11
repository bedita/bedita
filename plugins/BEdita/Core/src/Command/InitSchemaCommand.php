<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionInterface;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Locator\LocatorAwareTrait;
use Migrations\Migrations;

/**
 * InitSchema command.
 */
class InitSchemaCommand extends Command
{
    use LocatorAwareTrait;

    /**
     * Console arguments
     *
     * @var \Cake\Console\Arguments
     */
    protected $args;

    /**
     * Console IO
     *
     * @var \Cake\Console\ConsoleIo
     */
    protected $io;

    /**
     * Async jobs table
     *
     * @var \BEdita\Core\Model\Table\AsyncJobsTable
     */
    protected $table;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->setName('cake init_schema');
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
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
                'help' => 'Connection name to use.',
                'short' => 'c',
                'required' => false,
                'default' => 'default',
                'choices' => ConnectionManager::configured(),
            ]);
    }

    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return 'Init Schema';
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $this->args = $args;
        $this->io = $io;
        $connection = ConnectionManager::get($this->args->getOption('connection'));
        $this->io->verbose('<comment>You are about to initialize your instance.</comment>');
        $this->cleanup($connection);
        $this->migrate($connection);
        $this->seed($connection);

        return static::CODE_SUCCESS;
    }

    /**
     * Remove all tables in database.
     *
     * @param \Cake\Datasource\ConnectionInterface $connection Connection instance.
     * @return void
     * @codeCoverageIgnore
     */
    protected function cleanup(ConnectionInterface $connection): void
    {
        if (!($connection instanceof Connection) || count($connection->getSchemaCollection()->listTables()) === 0) {
            return;
        }

        if ($this->args->getOption('no-force')) {
            $this->params['force'] = false;
        } elseif (!$this->args->getOption('force')) {
            $this->_io->getStyle('blink', ['text' => 'red', 'blink' => true, 'bold' => true]);
            $this->io->quiet('<blink>CAREFUL!</blink> <warning>ALL CURRENT TABLES WILL BE DROPPED!</warning>');

            $this->params['force'] = ($this->io->askChoice('Do you really want to proceed?', ['y', 'n'], 'n') === 'y');
        }
        if (!$this->args->getOption('force')) {
            $this->io->abort('Database is not empty, no action has been performed');
        }

        $this->io->out('Dropping all tables in database...');
        $connection
            ->transactional(function (Connection $connection) {
                $tables = $connection->getSchemaCollection()->listTables();

                foreach ($tables as $table) {
                    $this->io->verbose(sprintf(' - Dropping constraints for table <comment>%s</comment>... ', $table), 0);

                    $sql = $connection->getSchemaCollection()->describe($table)->dropConstraintSql($connection);
                    foreach ($sql as $query) {
                        $connection->updateQuery($query);
                    }

                    $this->io->verbose('<info>DONE</info>');
                }
                foreach ($tables as $table) {
                    $this->io->verbose(sprintf(' - Dropping table <comment>%s</comment>... ', $table), 0);

                    $sql = $connection->getSchemaCollection()->describe($table)->dropSql($connection);
                    foreach ($sql as $query) {
                        $connection->updateQuery($query);
                    }

                    $this->io->verbose('<info>DONE</info>');
                }
            });
    }

    /**
     * Run schema migrations.
     *
     * @param \Cake\Datasource\ConnectionInterface $connection Connection instance.
     * @return void
     * @codeCoverageIgnore
     */
    protected function migrate(ConnectionInterface $connection)
    {
        $this->io->out('Running migrations... ', 0);

        $connection->transactional(function (Connection $connection) {
            $migrations = new Migrations([
                'connection' => $connection->configName(),
                'plugin' => 'BEdita/Core',
            ]);
            if (!$migrations->migrate()) {
                $this->io->out('<error>FAIL</error>');

                $this->io->abort('Could not migrate database, aborting');
            }
        });

        $this->io->out('<info>DONE</info>');
    }

    /**
     * Seed initial set of data.
     *
     * @param \Cake\Datasource\ConnectionInterface $connection Connection instance.
     * @return void
     * @codeCoverageIgnore
     */
    protected function seed(ConnectionInterface $connection)
    {
        if ($this->args->getOption('no-seed')) {
            $this->params['seed'] = false;
        } elseif (!$this->args->getOption('seed')) {
            $question = 'Would you like to populate your database with an optional set of data?';
            $this->params['seed'] = ($this->io->askChoice($question, ['y', 'n'], 'y') === 'y');
        }
        if (!$this->args->getOption('seed')) {
            return;
        }

        $this->io->out('Seeding data... ', 0);
        $connection->transactional(function (Connection $connection) {
            $migrations = new Migrations([
                'connection' => $connection->configName(),
                'plugin' => 'BEdita/Core',
            ]);
            if (!$migrations->seed(['plugin' => 'BEdita/Core', 'seed' => 'InitialSeed'])) {
                $this->io->out('<error>FAIL</error>');

                $this->io->abort('Could not seed initial data set');
            }
        });
        $this->io->out('<info>DONE</info>');
    }
}
