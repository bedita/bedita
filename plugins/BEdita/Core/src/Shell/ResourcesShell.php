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
use BEdita\Core\Model\Table\ApplicationsTable;
use Cake\Console\Shell;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
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
     * @var string[]
     */
    protected static $acceptedTypes = ['applications', 'roles', 'endpoints', 'endpoint_permissions'];

    /**
     * Editable resource fields
     *
     * @var string[]
     */
    protected static $editableFields = ['api_key', 'description', 'enabled', 'name', 'unchangeable'];

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
                'choices' => static::$acceptedTypes,
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
                        'choices' => static::$editableFields,
                    ],
                ],
            ]
        ]);

        return $parser;
    }

    /**
     * Init model table using `--type|-t` option
     *
     * @return \Cake\ORM\Table
     */
    protected function getTable()
    {
        $modelName = Inflector::camelize($this->param('type'));

        return TableRegistry::get($modelName);
    }

    /**
     * Return entity by ID or name.
     *
     * @param mixed $id Entity ID or name.
     * @return \Cake\Datasource\EntityInterface
     */
    protected function getEntity($id)
    {
        $table = $this->getTable();
        try {
            if (!is_numeric($id)) {
                return $table
                    ->find()
                    ->where(['name' => $id])
                    ->firstOrFail();
            }

            return $table->get($id);
        } catch (RecordNotFoundException $e) {
            $this->abort($e->getMessage());
        }
    }

    /**
     * Create a new resource
     *
     * @return void
     */
    public function add()
    {
        $table = $this->getTable();
        $entity = $table->newEntity();
        if ($this->param('type') === 'endpoint_permissions') {
            $this->setupEndpointPermissionEntity($entity);
        } else {
            $this->setupDefaultEntity($entity);
        }

        $table->saveOrFail($entity);
        $this->out(sprintf('Resource with id %d created', $entity->id));
    }

    /**
     * Setup entity for endpoint_permissions
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity to add
     * @return void
     */
    protected function setupEndpointPermissionEntity(EntityInterface $entity)
    {
        $fieldsTables = [
            'application_id' => 'Applications',
            'endpoint_id' => 'Endpoints',
            'role_id' => 'Roles',
        ];
        foreach ($fieldsTables as $field => $table) {
            $id = $this->in(sprintf('%s id or name', $table));
            if ($id && !is_numeric($id)) {
                $id = TableRegistry::get($table)->find()->where(['name' => $id])->firstOrFail()->id;
            }
            $entity->set($field, $id);
        }

        $perms = ['true', 'false', 'block', 'mine'];
        foreach (['read', 'write'] as $field) {
            $perm = $this->in("'$field' permission", $perms);
            $entity->set($field, $perm);
        }
    }

    /**
     * Setup default entity for applications, roles, endpoints
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity to add
     * @return void
     */
    protected function setupDefaultEntity(EntityInterface $entity)
    {
        $name = $this->in('Resource name');
        if (empty($name)) {
            $this->abort('Resource name cannot be empty');
        }
        $entity->set('name', $name);
        $description = $this->in('Resource description (optional)');
        $entity->set('description', $description);
    }

    /**
     * Modify a resource field
     *
     * @param mixed $id Resource sid or name
     * @return void
     */
    public function edit($id)
    {
        $table = $this->getTable();
        $entity = $this->getEntity($id);
        $field = $this->param('field');
        if ($field === 'api_key' && $table instanceof ApplicationsTable) {
             $entity->set('api_key', ApplicationsTable::generateApiKey());
        } else {
            $value = $this->in(sprintf('New value for "%s" [current is "%s"]', $field, $entity->get($field)));
            $entity->set($field, $value);
        }
        $table->saveOrFail($entity);
        $this->out(sprintf('Resource with id %d modified', $entity->id));
    }

    /**
     * List entities
     *
     * @return void
     */
    public function ls()
    {
        $table = $this->getTable();
        $action = new ListEntitiesAction(compact('table'));
        $query = $action(['filter' => $this->param('filter')]);
        $results = $query->toArray();
        $this->out(sprintf('<info>%d result(s) found</info>', count($results)));
        $this->out($results);
    }

    /**
     * Remove entity by name or id
     *
     * @param mixed $id Resource id or name
     * @return bool True on success, false on blocked execution
     */
    public function rm($id)
    {
        $table = $this->getTable();
        $res = $this->in(sprintf('You are REMOVING "%s" with name or id "%s" - are you sure?', $this->param('type'), $id), ['y', 'n'], 'n');
        if ($res !== 'y') {
            $this->info('No action performed');

            return false;
        }

        $entity = $this->getEntity($id);
        $action = new DeleteEntityAction(compact('table'));
        $action(compact('entity'));

        $this->out(sprintf('Record %d deleted', $id));

        return true;
    }
}
