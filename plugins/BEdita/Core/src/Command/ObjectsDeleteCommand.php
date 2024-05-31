<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 Channelweb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Command;

use BEdita\Core\Model\Entity\ObjectEntity;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Database\Expression\QueryExpression;
use Cake\I18n\FrozenDate;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * ObjectsDelete command.
 */
class ObjectsDeleteCommand extends Command
{
    use LocatorAwareTrait;

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->addOption('since', [
                'help' => 'Delete objects in trash since this date',
                'required' => false,
                'default' => '-1 month',
            ])
            ->addOption('type', [
                'help' => 'Delete objects in trash by type',
                'required' => false,
                'multiple' => true,
            ]);
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $since = $args->getOption('since');
        $types = (array)$args->getOption('type');
        $message = 'Deleting from trash objects, since ' . $since;
        $message .= !empty($types) ? ', for type(s) ' . implode(',', $types) : '';
        $io->info($message);
        $conditions = ['deleted' => true, 'locked' => false, 'modified <' => new FrozenDate($since)];
        $deleted = $errors = 0;
        if (empty($types)) {
            $types = [null];
        }
        foreach ($types as $objectType) {
            foreach ($this->objectsIterator($objectType, $conditions) as $object) {
                $this->deleteObject($io, $object, $deleted, $errors);
            }
        }
        $io->success(sprintf('Deleted from trash %d objects [%d errors]', $deleted, $errors));
        $io->info('Done');

        return self::CODE_SUCCESS;
    }

    /**
     * Delete object.
     *
     * @param \Cake\Console\ConsoleIo $io The console io
     * @param \BEdita\Core\Model\Entity\ObjectEntity $object The object
     * @param int $deleted The number of deleted objects
     * @param int $errors The number of errors
     * @return void
     */
    private function deleteObject(ConsoleIo $io, ObjectEntity $object, int &$deleted, int &$errors): void
    {
        try {
            $io->verbose(sprintf('Deleting object %s', $object->id));
            $object->getTable()->deleteOrFail($object);
            $deleted++;
        } catch (\Throwable $e) {
            $io->error(sprintf('Error deleting object %s: %s', $object->id, $e->getMessage()));
            $errors++;
        }
    }

    /**
     * Get objects as iterable.
     *
     * @param string|null $type The object type
     * @param array $conditions The conditions
     * @return iterable
     */
    private function objectsIterator(?string $type, array $conditions): iterable
    {
        $table = $this->fetchTable('Objects');
        $query = empty($type) ? $table->find() : $table->find('type', [$type]);
        $query = $query->where($conditions)->limit(200);
        $lastId = 0;
        while (true) {
            $q = clone $query;
            $q = $q->where(fn (QueryExpression $exp): QueryExpression => $exp->gt($table->aliasField('id'), $lastId));
            $results = $q->orderAsc($table->aliasField('id'))->all();
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
