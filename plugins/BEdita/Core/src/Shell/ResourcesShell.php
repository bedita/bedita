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

/**
 * Resource shell commands:
 *
 * - ls: list entities
 *
 * @since 4.0.0
 */
abstract class ResourcesShell extends Shell
{

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();
        if (empty($this->modelClass)) {
            $this->abort('Empty modelClass for shell');
        }
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
            'help' => 'create a new entity',
            'parser' => [
                'description' => [
                    'Create a new entity.'
                ]
            ]
        ]);
        $parser->addSubcommand('ls', [
            'help' => 'list entities',
            'parser' => [
                'description' => [
                    'List entities.',
                    'Option --filter (optional) provides listing filtered by comma separated key=value pairs.'
                ],
                'options' => [
                    'filter' => ['help' => 'List entities filtered by comma separated key=value pairs', 'required' => false],
                ]
            ]
        ]);
        $parser->addSubcommand('rm', [
            'help' => 'remove an entity',
            'parser' => [
                'description' => [
                    'Remove an entity.',
                    'First argument (required) indicates entity\'s id|name.'
                ],
                'arguments' => [
                    'name|id' => ['help' => 'Entity\'s name|id', 'required' => true]
                ]
            ]
        ]);

        return $parser;
    }

    /**
     * save data for entity
     *
     * @param \Cake\ORM\Entity $entity entity to save
     * @return void
     */
    public function processCreate(Entity $entity)
    {
        TableRegistry::get($this->modelClass)->save($entity);
        $this->out('Record ' . $entity->id . ' saved');
    }

    /**
     * List entities
     *
     * @return void
     */
    public function ls()
    {
        $action = new ListEntitiesAction(['table' => TableRegistry::get($this->modelClass)]);
        $query = $action(['filter' => $this->param('filter')]);
        $results = $query->toArray();
        $this->out($results ?: 'empty set');
    }

    /**
     * Remove entity by id
     *
     * @param int $id entity id
     * @return void
     */
    public function rm($id)
    {
        $entity = $this->getEntity($id);
        $action = new DeleteEntityAction(['table' => TableRegistry::get($this->modelClass)]);
        if (!$action(compact('entity'))) {
            $this->abort('Record ' . $id . ' could not be deleted');
        }
        $this->out('Record ' . $id . ' deleted');
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
            return TableRegistry::get($this->modelClass)
                ->find()
                ->where(['name' => $id])
                ->firstOrFail();
        }

        return TableRegistry::get($this->modelClass)->get($id);
    }
}
