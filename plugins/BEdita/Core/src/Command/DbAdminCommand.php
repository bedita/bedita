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

/**
 * DbAdmin command. Utility that internally calls database related commands:
 *  - check schema consistency (CheckSchemaCommand)
 *  - initialize a new database instance (InitSchemaCommand)
 */
class DbAdminCommand extends Command
{
    use SubcommandTrait;

    /**
     * Console arguments
     *
     * @var \Cake\Console\Arguments
     */
    protected Arguments $args;

    /**
     * Console IO
     *
     * @var \Cake\Console\ConsoleIo
     */
    protected ConsoleIo $io;

    /**
     * Subcommands.
     *
     * @var array
     */
    protected array $subcommands = [
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
                    'choices' => array_keys($this->subcommands),
                ],
            ])
            ->addOptions([
                // check_schema
                'connection' => ['required' => false, 'short' => 'c'],
                'ignore-migration-status' => ['required' => false],
                // init_schema
                'force' => ['required' => false, 'short' => 'f'],
                'no-force' => ['required' => false],
                'seed' => ['required' => false, 'short' => 's'],
                'no-seed' => ['required' => false],
                //'connection' already declared
            ]);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public static function getDescription(): string
    {
        return 'BEdita database management command. Available subcommands: check_schema, init_schema';
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
        if (in_array($subcommand, array_keys($this->subcommands))) {
            return $this->executeSubcommand($subcommand);
        }

        return $this->executeCommand($this, ['--help'], $io);
    }
}
