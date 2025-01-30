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

use BEdita\Core\Model\Action\DeleteEntityAction;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Table;
use Cake\Utility\Inflector;

/**
 * ResourcesRemove command.
 */
class ResourcesRemoveCommand extends Command
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
     * The table
     *
     * @var \Cake\ORM\Table
     */
    protected Table $table;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->setName('cake resources_remove');
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->addArgument('name|id', [
                'help' => 'Resource\'s name or id',
                'required' => true,
            ])
            ->addOption('type', [
                'help' => 'Entity type',
                'required' => true,
                'short' => 't',
                'choices' => ['applications', 'roles', 'endpoints', 'endpoint_permissions'],
            ]);
    }

    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return 'Resources remove';
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
        $id = $this->args->getArgument('name|id');
        $res = $this->io->askChoice(
            sprintf('You are REMOVING "%s" with name or id "%s" - are you sure?', $type, $id),
            ['y', 'n'],
            'n'
        );
        if ($res !== 'y') {
            $this->io->info('No action performed');

            return static::CODE_ERROR;
        }
        $condition = is_numeric($id) ? compact('id') : ['name' => $id];
        $entity = $this->table->find()
            ->where($condition)
            ->first();
        if (empty($entity)) {
            $this->io->abort(sprintf('Resource with id %s not found', $id));
        }
        $action = new DeleteEntityAction(['table' => $this->table]);
        $action(compact('entity'));
        $this->io->out(sprintf('Record "%s" deleted', $id));

        return static::CODE_SUCCESS;
    }
}
