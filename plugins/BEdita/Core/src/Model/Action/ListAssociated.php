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

use Cake\Datasource\Exception\InvalidPrimaryKeyException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Datasource\ResultSetInterface;
use Cake\ORM\Association;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\HasOne;

/**
 * Command to list entities associated to another entity.
 *
 * @since 4.0.0
 */
class ListAssociated
{

    /**
     * Association.
     *
     * @var \Cake\ORM\Association
     */
    protected $Association;

    /**
     * Command constructor.
     *
     * @param \Cake\ORM\Association $Association Association.
     */
    public function __construct(Association $Association)
    {
        $this->Association = $Association;
    }

    /**
     * Find existing relations.
     *
     * @param mixed $primaryKey Primary key of entity to find associations for.
     * @return \Cake\ORM\Query|\Cake\Datasource\EntityInterface|null
     * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException Throws an exception if an invalid
     *      primary key is passed.
     */
    public function __invoke($primaryKey)
    {
        $query = $this->buildQuery($primaryKey);

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
     * @return \Cake\ORM\Query
     * @throws \Cake\Datasource\Exception\RecordNotFoundException Throws an exception if trying to fetch associations
     *      for a missing resource.
     */
    protected function buildQuery($primaryKey)
    {
        $source = $this->Association->getSource();
        $conditions = $this->primaryKeyConditions($primaryKey);

        $existing = $source->find()
            ->where($conditions)
            ->count();
        if (!$existing) {
            throw new RecordNotFoundException(__('Record not found in table "{0}"', $source->getTable()));
        }

        $primaryKeyFields = array_map([$this->Association, 'aliasField'], (array)$this->Association->getPrimaryKey());

        $query = $source->find()
            ->innerJoinWith($this->Association->getName())
            ->select($primaryKeyFields)
            ->where($conditions)
            ->enableAutoFields(false)
            ->formatResults(function (ResultSetInterface $results) {
                return $results->map(function ($row) {
                    if (!isset($row['_matchingData']) || !is_array($row['_matchingData'])) {
                        return $row;
                    }

                    $row = current($row['_matchingData']);

                    return $row;
                });
            });

        return $query;
    }
}
