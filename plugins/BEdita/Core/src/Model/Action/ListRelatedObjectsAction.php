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

use BEdita\Core\Model\Entity\ObjectRelation;
use BEdita\Core\Model\Table\ObjectsTable;
use BEdita\Core\ORM\Association\RelatedTo;
use BEdita\Core\ORM\Inheritance\Table;
use Cake\Core\Configure;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Association;
use Cake\ORM\TableRegistry;

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
    protected function initialize(array $config)
    {
        parent::initialize($config);

        if ($this->Association instanceof RelatedTo) {
            $objectTypes = TableRegistry::getTableLocator()->get('ObjectTypes')
                ->find('byRelation', [
                    'name' => $this->Association->getName(),
                    'side' => 'right',
                ])
                ->contain(['LeftRelations.RightObjectTypes', 'RightRelations.LeftObjectTypes'])
                ->toArray();
            $table = $this->Association->getTarget();
            $objectType = null;
            if (count($objectTypes) === 1) {
                $objectType = current($objectTypes);
                $table->setupRelations($objectType);
            }
            $this->ListAction = new ListObjectsAction(array_filter(compact('table', 'objectType')));
        } elseif (
            $this->Association->getTarget() instanceof ObjectsTable
                || $this->Association->getTarget() instanceof Table
        ) {
            $table = $this->Association->getTarget();
            $this->ListAction = new ListObjectsAction(compact('table'));
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function buildQuery($primaryKey, array $data, Association $inverseAssociation)
    {
        $data += ['joinData' => true];

        $query = parent::buildQuery($primaryKey, $data, $inverseAssociation);

        if (!$this->Association->getTarget()->hasField('object_type_id')) {
            return $query;
        }

        if (!empty($data['list'])) {
            $query = $query->select([
                $this->Association->getTarget()->aliasField('object_type_id'),
            ]);
        }

        return $query->find('publishable');
    }

    /**
     * Prepare `joinData` entity on ObjectRelation associations.
     * If relation is inverse switch between `priority`/`inv_priority`.
     *
     * @param \Cake\Datasource\EntityInterface $joinData Join data entity.
     * @return void
     */
    protected function prepareJoinEntity(EntityInterface $joinData): void
    {
        if (
            !$joinData instanceof ObjectRelation ||
            ($this->Association instanceof RelatedTo && !$this->Association->isInverse())
        ) {
            return;
        }

        $invPriority = $joinData->get('priority');
        $priority = $joinData->get('inv_priority');
        $joinData->set('priority', $priority);
        $joinData->set('inv_priority', $invPriority);
    }
}
