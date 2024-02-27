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

use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Utility\Hash;

/**
 * Shell class to run pending jobs
 *
 * @since 4.0.0
 * @property \BEdita\Core\Model\Table\AsyncJobsTable $AsyncJobs
 * @deprecated version 5.18.0 Use `BEdita/Core.Command/JobsCommand` instead
 */
class JobsShell extends Shell /* @phpstan-ignore-line */
{
    /**
     * @inheritDoc
     */
    public $modelClass = 'AsyncJobs';

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $options = [
            'exit-on-error' => [
                'help' => 'Enable "fail hard" mode',
                'short' => 'F',
                'boolean' => true,
            ],
        ];

        return parent::getOptionParser() /* @phpstan-ignore-line */
            ->addSubcommand('run', [
                'help' => 'Process a job',
                'parser' => [
                    'arguments' => [
                        'uuid' => [
                            'help' => 'UUID of job to be processed',
                            'required' => true,
                        ],
                    ],
                    'options' => $options,
                ],
            ])
            ->addSubcommand('pending', [
                'help' => 'Run pending async jobs.',
                'parser' => [
                    'options' => $options + [
                        'limit' => [
                            'help' => 'Limit number of jobs being run',
                            'short' => 'l',
                            'required' => false,
                            'default' => null,
                        ],
                        'min-priority' => [
                            'help' => 'Run only pending jobs with a priority higher than this value',
                            'short' => 'p',
                            'required' => false,
                            'default' => null,
                        ],
                        'service' => [
                            'help' => 'Run only pending jobs for the supplied service',
                            'short' => 's',
                            'required' => false,
                            'default' => null,
                        ],
                    ],
                ],
            ]);
    }

    /**
     * Process a single pending job.
     *
     * @param string $uuid Job UUID.
     * @return void
     */
    public function run($uuid)
    {
        try {
            $this->verbose(sprintf('=====> Locking job "<info>%s</info>"...', $uuid));
            $asyncJob = $this->AsyncJobs->lock($uuid);
        } catch (RecordNotFoundException $e) {
            $this->out(sprintf('=====> <warning>Could not obtain lock on job "%s".</warning>', $uuid));

            return;
        }

        $this->out(sprintf('=====> Processing job "<info>%s</info>" [%s]...', $asyncJob->uuid, $asyncJob->service));
        $success = false;
        $messages = [];
        try {
            $result = $asyncJob->run();
            $success = is_bool($result) ? $result : (bool)Hash::get((array)$result, 'success');
            $messages = is_array($result) ? (array)Hash::get($result, 'messages') : [];
        } catch (\Exception $e) {
            $success = false;
            $messages[] = $e->getMessage();
            $this->log($e->getMessage(), 'error');
            $this->err(sprintf('=====> %s with message "%s"', get_class($e), $e->getMessage()));
        } finally {
            if ($success === false) {
                $message = sprintf('Job "%s" [%s] failed', $asyncJob->uuid, $asyncJob->service);
                $messages[] = $message;
                $this->log($message);
                $this->err(sprintf('=====> "%s"', $message));
            } else {
                $message = sprintf('Job "%s" [%s] completed successfully', $asyncJob->uuid, $asyncJob->service);
                $messages[] = $message;
                $this->out(sprintf('=====> <success>%s</success>', $message));
            }
            $this->AsyncJobs->updateResults($asyncJob, $success, $messages);

            $this->verbose(sprintf('=====> Unlocking job "<info>%s</info>"...', $asyncJob->uuid));
            $this->AsyncJobs->unlock($asyncJob->uuid, $success);
        }

        if ($success === false && $this->param('exit-on-error')) {
            $this->abort('=====> Aborting');
        }
    }

    /**
     * Run pending jobs, optionally filtered by status or priority.
     *
     * @return void
     */
    public function pending()
    {
        $this->out('=====> <info>Finding pending jobs...</info>');
        $query = $this->AsyncJobs
            ->find('list', ['valueField' => $this->AsyncJobs->getPrimaryKey()])
            ->find('priority', [
                'priority' => $this->param('min-priority'),
                'service' => $this->param('service'),
            ])
            ->enableBufferedResults(false);
        if ($this->param('limit') !== null) {
            $query = $query->limit($this->param('limit'));
        }

        if ($query->all()->isEmpty()) {
            $this->out('=====> <info>Nothing to do</info>');

            return;
        }

        $query->all()->each([$this, 'run']);

        $this->out('=====> <success>Operation complete</success>');
    }
}
