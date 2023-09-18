<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
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
 * Search command.
 *
 * This provides a command line interface to handle search indexes and data.
 * Operations available are:
 *
 * - `reindex`: reindex all objects in the system
 * - `index`: index a single object
 * - `delete`: delete an object from index
 * - `clear`: clear index
 */
class SearchCommand extends Command
{
    /**
     * Operations available.
     *
     * @var string[]
     */
    protected $operations = [
        'reindex',
        'index',
        'delete',
        'clear',
    ];

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        $parser->setDescription('Interface to handle search indexes and data.');
        $parser->addOption('reindex', [
            'help' => 'Reindex all objects in the system.',
            'required' => false,
        ]);
        $parser->addOption('index', [
            'help' => 'Index a single object.',
            'required' => false,
        ]);
        $parser->addOption('delete', [
            'help' => 'Delete an object from index.',
            'required' => false,
        ]);
        $parser->addOption('clear', [
            'help' => 'Clear index.',
            'required' => false,
        ]);

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $operation = $this->operation($args);
        if (empty($operation)) {
            $io->out($this->getOptionParser()->help());

            return Command::CODE_ERROR;
        }

        return $this->{$operation}($args, $io);
    }

    /**
     * Get operation from option, if any.
     *
     * @param \Cake\Console\Arguments $args The arguments
     * @return string
     */
    protected function operation(Arguments $args): string
    {
        $tmp = array_intersect_key($args->getOptions(), array_flip($this->operations));

        return empty($tmp) ? '' : array_key_first($tmp);
    }

    /**
     * Perform reindex.
     *
     * @param \Cake\Console\Arguments $args The arguments
     * @param \Cake\Console\ConsoleIo $io The io console
     * @return int
     */
    protected function reindex(Arguments $args, ConsoleIo $io): int
    {
        return Command::CODE_SUCCESS;
    }

    /**
     * Perform index.
     *
     * @param \Cake\Console\Arguments $args The arguments
     * @param \Cake\Console\ConsoleIo $io The io console
     * @return int
     */
    protected function index(Arguments $args, ConsoleIo $io): int
    {
        return Command::CODE_SUCCESS;
    }

    /**
     * Perform delete.
     *
     * @param \Cake\Console\Arguments $args The arguments
     * @param \Cake\Console\ConsoleIo $io The io console
     * @return int
     */
    protected function delete(Arguments $args, ConsoleIo $io): int
    {
        return Command::CODE_SUCCESS;
    }

    /**
     * Perform clear.
     *
     * @param \Cake\Console\Arguments $args The arguments
     * @param \Cake\Console\ConsoleIo $io The io console
     * @return int
     */
    protected function clear(Arguments $args, ConsoleIo $io): int
    {
        return Command::CODE_SUCCESS;
    }
}
