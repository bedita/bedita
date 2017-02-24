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
     * @param string $name application's name
     * @param string $description application's description
     * @return void
     */
    public function create($name, $description = null)
    {
        $entity = TableRegistry::get($this->modelClass)->newEntity();
        $entity->name = $name;
        $entity->description = $description;
        parent::processCreate($entity);
    }

    /**
     * enable an existing application
     *
     * @param mixed $id application name|id
     * @return void
     */
    public function enable($id)
    {
        $this->modifyApplication($id, 'enabled', true);
    }

    /**
     * disable an existing application
     *
     * @param mixed $id application name|id
     * @return void
     */
    public function disable($id)
    {
        $this->modifyApplication($id, 'enabled', false);
    }

    /**
     * renew API token for an existing application
     *
     * @param mixed $id application name|id
     * @return void
     */
    public function renewToken($id)
    {
        $this->modifyApplication($id, 'api_key', true);
    }

    /**
     * remove an existing application
     *
     * @param mixed $id application name|id
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

    /**
     * modify an existing application, by id, setting value $value for field $field
     *
     * @param mixed $id application name|id
     * @param string $field entity field
     * @param mixed $value value for field
     * @return void
     */
    private function modifyApplication($id, $field, $value)
    {
        $model = TableRegistry::get($this->modelClass);
        if (!is_numeric($id)) {
            $id = TableRegistry::get($this->modelClass)
                ->find()
                ->where(['name' => $id])
                ->firstOrFail()
                ->id;
        }
        $entity = $model->get($id);
        $operation = 'modified';
        if ($field === 'enabled') {
            $entity->enabled = $value;
            $operation = ($entity->enabled) ? 'enabled' : 'disabled';
        } else if ($field === 'api_key' && $value) {
            $entity->api_key = $model->generateApiKey();
        }
        $result = $model->save($entity);
        $this->out('Record ' . $id . ' ' . $operation);
    }
}
