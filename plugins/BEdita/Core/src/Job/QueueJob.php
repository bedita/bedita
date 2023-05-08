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
 * @property \BEdita\Core\Model\Table\AsyncJobsTable $AsyncJobs
 */
#[\AllowDynamicProperties]
class QueueJob implements JobInterface
{
    use LocatorAwareTrait;
    use LogTrait;

    /**
     * @inheritDoc
     */
    public function execute(Message $message): ?string
    {
        $this->AsyncJobs = $this->fetchTable('AsyncJobs');
        $uuid = $message->getArgument('uuid');
        $this->log(sprintf('Processing job "%s"', $uuid), 'debug');
        $success = $this->run($uuid);

        return $success ? Processor::ACK : Processor::REJECT;
    }

    /**
     * Process a single pending job.
     *
     * @param string $uuid Job UUID.
     * @return bool
     */
    protected function run($uuid): bool
    {
        try {
            $asyncJob = $this->AsyncJobs->lock($uuid);
        } catch (RecordNotFoundException $e) {
            $this->log(sprintf('Could not obtain lock on job "%s"', $uuid), 'warning');

            return false;
        }

        $success = false;
        try {
            $success = $asyncJob->run();
        } catch (\Exception $e) {
            $this->AsyncJobs->updateResults($asyncJob, $success, $e->getMessage());
            $this->log(sprintf('Error running job "%s" - %s', $uuid, $e->getMessage()), 'error');
        } finally {
            $result = $success ? 'completed successfully' : 'failed';
            $message = sprintf('Job "%s" [%s] %s', $asyncJob->uuid, $asyncJob->service, $result);
            $this->AsyncJobs->updateResults($asyncJob, $success, $message);
            $this->log($message, 'debug');
            $this->AsyncJobs->unlock($asyncJob->uuid, $success);
        }

        return $success;
    }
}
