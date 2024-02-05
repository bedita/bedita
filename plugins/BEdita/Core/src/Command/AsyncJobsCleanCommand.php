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
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $io->info('Cleaning async jobs older than 1 month');
        $deleted = $this->fetchTable('AsyncJobs')->deleteAll(['created <' => new FrozenDate('-1 month')]);
        $io->success(sprintf('Deleted %d async jobs', $deleted));
        $io->info('Done');

        return self::CODE_SUCCESS;
    }
}
