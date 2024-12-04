<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Command;

use BEdita\Core\Model\Table\AsyncJobsTable;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Inflector;

/**
 * ResourcesAdd command.
 */
class ResourcesAddCommand extends Command
{
    use LocatorAwareTrait;

    /**
     * Console arguments
     *
     * @var \Cake\Console\Arguments
     */
    protected Arguments $args;

    /**
     * Console IO
     *
     * @var \Cake\Console\ConsoleIo
     */
    protected ConsoleIo $io;

    /**
     * Async jobs table
     *
     * @var \BEdita\Core\Model\Table\AsyncJobsTable
     */
    protected AsyncJobsTable $table;

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->setName('cake resources_add');
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->addOption('type', [
                'help' => 'Entity type',
                'required' => true,
                'short' => 't',
                'choices' => ['applications', 'roles', 'endpoints', 'endpoint_permissions'],
            ]);
    }

    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return 'Resources add';
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $type = $args->getOption('type');
        $this->args = $args;
        $this->io = $io;
        $this->table = $this->fetchTable(Inflector::camelize($type));
        $entity = $this->table->newEntity([]);
        if ($type === 'endpoint_permissions') {
            $this->setupEndpointPermissionEntity($entity);
        } else {
            $this->setupDefaultEntity($entity);
        }
        $this->table->saveOrFail($entity);
        $this->io->out(sprintf('Resource with id %d created', $entity->id));

        return static::CODE_SUCCESS;
    }

    /**
     * Setup default entity for applications, roles, endpoints
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity to add
     * @return void
     */
    protected function setupDefaultEntity(EntityInterface $entity): void
    {
        $name = $this->io->ask('Resource name');
        if (empty($name)) {
            $this->io->abort('Resource name cannot be empty');
        }
        $entity->set('name', $name);
        $description = $this->io->ask('Resource description (optional)');
        $entity->set('description', $description);
    }

    /**
     * Setup entity for endpoint_permissions
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity to add
     * @return void
     */
    protected function setupEndpointPermissionEntity(EntityInterface $entity): void
    {
        $fieldsTables = [
            'application_id' => 'Applications',
            'endpoint_id' => 'Endpoints',
            'role_id' => 'Roles',
        ];
        foreach ($fieldsTables as $field => $modelName) {
            $param = $this->io->ask(sprintf('%s id or name', $modelName));
            if ($param && !is_numeric($param)) {
                $param = $this->fetchTable($modelName)
                    ->find()
                    ->where(['name' => $param])
                    ->firstOrFail()
                    ->id;
            }
            $entity->set($field, $param);
        }

        $perms = ['true', 'false', 'block', 'mine'];
        foreach (['read', 'write'] as $field) {
            $perm = $this->io->askChoice("'$field' permission", $perms);
            $entity->set($field, $perm);
        }
    }
}
