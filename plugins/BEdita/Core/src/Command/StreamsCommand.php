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

use BEdita\Core\Model\Entity\Stream;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\ORM\Query;

/**
 * Streams command.
 */
class StreamsCommand extends Command
{
    use LocatorAwareTrait;

    /**
     * Console arguments
     *
     * @var \Cake\Console\Arguments
     */
    protected $args;

    /**
     * Console IO
     *
     * @var \Cake\Console\ConsoleIo
     */
    protected $io;

    /**
     * Async jobs table
     *
     * @var \BEdita\Core\Model\Table\AsyncJobsTable
     */
    protected $table;

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->addArgument('action', [
                'required' => true,
                'help' => 'Action to perform: removeOrphans, refreshMetadata',
                'choices' => ['removeOrphans', 'refreshMetadata'],
            ])
            ->addOption('days', [
                'help' => 'Days to consider for stream research for orphans (remove data older than specified days)',
                'required' => false,
                'default' => 1,
            ])
            ->addOption('force', [
                'help' => 'Force refreshing all streams, not only those with empty metadata',
                'required' => false,
                'default' => false,
                'boolean' => true,
            ]);
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        $this->table = $this->fetchTable('Streams');
        $this->args = $args;
        $this->io = $io;
        $action = $this->args->getArgument('action');
        $this->{$action}();

        return self::CODE_SUCCESS;
    }

    /**
     * Remove orphans older than specified days (default: older than 1 day)
     *
     * @return void
     */
    public function removeOrphans()
    {
        $days = (int)$this->args->getOption('days');
        $query = $this->table->find()
            ->where([
                'object_id IS NULL',
                'created <' => \Cake\I18n\FrozenTime::now()->subDays($days),
            ]);
        $count = 0;
        foreach ($query as $stream) {
            $this->io->verbose(sprintf('Deleting stream %s [file_name %s]', $stream->uuid, $stream->file_name));
            $this->table->deleteOrFail($stream);
            $count++;
        }
        $this->io->out(sprintf('%d stream(s) deleted', $count));
    }

    /**
     * Re-read streams metadata from file and update information in database.
     *
     * @return void
     */
    public function refreshMetadata()
    {
        $query = $this->table->find('all');
        if ((bool)$this->args->getOption('force') === false) {
            $query = $query->where(function (QueryExpression $exp): QueryExpression {
                return $exp->or(function (QueryExpression $exp): QueryExpression {
                    return $exp
                        ->eq($this->table->aliasField('file_size'), 0)
                        ->isNull($this->table->aliasField('width'))
                        ->isNull($this->table->aliasField('height'));
                });
            });
        }
        $count = $query->count();
        $this->io->info(sprintf('Approximately %d streams to be processed', $count));
        $success = 0;
        foreach ($this->streamsGenerator($query) as $stream) {
            if ($this->updateStreamMetadata($stream)) {
                $success++;
            }
        }
        $this->io->info(sprintf('Refresh completed: %d streams updated successfully, %d failed', $success, $count - $success));
    }

    /**
     * Update stream metadata.
     *
     * @param \BEdita\Core\Model\Entity\Stream $stream The stream to update
     * @return bool Success status of the operation
     */
    protected function updateStreamMetadata(Stream $stream): bool
    {
        try {
            // Read current file's content...
            $content = $stream->contents;
            if ($content === null) {
                $this->io->warning(sprintf('  stream %s (object %d) is empty or could not be read', $stream->uuid, $stream->object_id));

                return false;
            }
            // ...and write it back, triggering Stream model's methods to read metadata from file
            $stream->contents = $content;
            $this->table->saveOrFail($stream);
        } catch (\Throwable $t) {
            $this->io->error(sprintf('  error updating stream %s (object %d): %s', $stream->uuid, $stream->object_id, $t->getMessage()));

            return false;
        }

        return true;
    }

    /**
     * Generator to paginate through all streams.
     *
     * @param \Cake\ORM\Query $query Query to retrieve concerned streams
     * @param int $limit Limit amount of objects retrieved with each internal iteration
     * @return \Generator|\BEdita\Core\Model\Entity\Stream[]
     */
    protected function streamsGenerator(Query $query, int $limit = 100): \Generator
    {
        // Although `uuid` is not a monotonically increasing field, we will at most skip the streams that are created
        // AFTER we launch the script, and whose UUID is lexicographically less than the one we are currently
        // checking — but we still cover all streams created before our script starts!
        $query = $query->orderAsc($this->table->aliasField('uuid'));
        $q = clone $query;
        do {
            $results = $q->limit($limit)->all();
            if ($results->isEmpty()) {
                break;
            }
            yield from $results;
            /** @var \BEdita\Core\Model\Entity\Stream $last */
            $last = $results->last();
            $q = clone $query;
            $q = $q->where(function (QueryExpression $exp) use ($last): QueryExpression {
                return $exp->gt($this->table->aliasField('uuid'), $last->uuid);
            });
        } while ($q->count() > 0);
    }
}
