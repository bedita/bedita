<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2022 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Command;

use BEdita\Core\Model\Table\TreesTable;
use Cake\Collection\CollectionInterface;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Database\Expression\IdentifierExpression;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;

/**
 * Commend to check tree sanity and perform objects-aware tree recovery.
 *
 * @since 4.8.0
 * @property \BEdita\Core\Model\Table\ObjectsTable $Objects
 */
class TreeCheckCommand extends Command
{
    /**
     * @inheritDoc
     */
    public $modelClass = 'Objects';

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public static function defaultName(): string
    {
        return 'tree check';
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return parent::buildOptionParser($parser)
            ->setDescription('Objects-aware sanity checks on tree.');
    }

    /**
     * Implement this method with your command's logic.
     *
     * @param \Cake\Console\Arguments $args The command arguments.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return null|int The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $code = static::CODE_SUCCESS;

        // Run tree integrity checks.
        $messages = $this->Objects->TreeNodes->checkIntegrity();
        if (!empty($messages)) {
            $io->out('=====> <error>Tree is corrupt!</error>');
            foreach ($messages as $msg) {
                $io->verbose(sprintf('=====>   - %s', $msg));
            }

            $code = static::CODE_ERROR;
        } else {
            $io->verbose('=====> <success>Tree integrity check passed.</success>');
        }

        // Checks on folders not in tree.
        $results = $this->getFoldersNotInTree()->all();
        if (!$results->isEmpty()) {
            $code = static::CODE_ERROR;
        }
        $this->report($io, $results, 'folders not in tree', 'is not in the tree');

        // Checks on ubiquitous folders.
        $results = $this->getUbiquitousFolders()->all();
        if (!$results->isEmpty()) {
            $code = static::CODE_ERROR;
        }
        $this->report($io, $results, 'ubiquitous folders', 'is ubiquitous');

        // Checks on other objects in root.
        $results = $this->getObjectsInRoot()->all();
        if (!$results->isEmpty()) {
            $code = static::CODE_ERROR;
        }
        $this->report($io, $results, 'other objects in root', 'is a root');

        // Checks on other objects with children.
        $results = $this->getObjectsWithChildren()->all();
        if (!$results->isEmpty()) {
            $code = static::CODE_ERROR;
        }
        $this->report($io, $results, 'other objects with children', 'has children');

        // Checks on other objects twice inside same folder.
        $results = $this->getObjectsTwiceInFolder()->all();
        if (!$results->isEmpty()) {
            $code = static::CODE_ERROR;
        }
        $this->report($io, $results, 'objects that are present multiple times within same parent', 'is positioned multiple times within the same parent');

        // Checks matching `parent_id` in parent tree node.
        $results = $this->getNotMatchingParentId()->all();
        if (!$results->isEmpty()) {
            $code = static::CODE_ERROR;
        }
        $this->report($io, $results, 'tree nodes that reference a different parent than the object of the parent node', 'references a different parent_id than the object_id in the parent node');

        // Checks matching `root_id` in parent tree node.
        $results = $this->getNotMatchingRootId()->all();
        if (!$results->isEmpty()) {
            $code = static::CODE_ERROR;
        }
        $this->report($io, $results, 'tree nodes that reference a different root than the root of the parent node', 'references a different root_id than the one in the parent node');

        return $code;
    }

    /**
     * Return query to find all folders that are not in the tree.
     *
     * @return \Cake\ORM\Query
     */
    protected function getFoldersNotInTree(): Query
    {
        return $this->Objects->find('type', ['folders'])
            ->select([
                $this->Objects->aliasField('id'),
                $this->Objects->aliasField('uname'),
                $this->Objects->aliasField('object_type_id'),
            ])
            ->notMatching('TreeNodes');
    }

    /**
     * Return query to find all folders that are ubiquitous.
     *
     * @return \Cake\ORM\Query
     */
    protected function getUbiquitousFolders(): Query
    {
        return $this->Objects->find('type', ['folders'])
            ->select([
                $this->Objects->aliasField('id'),
                $this->Objects->aliasField('uname'),
                $this->Objects->aliasField('object_type_id'),
            ])
            ->innerJoinWith('TreeNodes')
            ->group([
                $this->Objects->aliasField('id'),
            ])
            ->having(function (QueryExpression $exp, Query $query): QueryExpression {
                return $exp->gt($query->func()->count('*'), 1, 'integer');
            });
    }

