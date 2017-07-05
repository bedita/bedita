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
use Cake\Utility\Hash;

/**
 * Command to add relations between objects.
 *
 * @since 4.0.0
 */
class AddRelatedObjectsAction extends UpdateRelatedObjectsAction
{

    /**
     * Filter entities to be actually updated.
     *
     * @param \Cake\Datasource\EntityInterface $entity Source entity.
     * @param \Cake\Datasource\EntityInterface[] $relatedEntities Related entities.
     * @return \Cake\Datasource\EntityInterface[]
     */
    protected function diff(EntityInterface $entity, $relatedEntities)
    {
        $bindingKey = $this->Association->getBindingKey();
        $existing = $this->existing($entity)->indexBy($this->Association->getTargetForeignKey())->toArray();

        $junctionEntityClass = $this->Association->junction()->getEntityClass();
        $ignoredKeys = array_flip(
            [$this->Association->getForeignKey(), $this->Association->getTargetForeignKey()]
        );

        $diff = [];
        foreach ($relatedEntities as $relatedEntity) {
            $primaryKey = $relatedEntity->get($bindingKey);
            if (!array_key_exists($primaryKey, $existing)) {
                // Relation was missing.
                $diff[] = $relatedEntity;

                continue;
            }

            // Obtain new join data.
            $joinData = $this->Association->junction()->newEntity();
            if (!empty($relatedEntity->_joinData) && $relatedEntity->_joinData instanceof $junctionEntityClass) {
                $joinData = $relatedEntity->_joinData;
            }

            // Extract meaningful values from both new and existing.
            $joinData = array_diff_key($joinData->getOriginalValues(), $ignoredKeys);
            $existingJoinData = array_diff_key($existing[$primaryKey]->getOriginalValues(), $ignoredKeys);

            // Compare existing and new join data.
            ksort($joinData);
            ksort($existingJoinData);
            if ($joinData !== $existingJoinData) {
                $diff[] = $relatedEntity;
            }
        }

        return $diff;
    }

    /**
     * {@inheritDoc}
     *
     * @return array|false
     */
    protected function update(EntityInterface $entity, $relatedEntities)
    {
        if (!($this->Association instanceof RelatedTo)) {
            $action = new AddAssociatedAction($this->getConfig());

            return $action->execute(compact('entity', 'relatedEntities'));
        }

        $relatedEntities = $this->prepareRelatedEntities($relatedEntities, $entity);

        return $this->Association->getConnection()->transactional(function () use ($entity, $relatedEntities) {
            $relatedEntities = $this->diff($entity, $relatedEntities);

            if (!$this->Association->link($entity, $relatedEntities)) {
                return false;
            }

            return Hash::extract($relatedEntities, sprintf('{n}.%s', $this->Association->getBindingKey()));
        });
    }
}
