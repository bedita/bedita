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

use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Network\Exception\BadRequestException;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;

/**
 * Command to add links between entities.
 *
 * @since 4.0.0
 *
 * @property \Cake\ORM\Association\BelongsToMany|\Cake\ORM\Association\HasMany $Association
 */
class AddAssociatedAction extends UpdateAssociatedAction
{

    /**
     * Find existing join data relative to an entity.
     *
     * @param \Cake\Datasource\EntityInterface $relatedEntity Related entity to find join data for.
     * @param \Cake\Datasource\EntityInterface[] $joinData Loaded join data.
     * @return \Cake\Datasource\EntityInterface|null
     */
    protected function findJoinData(EntityInterface $relatedEntity, array $joinData)
    {
        $primaryKey = array_values($relatedEntity->extract((array)$this->Association->getBindingKey()));
        foreach ($joinData as $joinDatum) {
            $foreignKey = array_values($joinDatum->extract((array)$this->Association->getTargetForeignKey()));
            if ($primaryKey === $foreignKey) {
                return $joinDatum;
            }
        }

        return null;
    }

    /**
     * Filter entities to be actually updated.
     *
     * @param \Cake\Datasource\EntityInterface $entity Source entity.
     * @param \Cake\Datasource\EntityInterface[] $diff Related entities.
     * @return \Cake\Datasource\EntityInterface[]
     */
    protected function patchJoinData(EntityInterface $entity, array $diff)
    {
        if (!($this->Association instanceof BelongsToMany) || empty($diff)) {
            return $diff;
        }

        // Load existing join data.
        $junctionTablePrefix = sprintf('%s.', $this->Association->junction()->getAlias());
        $sourcePrimaryKey = (array)$this->Association->getSource()->getPrimaryKey();
        $bindingKey = (array)$this->Association->getBindingKey();
        $joinData = $this->Association->junction()->find()

            // Apply association-defined conditions.
            ->where(array_filter(
                $this->Association->getConditions(),
                function ($key) use ($junctionTablePrefix) {
                    // Filter only conditions that apply to junction table.
                    return substr($key, 0, strlen($junctionTablePrefix)) === $junctionTablePrefix;
                },
                ARRAY_FILTER_USE_KEY
            ))

            // Build conditions on source entity primary key (can be composite).
            ->where(array_combine(
                (array)$this->Association->getForeignKey(),
                $entity->extract($sourcePrimaryKey)
            ))

            // Build conditions on target entities primary key (can be composite).
            ->where(function (QueryExpression $exp) use ($bindingKey, $diff) {
                $conditions = array_map(
                    function (EntityInterface $relatedEntity) use ($bindingKey) {
                        return function (QueryExpression $exp) use ($bindingKey, $relatedEntity) {
                            $conditions = array_combine(
                                (array)$this->Association->getTargetForeignKey(),
                                $relatedEntity->extract($bindingKey)
                            );
                            foreach ($conditions as $field => $value) {
                                $exp = $exp->eq($field, $value);
                            }

                            return $exp;
                        };
                    },
                    $diff
                );

                return $exp->or_($conditions);
            })

            ->toArray();

        // Patch existing join data with new values.
        foreach ($diff as $relatedEntity) {
            if (!$relatedEntity->has('_joinData')) {
                continue;
            }

            $existing = $this->findJoinData($relatedEntity, $joinData);
            if ($existing === null) {
                continue;
            }

            $new = $relatedEntity->get('_joinData');
            if ($new instanceof EntityInterface) {
                $new = $new->toArray();
            }

            $relatedEntity->set('_joinData', $this->Association->junction()->patchEntity($existing, $new));
        }

        return $diff;
    }

    /**
     * Filter entities to be actually updated.
     *
     * @param \Cake\Datasource\EntityInterface $entity Source entity.
     * @param \Cake\Datasource\EntityInterface[] $relatedEntities Related entities.
     * @return \Cake\Datasource\EntityInterface[]
     */
    protected function diff(EntityInterface $entity, array $relatedEntities)
    {
        $bindingKey = (array)$this->Association->getBindingKey();
        $existing = $this->existing($entity);

        /** @var \Cake\Datasource\EntityInterface[] $diff */
        $diff = [];
        foreach ($relatedEntities as $relatedEntity) {
            $primaryKey = $relatedEntity->extract($bindingKey);
            if (in_array($primaryKey, $existing) && (!($this->Association instanceof BelongsToMany) || !$relatedEntity->has('_joinData'))) {
                continue;
            }

            $diff[] = $relatedEntity;
        }

        $diff = $this->patchJoinData($entity, $diff);

        return $diff;
    }

    /**
     * Add new relations.
     *
     * @param \Cake\Datasource\EntityInterface $entity Source entity.
     * @param \Cake\Datasource\EntityInterface|\Cake\Datasource\EntityInterface[]|null $relatedEntities Related entity(-ies).
     * @return int|false Number of updated relationships, or `false` on failure.
     * @throws \RuntimeException Throws an exception if an unsupported association is passed.
     */
    protected function update(EntityInterface $entity, $relatedEntities)
    {
        if ($this->Association instanceof BelongsToMany || $this->Association instanceof HasMany) {
            if ($relatedEntities === null) {
                $relatedEntities = [];
            } elseif (!is_array($relatedEntities)) {
                $relatedEntities = [$relatedEntities];
            }

            $relatedEntities = $this->prepareRelatedEntities($relatedEntities, $entity);

            return $this->Association->getConnection()->transactional(function () use ($entity, $relatedEntities) {
                $relatedEntities = $this->diff($entity, $relatedEntities);

                if (!$this->Association->link($entity, $relatedEntities, ['atomic' => false])) {
                    return false;
                }
                foreach ($relatedEntities as $relatedEntity) {
                    if ($relatedEntity->has('_joinData') && $relatedEntity->get('_joinData')->getErrors()) {
                        throw new BadRequestException([
                            'title' => __d('bedita', 'Error linking entities'),
                            'detail' => $relatedEntity->get('_joinData')->getErrors(),
                        ]);
                    }
                }

                return count($relatedEntities);
            });
        }

        throw new \RuntimeException(
            __d('bedita', 'Unable to add additional links with association of type "{0}"', get_class($this->Association))
        );
    }
}
