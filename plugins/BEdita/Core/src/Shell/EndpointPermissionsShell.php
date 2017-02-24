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
                    'First argument (required) indicates endpoint permission\'s read permission.',
                    'Second argument (required) indicates endpoint permission\'s write permission.'
                ],
                'arguments' => [
                    'read' => ['help' => 'Read permission', 'required' => true, 'choices' => ['true', 'false', 'block', 'mine']],
                    'write' => ['help' => 'Write permission', 'required' => true, 'choices' => ['true', 'false', 'mine', 'block']], // @TODO: invert order, when arguments-with-same-choices bug will be solved (ConsoleInputArgument::usage())
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
     * @param string $read permission: can be 'true', 'false', 'block', 'mine'
     * @param string $write permission: can be 'true', 'false', 'block', 'mine'
     * @return void
     */
    public function create($read, $write)
    {
        $entity = TableRegistry::get($this->modelClass)->newEntity();
        $entity->read = $read;
        $entity->write = $write;
        if (!empty($this->params['application'])) {
            $tmpId = $this->params['application'];
            if (!is_numeric($tmpId)) {
                $tmpId = TableRegistry::get('Applications')
                    ->find()
                    ->where(['name' => $tmpId])
                    ->firstOrFail()
                    ->id;
            }
            $entity->application_id = $tmpId;
        }
        if (!empty($this->params['endpoint'])) {
            $tmpId = $this->params['endpoint'];
            if (!is_numeric($tmpId)) {
                $tmpId = TableRegistry::get('Endpoints')
                    ->find()
                    ->where(['name' => $tmpId])
                    ->firstOrFail()
                    ->id;
            }
            $entity->endpoint_id = $tmpId;
        }
        if (!empty($this->params['role'])) {
            $tmpId = $this->params['role'];
            if (!is_numeric($tmpId)) {
                $tmpId = TableRegistry::get('Roles')
                    ->find()
                    ->where(['name' => $tmpId])
                    ->firstOrFail()
                    ->id;
            }
            $entity->role_id = $tmpId;
        }
        parent::processCreate($entity);
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
