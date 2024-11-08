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

use BEdita\Core\Model\Table\ApplicationsTable;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Inflector;

/**
 * ResourcesModify command.
 */
class ResourcesModifyCommand extends Command
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
        $this->setName('cake resources_modify');
        parent::__construct();
    }

    /**
     * Editable resource fields
     *
     * @var string[]
     */
    public static $editableFields = ['api_key', 'description', 'enabled', 'name', 'unchangeable'];

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
            ])
            ->addOption('field', [
                'help' => 'Field name',
                'required' => true,
                'short' => 'f',
                'choices' => static::$editableFields,
            ]);
    }

    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return 'Resources modify';
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $type = $args->getOption('type');
        $field = $args->getOption('field');
        if (empty($type) || empty($field)) {
            $this->displayHelp($this->getOptionParser(), $args, $io);

            return static::CODE_ERROR;
        }
        $this->args = $args;
        $this->io = $io;
        $this->table = $this->fetchTable(Inflector::camelize($type));
        $id = $this->args->getArgument('name|id');
        $condition = is_numeric($id) ? compact('id') : ['name' => $id];
        $entity = $this->table->find()
            ->where($condition)
            ->first();
        if (empty($entity)) {
            $this->io->abort(sprintf('Resource with id %d not found', $id));
        }
        if ($field === 'api_key' && $this->table instanceof ApplicationsTable) {
            $entity->set('api_key', ApplicationsTable::generateApiKey());
        } else {
            $value = $this->io->ask(sprintf('New value for "%s" [current is "%s"]', $field, $entity->get($field)));
            $entity->set($field, $value);
        }
        $this->table->saveOrFail($entity);
        $this->io->out(sprintf('Resource with id %d modified', $entity->id));

        return static::CODE_SUCCESS;
    }
}