    /**
     * Return query to find all objects that are roots despite not being folders.
     *
     * @return \Cake\ORM\Query
     */
    protected function getObjectsInRoot(): Query
    {
        return $this->Objects->find('type', ['!=' => 'folders'])
            ->select([
                $this->Objects->aliasField('id'),
                $this->Objects->aliasField('uname'),
                $this->Objects->aliasField('object_type_id'),
            ])
            ->innerJoinWith('TreeNodes', function (Query $query): Query {
                return $query->where(function (QueryExpression $exp): QueryExpression {
                    return $exp->isNull($this->Objects->TreeNodes->aliasField('parent_id'));
                });
            });
    }

    /**
     * Return query to find all objects that have children despite not being folders.
     *
     * @return \Cake\ORM\Query
     */
    protected function getObjectsWithChildren(): Query
    {
        // This association normally would live in the "Folders" table, but we're checking for anomalies,
        // so let's assume it makes sense here.
        $this->Objects->hasMany('TreeParentNodes', [
            'className' => TreesTable::class,
            'foreignKey' => 'parent_id',
        ]);

        return $this->Objects->find('type', ['!=' => 'folders'])
            ->select([
                $this->Objects->aliasField('id'),
                $this->Objects->aliasField('uname'),
                $this->Objects->aliasField('object_type_id'),
            ])
            ->innerJoinWith('TreeParentNodes');
    }

    /**
     * Return query to find all objects that are placed twice inside same parent.
     *
     * @return \Cake\ORM\Query
     */
    protected function getObjectsTwiceInFolder(): Query
    {
        return $this->Objects->find('type', ['!=' => 'folders'])
            ->select([
                $this->Objects->aliasField('id'),
                $this->Objects->aliasField('uname'),
                $this->Objects->aliasField('object_type_id'),
                $this->Objects->Parents->aliasField('id'),
                $this->Objects->Parents->aliasField('uname'),
            ])
            ->innerJoinWith('Parents')
            ->group([
                $this->Objects->aliasField('id'),
                $this->Objects->Parents->aliasField('id'),
            ])
            ->having(function (QueryExpression $exp, Query $query): QueryExpression {
                return $exp->gt($query->func()->count('*'), 1, 'integer');
            });
    }

    /**
     * Return query to find all rows in `trees` table that reference a different `parent_id` than the `object_id` in the parent tree node.
     *
     * @return \Cake\ORM\Query
     */
    protected function getNotMatchingParentId(): Query
    {
        return $this->Objects->find()
            ->select([
                $this->Objects->aliasField('id'),
                $this->Objects->aliasField('uname'),
                $this->Objects->aliasField('object_type_id'),
            ])
            ->innerJoinWith('TreeNodes.ParentNode')
            ->where(function (QueryExpression $exp): QueryExpression {
                return $exp->notEq(
                    $this->Objects->TreeNodes->aliasField('parent_id'),
                    new IdentifierExpression($this->Objects->TreeNodes->ParentNode->aliasField('object_id'))
                );
            });
    }

    /**
     * Return query to find all rows in `trees` table that reference a different `parent_id` than the `object_id` in the parent tree node.
     *
     * @return \Cake\ORM\Query
     */
    protected function getNotMatchingRootId(): Query
    {
        return $this->Objects->find()
            ->select([
                $this->Objects->aliasField('id'),
                $this->Objects->aliasField('uname'),
                $this->Objects->aliasField('object_type_id'),
            ])
            ->leftJoinWith('TreeNodes.ParentNode')
            ->where(function (QueryExpression $exp, Query $query): QueryExpression {
                return $exp->notEq(
                    $this->Objects->TreeNodes->aliasField('root_id'),
                    $query->func()->coalesce([
                        $this->Objects->TreeNodes->ParentNode->aliasField('root_id') => 'identifier',
                        $this->Objects->TreeNodes->aliasField('object_id') => 'identifier',
                    ])
                );
            });
    }

    /**
     * Output a report section.
     *
     * @param \Cake\Console\ConsoleIo $io Console I/O.
     * @param \Cake\Collection\CollectionInterface $results Results.
     * @param string $title Section title.
     * @param string $message Error message.
     * @return void
     */
    protected function report(ConsoleIo $io, CollectionInterface $results, string $title, string $message): void
    {
        $count = $results->count();
        if ($count === 0) {
            $io->verbose(sprintf('=====> <success>There are no %s.</success>', $title));

            return;
        }

        $io->out(sprintf('=====> <warning>Found %d %s!</warning>', $count, $title));
        $results->each(function (EntityInterface $entity) use ($io, $message): void {
            $io->verbose(
                sprintf(
                    '=====>   - %s <info>%s</info> (#<info>%d</info>) %s',
                    $this->Objects->ObjectTypes->get($entity['object_type_id'])->get('singular'),
                    $entity['uname'],
                    $entity['id'],
                    $message
                )
            );
        });
    }
}
