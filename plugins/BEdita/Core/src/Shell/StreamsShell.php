<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Shell;

use BEdita\Core\Model\Entity\Stream;
use Cake\Console\Shell;
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;

/**
 * Stream shell commands: removeOrphans
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\StreamsTable $Streams
 */
class StreamsShell extends Shell
{
    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Streams';

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addSubcommand('removeOrphans', [
            'help' => 'remove obsolete/orphans streams and related files',
            'parser' => [
                'description' => [
                    'Remove orphans streams.'
                ],
                'options' => [
                    'days' => [
                        'help' => 'Days to consider for stream research for orphans (remove data older than specified days)',
                        'required' => false,
                        'default' => 1,
                    ],
                ],
            ]
        ]);
        $parser->addSubcommand('refreshMetadata', [
            'help' => 'read streams metadata from file and update database information',
            'parser' => [
                'description' => [
                    'Refresh streams metadata in database.',
                ],
                'options' => [
                    'force' => [
                        'help' => 'Force refreshing all streams, not only those with empty metadata',
                        'required' => false,
                        'default' => false,
                        'boolean' => true,
                    ],
                ],
            ],
        ]);

        return $parser;
    }

    /**
     * Remove orphans older than specified days (default: older than 1 day)
     *
     * @return void
     */
    public function removeOrphans()
    {
        $days = (int)$this->param('days');
        $query = $this->Streams->find()
            ->where([
                'object_id IS NULL',
                'created <' => \Cake\I18n\Time::now()->subDays($days),
            ]);
        $count = 0;
        foreach ($query as $stream) {
            $this->verbose(sprintf('Deleting stream %s...', $stream->id));
            $this->Streams->deleteOrFail($stream);
            $count++;
        }
        $this->out(sprintf('%d stream(s) deleted', $count));
    }

    /**
     * Re-read streams metadata from file and update information in database.
     *
     * @return void
     */
    public function refreshMetadata()
    {
        $query = $this->Streams->find('all');
        if ((bool)$this->param('force') === false) {
            $query = $query->where(function (QueryExpression $exp): QueryExpression {
                return $exp->or_(function (QueryExpression $exp): QueryExpression {
                    return $exp
                        ->isNull($this->Streams->aliasField('file_size'))
                        ->isNull($this->Streams->aliasField('width'))
                        ->isNull($this->Streams->aliasField('height'));
                });
            });
        }

        $count = $query->count();
        $this->info(sprintf('Approximately %d streams to be processed', $count));
        $success = 0;

        foreach ($this->streamsGenerator($query) as $stream) {
            if ($this->updateStreamMetadata($stream)) {
                $success++;
            }
        }

        $this->info(sprintf('Refresh completed: %d streams updated successfully, %d failed', $success, ($count - $success)));
    }

    /**
     * Update stream metadata.
     *
     * @param Stream $stream The stream to update
     * @return bool Success status of the operation
     */
    protected function updateStreamMetadata(Stream $stream): bool
    {
        try {
            // Read current file's content...
            $content = $stream->contents;
            if ($content === null) {
                $this->warn(sprintf('  stream %s (object %d) is empty or could not be read', $stream->uuid, $stream->object_id));

                return false;
            }

            // ...and write it back, triggering Stream model's methods to read metadata from file
            $stream->contents = $content;
            if (!$this->Streams->save($stream)) {
                $this->err(sprintf('  error updating stream %s (object %d): %s', $stream->uuid, $stream->object_id, print_r($stream->getErrors(), true)));

                return false;
            }
        } catch (\Throwable $t) {
            $this->err(sprintf('  error updating stream %s (object %d): %s', $stream->uuid, $stream->object_id, $t->getMessage()));

            return false;
        }

        return true;
    }

    /**
     * Generator to paginate through all streams.
     *
     * @param \Cake\ORM\Query $query Query to retrieve concerned streams
     * @param int $limit Limit amount of objects retrieved with each internal iteration
     * @return \Generator|Stream[]
     */
    protected function streamsGenerator(Query $query, int $limit = 100): \Generator
    {
        // Although `uuid` is not a monotonically increasing field, we will at most skip the streams that are created
        // AFTER we launch the script, and whose UUID is lexicographically less than the one we are currently
        // checking — but we still cover all streams created before our script starts!
        $query = $query->orderAsc($this->Streams->aliasField('uuid'));
        $q = clone $query;
        do {
            $results = $q->limit($limit)->all();
            if ($results->isEmpty()) {
                break;
            }

            yield from $results;

            /** @var Stream $last */
            $last = $results->last();
            $q = clone $query;
            $q = $q->where(function (QueryExpression $exp) use ($last): QueryExpression {
                return $exp->gt($this->Streams->aliasField('uuid'), $last->uuid);
            });
        } while ($q->count() > 0);
    }
}
