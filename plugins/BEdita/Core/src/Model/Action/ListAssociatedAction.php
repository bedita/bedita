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

use Cake\Collection\CollectionInterface;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\InvalidPrimaryKeyException;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Association;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Association\HasOne;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Command to list entities associated to another entity.
 *
 * @since 4.0.0
 */
class ListAssociatedAction extends BaseAction
{

    /**
     * Name of inverse association.
     *
     * @var string
     */
    const INVERSE_ASSOCIATION_NAME = '_InverseAssociation';

    /**
     * Association.
     *
     * @var \Cake\ORM\Association
     */
    protected $Association;

    /**
     * Action used for listing entities.
     *
     * @var \BEdita\Core\Model\Action\BaseAction
     */
    protected $ListAction;

    /**
     * {@inheritDoc}
     */
    protected function initialize(array $config)
    {
        $this->Association = $this->getConfig('association');

        $table = $this->Association->getTarget();
        $this->ListAction = new ListEntitiesAction(compact('table'));
    }

    /**
     * Build conditions for primary key.
     *
     * @param \Cake\ORM\Table $table Table object.
     * @param mixed $primaryKey Primary key.
     * @return array
     * @throws \Cake\Datasource\Exception\InvalidPrimaryKeyException Throws an exception if primary key is invalid.
     */
    protected function primaryKeyConditions(Table $table, $primaryKey)
    {
        $primaryKeyFields = array_map([$table, 'aliasField'], (array)$table->getPrimaryKey());

        $primaryKey = (array)$primaryKey;
        if (count($primaryKeyFields) !== count($primaryKey)) {
            $primaryKey = $primaryKey ?: [null];
            $primaryKey = array_map(function ($key) {
                return var_export($key, true);
            }, $primaryKey);

            throw new InvalidPrimaryKeyException(__(
                'Record not found in table "{0}" with primary key [{1}]',
                $table->getTable(),
                implode($primaryKey, ', ')
            ));
        }

        return array_combine($primaryKeyFields, $primaryKey);
    }

    /**
     * Check that the entity for which associated entities should be listed actually exists.
     *
     * @param array $data Data.
     * @return void
     * @throws \InvalidArgumentException Throws an exception if required option `primaryKey` is missing.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException Throws an exception if the record could not be found.
     */
    protected function checkEntityExists(array $data)
    {
        if (empty($data['primaryKey'])) {
            throw new \InvalidArgumentException(__d('bedita', 'Missing required option "{0}"', 'primaryKey'));
        }

        $source = $this->Association->getSource();
        $conditions = $this->primaryKeyConditions($source, $data['primaryKey']);

        $existing = $source->find()
            ->where($conditions)
            ->count();
        if (!$existing) {
            throw new RecordNotFoundException(__('Record not found in table "{0}"', $source->getTable()));
        }
    }

    /**
     * Build inverse association for joining.
     *
     * @return \Cake\ORM\Association
     * @throws \LogicException Throws an exception if an Association of an unknown type is passed.
     */
    protected function buildInverseAssociation()
    {
        $sourceTable = $this->Association->getTarget();
        $targetTable = TableRegistry::get(static::INVERSE_ASSOCIATION_NAME, [
            'className' => $this->Association->getSource()->getRegistryAlias(),
        ]);
        $targetTable->setTable($this->Association->getSource()->getTable());
        $propertyName = Inflector::underscore(static::INVERSE_ASSOCIATION_NAME);

        $options = compact('propertyName', 'sourceTable', 'targetTable');
        if ($this->Association instanceof HasOne || $this->Association instanceof HasMany) {
            $options += [
                'foreignKey' => $this->Association->getForeignKey(),
                'bindingKey' => $this->Association->getBindingKey(),
            ];

            $association = new BelongsTo(static::INVERSE_ASSOCIATION_NAME, $options);
        } elseif ($this->Association instanceof BelongsTo) {
            $options += [
                'foreignKey' => $this->Association->getForeignKey(),
                'bindingKey' => $this->Association->getBindingKey(),
            ];

            $association = new HasMany(static::INVERSE_ASSOCIATION_NAME, $options);
        } elseif ($this->Association instanceof BelongsToMany) {
            $options += [
                'through' => $this->Association->junction()->getRegistryAlias(),
                'foreignKey' => $this->Association->getTargetForeignKey(),
                'targetForeignKey' => $this->Association->getForeignKey(),
                'conditions' => $this->Association->getConditions(),
            ];

            $association = new BelongsToMany(static::INVERSE_ASSOCIATION_NAME, $options);
        } else {
            throw new \LogicException(sprintf('Unknown association type "%s"', get_class($this->Association)));
        }

        return $sourceTable->associations()->add($association->getName(), $association);
    }

