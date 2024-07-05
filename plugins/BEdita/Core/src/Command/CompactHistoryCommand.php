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

use BEdita\Core\Model\Entity\History;
use BEdita\Core\Model\Entity\ObjectEntity;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\Query;
use Generator;

/**
 * CompactHistory command: remove duplicates.
 *
 * @since 4.6.0
 * @property \BEdita\Core\Model\Table\ObjectsTable $Objects
 */
class CompactHistoryCommand extends Command
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
     * Page size for queries
     *
     * @var int
     */
    public const PAGE_SIZE = 500;

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
        $application = $this->fetchTable('Applications')->find()->orderAsc('id')->firstOrFail();
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
        $io->info(sprintf('Min ID: %d - Max ID: %d', $this->minId, $this->maxId));
        $count = $processed = 0;
        $current = $this->minId;
        while ($current <= $this->maxId) {
            $io->verbose(sprintf('Process ID %d', $current));
            if (!$this->Objects->exists([$this->Objects->aliasField('id') => $current])) {
                $io->verbose(sprintf('ID %d not found. Skip', $current));
                $current++;

                continue;
            }
            if ($this->fixHistoryCompact($current, $io)) {
                $count++;
            }
            $processed++;
            $current++;
        }
        $io->success(sprintf('Processed %d, removed duplicates for %d object(s)', $processed, $count));

        $io->success('Done');

        return null;
    }

    /**
     * Compact history records per object
     *
     * @param int $objectId The object ID
     * @return bool
     */
    protected function fixHistoryCompact(int $objectId, ConsoleIo $io): bool
    {
        $query = $this->History
            ->find()
            ->where([
                $this->History->aliasField('resource_id') => $objectId,
                $this->History->aliasField('resource_type') => 'objects',
            ]);
        $prev = null;
        $duplicated = [];
        $processed = 0;
        $stack = [];
        foreach ($this->objectsGenerator($query) as $current) {
            $processed++;
            if ($prev === null) {
                $prev = $current;
                continue;
            }
            switch (count($stack))
            {
                case 0:
                case 1:
                    $stack = [$prev, $current];
                    break;
                case 2:
                    $stack = [$stack[1], $prev, $current];
                    break;
                default:
                    $stack = [$stack[1], $stack[2], $current];
                    break;
            }
            $io->verbose(
                sprintf(
                    ':[%d] Resource ID %d',
                    $processed,
                    $objectId
                )
            );
            foreach ($stack as $i => $h) {
                $io->verbose(sprintf(':: History ID %d: %s', $h->id, $this->serialize($h)));
                if ($i === 0) {
                    continue;
                }
                if ($i === 1) {
                    if ($this->compare($h, $stack[$i - 1])) {
                        $duplicated[] = $stack[$i - 1];
                    }
                    continue;
                }
                // $i === 2
                if ($this->compare($h, $stack[$i - 1])) {
                    $duplicated[] = $stack[$i - 1];
                }
                if ($this->compare($h, $stack[$i - 2])) {
                    $duplicated[] = $stack[$i - 2];
                }
            }
            $io->verbose(sprintf(':[%d] Resource ID %d, history ID %d: duplicated %d', $processed, $objectId, $current->id, count($duplicated)));
            $prev = $current;
        }
        if (empty($duplicated)) {
            $io->verbose(':: No duplicates found');

            return false;
        }
        // can be a lot... do not delete all at once
        foreach ($duplicated as $duplicate) {
            $io->verbose(sprintf(':: Delete duplicated history ID %d', $duplicate->id));
            $this->History->delete($duplicate);
        }
        $io->verbose(
            sprintf(
                'Fixed "duplicated" on resource ID [%d]: removed %d records',
                $objectId,
                count($duplicated)
            )
        );

        return true;
    }

    /**
     * Compare two history records
     *
     * @param \BEdita\Core\Model\Entity\History $history1 History entity
     * @param \BEdita\Core\Model\Entity\History $history2 History entity
     * @return bool
     */
    protected function compare($history1, $history2)
    {
        return $this->serialize($history1) === $this->serialize($history2);
    }

    /**
     * Serialize history entity
     *
     * @param \BEdita\Core\Model\Entity\History $history History entity
     * @return string
     */
    protected function serialize($history): string
    {
        return $history->user_action . '-' . json_encode($history->changed);
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
     * Objects generator.
     *
     * @param \Cake\ORM\Query $query Query object
     * @return \Generator
     */
    protected function objectsGenerator(Query $query): Generator
    {
        $pageSize = self::PAGE_SIZE;
        $pages = ceil($query->count() / $pageSize);

        for ($page = 1; $page <= $pages; $page++) {
            yield from $query
                ->page($page, $pageSize)
                ->toArray();
        }
    }
}
