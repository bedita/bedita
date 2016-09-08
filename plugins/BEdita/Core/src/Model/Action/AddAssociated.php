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
 * Command to add links between entities.
 *
 * @since 4.0.0
 */
class AddAssociated extends UpdateAssociated
{

    /**
     * Add new relations.
     *
     * @param \Cake\Datasource\EntityInterface $entity Source entity.
     * @param \Cake\Datasource\EntityInterface|\Cake\Datasource\EntityInterface[]|null $relatedEntities Related entity(-ies).
     * @return bool
     * @throws \RuntimeException Throws an exception if an unsupported association is passed.
     */
    public function __invoke(EntityInterface $entity, $relatedEntities)
    {
        if ($this->Association instanceof BelongsToMany || $this->Association instanceof HasMany) {
            if ($relatedEntities === null) {
                $relatedEntities = [];
            } elseif (!is_array($relatedEntities)) {
                $relatedEntities = [$relatedEntities];
            }

            return $this->Association->connection()->transactional(function () use ($entity, $relatedEntities) {
                // @todo: find links diff instead of removing and recreating links
                $this->Association->unlink($entity, $relatedEntities);

                return $this->Association->link($entity, $relatedEntities);
            });
        }

        throw new \RuntimeException(
            __('Unable to add additional links with association of type "{0}"', get_class($this->Association))
        );
    }
}
