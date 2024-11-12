<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Command;

use BEdita\Core\Model\Table\ApplicationsTable;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Locator\LocatorAwareTrait;

/**
 * CheckApiKey command.
 */
class CheckApiKeyCommand extends Command
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
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->setName('cake check_api_key');
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser;
    }

    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return 'Check API key';
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $this->args = $args;
        $this->io = $io;
        $this->table = $this->fetchTable('Applications');
        try {
            $this->io->verbose('=====> Loading default application... ', 0);
            $application = $this->table->get(ApplicationsTable::DEFAULT_APPLICATION);
            $this->io->verbose('<info>DONE</info>');
        } catch (RecordNotFoundException $e) {
            $this->io->verbose('<error>FAIL</error>');
            $this->io->abort('Default application is missing, please check your installation');
        }
        if (empty($application->api_key)) {
            $this->io->out('=====> <warning>Default application has no API key</warning>');

            return static::CODE_SUCCESS;
        }
        $this->io->out(sprintf('=====> Default API key is: <info>%s</info>', $application->api_key));
        $this->io->out('=====> <success>API key is ok. You can now make your requests even more handsome with it!</success>');

        return static::CODE_SUCCESS;
    }
}
