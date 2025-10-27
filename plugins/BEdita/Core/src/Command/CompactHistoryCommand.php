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
use BEdita\Core\Model\Table\HistoryTable;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\ORM\Query;
use Generator;

/**
 * CompactHistory command: remove duplicates.
 *
 * @since 5.40.0
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
     * @var \BEdita\Core\Model\Table\HistoryTable
     */
    public HistoryTable $History;

    /**
     * Dry run mode flag
     *
     * @var bool
     */
    protected bool $dryrun = false;

    /**
     * Application ID
     *
     * @var int
     */
    public int $appId;

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
    protected int $minId;

    /**
     * Max object ID to scan
     *
     * @var int
     */
    protected int $maxId;

    /**
     * Max number of versions to keep for each object
     *
     * @var int
     */
    protected int $versions;

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
            ])
            ->addOption('versions', [
                'help' => 'Max number of versions to keep for each object',
                'short' => 'k',
                'required' => false,
            ])
            ->addOption('dryrun', [
                'help' => 'dry run mode',
                'required' => false,
                'short' => 'd',
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
        if ($args->getOption('dryrun')) {
            $this->dryrun = true;
        }
        $io->out(sprintf('Dry run mode: %s', $this->dryrun === true ? 'yes' : 'no'));
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
        $vstr = '';
        if ($args->getOption('versions')) {
            $this->versions = intval($args->getOption('versions'));
            $vstr = sprintf(' (keep last %d versions)', $this->versions);
        }
        $io->info(sprintf('Min ID: %d - Max ID: %d%s', $this->minId, $this->maxId, $vstr));
        $count = $processed = 0;
        $current = $this->minId;
        while ($current <= $this->maxId) {
            $io->verbose(sprintf('Process ID %d', $current));
            if ($this->compactHistory($current, $io)) {
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
     * @param \Cake\Console\ConsoleIo $io Console IO
     * @return bool
     */
    protected function compactHistory(int $objectId, ConsoleIo $io): bool
    {
        if (!empty($this->versions)) {
            $io->verbose(sprintf(':: Keep last %d versions', $this->versions));
            $items = $this->History
                ->find()
                ->where([
                    $this->History->aliasField('resource_id') => $objectId,
                    $this->History->aliasField('resource_type') => 'objects',
                ])
                ->orderDesc($this->History->aliasField('created'))
                ->toArray();
            if (count($items) > $this->versions) {
                $toDelete = array_slice($items, $this->versions);
                foreach ($toDelete as $item) {
                    $io->verbose(sprintf(':: Delete history ID %d', $item->id));
                    if (!$this->dryrun) {
                        $this->History->delete($item);
                    }
                }
                $io->verbose(
                    sprintf(
                        'Fixed "versions" on resource ID [%d]: removed %d records',
                        $objectId,
                        count($toDelete),
                    ),
                );

                return true;
            } else {
                $io->verbose(sprintf(':: No versions to delete for resource ID %d', $objectId));
            }
        }
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
            $io->verbose(sprintf(':[%d] Resource ID %d', $processed, $objectId));
            $this->processHistory($prev, $current, $duplicated, $stack, $io);
            $io->verbose(sprintf(':[%d] Resource ID %d, history ID %d: duplicated %d', $processed, $objectId, $current->id, count($duplicated)));
            $prev = $current;
        }
        if (empty($duplicated)) {
            $io->verbose(':: No duplicates found');

            return false;
        }
        if ($this->dryrun === true) {
            $io->verbose(':: Dry run mode: do not delete duplicated history records');

            return true;
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
                count($duplicated),
            ),
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
    protected function compare(History $history1, History $history2): bool
    {
        $h1 = $history1->user_action . '-' . json_encode($history1->changed);
        $h2 = $history2->user_action . '-' . json_encode($history2->changed);

        return $h1 === $h2;
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

    /**
     * Process history records
     *
     * @param \BEdita\Core\Model\Entity\History $prev Previous history record
     * @param \BEdita\Core\Model\Entity\History $current Current history record
     * @param array $duplicated Duplicated history records
     * @param array $stack History records stack
     * @param \Cake\Console\ConsoleIo $io Console IO
     * @return void
     */
    protected function processHistory(History $prev, History $current, array &$duplicated, array &$stack, ConsoleIo $io): void
    {
        switch (count($stack)) {
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
        foreach ($stack as $i => $h) {
            $io->verbose(sprintf(':: History ID %d: %s', $h->id, $h->user_action . '-' . json_encode($h->changed)));
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
    }
}
