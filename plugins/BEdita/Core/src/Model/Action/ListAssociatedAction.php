<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Action;

use BEdita\Core\ORM\Inheritance\Table as InheritanceTable;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\Exception\InvalidPrimaryKeyException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Association\HasOne;
use Cake\ORM\Entity;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Utility\Hash;

/**
 * Command to list entities associated to another entity.
 *
 * @since 4.0.0
 */
class ListAssociatedAction extends BaseAction
{

    /**
     * Association.
     *
     * @var \Cake\ORM\Association
     */
    protected $Association;

    /**
     * {@inheritDoc}
     */
    protected function initialize(array $config)
    {
        $this->Association = $this->getConfig('association');
    }

    /**
     * {@inheritDoc}
     *
     * @return \Cake\ORM\Query|\Cake\Datasource\EntityInterface|null
     * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException Throws an exception if an invalid
     *      primary key is passed.
     */
    public function execute(array $data = [])
    {
        $query = $this->buildQuery($data['primaryKey'], $data);

        if ($this->Association instanceof HasOne || $this->Association instanceof BelongsTo) {
            return $query->first();
        }

        return $query;
    }

    /**
     * Build conditions for primary key.
     *
     * @param mixed $primaryKey Primary key.
     * @return array
     * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException Throws an exception if primary key is invalid.
     */
    protected function primaryKeyConditions($primaryKey)
    {
        $source = $this->Association->getSource();
        $primaryKeyFields = array_map([$source, 'aliasField'], (array)$source->getPrimaryKey());

        $primaryKey = (array)$primaryKey;
        if (count($primaryKeyFields) !== count($primaryKey)) {
            $primaryKey = $primaryKey ?: [null];
            $primaryKey = array_map(function ($key) {
                return var_export($key, true);
            }, $primaryKey);

            throw new InvalidPrimaryKeyException(__(
                'Record not found in table "{0}" with primary key [{1}]',
                $source->getTable(),
                implode($primaryKey, ', ')
            ));
        }

        return array_combine($primaryKeyFields, $primaryKey);
    }

    /**
     * Build the query object.
     *
     * @param mixed $primaryKey Primary key.
     * @param array $options Additional options.
     * @return \Cake\ORM\Query
     * @throws \Cake\Datasource\Exception\RecordNotFoundException Throws an exception if trying to fetch associations
     *      for a missing resource.
     */
    protected function buildQuery($primaryKey, array $options)
    {
        $list = !empty($options['list']);
        $joinData = !empty($options['joinData']);

        $source = $this->Association->getSource();
        $conditions = $this->primaryKeyConditions($primaryKey);

        $existing = $source->find()
            ->where($conditions)
            ->count();
        if (!$existing) {
            throw new RecordNotFoundException(__('Record not found in table "{0}"', $source->getTable()));
        }

        list($builder, $select) = $this->getQueryClauses($list);

        $query = $source->find()
            ->innerJoinWith($this->Association->getName(), $builder)
            ->select($select)
            ->where($conditions)
            ->formatResults(function (ResultSetInterface $results) {
                return $results->map(function ($row) {
                    if (!isset($row['_matchingData']) || !is_array($row['_matchingData'])) {
                        return $row;
                    }

                    $joinData = null;
                    if ($this->Association instanceof BelongsToMany) {
                        $junctionAlias = $this->Association->junction()->getAlias();
                        $joinData = Hash::get($row['_matchingData'], $junctionAlias);
                        unset($row['_matchingData'][$junctionAlias]);
                    }

                    $result = array_shift($row['_matchingData']);

                    foreach ($row['_matchingData'] as $entity) {
                        if ($entity instanceof Entity) {
                            $entity = $entity->getOriginalValues();
                        }

                        $result->set($entity, ['setter' => false, 'guard' => false]);
                    }

                    if (!empty($joinData)) {
                        $result->set('_joinData', $joinData);
                    }

                    return $result;
                });
            });

        if (!empty($options['only'])) {
            $query = $query
                ->andWhere(function (QueryExpression $exp) use ($options) {
                    return $exp->in(
                        $this->Association->aliasField($this->Association->getPrimaryKey()),
                        $options['only']
                    );
                });
        }

        if ($this->Association instanceof BelongsToMany || $this->Association instanceof HasMany) {
            $query = $query->order($this->Association->sort());
        }
        if ($joinData && $this->Association instanceof BelongsToMany) {
            $query = $query->select($this->Association->junction());
        }

        return $query;
    }

    /**
     * Get clauses for query.
     *
     * @param bool $list Should only associated entity's primary key be selected?
     * @return array
     */
    protected function getQueryClauses($list)
    {
        $target = $this->Association->getTarget();

        $builder = null;
        $select = $target;
        if ($list) {
            $select = array_map([$target, 'aliasField'], (array)$target->getPrimaryKey());
        } elseif ($target instanceof InheritanceTable) {
            $select = array_map([$target, 'aliasField'], $target->getSchema()->columns());

            $inheritedTables = $target->inheritedTables(true);
            foreach ($inheritedTables as $inheritedTable) {
                $select = array_merge(
                    $select,
                    array_map([$inheritedTable, 'aliasField'], $inheritedTable->getSchema()->columns())
                );
            }

            $inheritedTables = array_reverse($inheritedTables);
            foreach ($inheritedTables as $inheritedTable) {
                if (!$inheritedTable instanceof Table) {
                    continue;
                }

                $builder = function (Query $query) use ($inheritedTable, $builder) {
                    return $query->innerJoinWith($inheritedTable->getAlias(), $builder);
                };
            }
        }

        return [$builder, $select];
    }
}
