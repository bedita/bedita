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
use Cake\I18n\Date;
use Cake\Console\Shell;
use Cake\ORM\TableRegistry;

/**
 * Stream shell commands: removeOrphans
 *
 * @since 4.0.0
 */
class StreamsShell extends Shell
{

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
                        'help' => 'Days to consider for stream research for orphans',
                        'required' => false,
                        'default' => 1,
                    ],
                ],
            ]
        ]);

        return $parser;
    }

    /**
     * Create a new Stream
     *
     * @return void
     */
    public function removeOrphans()
    {
        $days = (int)$this->param('days');
        $streamsTable = TableRegistry::get('Streams');
        $query = $streamsTable
            ->find()
            ->where([
                'object_id IS NULL',
                'created >=' => new \DateTime(sprintf('-%d days', $days)),
            ]);
        $n = $query->count();
        if ($n > 0) {
            $query = $streamsTable->query();
            $query->delete()
                ->where([
                    'object_id IS NULL',
                    'created >=' => new \DateTime(sprintf('-%d days', $days)),
                ])
                ->execute();
        }
        $this->out(sprintf('%d stream(s) deleted', $n));
    }
}
