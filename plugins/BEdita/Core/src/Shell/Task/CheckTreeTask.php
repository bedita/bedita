<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Shell\Task;

use Cake\Console\Shell;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\Utility\Hash;

/**
 * Task to check tree sanity and perform objects-aware tree recovery.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\Model\Table\ObjectsTable $Objects
 */
class CheckTreeTask extends Shell
{

    /**
     * {@inheritDoc}
     */
    public $modelClass = 'Objects';

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();

        return $parser;
    }

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        // Add association to help building queries. This association normally would live in the "Folders" table,
        // but we're checking for anomalies, so let's assume it makes sense here.
        $this->Objects->hasMany('TreeParentNodes', [
            'className' => 'Trees',
            'foreignKey' => 'parent_id',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function main()
    {
        $ok = true;

        // Checks on folders not in tree.
        $results = $this->getFoldersNotInTree()
            ->all();
        $count = $results->count();
        if ($count > 0) {
            $ok = false;

            $this->out(sprintf('=====> <warning>Found %d folders not in tree!</warning>', $count));
            $results->each(function (EntityInterface $entity) {
                $this->verbose(
                    sprintf(
                        '=====>   - folder <info>%s</info> (#<info>%d</info>) is not in the tree',
                        $entity['uname'],
                        $entity['id']
                    )
                );
            });
        } else {
            $this->verbose('=====> <success>There are no folders that are not in tree.</success>');
        }

        // Checks on ubiquitous folders.
        $results = $this->getUbiquitousFolders()
            ->all();
        $count = $results->count();
        if ($count > 0) {
            $ok = false;

            $this->out(sprintf('=====> <warning>Found %d ubiquitous folders!</warning>', $count));
            $results->each(function (EntityInterface $entity) {
                $this->verbose(
                    sprintf(
                        '=====>   - folder <info>%s</info> (#<info>%d</info>) is ubiquitous',
                        $entity['uname'],
                        $entity['id']
                    )
                );
            });
        } else {
            $this->verbose('=====> <success>There are no ubiquitous folders.</success>');
        }

        // Checks on other objects in root.
        $results = $this->getObjectsInRoot()
            ->all();
        $count = $results->count();
        if ($count > 0) {
            $ok = false;

            $this->out(sprintf('=====> <warning>Found %d other objects in root!</warning>', $count));
            $results->each(function (EntityInterface $entity) {
                $this->verbose(
                    sprintf(
                        '=====>   - %s <info>%s</info> (#<info>%d</info>) is a root',
                        $this->Objects->ObjectTypes->get($entity['object_type_id'])->get('singular'),
                        $entity['uname'],
                        $entity['id']
                    )
                );
            });
        } else {
            $this->verbose('=====> <success>There are no other objects in root.</success>');
        }

        // Checks on other objects with children.
        $results = $this->getObjectsWithChildren()
            ->all();
        $count = $results->count();
        if ($count > 0) {
            $ok = false;

            $this->out(sprintf('=====> <warning>Found %d other objects with children!</warning>', $count));
            $results->each(function (EntityInterface $entity) {
                $this->verbose(
                    sprintf(
                        '=====>   - %s <info>%s</info> (#<info>%d</info>) has children',
                        $this->Objects->ObjectTypes->get($entity['object_type_id'])->get('singular'),
                        $entity['uname'],
                        $entity['id']
                    )
                );
            });
        } else {
            $this->verbose('=====> <success>There are no other objects with children.</success>');
        }

        // Checks on other objects with children.
        $results = $this->getObjectsTwiceInFolder()
            ->all();
        $count = $results->count();
        if ($count > 0) {
            $ok = false;

            $this->out(sprintf('=====> <warning>Found %d objects that are present multiple times within same parent!</warning>', $count));
            $results->each(function (EntityInterface $entity) {
                $this->verbose(
                    sprintf(
                        '=====>   - %s <info>%s</info> (#<info>%d</info>) is present multiple times within parent <info>%s</info> (#<info>%d</info>)',
                        $this->Objects->ObjectTypes->get($entity['object_type_id'])->get('singular'),
                        $entity['uname'],
                        $entity['id'],
                        Hash::get($entity, '_matchingData.Parents.uname', '(unknown)'),
                        Hash::get($entity, '_matchingData.Parents.id', 0)
                    )
                );
            });
        } else {
            $this->verbose('=====> <success>There are no objects that are present multiple times within same parent.</success>');
        }

        return $ok;
    }

    /**
     * Return query to find all folders that are not in the tree.
     *
     * @return \Cake\ORM\Query
     */
    protected function getFoldersNotInTree()
    {
        return $this->Objects->find('type', ['folders'])
            ->select([
                $this->Objects->aliasField('id'),
                $this->Objects->aliasField('uname'),
            ])
            ->notMatching('TreeNodes');
    }

    /**
     * Return query to find all folders that are ubiquitous.
     *
     * @return \Cake\ORM\Query
     */
    protected function getUbiquitousFolders()
    {
        $query = $this->Objects->find('type', ['folders']);

        return $query
            ->select([
                $this->Objects->aliasField('id'),
                $this->Objects->aliasField('uname'),
            ])
            ->innerJoinWith('TreeNodes')
            ->group([
                $this->Objects->aliasField($this->Objects->getPrimaryKey()),
            ])
            ->having(function (QueryExpression $exp) use ($query) {
                return $exp->gt($query->func()->count('*'), 1, 'integer');
            });
    }

    /**
     * Return query to find all objects that are roots despite not being folders.
     *
     * @return \Cake\ORM\Query
     */
    protected function getObjectsInRoot()
    {
        return $this->Objects->find('type', ['!=' => 'folders'])
            ->select([
                $this->Objects->aliasField('id'),
                $this->Objects->aliasField('uname'),
                $this->Objects->aliasField('object_type_id'),
            ])
            ->innerJoinWith('TreeNodes', function (Query $query) {
                return $query->where(function (QueryExpression $exp) {
                    return $exp->isNull($this->Objects->TreeNodes->aliasField('parent_id'));
                });
            });
    }

    /**
     * Return query to find all objects that have children despite not being folders.
     *
     * @return \Cake\ORM\Query
     */
    protected function getObjectsWithChildren()
    {
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
    protected function getObjectsTwiceInFolder()
    {
        $query = $this->Objects->find('type', ['!=' => 'folders']);

        return $query
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
            ->having(function (QueryExpression $exp) use ($query) {
                return $exp->gt($query->func()->count('*'), 1, 'integer');
            });
    }
}
