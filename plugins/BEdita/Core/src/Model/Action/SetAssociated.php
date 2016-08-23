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
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Association\HasOne;

/**
 * Command to replace all entities associated to another entity.
 *
 * @since 4.0.0
 */
class SetAssociated extends UpdateAssociated
{

    /**
     * Replace existing relations.
     *
     * @param \Cake\Datasource\EntityInterface $entity Source entity.
     * @param \Cake\Datasource\EntityInterface|\Cake\Datasource\EntityInterface[]|null $relatedEntities Related entity(-ies).
     * @return bool
     * @throws \RuntimeException Throws an exception if an unsupported association is passed.
     */
    public function __invoke(EntityInterface $entity, $relatedEntities)
    {
        if ($this->Association instanceof BelongsToMany || $this->Association instanceof HasMany) {
            $relatedEntities = $this->prepareEntities($relatedEntities, true);

            if ($this->Association instanceof HasMany) {
                return $this->Association->replace($entity, $relatedEntities);
            }

            return $this->Association->replaceLinks($entity, $relatedEntities);
        }

        $relatedEntities = $this->prepareEntities($relatedEntities, false);

        if ($this->Association instanceof BelongsTo) {
            $entity[$this->Association->property()] = $relatedEntities;

            return (bool)$this->Association->source()->save($entity);
        }

        if ($this->Association instanceof HasOne) {
            $relatedEntities[$this->Association->foreignKey()] = $entity;

            return (bool)$this->Association->target()->save($relatedEntities);
        }

        throw new \RuntimeException(__('Unknown association of type "{0}"', get_class($this->Association)));
    }

    /**
     * Prepare related entities.
     *
     * @param \Cake\Datasource\EntityInterface|\Cake\Datasource\EntityInterface[]|null $relatedEntities Related entity(-ies).
     * @param bool $multiple Are multiple entities expected?
     * @return array|null
     * @throws \InvalidArgumentException Throws an exception if multiple entities are not supported, and a list is passed.
     */
    protected function prepareEntities($relatedEntities, $multiple)
    {
        if ($relatedEntities === null) {
            return $multiple ? [] : null;
        }

        if (!$multiple && !($relatedEntities instanceof EntityInterface)) {
            throw new \InvalidArgumentException(__('Unable to link multiple entities'));
        }

        if ($multiple && !is_array($relatedEntities)) {
            return [$relatedEntities];
        }

        return $relatedEntities;
    }
}
