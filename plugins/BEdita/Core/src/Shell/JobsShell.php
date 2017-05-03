<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Shell;

use BEdita\Core\Job\ServiceRunner;
use Cake\Cache\Cache;
use Cake\Console\Shell;

/**
 * Shell class to run pending jobs
 *
 * @since 4.0.0
 */
class JobsShell extends Shell
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser
            ->addSubcommand('run', [
                'help' => 'Run pending async jobs.',
                'parser' => [
                    'options' => [
                        'limit' => [
                            'help' => 'Max number of jobs to run',
                            'short' => 'l',
                            'required' => false,
                            'default' => 0,
                        ],
                    ],
                ],
            ]);

        return $parser;
    }

    /**
     * Run async pending jobs
     *
     * @return bool False on at least a job failure, true otherwise
     */
    public function run()
    {
        $limit = $this->param('limit');
        if (!$limit) {
            $this->out('Running all pending jobs...');
        } else {
            $this->out(sprintf('Running pending jobs, limit number: %s', $limit));
        }
        $result = ServiceRunner::runPending($limit);
        $this->out('<info>Jobs terminated</info>');
        $this->hr();
        $this->out(sprintf('Executed: %s', $result['count']));
        $this->out(sprintf('Success:  %s', count($result['success'])));
        $this->out(sprintf('Failure:  %s', count($result['failure'])));
        $this->hr();

        return empty($result['failure']);
    }
}
