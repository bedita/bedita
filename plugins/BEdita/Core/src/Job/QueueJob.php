<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2022 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Job;

use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Log\LogTrait;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Queue\Job\JobInterface;
use Cake\Queue\Job\Message;
use Interop\Queue\Processor;

/**
 * Default Queue Job class consuming AsyncJobs
 *
 * @param \BEdita\Core\Model\Table\AsyncJobsTable $AsyncJobs
 */
class QueueJob implements JobInterface
{
    use LocatorAwareTrait;
    use LogTrait;

    /**
     * @inheritDoc
     */
    public function execute(Message $message): ?string
    {
        $uuid = $message->getArgument('uuid');
        $this->AsyncJobs = $this->fetchTable('AsyncJobs');
        $this->log(sprintf('%s', $uuid), 'debug');
        $this->run($uuid);

        return Processor::ACK;
    }

    /**
     * Process a single pending job.
     *
     * @param string $uuid Job UUID.
     * @return void
     */
    protected function run($uuid): void
    {
        try {
            $asyncJob = $this->AsyncJobs->lock($uuid);
        } catch (RecordNotFoundException $e) {
            $this->log(sprintf('Could not obtain lock on job "%s"', $uuid), 'warning');

            return;
        }

        try {
            $success = $asyncJob->run();
        } catch (\Exception $e) {
            $success = false;

            $this->log('Error running job - ' . $e->getMessage(), 'error');
        } finally {
            $this->AsyncJobs->unlock($asyncJob->uuid, $success);
        }
    }
}
