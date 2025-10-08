<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Shell;

use Cake\Cache\Cache;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Locator\LocatorInterface;

/**
 * Database related shell commands like:
 *  - initialize a new database instance
 *  - check schema consistency
 *  - create schema files
 *
 * @since 4.0.0
 * @deprecated version 5.35.0 Use `BEdita\Core\Command\DbAdminCommand` instead
 * @property \BEdita\Core\Shell\Task\InitSchemaTask $Init
 * @property \BEdita\Core\Shell\Task\CheckSchemaTask $CheckSchema
 */
class DbAdminShell extends Shell /* @phpstan-ignore-line */
{
    /**
     * @inheritDoc
     */
    public $tasks = [
        'Init' => ['class' => 'BEdita/Core.InitSchema', 'config' => []],
        'CheckSchema' => ['class' => 'BEdita/Core.CheckSchema', 'config' => []],
    ];

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function startup(): void
    {
        Cache::disable();

        parent::startup();
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _welcome(): void
    {
        parent::_welcome();

        if ($this->param('connection')) {
            $info = ConnectionManager::get($this->param('connection'))->config();
            $info['vendor'] = explode('\\', $info['driver']);
            $info['vendor'] = strtolower(end($info['vendor']));

            if (isset($info['host'])) {
                $this->out('<info>Host</info>    : ' . $info['host']);
            }
            $this->out('<info>Database</info>: ' . $info['database']);
            $this->out('<info>Vendor</info>  : ' . $info['vendor']);
            $this->hr();
        }
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct(?ConsoleIo $io = null, ?LocatorInterface $locator = null)
    {
        deprecationWarning('"DbAdminShell" should not be used. Use `BEdita\Core\Command\DbAdminCommand` instead.');
        parent::__construct($io, $locator);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser(): ConsoleOptionParser
    {
        $parser = parent::getOptionParser();
        $parser
            ->addSubcommand('init', [
                'help' => 'Initialize a new BEdita 4 database instance.',
                'parser' => $this->Init->getOptionParser(),
            ])
            ->addSubcommand('check_schema', [
                'help' => 'Check SQL naming conventions and schema differences between current database and dump file.',
                'parser' => $this->CheckSchema->getOptionParser(),
            ]);

        return $parser;
    }
}
