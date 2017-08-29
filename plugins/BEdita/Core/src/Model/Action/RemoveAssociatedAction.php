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
use Cake\ORM\Association\HasMany;

/**
 * Command to remove links between entities.
 *
 * @since 4.0.0
 *
 * @property \Cake\ORM\Association\BelongsToMany|\Cake\ORM\Association\HasMany $Association
 */
class RemoveAssociatedAction extends UpdateAssociatedAction
{

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

        $diff = [];
        foreach ($relatedEntities as $relatedEntity) {
            $primaryKey = $relatedEntity->extract($bindingKey);
            if (!in_array($primaryKey, $existing)) {
                continue;
            }

            $diff[] = $relatedEntity;
        }

        return $diff;
    }

    /**
     * Remove existing relations.
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

            return $this->Association->getConnection()->transactional(function () use ($entity, $relatedEntities) {
                $relatedEntities = $this->diff($entity, $relatedEntities);

                $this->Association->unlink($entity, $relatedEntities);

                return count($relatedEntities);
            });
        }

        throw new \RuntimeException(
            __d('bedita', 'Unable to remove existing links with association of type "{0}"', get_class($this->Association))
        );
    }
}
