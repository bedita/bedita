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
 * Endpoint permissions shell commands:
 *
 * - create: add a new endpoint
 * - ls: list existing endpoints
 * - rm: remove an existing endpoint
 *
 * @since 4.0.0
 */
class EndpointPermissionsShell extends ResourcesShell
{

    /**
     * {@inheritDoc}
     */
    public $modelClass = 'EndpointPermissions';

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addSubcommand('create', [
            'help' => 'create a new endpoint permission',
            'parser' => [
                'description' => [
                    'Create a new endpoint permission.',
                    'First argument (required) indicates endpoint permission\'s read mask.',
                    'Second argument (optional) indicates endpoint permission\'s write mask.'
                ],
                'arguments' => [
                    'read' => ['help' => 'Read mask', 'required' => true],
                    'write' => ['help' => 'Write mask', 'required' => true]
                ],
                'options' => [
                    'application' => ['help' => 'Application name|id', 'required' => false],
                    'endpoint' => ['help' => 'Endpoint name|id', 'required' => false],
                    'role' => ['help' => 'Role name|id', 'required' => false]
                ]
            ]
        ]);

        return $parser;
    }

    /**
     * create a new endpoint permission
     *
     * @return void
     */
    public function create()
    {
        $this->out('usage: bin/cake endpoint_permissions create <read> <write> [--application=<name|id>] [--endpoint=<name|id>] [--role=<name|id>]');
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
