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
use Cake\Utility\Hash;

/**
 * Search command.
 *
 * This provides a command line interface to handle search indexes and data.
 * Operations available are:
 *
 * - `--reindex`: reindex all objects in the system
 * - `--index`: index a single object
 * - `--delete`: delete an object from index
 * - `--clear`: clear index
 *
 * Options available are:
 *
 * - `--dry-run`: dry run, do not perform any operation
 *
 * Usage examples:
 *
 * ```bash
 * bin/cake search --reindex
 * bin/cake search --reindex documents,events
 * bin/cake search --reindex --dry-run
 * bin/cake search --reindex documents,events --dry-run
 * bin/cake search --index 25
 * bin/cake search --index 25 --dry-run
 * bin/cake search --clear
 * bin/cake search --clear documents,events
 * bin/cake search --clear --dry-run
 * bin/cake search --clear documents,events --dry-run
 * bin/cake search --delete 25
 * bin/cake search --delete 25 --dry-run
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
     * Dry run flag.
     *
     * @var bool
     */
    protected $dryrun = false;

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        $parser->setDescription('Interface to handle search indexes and data.');
        $parser->addOption('reindex', [
            'help' => 'Reindex all or multiple objects in the system.',
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
            'help' => 'Remove index for all or multiple objects.',
            'required' => false,
        ]);
        $parser->addOption('dry-run', [
            'help' => 'Dry run, do not perform any operation.',
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
        $message = empty($operation) ? $this->getOptionParser()->help() : 'Perform ' . $operation . ' operation';
        $io->out($message);
        $this->Objects = $this->fetchTable('Objects');
        $this->dryrun = $args->getOption('dry-run') !== null;

        return empty($operation) ? Command::CODE_ERROR : $this->{$operation}($args, $io);
    }

    /**
     * Save index for all or multiple objects using all available adapters.
     *
     * @param \Cake\Console\Arguments $args The arguments
     * @param \Cake\Console\ConsoleIo $io The io console
     * @return int
     */
    protected function reindex(Arguments $args, ConsoleIo $io): int
    {
        return $this->doMultiIndex($args, $io, 'reindex', 'edit');
    }

    /**
     * Remove index for all or multiple objects using all available adapters.
     *
     * @param \Cake\Console\Arguments $args The arguments
     * @param \Cake\Console\ConsoleIo $io The io console
     * @return int
     */
    protected function clear(Arguments $args, ConsoleIo $io): int
    {
        return $this->doMultiIndex($args, $io, 'clear', 'delete');
    }

    /**
     * Save index on single object by ID using all available adapters.
     *
     * @param \Cake\Console\Arguments $args The arguments
     * @param \Cake\Console\ConsoleIo $io The io console
     * @return int
     */
    protected function index(Arguments $args, ConsoleIo $io): int
    {
        return $this->doSingleIndex($args, $io, 'index', 'edit');
    }

    /**
     * Remove index on single object by ID using all available adapters.
     *
     * @param \Cake\Console\Arguments $args The arguments
     * @param \Cake\Console\ConsoleIo $io The io console
     * @return int
     */
    protected function delete(Arguments $args, ConsoleIo $io): int
    {
        return $this->doSingleIndex($args, $io, 'delete', 'delete');
    }

    /**
     * Save or remove index on multiple objects.
     * If no type is specified, all enabled types are processed.
     * If a type is specified, only objects of that type are processed.
     *
     * @param \Cake\Console\Arguments $args The arguments
     * @param \Cake\Console\ConsoleIo $io The console io
     * @param string $operation The operation, can be `reindex` or `clear`
     * @param string $indexOperation The index operation, can be `edit` or `delete`
     * @return int
     */
    protected function doMultiIndex(Arguments $args, ConsoleIo $io, string $operation, string $indexOperation): int
    {
        $types = array_filter(explode(',', (string)$args->getOption($operation)));
        if (empty($types)) {
            $result = $this->fetchTable('ObjectTypes')->find()->where(['enabled' => true])->toArray();
            $types = (array)Hash::extract($result, '{n}.name');
        }
        foreach ($types as $type) {
            $io->out(sprintf('Perform "%s" on type "%s"', $operation, $type));
            $table = $this->fetchTable($type);
            $query = $table->find('type', [$type])->where(['deleted' => false]);
            foreach ($query->toArray() as $entity) {
                try {
                    $this->doIndexResource($entity, $io, $indexOperation);
                } catch (\Exception $e) {
                    $io->error($e->getMessage());

                    return Command::CODE_ERROR;
                }
            }
        }

        return Command::CODE_SUCCESS;
    }

    /**
     * Save or remove index on single object by ID using all available adapters.
     *
     * @param \Cake\Console\Arguments $args The arguments
     * @param \Cake\Console\ConsoleIo $io The console io
     * @param string $operation The operation, can be `index` or `delete`
     * @param string $indexOperation The index operation, can be `edit` or `delete`
     * @return int
     */
    protected function doSingleIndex(Arguments $args, ConsoleIo $io, string $operation, string $indexOperation): int
    {
        $id = $args->getOption($operation);
        if (empty($id)) {
            $io->error('Missing object ID');

            return Command::CODE_ERROR;
        }
        try {
            $obj = $this->Objects->find()->where(['id' => $id])->firstOrFail();
            $type = $obj->type;
            $table = $this->fetchTable($type);
            $entity = $table->get($id);
            $this->doIndexResource($entity, $io, $indexOperation);
        } catch (\Exception $e) {
            $io->error($e->getMessage());

            return Command::CODE_ERROR;
        }

        return Command::CODE_SUCCESS;
    }

    /**
     * Save or remove index for entity using all available adapters.
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param \Cake\Console\ConsoleIo $io The console io
     * @param string $indexOperation The index operation, can be `edit` or `delete`
     * @return void
     */
    protected function doIndexResource(EntityInterface $entity, ConsoleIo $io, string $indexOperation): void
    {
        $table = $this->fetchTable($entity->getSource());
        foreach ($table->getSearchAdapters() as $adapter) {
            $io->out(
                sprintf(
                    'Index %s [%s] [Adapter: %s]',
                    $entity->id,
                    $entity->uname,
                    get_class($adapter)
                )
            );
            if (!$this->dryrun) {
                $adapter->indexResource($entity, $indexOperation);
            }
        }
    }
}
