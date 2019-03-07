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
}
