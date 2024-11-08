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
 * Resources command.
 */
class ResourcesCommand extends Command
{
    /**
     * Subcommands.
     *
     * @var array
     */
    protected array $subcommands = [
        'add' => [
            'class' => ResourcesAddCommand::class,
            'arguments' => [],
            'options' => ['type'],
        ],
        'edit' => [
            'class' => ResourcesModifyCommand::class,
            'arguments' => ['name|id'],
            'options' => ['field', 'type'],
        ],
        'ls' => [
            'class' => ResourcesListCommand::class,
            'arguments' => [],
            'options' => ['filter', 'type'],
        ],
        'rm' => [
            'class' => ResourcesRemoveCommand::class,
            'arguments' => ['name|id'],
            'options' => ['type'],
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
            ])
            ->addArgument('name|id', [
                'help' => 'Resource\'s name or id',
                'required' => false, // required only in some subcommands
            ])
            ->addOption('type', [
                'help' => 'Entity type',
                'required' => false, // required only in some subcommands
                'short' => 't',
            ])
            ->addOption('filter', [
                'help' => 'List entities filtered by comma separated key=value pairs',
                'required' => false,
                'short' => 'q',
            ])
            ->addOption('field', [
                'help' => 'Field name',
                'required' => false, // required only in some subcommands
                'short' => 'f',
                'choices' => ResourcesModifyCommand::$editableFields,
            ]);
    }

    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return 'Resources management command. Available subcommands: add, edit, ls, rm';
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $subcommand = $args->getArgument('subcommand');
        if (empty($subcommand) || !in_array($subcommand, array_keys($this->subcommands))) {
            return $this->executeCommand($this, ['--help']);
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

        return $this->executeCommand($this->subcommands[$subcommand]['class'], $subcommandArguments);
    }
}
