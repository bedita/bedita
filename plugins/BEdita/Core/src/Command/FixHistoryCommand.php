<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Command;

use BEdita\Core\Model\Entity\History;
use BEdita\Core\Model\Entity\ObjectEntity;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Database\Driver\Postgres;
use Cake\Database\Expression\IdentifierExpression;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Generator;

/**
 * FixHistory command: add missing history items or repair existing ones
 * looking at `objects.created_by` and `objects.modified_by` properties.
 *
 * @since 4.6.0
 * @property \BEdita\Core\Model\Table\ObjectsTable $Objects
 */
class FixHistoryCommand extends Command
{
    /**
     * @inheritDoc
     */
    public $defaultTable = 'Objects';

    /**
     * History table
     *
     * @var \Cake\ORM\Table
     */
    public $History;

    /**
     * Application ID
     *
     * @var int
     */
    public $appId;

    /**
     * Increment to use in object ID scan
     *
     * @var int
     */
    public const INCREMENT = 500;

    /**
     * Min object ID to scan
     *
     * @var int
     */
    protected $minId;

    /**
     * Max object ID to scan
     *
     * @var int
     */
    protected $maxId;

    /**
     * @inheritDoc
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser->addOption('from', [
                'help' => 'Min ID to check',
                'short' => 'f',
                'required' => false,
            ])
            ->addOption('to', [
                'help' => 'Max ID to check',
                'short' => 't',
                'required' => false,
            ]);
    }

    /**
     * Setup History table and application id
     *
     * @return void
     */
    public function initialize(): void
    {
        $this->History = $this->Objects->getBehavior('History')->Table;
        $application = TableRegistry::getTableLocator()->get('Applications')
            ->find()->orderAsc('id')->firstOrFail();
        $this->appId = $application->get('id');
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $this->minId = intval($args->getOption('from'));
        if ($this->minId === 0) {
            $this->minId = 1;
        }
        $this->maxId = intval($args->getOption('to'));
        if ($this->maxId === 0) {
            $q = $this->Objects->find();
            $max = $q->select(['max_id' => $q->func()->max('id')])->first()->get('max_id');
            $this->maxId = intval($max);
        }

        $io->info('Repair `create` history actions');
        $count = 0;
        foreach ($this->objectsGenerator(true, $io) as $object) {
            /** @var \BEdita\Core\Model\Entity\ObjectEntity $object */
            $this->fixHistoryCreate($object);
            $count++;
            $this->objectDetails($object, 'create', $io);
        }
        $io->success('History creation items fixed: ' . $count);

        $io->info('Repair `update` history actions');
        $count = 0;
        foreach ($this->objectsGenerator(false, $io) as $object) {
            /** @var \BEdita\Core\Model\Entity\ObjectEntity $object */
            $this->fixHistoryUpdate($object);
            $count++;
            $this->objectDetails($object, 'update', $io);
        }
        $io->success('History update items fixed: ' . $count);

        $io->success('Done');

        return null;
    }

    /**
     * Output object details to console
     *
     * @param \BEdita\Core\Model\Entity\ObjectEntity $object Object entity
     * @param string $action History action
     * @param \Cake\Console\ConsoleIo $io Console I/O
     * @return void
     */
    protected function objectDetails(ObjectEntity $object, string $action, ConsoleIo $io): void
    {
        $msg = sprintf(
            'Fixed "%s" on [%d] "%s" [%s]',
            $action,
            $object->id,
            $object->title,
            $object->type
        );
        $io->info($msg);
    }

    /**
     * Repair history `create` item
     *
     * @param \BEdita\Core\Model\Entity\ObjectEntity $object Object entity
     * @return void
     */
    protected function fixHistoryCreate(ObjectEntity $object): void
    {
        /** @var \BEdita\Core\Model\Entity\History|null $history */
        $history = $this->History
            ->find()->where([
                $this->History->aliasField('resource_id') => $object->id,
                $this->History->aliasField('resource_type') => 'objects',
                $this->History->aliasField('user_action') => 'create',
            ])
            ->first();
        if (empty($history)) {
            $history = $this->historyEntity($object);
            $history->user_action = 'create';
        }
        $history->user_id = $object->get('created_by');
        $history->created = $object->get('created');

        $this->History->saveOrFail($history);
    }

