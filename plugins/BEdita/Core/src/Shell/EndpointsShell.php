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

/**
 * Endpoint shell commands:
 *
 * - create: add a new endpoint
 * - ls: list existing endpoints
 * - enable: set enabled an existing endpoint
 * - disable: set disabled an existing endpoint
 * - rm: remove an existing endpoint
 *
 * @since 4.0.0
 */
class EndpointsShell extends Shell
{

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addSubcommand('create', [
            'help' => 'create a new endpoint',
            'parser' => [
                'description' => [
                    'Create a new endpoint.',
                    'First argument (required) indicates endpoint\'s name.',
                    'Second argument (optional) indicates endpoint\'s description.'
                ],
                'arguments' => [
                    'name' => ['help' => 'Endpoint name', 'required' => true],
                    'description' => ['help' => 'Endpoint description', 'required' => false]
                ],
                'options' => [
                    'object-type' => ['help' => 'Object type name|id', 'required' => false]
                ]
            ]
        ]);
        $parser->addSubcommand('ls', [
            'help' => 'list existing endpoints',
            'parser' => [
                'description' => [
                    'List endpoints.',
                    'Option --enabled (optional) forces only enabled endpoints listing.'
                ],
                'options' => [
                    'enabled' => ['help' => 'List enabled endpoints', 'required' => false]
                ]
            ]
        ]);
        $parser->addSubcommand('enable', [
            'help' => 'enable an existing endpoint',
            'parser' => [
                'description' => [
                    'Enable endpoints.',
                    'First argument (required) indicates endpoint\'s id|name.'
                ],
                'arguments' => [
                    'name|id' => ['help' => 'Endpoints name|id', 'required' => true]
                ]
            ]
        ]);
        $parser->addSubcommand('disable', [
            'help' => 'disable an existing endpoint',
            'parser' => [
                'description' => [
                    'Disable endpoints.',
                    'First argument (required) indicates endpoint\'s id|name.'
                ],
                'arguments' => [
                    'name|id' => ['help' => 'Endpoints name|id', 'required' => true]
                ]
            ]
        ]);
        $parser->addSubcommand('rm', [
            'help' => 'remove an existing endpoint',
            'parser' => [
                'description' => [
                    'Remove an endpoint.',
                    'First argument (required) indicates endpoint\'s id|name.'
                ],
                'arguments' => [
                    'name|id' => ['help' => 'Endpoints name|id', 'required' => true]
                ]
            ]
        ]);

        return $parser;
    }

    /**
     * create a new endpoint
     *
     * @return void
     */
    public function create()
    {
        $this->out('usage: bin/cake endpoints create <name> [<description>] [--object-type=<name|id>]');
        $this->out('... coming soon');
    }

    /**
     * list existing endpoints
     *
     * @return void
     */
    public function ls()
    {
        $this->out('usage: bin/cake endpoints ls [--enabled]');
        $this->out('... coming soon');
    }

    /**
     * enable an existing endpoint
     *
     * @return void
     */
    public function enable()
    {
        $this->out('usage: bin/cake endpoints enable <name|id>');
        $this->out('... coming soon');
    }

    /**
     * disable an existing endpoint
     *
     * @return void
     */
    public function disable()
    {
        $this->out('usage: bin/cake endpoints disable <name|id>');
        $this->out('... coming soon');
    }

    /**
     * remove an existing endpoint
     *
     * @return void
     */
    public function rm()
    {
        $this->out('usage: bin/cake endpoints rm <name|id>');
        $this->out('... coming soon');
    }
}
