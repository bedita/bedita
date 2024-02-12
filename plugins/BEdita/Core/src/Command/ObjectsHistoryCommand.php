<?php
declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2024 Atlas Srl, Chialab Srl
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
use Cake\I18n\FrozenDate;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query;
use Cake\Utility\Hash;

/**
 * ObjectsHistory command.
 */
class ObjectsHistoryCommand extends Command
{
    use LocatorAwareTrait;

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->addOption('action', [
                'help' => 'Action to execute',
                'default' => 'read',
                'required' => false,
                'choices' => ['read', 'delete'],
            ])
            ->addOption('id', [
                'help' => 'Filter history by resource id(s)',
                'required' => false,
                'multiple' => true,
            ])
            ->addOption('since', [
                'help' => 'Consider history since this date',
                'required' => false,
            ])
            ->addOption('type', [
                'help' => 'Filter history by type',
                'required' => false,
                'multiple' => true,
            ]);
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $action = $args->getOption('action');
        $id = (array)$args->getOption('id');
        $since = $args->getOption('since');
        $types = (array)$args->getOption('type');
        $message = sprintf('Perform "%s" on objects history', $action);
        $message .= !empty($id) ? ', for resource(s) id(s) ' . implode(',', $id) : '';
        $message .= !empty($since) ? sprintf(', since %s', $since) : '';
        $message .= !empty($types) ? ', for type(s) ' . implode(',', $types) : '';
        $io->info($message);
        $options = compact('id', 'since', 'types');
        $this->{$action}($io, $options);

        $io->info('Done');

        return self::CODE_SUCCESS;
    }

    /**
     * Delete history items.
     *
     * @param \Cake\Console\ConsoleIo $io The console IO
     * @param array $options The options
     * @return void
     */
    private function delete(ConsoleIo $io, array $options): void
    {
        $counter = $errors = 0;
        $query = $this->fetchQuery($options);
        $historyTable = $this->fetchTable('Objects')->getBehavior('History')->Table;
        $aliasId = $historyTable->aliasField('id');
        foreach ($this->historyIterator($query, $aliasId) as $historyItem) {
            $io->verbose('======> Deleting history item ' . $historyItem->id);
            try {
                $historyTable->deleteOrFail($historyItem);
                $counter++;
            } catch (\Exception $e) {
                $errors++;
                $io->error('Error deleting history item ' . $historyItem->id . ': ' . $e->getMessage());
            }
        }
        $io->success(sprintf('Deleted %d items [%d errors]', $counter, $errors));
    }

    /**
     * Read history items.
     *
     * @param \Cake\Console\ConsoleIo $io The console IO
     * @param array $options The options
     * @return void
     */
    private function read(ConsoleIo $io, array $options): void
    {
        $counter = 0;
        $query = $this->fetchQuery($options);
        $historyTable = $this->fetchTable('Objects')->getBehavior('History')->Table;
        $aliasId = $historyTable->aliasField('id');
        foreach ($this->historyIterator($query, $aliasId) as $historyItem) {
            $counter++;
            $io->info('======> ' . json_encode($historyItem->toArray()));
        }

        $io->success(sprintf('Found %d items', $counter));
    }

    /**
     * Query to fetch history items.
     *
     * @param array $options The options
     * @return \Cake\ORM\Query
     */
    private function fetchQuery(array $options): Query
    {
        $objectsTable = $this->fetchTable('Objects');
        $objectTypesTable = $this->fetchTable('ObjectTypes');
        $historyTable = $objectsTable->getBehavior('History')->Table;
        $historyTable->belongsTo('Objects', [
            'foreignKey' => false,
            'joinType' => 'INNER',
            'conditions' => function (QueryExpression $exp, Query $q) use ($historyTable, $objectsTable) {
                return $exp->eq(
                    $historyTable->aliasField('resource_id'),
                    $q->func()->cast($objectsTable->aliasField('id'), 'varchar')
                );
            },
        ]);
        $aliasCreated = $historyTable->aliasField('created');
        $aliasResourceId = $historyTable->aliasField('resource_id');
        $conditions = [];
        $conditions += !empty($options['id']) ? [$aliasResourceId . ' IN' => $options['id']] : [];
        $conditions += !empty($options['since']) ? [$aliasCreated . ' >' => new FrozenDate($options['since'])] : [];
        $query = $historyTable->find()->where($conditions);
        $types = (array)Hash::get($options, 'types', []);

        return $query
            ->innerJoinWith('Objects.ObjectTypes', function (Query $q) use ($objectTypesTable, $types) {
                if (empty($types)) {
                    return $q;
                }

                return $q->where([
                    $objectTypesTable->aliasField('name') . ' IN' => $types,
                ]);
            });
    }

    /**
     * Get history items as iterable.
     *
     * @param \Cake\ORM\Query $query The query
     * @return iterable
     */
    private function historyIterator(Query $query, string $aliasId): iterable
    {
        $lastId = 0;
        while (true) {
            $q = clone $query;
            $q = $q->where(fn (QueryExpression $exp): QueryExpression => $exp->gt($aliasId, $lastId));
            $results = $q->orderAsc($aliasId)->all();
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
