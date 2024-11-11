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
            'arguments' => [],
            'options' => [],
        ],
        'check_schema' => [
            'class' => CheckSchemaCommand::class,
            'arguments' => [],
            'options' => [],
        ],
        'init_schema' => [
            'class' => InitSchemaCommand::class,
            'arguments' => [],
            'options' => [],
        ],
        'setup_admin_user' => [
            'class' => SetupAdminUserCommand::class,
            'arguments' => [],
            'options' => [],
        ],
        'setup_connection' => [
            'class' => SetupConnectionCommand::class,
            'arguments' => [],
            'options' => [],
        ],
    ];

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->addArgument('subcommand', [
                'help' => 'Subcommand to perform',
                'choices' => array_keys($this->subcommands),
            ]);
    }

    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return 'BEdita management command. Available subcommands: check_api_key, check_filesystem, check_schema, init_schema, setup_admin_user, setup_connection';
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $subcommand = $args->getArgument('subcommand');
        if (empty($subcommand) || !in_array($subcommand, array_keys($this->subcommands))) {
            return $this->executeCommand($this, ['--help'], $io);
        }
        $subcommandArguments = [];
        $allowedArguments = $this->subcommands[$subcommand]['arguments'];
        foreach ($allowedArguments as $argumentName) {
            $subcommandArguments[] = $args->getArgument($argumentName);
        }
        $allowedOptions = $this->subcommands[$subcommand]['options'];
        foreach ($args->getOptions() as $option => $value) {
            if (in_array($option, $allowedOptions)) {
                $subcommandArguments[] = sprintf('--%s', $option);
                $subcommandArguments[] = $value;
            }
        }

        return $this->executeCommand($this->subcommands[$subcommand]['class'], $subcommandArguments, $io);
    }
}
