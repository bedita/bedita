<?php
declare(strict_types=1);
/**
 * BEdita, API-first content management framework
 * Copyright 2024 Channelweb Srl, Chialab Srl
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
use Cake\I18n\FrozenDate;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * AsyncJobsClean command.
 */
class AsyncJobsCleanCommand extends Command
{
    use LocatorAwareTrait;

    /**
     * @inheritDoc
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->addOption('since', [
                'help' => 'Delete async jobs older than this date',
                'required' => false,
            ])
            ->addOption('service', [
                'help' => 'Delete async jobs for this service',
                'required' => false,
            ]);
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $since = $args->getOption('since') ?? '-1 month';
        $service = $args->getOption('service');
        $message = 'Cleaning async jobs, since ' . $since;
        $conditions = ['created <' => new FrozenDate($since)];
        if ($service) {
            $conditions['service'] = $service;
            $message .= ', for service ' . $service;
        }
        $io->info($message);
        $this->log($message, 'info');
        $deleted = $this->fetchTable('AsyncJobs')->deleteAll($conditions);
        $this->log(sprintf('Deleted %d async jobs', $deleted), 'info');
        $io->success(sprintf('Deleted %d async jobs', $deleted));
        $io->info('Done');

        return self::CODE_SUCCESS;
    }
}
