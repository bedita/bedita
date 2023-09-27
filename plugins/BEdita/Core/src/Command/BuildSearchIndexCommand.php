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
 * @property \BEdita\Core\Model\Table\ObjectsTable $Objects
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

        return $parser;
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $this->Objects = $this->fetchTable('Objects');

        $types = array_filter(explode(',', (string)$args->getOption('type')));
        if (empty($types)) {
            $result = $this->fetchTable('ObjectTypes')->find()->where(['enabled' => true])->toArray();
            $types = (array)Hash::extract($result, '{n}.name');
        }
        foreach ($types as $type) {
            $io->out(sprintf('Reindex objects by type "%s"', $type));
            foreach ($this->objectsIterator($type) as $entity) {
                try {
                    $this->doIndexResource($entity, $io, 'edit');
                } catch (\Exception $e) {
                    $io->error($e->getMessage());

                    return Command::CODE_ERROR;
                }
            }
        }

        return Command::CODE_SUCCESS;
    }

    /**
     * Save or remove index for entity using all available adapters.
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity
     * @param \Cake\Console\ConsoleIo $io The console io
     * @param string $idxOperation The index operation, can be `edit`
     * @return void
     */
    protected function doIndexResource(EntityInterface $entity, ConsoleIo $io, string $idxOperation): void
    {
        $table = $this->fetchTable($entity->getSource());
        foreach ($table->getSearchAdapters() as $adapter) {
            $io->out(
                sprintf(
                    'Index %s [%s] [op: %s] [Adapter: %s]',
                    $entity->id,
                    $entity->uname,
                    $idxOperation,
                    get_class($adapter)
                )
            );
            if (!$this->dryrun) {
                $adapter->indexResource($entity, $idxOperation);
            }
        }
    }

    /**
     * Get objects by type and return an iterable.
     *
     * @param string $type The object type
     * @return iterable
     */
    protected function objectsIterator(string $type): iterable
    {
        $table = $this->fetchTable($type);
        $query = $table->find()->orderAsc($table->aliasField('id'))->limit(200);
        $query = $query->find('type', [$type])->where(['deleted' => false]);
        $lastId = 0;
        while (true) {
            $q = clone $query;
            $q = $q->where(fn (QueryExpression $exp): QueryExpression => $exp->gt($table->aliasField('id'), $lastId));
            $results = $query->all();
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
