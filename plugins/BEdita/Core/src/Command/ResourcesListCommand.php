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

use BEdita\Core\Model\Action\ListEntitiesAction;
use BEdita\Core\Model\Table\AsyncJobsTable;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Inflector;

/**
 * ResourcesList command.
 */
class ResourcesListCommand extends Command
{
    use LocatorAwareTrait;

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
     * Async jobs table
     *
     * @var \BEdita\Core\Model\Table\AsyncJobsTable
     */
    protected AsyncJobsTable $table;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->setName('cake resources_list');
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->addOption('type', [
                'help' => 'Entity type',
                'required' => true,
                'short' => 't',
                'choices' => ['applications', 'roles', 'endpoints', 'endpoint_permissions'],
            ])
            ->addOption('filter', [
                'help' => 'List entities filtered by comma separated key=value pairs',
                'required' => false,
            ]);
    }

    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return 'Resources list';
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $type = $args->getOption('type');
        $this->args = $args;
        $this->io = $io;
        $this->table = $this->fetchTable(Inflector::camelize($type));
        $action = new ListEntitiesAction(['table' => $this->table]);
        $filter = $args->getOption('filter');
        $query = $action(compact('filter'));
        $results = $query->toArray();
        $this->io->out(sprintf('<info>%d result(s) found</info>', count($results)));
        $this->io->out($results);

        return static::CODE_SUCCESS;
    }
}