    /**
     * Repair history `update` item
     *
     * @param \BEdita\Core\Model\Entity\ObjectEntity $object Object entity
     * @return void
     */
    protected function fixHistoryUpdate(ObjectEntity $object): void
    {
        /** @var \BEdita\Core\Model\Entity\History|null $history */
        $history = $this->History
            ->find()->where([
                $this->History->aliasField('resource_id') => $object->id,
                $this->History->aliasField('resource_type') => 'objects',
                sprintf("%s != 'create'", $this->History->aliasField('user_action')),
            ])
            ->first();
        if (empty($history)) {
            $history = $this->historyEntity($object);
            $history->user_action = 'update';
        }
        $history->user_id = $object->get('modified_by');
        $history->created = $object->get('modified');

        $this->History->saveOrFail($history);
    }

    /**
     * History entity
     *
     * @param \BEdita\Core\Model\Entity\ObjectEntity $object Object entity
     * @return \BEdita\Core\Model\Entity\History
     */
    protected function historyEntity(ObjectEntity $object): History
    {
        /** @var \BEdita\Core\Model\Entity\History $history */
        $history = $this->History->newEntity([]);
        $history->resource_id = $object->get('id');
        $history->resource_type = 'objects';
        $history->application_id = $this->appId;

        return $history;
    }

    /**
     * Create query for missing history data.
     *
     * @param bool $created Created flag, if true look for `create` action in history
     * @param int $from From ID
     * @param int $to To ID
     * @return \Cake\ORM\Query
     */
    protected function missingHistoryQuery(bool $created, int $from, int $to): Query
    {
        $query = $this->Objects->find();

        return $query->leftJoin(
            [$this->History->getAlias() => $this->History->getTable()],
            $this->joinConditions($query, $created)
        )->where(function (QueryExpression $exp, Query $q) use ($from, $to) {
            return $exp->and([
                $q->expr()->between($this->Objects->aliasField('id'), $from, $to),
                $q->expr()->isNull($this->History->aliasField('resource_id')),
            ]);
        });
    }

    /**
     * Join conditions used in `missingHistoryQuery`
     *
     * @param \Cake\ORM\Query $query Query object
     * @param bool $created Created flag, if true look for `create` action in history
     * @return array
     */
    protected function joinConditions(Query $query, bool $created): array
    {
        $idField = $this->History->aliasField('resource_id');
        // On Postgres we need an explicit cast to INTEGER to avoid
        // this error "operator does not exist: character varying = integer"
        if ($query->getConnection()->getDriver() instanceof Postgres) {
            $idField = $query->func()->cast($idField, 'INTEGER');
        }

        $userField = 'created_by';
        if (!$created) {
            $userField = 'modified_by';
        }
        $joinConditions = [
            $query->expr()->eq($this->History->aliasField('resource_type'), 'objects'),
            $query->expr()->eq($idField, new IdentifierExpression($this->Objects->aliasField('id'))),
            $query->expr()->equalFields(
                $this->History->aliasField('user_id'),
                $this->Objects->aliasField($userField)
            ),
        ];
        if ($created) {
            $joinConditions[] = $query->expr()->eq($this->History->aliasField('user_action'), 'create');
        }

        return $joinConditions;
    }

    /**
     * Objects generator.
     *
     * @param bool $created Created flag, if true look for `create` action in history
     * @param \Cake\Console\ConsoleIo $io Console I/O
     * @return \Generator
     */
    protected function objectsGenerator(bool $created, ConsoleIo $io): Generator
    {
        $from = $this->minId;
        $to = $from + min(self::INCREMENT - 1, $this->maxId - $from);

        while ($from <= $this->maxId) {
            $io->info(sprintf('Searching IDs from %d to %d', $from, $to));

            yield from $this->missingHistoryQuery($created, $from, $to)->toArray();

            $from = $to + 1;
            $to = $from + min(self::INCREMENT - 1, $this->maxId - $from);
        }
    }
}
