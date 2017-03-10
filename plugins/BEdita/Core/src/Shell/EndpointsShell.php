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
class EndpointsShell extends ResourcesShell
{

    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Endpoints';

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

        return $parser;
    }

    /**
     * create a new endpoint
     *
     * @param string $name endpoint's name
     * @param string $description endpoint's description
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
     * enable an existing endpoint
     *
     * @param mixed $id endpoint name|id
     * @return void
     */
    public function enable($id)
    {
        $this->modifyEndpoint($id, 'enabled', true);
    }

    /**
     * disable an existing endpoint
     *
     * @param mixed $id endpoint name|id
     * @return void
     */
    public function disable($id)
    {
        $this->modifyEndpoint($id, 'enabled', false);
    }

    /**
     * modify an existing endpoint, by id, setting value $value for field $field
     *
     * @param mixed $id endpoint name|id
     * @param string $field entity field
     * @param mixed $value value for field
     * @return void
     */
    private function modifyEndpoint($id, $field, $value)
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
        }
        $result = $model->save($entity);
        $this->out('Record ' . $id . ' ' . $operation);
    }
}
