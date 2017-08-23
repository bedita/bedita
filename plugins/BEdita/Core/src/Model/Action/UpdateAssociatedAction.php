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

use Cake\Datasource\EntityInterface;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Query;

/**
 * Abstract class for updating associations between entities.
 *
 * @since 4.0.0
 */
abstract class UpdateAssociatedAction extends BaseAction
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
     * Find existing associations.
     *
     * @param \Cake\Datasource\EntityInterface $entity Source entity.
     * @return array|null
     */
    protected function existing(EntityInterface $entity)
    {
        $list = new ListAssociatedAction(['association' => $this->Association]);
        $sourcePrimaryKey = (array)$this->Association->getSource()->getPrimaryKey();
        $bindingKey = (array)$this->Association->getBindingKey();

        $existing = $list(['primaryKey' => $entity->extract($sourcePrimaryKey), 'list' => true]);

        if ($existing instanceof EntityInterface) {
            return $existing
                ->extract($bindingKey);
        }

        if (!($existing instanceof Query)) {
            return null;
        }

        return $existing
            ->map(function (EntityInterface $relatedEntity) use ($bindingKey) {
                return $relatedEntity->extract($bindingKey);
            })
            ->toArray();
    }

    /**
     * Prepare related entities.
     *
     * @param \Cake\Datasource\EntityInterface|\Cake\Datasource\EntityInterface[]|null $relatedEntities Related entities.
     * @param \Cake\Datasource\EntityInterface $sourceEntity Source entity.
     * @return \Cake\Datasource\EntityInterface[]
     */
    protected function prepareRelatedEntities($relatedEntities, EntityInterface $sourceEntity)
    {
        if ($relatedEntities === null) {
            return [];
        }

        if (!($this->Association instanceof BelongsToMany)) {
            return $relatedEntities;
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
            function (EntityInterface $entity) use ($sourceEntity, $extraFields, $junction, $junctionEntityClass) {
                if (empty($entity->_joinData) || !($entity->_joinData instanceof $junctionEntityClass)) {
                    if (empty($extraFields)) {
                        return;
                    }
                    $entity->_joinData = $junction->newEntity();
                }

                $joinData = $extraFields;
                $joinData += array_combine(
                    (array)$this->Association->getTargetForeignKey(),
                    $entity->extract((array)$this->Association->getPrimaryKey())
                );
                $joinData += array_combine(
                    (array)$this->Association->getForeignKey(),
                    $sourceEntity->extract((array)$this->Association->getSource()->getPrimaryKey())
                );

                $entity->_joinData->set($joinData, ['guard' => false]);
            }
        );

        return $relatedEntities;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(array $data = [])
    {
        return $this->update($data['entity'], $data['relatedEntities']);
    }

    /**
     * Perform update.
     *
     * @param \Cake\Datasource\EntityInterface $entity Source entity.
     * @param \Cake\Datasource\EntityInterface|\Cake\Datasource\EntityInterface[]|null $relatedEntities Related entity(-ies).
     * @return int|false
     */
    abstract protected function update(EntityInterface $entity, $relatedEntities);
}
