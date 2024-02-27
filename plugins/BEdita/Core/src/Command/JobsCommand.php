<?php
declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2024 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Hash;

/**
 * Jobs command.
 */
class JobsCommand extends Command
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
                'help' => 'Action to perform: process, pending, run',
            ])
            ->addArgument('uuid', [
                'help' => 'UUID of job to be processed',
                'required' => false,
            ])
            ->addOption('exit-on-error', [
                'help' => 'Enable "fail hard" mode',
                'short' => 'F',
                'boolean' => true,
            ])
            ->addOption('limit', [
                'help' => 'Limit number of jobs being run',
                'short' => 'l',
                'required' => false,
                'default' => null,
            ])
            ->addOption('min-priority', [
                'help' => 'Run only pending jobs with a priority higher than this value',
                'short' => 'p',
                'required' => false,
                'default' => null,
            ])
            ->addOption('service', [
                'help' => 'Run only pending jobs for the supplied service',
                'short' => 's',
                'required' => false,
                'default' => null,
            ]);
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $this->table = $this->fetchTable('AsyncJobs');
        $this->args = $args;
        $this->io = $io;
        $action = $this->args->getArgument('action');
        // this is to make it retrocompatible: bin/cake jobs run <uuid>
        if (in_array($action, ['process', 'run'])) {
            return $this->process($this->args->getArgument('uuid'));
        }

        return $this->{$action}();
    }

    /**
     * Process a single pending job.
     *
     * @param string $uuid Job UUID.
     * @return int
     */
    public function process(string $uuid): int
    {
        try {
            $this->io->verbose(sprintf('=====> Locking job "<info>%s</info>"...', $uuid));
            $asyncJob = $this->table->lock($uuid);
        } catch (RecordNotFoundException $e) {
            $this->io->out(sprintf('=====> <warning>Could not obtain lock on job "%s".</warning>', $uuid));

            return self::CODE_ERROR;
        }

        $this->io->out(sprintf('=====> Processing job "<info>%s</info>" [%s]...', $asyncJob->uuid, $asyncJob->service));
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
            $this->io->err(sprintf('=====> %s with message "%s"', get_class($e), $e->getMessage()));
        } finally {
            if ($success === false) {
                $message = sprintf('Job "%s" [%s] failed', $asyncJob->uuid, $asyncJob->service);
                $messages[] = $message;
                $this->log($message);
                $this->io->err(sprintf('=====> "%s"', $message));
            } else {
                $message = sprintf('Job "%s" [%s] completed successfully', $asyncJob->uuid, $asyncJob->service);
                $messages[] = $message;
                $this->io->out(sprintf('=====> <success>%s</success>', $message));
            }
            $this->table->updateResults($asyncJob, $success, $messages);

            $this->io->verbose(sprintf('=====> Unlocking job "<info>%s</info>"...', $asyncJob->uuid));
            $this->table->unlock($asyncJob->uuid, $success);
        }
        if ($success === false && $this->args->getOption('exit-on-error')) {
            $this->io->abort('=====> Aborting');
        }

        return self::CODE_SUCCESS;
    }

    /**
     * Run pending jobs, optionally filtered by status or priority.
     *
     * @return int
     */
    public function pending(): int
    {
        $this->io->out('=====> <info>Finding pending jobs...</info>');
        $query = $this->table
            ->find('list', ['valueField' => $this->table->getPrimaryKey()])
            ->find('priority', [
                'priority' => $this->args->getOption('min-priority'),
                'service' => $this->args->getOption('service'),
            ])
            ->enableBufferedResults(false);
        if ($this->args->getOption('limit') !== null) {
            $query = $query->limit($this->args->getOption('limit'));
        }
        if ($query->all()->isEmpty()) {
            $this->io->out('=====> <info>Nothing to do</info>');

            return self::CODE_SUCCESS;
        }
        $query->all()->each([$this, 'process']);
        $this->io->out('=====> <success>Operation complete</success>');

        return self::CODE_SUCCESS;
    }
}