    /**
     * Build the query object.
     *
     * @param mixed $primaryKey Primary key
     * @param array $data Data.
     * @param \Cake\ORM\Association $inverseAssociation Inverse association.
     * @return \Cake\ORM\Query
     * @throws \LogicException Throws an exception if the result of the inner invoked action is not a Query object.
     */
    protected function buildQuery($primaryKey, array $data, Association $inverseAssociation)
    {
        $joinData = !empty($data['joinData']);
        $list = !empty($data['list']);
        $only = (array)Hash::get($data, 'only', []);
        unset($data['joinData'], $data['list'], $data['only']);

        $table = $this->Association->getTarget();
        $query = $this->ListAction->execute($data);
        if (!($query instanceof Query)) {
            $type = is_object($query) ? get_class($query) : gettype($query);

            throw new \LogicException(sprintf('Instance of "%s" expected, got "%s"', Query::class, $type));
        }

        if ($list) {
            $primaryKeyFields = array_map([$table, 'aliasField'], (array)$table->getPrimaryKey());
            $query = $query->select($primaryKeyFields);
        }
        if (!empty($only)) {
            $query = $query->where(function (QueryExpression $exp) use ($table, $only) {
                return $exp->in($table->aliasField($table->getPrimaryKey()), $only);
            });
        }
        if ($this->Association instanceof BelongsToMany && $joinData) {
            $query = $query->select($this->Association->junction());
        }
        if ($this->Association instanceof BelongsToMany || $this->Association instanceof HasMany) {
            $query = $query->order($this->Association->sort());
        }

        $primaryKeyConditions = $this->primaryKeyConditions($inverseAssociation->getTarget(), $primaryKey);

        return $query
            ->enableAutoFields(!$list)
            ->find($this->Association->getFinder())
            ->innerJoinWith($inverseAssociation->getName(), function (Query $query) use ($primaryKeyConditions) {
                return $query->where($primaryKeyConditions);
            })
            ->formatResults(function (CollectionInterface $results) use ($inverseAssociation) {
                return $results->map(function (EntityInterface $entity) use ($inverseAssociation) {
                    if (!($this->Association instanceof BelongsToMany)) {
                        return $entity->setHidden([$inverseAssociation->getProperty()], true);
                    }

                    $joinData = Hash::get($entity, '_matchingData.' . $this->Association->junction()->getAlias());
                    $entity->unsetProperty('_matchingData');
                    $entity->setHidden([$inverseAssociation->getProperty()], true);

                    if (!empty($joinData)) {
                        $entity->set('_joinData', $joinData);
                    }

                    return $entity;
                });
            });
    }

    /**
     * {@inheritDoc}
     *
     * @return \Cake\ORM\Query|\Cake\Datasource\EntityInterface|null
     */
    public function execute(array $data = [])
    {
        $this->checkEntityExists($data);
        $primaryKey = $data['primaryKey'];
        unset($data['primaryKey']);

        $inverseAssociation = $this->buildInverseAssociation();
        $query = $this->buildQuery($primaryKey, $data, $inverseAssociation);

        if ($this->Association instanceof HasOne || $this->Association instanceof BelongsTo) {
            return $query->first();
        }

        return $query;
    }
}
