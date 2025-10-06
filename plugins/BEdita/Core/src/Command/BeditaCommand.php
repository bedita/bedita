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
use Cake\Datasource\ConnectionManager;

/**
 * Bedita command. Utility that internally calls other commands.
 *
 * - CheckApiKeyCommand
 * - CheckFilesystemCommand
 * - CheckSchemaCommand
 * - InitSchemaCommand
 * - SetupAdminUserCommand
 * - SetupConnectionCommand
 */
class BeditaCommand extends Command
{
    use SubcommandTrait;

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
     * Subcommands.
     *
     * @var array
     */
    protected array $subcommands = [
        'check_api_key' => [
            'class' => CheckApiKeyCommand::class,
            'arguments' => [],
            'options' => [],
        ],
        'check_filesystem' => [
            'class' => CheckFilesystemCommand::class,
            'arguments' => [
                'paths',
            ],
            'options' => [
                'httpd-user',
            ],
        ],
        'check_schema' => [
            'class' => CheckSchemaCommand::class,
            'arguments' => [],
            'options' => [
                'connection',
                'ignore-migration-status',
            ],
        ],
        'init_schema' => [
            'class' => InitSchemaCommand::class,
            'arguments' => [],
            'options' => [
                'force',
                'no-force',
                'seed',
                'no-seed',
                'connection',
            ],
        ],
        'setup_admin_user' => [
            'class' => SetupAdminUserCommand::class,
            'arguments' => [],
            'options' => [
                'connection',
                'admin-overwrite',
                'no-admin-overwrite',
                'admin-username',
                'admin-password',
            ],
        ],
        'setup_connection' => [
            'class' => SetupConnectionCommand::class,
            'arguments' => [],
            'options' => [
                'connection',
                'config-file',
                'connection-driver',
                'connection-host',
                'connection-port',
                'connection-database',
                'connection-username',
                'connection-password',
                'connection-password-empty',
            ],
        ],
    ];

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->addArguments([
                'subcommand' => [
                    'help' => 'Subcommand to perform',
                    'choices' => array_merge(['check', 'setup'], array_keys($this->subcommands)),
                ],
                // check_filesystem
                'paths' => ['required' => false],
            ])
            ->addOptions([
                // check_schema
                'connection' => [
                    'help' => 'Connection name to use.',
                    'short' => 'c',
                    'required' => false,
                    'default' => 'default',
                    'choices' => ConnectionManager::configured(),
                ],
                'ignore-migration-status' => ['required' => false],
                // check_filesystem
                'httpd-user' => ['required' => false],
                // init_schema
                'force' => ['required' => false, 'short' => 'f'],
                'no-force' => ['required' => false],
                'seed' => ['required' => false, 'short' => 's'],
                'no-seed' => ['required' => false],
                //'connection' already declared
                // setup_admin_user
                'admin-overwrite' => ['required' => false],
                'no-admin-overwrite' => ['required' => false],
                'admin-username' => ['required' => false],
                'admin-password' => ['required' => false],
                // setup_connection
                //'connection' already declared
                'config-file' => ['required' => false],
                'connection-driver' => ['required' => false],
                'connection-host' => ['required' => false],
                'connection-port' => ['required' => false],
                'connection-database' => ['required' => false],
                'connection-username' => ['required' => false],
                'connection-password' => ['required' => false],
                'connection-password-empty' => ['required' => false],
            ]);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public static function getDescription(): string
    {
        return 'BEdita management command. Available subcommands: check_api_key, check_filesystem, check_schema, init_schema, setup_admin_user, setup_connection';
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $this->args = $args;
        $this->io = $io;
        $subcommand = $args->getArgument('subcommand');
        if (in_array($subcommand, ['check', 'setup'])) {
            $this->{$subcommand}();

            return static::CODE_SUCCESS;
        }
        if (in_array($subcommand, array_keys($this->subcommands))) {
            return $this->executeSubcommand($subcommand);
        }

        return $this->executeCommand($this, ['--help'], $io);
    }

    /**
     * Check bedita instance.
     *
     * @return void
     * @codeCoverageIgnore
     */
    public function check(): void
    {
        $this->io->out('=====> Checking schema');
        $this->executeSubcommand('check_schema');

        $this->io->hr();

        $this->io->out('=====> Checking filesystem permissions');
        $this->executeSubcommand('check_filesystem');
    }

    /**
     * Initial set up for a BEdita instance.
     *
     * @return void
     * @codeCoverageIgnore
     */
    public function setup(): void
    {
        $this->io->out('=====> Checking connection');
        $this->executeSubcommand('setup_connection');

        $this->io->hr();

        $tables = ConnectionManager::get($this->args->getOption('connection'))->getSchemaCollection()->listTables();
        if (empty($tables)) {
            $this->io->out('=====> Initializing schema');
            $this->executeSubcommand('init_schema');
        } else {
            $this->io->out('=====> Checking schema');
            $this->executeSubcommand('check_schema');
        }

        $this->io->hr();

        $this->io->out('=====> Checking filesystem permissions');
        $this->executeSubcommand('check_filesystem');

        $this->io->hr();

        if ($this->args->getOption('connection') !== 'default') {
            ConnectionManager::alias($this->args->getOption('connection'), 'default');
        }
        try {
            $this->io->out('=====> Configuring default administrator user');
            $this->executeSubcommand('setup_admin_user');

            $this->io->hr();

            $this->io->out('=====> Checking API key');
            $this->executeSubcommand('check_api_key');
        } finally {
            ConnectionManager::dropAlias('default');
        }
    }
}
