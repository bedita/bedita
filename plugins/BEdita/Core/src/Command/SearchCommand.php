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
use Cake\Datasource\EntityInterface;

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
 *
 * Usage:
 *
 * ```bash
 * bin/cake search --reindex
 * bin/cake search --index 25
 * bin/cake search --clear
 * bin/cake search --delete 25
 * ```
 *
 * @since 5.14.0
 * @property \BEdita\Core\Model\Table\ObjectsTable $Objects
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
            'help' => 'Clear index by deleting all data.',
            'required' => false,
        ]);

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $tmp = array_intersect_key($args->getOptions(), array_flip($this->operations));
        $operation = empty($tmp) ? '' : (string)array_key_first($tmp);
        if (empty($operation)) {
            $io->out($this->getOptionParser()->help());
        }
        $this->Objects = $this->fetchTable('Objects');

        return empty($operation) ? Command::CODE_ERROR : $this->{$operation}($args, $io);
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
        $id = 1; // this is admin object, we don't want to reindex it
        $types = array_filter(explode(',', (string)$args->getOption('reindex')));
        $query = !empty($types) ? $this->Objects->find('type', $types) : $this->Objects->find();
        foreach ($query->where(['id >' => $id])->toArray() as $obj) {
            $this->saveIndexEntity($obj, $io);
            $id = $obj->id;
        }

        return Command::CODE_SUCCESS;
    }

    /**
     * Perform index on single object by ID.
     *
     * @param \Cake\Console\Arguments $args The arguments
     * @param \Cake\Console\ConsoleIo $io The io console
     * @return int
     */
    protected function index(Arguments $args, ConsoleIo $io): int
    {
        $id = $args->getOption('index');
        if (empty($id)) {
            $io->error('Missing object ID');

            return Command::CODE_ERROR;
        }
        try {
            $obj = $this->Objects->find()->where(['id' => $id])->firstOrFail();
            $this->saveIndexEntity($obj, $io);
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::CODE_ERROR;
        }

        return Command::CODE_SUCCESS;
    }

    /**
     * Perform delete on single object by ID.
     *
     * @param \Cake\Console\Arguments $args The arguments
     * @param \Cake\Console\ConsoleIo $io The io console
     * @return int
     */
    protected function delete(Arguments $args, ConsoleIo $io): int
    {
        $id = $args->getOption('delete');
        if (empty($id)) {
            $io->error('Missing object ID');

            return Command::CODE_ERROR;
        }
        try {
            $obj = $this->Objects->find()->where(['id' => $id])->firstOrFail();
            $this->removeIndexEntity($obj, $io);
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::CODE_ERROR;
        }

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
        $id = 1;
        foreach ($this->Objects->find()->where(['id >' => $id])->toArray() as $obj) {
            $this->removeIndexEntity($obj, $io);
            $id = $obj->id;
        }

        return Command::CODE_SUCCESS;
    }

    /**
     * Save index for entity using all available adapters.
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void
     */
    protected function saveIndexEntity(EntityInterface $entity, ConsoleIo $io): void
    {
        foreach ($this->Objects->getSearchAdapters() as $adapter) {
            $io->out('Save index ' . $entity->id . ' [' . $entity->uname . '] [Adapter: ' . get_class($adapter) . ']');
            $adapter->indexResource($entity, 'afterSave');
        }
    }

    /**
     * Remove index for entity using all available adapters.
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void
     */
    protected function removeIndexEntity(EntityInterface $entity, ConsoleIo $io): void
    {
        foreach ($this->Objects->getSearchAdapters() as $adapter) {
            $io->out('Remove index ' . $entity->id . ' [' . $entity->uname . '] [Adapter: ' . get_class($adapter) . ']');
            $adapter->indexResource($entity, 'afterDelete');
        }
    }
}
