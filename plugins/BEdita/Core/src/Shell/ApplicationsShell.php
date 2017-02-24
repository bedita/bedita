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

use Cake\Console\Shell;
use Cake\ORM\TableRegistry;

/**
 * Application shell commands:
 *
 * - create: add a new application
 * - ls: list existing applications
 * - enable: set enabled an existing application
 * - disable: set disabled an existing application
 * - renew_token: renew API token for an existing application
 * - rm: remove an existing application
 *
 * @since 4.0.0
 */
class ApplicationsShell extends ResourcesShell
{

    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Applications';

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addSubcommand('create', [
            'help' => 'create a new application',
            'parser' => [
                'description' => [
                    'Create a new application.',
                    'First argument (required) indicates application\'s name.',
                    'Second argument (optional) indicates application\'s description.'
                ],
                'arguments' => [
                    'name' => ['help' => 'Application name', 'required' => true],
                    'description' => ['help' => 'Application description', 'required' => false]
                ]
            ]
        ]);
        $parser->addSubcommand('enable', [
            'help' => 'enable an existing application',
            'parser' => [
                'description' => [
                    'Enable applications.',
                    'First argument (required) indicates application\'s id|name.'
                ],
                'arguments' => [
                    'name|id' => ['help' => 'Applications name|id', 'required' => true]
                ]
            ]
        ]);
        $parser->addSubcommand('disable', [
            'help' => 'disable an existing application',
            'parser' => [
                'description' => [
                    'Disable applications.',
                    'First argument (required) indicates application\'s id|name.'
                ],
                'arguments' => [
                    'name|id' => ['help' => 'Applications name|id', 'required' => true]
                ]
            ]
        ]);
        $parser->addSubcommand('renew_token', [
            'help' => 'renew API token for an existing application',
            'parser' => [
                'description' => [
                    'Get a new API token for an application.',
                    'First argument (required) indicates application\'s id|name.'
                ],
                'arguments' => [
                    'name|id' => ['help' => 'Applications name|id', 'required' => true]
                ]
            ]
        ]);

        return $parser;
    }

    /**
     * create a new application
     *
     * @return void
     */
    public function create()
    {
        $this->out('usage: bin/cake applications create <name> [<description>]');
        $this->out('... coming soon');
    }

    /**
     * enable an existing application
     *
     * @return void
     */
    public function enable()
    {
        $this->out('usage: bin/cake applications enable <name|id>');
        $this->out('... coming soon');
    }

    /**
     * disable an existing application
     *
     * @return void
     */
    public function disable()
    {
        $this->out('usage: bin/cake applications disable <name|id>');
        $this->out('... coming soon');
    }

    /**
     * renew API token for an existing application
     *
     * @return void
     */
    public function renewToken()
    {
        $this->out('usage: bin/cake applications renew_token <name|id>');
        $this->out('... coming soon');
    }

    /**
     * remove an existing application
     *
     * @param int $id
     * @return void
     */
    public function rm($id)
    {
        if (!is_numeric($id)) {
            $id = TableRegistry::get($this->modelClass)
                ->find()
                ->where(['name' => $id])
                ->firstOrFail()
                ->id;
        }
        parent::rm($id);
    }
}
