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

namespace BEdita\Core\Model\Action;

use BEdita\Core\ORM\Association\RelatedTo;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Query;
use Cake\ORM\ResultSet;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

/**
 * Command to list associated objects.
 *
 * @since 4.0.0
 */
class ListRelatedObjectsAction extends ListAssociatedAction
{

    /**
     * {@inheritDoc}
     */
    public function execute(array $data = [])
    {
        $data += ['joinData' => true];

        if (!($this->Association instanceof RelatedTo)) {
            return parent::execute($data);
        }

        $source = $this->Association->getSource();
        $conditions = $this->primaryKeyConditions($data['primaryKey']);

        $existing = $source->find()
            ->where($conditions)
            ->count();
        if (!$existing) {
            throw new RecordNotFoundException(__('Record not found in table "{0}"', $source->getTable()));
        }

        $table = $this->Association->getTarget();

        $target = TableRegistry::get('InverseRelation', [
            'className' => $source->getRegistryAlias(),
        ]);
        $target->setTable($source->getTable());

        $options = [
            'sourceTable' => $table,
            'targetTable' => $target,
            'through' => $this->Association->junction()->getRegistryAlias(),
            'foreignKey' => $this->Association->getTargetForeignKey(),
            'targetForeignKey' => $this->Association->getForeignKey(),
            'conditions' => $this->Association->getConditions(),
        ];
        $association = new RelatedTo('InverseRelation', $options);

        $inverseAssociation = $table->associations()->add($association->getName(), $association);

        $objectTypes = TableRegistry::get('ObjectTypes')
            ->find('byRelation', [
                'name' => $this->Association->getName(),
                'side' => 'right',
            ])
            ->toArray();
        if (count($objectTypes) === 1) {
            $objectType = current($objectTypes);
        }

        $action = new ListObjectsAction(compact('table', 'objectType'));
        $query = $action->execute($data);

        if (!empty($data['list'])) {
            $query = $query
                ->select([
                    $table->aliasField($table->getPrimaryKey()),
                    $table->aliasField('object_type_id'),
                ]);
        } else {
            $query = $query->enableAutoFields(true);
        }

        if (!empty($data['only'])) {
            $query = $query->where([
                $table->aliasField($table->getPrimaryKey()) . ' IN' => (array)$data['only'],
            ]);
        }

        return $query
            ->select($this->Association->junction())
            ->innerJoinWith($inverseAssociation->getName(), function (Query $query) use ($data, $inverseAssociation) {
                return $query->where([$inverseAssociation->aliasField('id') => $data['primaryKey']]);
            })
            ->order($this->Association->sort())
            ->formatResults(function (ResultSet $results) {
                return $results->map(function (EntityInterface $entity) {
                    $joinData = Hash::get($entity, '_matchingData.' . $this->Association->junction()->getAlias());
                    $entity->unsetProperty('_matchingData');

                    if (!empty($joinData)) {
                        $entity->set('_joinData', $joinData);
                    }

                    return $entity;
                });
            });
    }
}
