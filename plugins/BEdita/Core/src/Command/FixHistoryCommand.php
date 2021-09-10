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
use Cake\Console\Arguments;
use Cake\Console\Command;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Database\Driver\Postgres;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Generator;

/**
 * FixHistory command: add missing history items or repair existing ones
 * looking at `objects.created_by` and `objects.modified_by` properties.
 *
 * @since 4.6.0
 *
 * @property \BEdita\Core\Model\Table\ObjectsTable $Objects
 */
class FixHistoryCommand extends Command
{
    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Objects';

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
     * {@inheritDoc}
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser->addOption('id', [
                'help' => 'Object ID to check',
                'required' => false,
            ])
            ->addOption('type', [
                'help' => 'Object type name to check',
                'short' => 't',
                'required' => false,
            ]);
    }

    /**
     * Setup History table and application id
     *
     * @return void
     */
    public function initialize()
    {
        $this->History = $this->Objects->getBehavior('History')->Table;
        $application = TableRegistry::getTableLocator()->get('Applications')
            ->find()->orderAsc('id')->firstOrFail();
        $this->appId = $application->get('id');
    }

    /**
     * {@inheritDoc}
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        // repair missing `create` actions
        $query = $this->missingHistoryQuery(true, $args);
        $count = 0;
        foreach ($this->objectsGenerator($query) as $object) {
            /** @var \BEdita\Core\Model\Entity\ObjectEntity $object */
            $this->fixHistoryCreate($object);
            $count++;
        }
        $io->success('History creation items fixed: ' . $count);

        // repair missing `update` actions
        $query = $this->missingHistoryQuery(false, $args);
        $count = 0;
        foreach ($this->objectsGenerator($query) as $object) {
            /** @var \BEdita\Core\Model\Entity\ObjectEntity $object */
            $this->fixHistoryUpdate($object);
            $count++;
        }
        $io->success('History update items fixed: ' . $count);

        $io->success('Done');

        return null;
    }

    /**
     * Repair history `create` item
     *
     * @param \BEdita\Core\Model\Entity\ObjectEntity $object Object entity
     * @return void
     */
    protected function fixHistoryCreate(ObjectEntity $object): void
    {
        /** @var \BEdita\Core\Model\Entity\History $history */
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
        /** @var \BEdita\Core\Model\Entity\History $history */
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
        $history = $this->History->newEntity();
        $history->resource_id = $object->get('id');
        $history->resource_type = 'objects';
        $history->application_id = $this->appId;

        return $history;
    }

    /**
     * Create query for missing history data.
     *
     * @param bool $created Created flag, if true look for `create` action in history
     * @param Arguments $args Command arguments
     * @return Query
     */
    protected function missingHistoryQuery(bool $created, Arguments $args): Query
    {
        $query = $this->Objects->find();
        if ($args->getOption('type')) {
            $query = $query->find('type', [$args->getOption('type')]);
        }

        $conditions = [$this->History->aliasField('resource_id') . ' IS NULL'];
        if ($args->getOption('id')) {
            $conditions[] = [$this->Objects->aliasField('id') => $args->getOption('id')];
        }

        return $query->leftJoin(
            [$this->History->getAlias() => $this->History->getTable()],
            $this->joinConditions($query, $created)
        )->where($conditions);
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
            $query->newExpr()->eq($this->History->aliasField('resource_type'), 'objects'),
            $query->newExpr()->equalFields($idField, $this->Objects->aliasField('id')),
            $query->newExpr()->equalFields(
                $this->History->aliasField('user_id'),
                $this->Objects->aliasField($userField)
            ),
        ];
        if ($created) {
            $joinConditions[] = $query->newExpr()->eq($this->History->aliasField('user_action'), 'create');
        }

        return $joinConditions;
    }

    /**
     * Objects generator.
     *
     * @param \Cake\ORM\Query $query Query object
     * @return \Generator
     */
    protected function objectsGenerator(Query $query): Generator
    {
        $pageSize = 1000;
        $pages = ceil($query->count() / $pageSize);

        for ($page = 1; $page <= $pages; $page++) {
            yield from $query
                ->page($page, $pageSize)
                ->toArray();
        }
    }
}
