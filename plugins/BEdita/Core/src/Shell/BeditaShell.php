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

use BEdita\Core\Shell\Task\CheckApiKeyTask;
use BEdita\Core\Shell\Task\CheckFilesystemTask;
use BEdita\Core\Shell\Task\CheckSchemaTask;
use BEdita\Core\Shell\Task\InitSchemaTask;
use BEdita\Core\Shell\Task\SetupAdminUserTask;
use BEdita\Core\Shell\Task\SetupConnectionTask;
use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;

/**
 * Shell to manage instance at a system level
 *
 * Basic shell commands:
 *  - setup a new instance
 *  - check instance
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Shell\Task\CheckApiKeyTask $CheckApiKey
 * @property \BEdita\Core\Shell\Task\CheckFilesystemTask $CheckFilesystem
 * @property \BEdita\Core\Shell\Task\CheckSchemaTask $CheckSchema
 * @property \BEdita\Core\Shell\Task\InitSchemaTask $InitSchema
 * @property \BEdita\Core\Shell\Task\SetupAdminUserTask $SetupAdminUser
 * @property \BEdita\Core\Shell\Task\SetupConnectionTask $SetupConnection
 */
class BeditaShell extends Shell
{

    /**
     * {@inheritDoc}
     */
    public $tasks = [
        'CheckApiKey' => ['className' => CheckApiKeyTask::class],
        'CheckFilesystem' => ['className' => CheckFilesystemTask::class],
        'CheckSchema' => ['className' => CheckSchemaTask::class],
        'InitSchema' => ['className' => InitSchemaTask::class],
        'SetupAdminUser' => ['className' => SetupAdminUserTask::class],
        'SetupConnection' => ['className' => SetupConnectionTask::class],
    ];

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $parser->addSubcommand('setup', [
            'help' => 'Setup new instance.',
            'parser' => [
                'description' => 'Use this interactive shell command to setup a new instance.',
                'options' => array_merge(
                    $this->SetupConnection->getOptionParser()->options(),
                    $this->InitSchema->getOptionParser()->options(),
                    $this->CheckSchema->getOptionParser()->options(),
                    $this->CheckFilesystem->getOptionParser()->options(),
                    $this->SetupAdminUser->getOptionParser()->options(),
                    $this->CheckApiKey->getOptionParser()->options()
                ),
            ],
        ]);

        $parser->addSubcommand('check', [
            'help' => 'Check current setup.',
            'parser' => [
                'description' => 'Use this shell command to check current instance configuration/status.',
                'options' => array_merge(
                    $this->CheckSchema->getOptionParser()->options(),
                    $this->CheckFilesystem->getOptionParser()->options(),
                    $this->CheckApiKey->getOptionParser()->options()
                ),
            ],
        ]);

        return $parser;
    }

    /**
     * Initial set up for a BEdita instance.
     *
     * @return void
     */
    public function setup()
    {
        $this->out('=====> Checking connection');
        $this->SetupConnection->params = $this->params;
        $this->SetupConnection->main();

        $this->hr();

        $tables = ConnectionManager::get($this->param('connection'))->schemaCollection()->listTables();
        if (empty($tables)) {
            $this->out('=====> Initializing schema');
            $this->InitSchema->params = ['seed' => true] + $this->params;
            $this->InitSchema->main();
        } else {
            $this->out('=====> Checking schema');
            $this->CheckSchema->params = $this->params;
            $this->CheckSchema->main();
        }

        $this->hr();

        $this->out('=====> Checking filesystem permissions');
        $this->CheckFilesystem->params = $this->params;
        $this->CheckFilesystem->main();

        $this->hr();

        if ($this->param('connection') !== 'default') {
            ConnectionManager::alias($this->param('connection'), 'default');
        }
        try {
            $this->out('=====> Configuring default administrator user');
            $this->SetupAdminUser->params = $this->params;
            $this->SetupAdminUser->main();

            $this->hr();

            $this->out('=====> Checking API key');
            $this->CheckApiKey->params = $this->params;
            $this->CheckApiKey->main();
        } finally {
            ConnectionManager::dropAlias('default');
        }
    }

    /**
     * Check bedita instance.
     *
     * @return void
     */
    public function check()
    {
        $this->out('=====> Checking schema');
        $this->CheckSchema->params = $this->params;
        $this->CheckSchema->main();

        $this->hr();

        $this->out('=====> Checking filesystem permissions');
        $this->CheckFilesystem->params = $this->params;
        $this->CheckFilesystem->main();
    }
}
