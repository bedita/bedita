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

use BEdita\Core\Model\Action\DeleteEntityAction;
use BEdita\Core\Model\Action\ListEntitiesAction;
use Cake\Console\Shell;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Utility\Inflector;

/**
 * Resource shell commands: list, create, remove, enable and disable common entities
 *
 * @since 4.0.0
 */
class ResourcesShell extends Shell
{

    /**
     * Accepted resource types
     *
     * @var array
     */
    protected $acceptedTypes = ['applications', 'roles', 'endpoints', 'endpoint_permissions'];

    /**
     * Editable resource fields
     *
     * @var array
     */
    protected $editableFields = ['api_key', 'description', 'enabled', 'name', 'unchangeable'];

    /**
     * Resource model table
     *
     * @var \Cake\ORM\Table
     */
    protected $modelTable = null;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        $options = [
            'type' => [
                'help' => 'Entity type (applications, roles, endpoints, endpoint_permissions)',
                'short' => 't',
                'required' => true,
                'default' => null,
                'choices' => $this->acceptedTypes,
            ],
        ];

        $arguments = [
            'name|id' => [
                'help' => 'Resource\'s name or id',
                'required' => true
            ],
        ];

        $parser->addSubcommand('add', [
            'help' => 'create a new entity',
            'parser' => [
                'description' => [
                    'Create a new resource.'
                ],
                'options' => $options,
            ]
        ]);
        $parser->addSubcommand('ls', [
            'help' => 'list entities',
            'parser' => [
                'description' => [
                    'List entities.',
                    'Option --filter (optional) provides listing filtered by comma separated key=value pairs.'
                ],
                'options' => $options + [
                    'filter' => [
                        'help' => 'List entities filtered by comma separated key=value pairs',
                        'required' => false
                    ],
                ],
            ]
        ]);
        $parser->addSubcommand('rm', [
            'help' => 'remove an entity',
            'parser' => [
                'description' => [
                    'Remove an entity.',
                    'First argument (required) indicates entity\'s id|name.'
                ],
                'arguments' => $arguments,
                'options' => $options,
            ]
        ]);
        $parser->addSubcommand('edit', [
            'help' => 'modify an entity field',
            'parser' => [
                'description' => [
                    'Modify a field on a single resource.',
                    'Required entity\'s id|name and field'
                ],
                'arguments' => $arguments,
                'options' => $options + [
                    'field' => [
                        'help' => 'Field name',
                        'short' => 'f',
                        'required' => true,
                        'choices' => $this->editableFields,
                    ],
                ],
            ]
        ]);

        return $parser;
    }

    /**
     * Init model table using --type|-t option
     *
     * @return void
     */
    protected function initModel()
    {
        $modelName = Inflector::camelize($this->param('type'));
        $this->modelTable = TableRegistry::get($modelName);
    }

    /**
     * Create a new resource
     *
     * @return \Cake\ORM\Entity $entity Entity created
     */
    public function add()
    {
        $this->initModel();
        $entity = $this->modelTable->newEntity();
        if ($this->param('type') === 'endpoint_permissions') {
            $this->setupEndpointPermissionEntity($entity);
        } else {
            $this->setupDefaultEntity($entity);
        }

        $this->modelTable->save($entity);
        $this->out('Resource with id ' . $entity->id . ' created');

        return $entity;
    }

    /**
     * Setup entity for endpoint_permissions
     *
     * @param \Cake\ORM\Entity $entity Entity to add
     * @return void
     */
    protected function setupEndpointPermissionEntity($entity)
    {
        $fieldsTables = [
            'application_id' => 'Applications',
            'endpoint_id' => 'Endpoints',
            'role_id' => 'Roles',
        ];
        foreach ($fieldsTables as $field => $table) {
            $id = $this->in($table . ' id or name');
            if ($id && !is_numeric($id)) {
                $id = TableRegistry::get($table)->find()->where(['name' => $id])->firstOrFail()->id;
            }
            $entity->$field = $id;
        }

        $perms = ['true', 'false', 'block', 'mine'];
        foreach (['read', 'write'] as $field) {
            $perm = $this->in("'$field' permission", $perms);
            $entity->$field = $perm;
        }
    }

    /**
     * Setup default entity for applications, roles, endpoints
     *
     * @param \Cake\ORM\Entity $entity Entity to add
     * @return void
     */
    protected function setupDefaultEntity($entity)
    {
        $name = $this->in('Resource name');
        if (empty($name)) {
            $this->abort('Resource name cannot be empty');
        }
        $entity->name = $name;
        $description = $this->in('Resource description (optional)');
        $entity->description = $description;
    }

    /**
     * Modify a resource field
     *
     * @param mixed $id Resource sid or name
     * @return void
     */
    public function edit($id)
    {
        $this->initModel();
        $entity = $this->getEntity($id);
        $field = $this->param('field');
        if ($field === 'api_key') {
             $entity->api_key = $this->modelTable->generateApiKey();
        } else {
            $value = $this->in(sprintf('New value for "%s" [current is "%s"]', $field, $entity->get($field)));
            $entity->set($field, $value);
        }
        $this->modelTable->save($entity);
        $this->out('Resource with id ' . $entity->id . ' modified');
    }

    /**
     * List entities
     *
     * @return array applications list
     */
    public function ls()
    {
        $this->initModel();
        $action = new ListEntitiesAction(['table' => $this->modelTable]);
        $query = $action(['filter' => $this->param('filter')]);
        $results = $query->toArray();
        $this->out($results ?: 'empty set');

        return $results;
    }

    /**
     * Remove entity by name or id
     *
     * @param mixed $id Resource id or name
     * @return bool True on success, false on blocked execution
     */
    public function rm($id)
    {
        $this->initModel();
        $res = $this->in(sprintf('You are REMOVING "%s" with name or id "%s" - are you sure?', $this->param('type'), $id), ['y', 'n'], 'n');
        if ($res != 'y') {
            $this->info('Remove not executed');

            return false;
        }
        $entity = $this->getEntity($id);
        $action = new DeleteEntityAction(['table' => $this->modelTable]);
        $action(compact('entity'));

        $this->out('Record ' . $id . ' deleted');

        return true;
    }

    /**
     * Return entity by $id name|id
     *
     * @param mixed $id entity name|id
     * @return \Cake\ORM\Entity entity
     */
    protected function getEntity($id)
    {
        if (!is_numeric($id)) {
            return $this->modelTable
                ->find()
                ->where(['name' => $id])
                ->firstOrFail();
        }

        return $this->modelTable->get($id);
    }
}
