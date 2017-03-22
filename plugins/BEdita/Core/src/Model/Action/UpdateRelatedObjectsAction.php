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

use Cake\Datasource\EntityInterface;

/**
 * Abstract class for updating relations between BEdita objects.
 *
 * @since 4.0.0
 *
 * @property \BEdita\Core\ORM\Association\RelatedTo $Association
 */
abstract class UpdateRelatedObjectsAction extends UpdateAssociatedAction
{

    /**
     * {@inheritDoc}
     *
     * @return \Cake\ORM\Query
     */
    protected function existing(EntityInterface $entity)
    {
        $jointTable = $this->Association->junction();

        $sourcePrimaryKey = $entity->extract((array)$this->Association->getSource()->getPrimaryKey());
        $foreignKey = array_map([$jointTable, 'aliasField'], (array)$this->Association->getForeignKey());

        $existing = $jointTable->find()
            ->where($this->Association->getConditions())
            ->andWhere(array_combine($foreignKey, $sourcePrimaryKey));

        return $existing;
    }

    /**
     * Prepare related entities.
     *
     * @param \Cake\Datasource\EntityInterface|\Cake\Datasource\EntityInterface[]|null $relatedEntities Related entities.
     * @return \Cake\Datasource\EntityInterface[]
     */
    protected function prepareRelatedEntities($relatedEntities)
    {
        if ($relatedEntities === null) {
            return [];
        }

        $junction = $this->Association->junction();

        $conditions = $this->Association->getConditions();
        $prefix = sprintf('%s.', $junction->getAlias());
        $extraFields = [];
        foreach ($conditions as $field => $value) {
            if (substr($field, 0, strlen($prefix)) === $prefix) {
                $field = substr($field, strlen($prefix));
            }

            $extraFields[$field] = $value;
        }

        if (!is_array($relatedEntities)) {
            $relatedEntities = [$relatedEntities];
        }
        $junctionEntityClass = $junction->getEntityClass();
        array_walk(
            $relatedEntities,
            function (EntityInterface $entity) use ($extraFields, $junction, $junctionEntityClass) {
                if (empty($entity->_joinData) || !($entity->_joinData instanceof $junctionEntityClass)) {
                    $entity->_joinData = $junction->newEntity();
                }

                $entity->_joinData->set($extraFields, ['guard' => false]);
            }
        );

        return $relatedEntities;
    }
}
