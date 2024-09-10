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
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Utility\Hash;

/**
 * Build search index command.
 *
 * This provides a command line interface to index objects for search.
 * Available arguments are:
 *
 * - `--type`: (multiple): reindex only objects from one or more specific types
 * - `--id` (multiple): reindex only one or more specific objects by ID
 * - `--uname` (multiple): same as --id, but using the object(s) unique names
 * - `--adapter` (multiple): reindex only using one or more specific adapters
 *
 * Usage examples:
 *
 * ```bash
 * bin/cake build_search_index
 * bin/cake build_search_index --type documents,events
 * bin/cake build_search_index --id 1,2,3,4,5
 * bin/cake build_search_index --uname my-document,my-event
 * ```
 *
 * @since 5.14.0
 */
class BuildSearchIndexCommand extends Command
{
    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        $parser = parent::buildOptionParser($parser);
        $parser->setDescription('Command to reindex objects for search.');
        $parser->addOption('type', [
            'help' => 'Reindex only objects from one or more specific types.',
            'required' => false,
        ]);
        $parser->addOption('id', [
            'help' => 'Reindex only one or more specific objects by ID.',
            'required' => false,
        ]);
        $parser->addOption('uname', [
            'help' => 'Reindex only one or more specific objects by uname.',
            'required' => false,
        ]);
        $parser->addOption('ancestor', [
            'help' => 'Reindex only objects with a specific ancestor folder.',
            'required' => false,
        ]);
        $parser->addOption('adapter', [
            'help' => 'Reindex only using one or more specific adapters.',
            'required' => false,
        ]);

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $io->out('Building search index');
        $io->out("\n" . 'This may take a while, please wait... (Ctrl+C to abort)');
        $types = array_filter(explode(',', (string)$args->getOption('type')));
        if (empty($types)) {
            $result = $this->fetchTable('ObjectTypes')
                ->find()
                ->where(['enabled' => true, 'is_abstract' => false])
                ->orderAsc('name')
                ->toArray();
            $types = (array)Hash::extract($result, '{n}.name');
        }
        $summary = [];
        $adapters = array_filter(explode(',', (string)$args->getOption('adapter')));
        foreach ($types as $type) {
            $io->verbose("\n" . sprintf('Indexing %s', $type) . "\n");
            $counter = 0;
            try {
                foreach ($this->objectsIterator($args, $type) as $entity) {
                    try {
                        $indexed = $this->doIndexResource($entity, $adapters, $io);
                        $counter = $counter + $indexed;
                    } catch (\Exception $e) {
                        $io->error($e->getMessage());

                        return Command::CODE_ERROR;
                    }
                }
            } catch (RecordNotFoundException $e) {
                $io->error($e->getMessage());

                return Command::CODE_ERROR;
            }
            $io->verbose(sprintf('> %s: %d', $type, $counter));
            if ($counter > 0) {
                $summary[] = sprintf('> %s: %d', $type, $counter);
            }
        }
        $io->out('');
        $io->out('Done. Summary:');
        $io->out('');
        if (empty($summary)) {
            $io->out('0 objects indexed.');
        }
        foreach ($summary as $msg) {
            $io->out($msg);
        }
        $io->out('');

        return Command::CODE_SUCCESS;
    }

    /**
     * Save or remove index for entity using all available adapters.
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param array $adapters The adapters to use. If empty, use all available adapters.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int
     */
    protected function doIndexResource(EntityInterface $entity, array $adapters, ConsoleIo $io): int
    {
        $indexed = 0;
        $table = $this->fetchTable($entity->getSource());
        foreach ($table->getSearchAdapters() as $adapter) {
            if (!empty($adapters) && !in_array($adapter->getAlias(), $adapters)) {
                continue;
            }
            $io->verbose(
                sprintf(
                    '> ID %s [%s] [Adapter: %s]',
                    $entity->id,
                    $entity->uname,
                    get_class($adapter)
                )
            );
            $adapter->indexResource($entity, 'edit');
            $indexed++;
        }

        return $indexed;
    }

    /**
     * Get objects by type and return an iterable.
     *
     * @param \Cake\Console\Arguments $args The command arguments
     * @param string $type The object type
     * @return iterable
     */
    protected function objectsIterator(Arguments $args, string $type): iterable
    {
        $table = $this->fetchTable('Objects');
        $query = $table->find()->orderAsc($table->aliasField('id'))->limit(200);
        $query = $query->find('type', [$type]);
        $id = array_filter(explode(',', (string)$args->getOption('id')));
        if (!empty($id)) {
            $query = $query->where([$table->aliasField('id') . ' IN' => $id]);
        }
        $uname = array_filter(explode(',', (string)$args->getOption('uname')));
        if (!empty($uname)) {
            $query = $query->where([$table->aliasField('uname') . ' IN' => $uname]);
        }
        $ancestor = (string)$args->getOption('ancestor');
        if (!empty($ancestor)) {
            $query = $query->find('ancestor', [$ancestor]);
        }
        $lastId = 0;
        while (true) {
            $q = clone $query;
            $q = $q->where(fn (QueryExpression $exp): QueryExpression => $exp->gt($table->aliasField('id'), $lastId));
            $results = $q->all();
            if ($results->isEmpty()) {
                break;
            }

            foreach ($results as $entity) {
                $lastId = $entity->id;

                yield $entity;
            }
        }
    }
}
