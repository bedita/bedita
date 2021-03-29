<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
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
use Cake\Event\EventDispatcherTrait;

/**
 * Command to remove links between related objects or objects and associated entities.
 *
 * @since 4.0.0
 *
 * @property \Cake\ORM\Association\BelongsToMany|\Cake\ORM\Association\HasMany $Association
 */
class RemoveRelatedObjectsAction extends UpdateRelatedObjectsAction
{
    use EventDispatcherTrait;

    /**
     * Remove existing relations using `\BEdita\Core\Model\Action\RemoveAssociatedAction`.
     *
     * @param \Cake\Datasource\EntityInterface $entity Source entity.
     * @param \Cake\Datasource\EntityInterface|\Cake\Datasource\EntityInterface[]|null $relatedEntities Related entity(-ies).
     * @return int|false Number of updated relationships, or `false` on failure.
     */
    protected function update(EntityInterface $entity, $relatedEntities)
    {
        $action = new RemoveAssociatedAction($this->getConfig());
        $action->setEventManager($this->getEventManager());

        return $action->execute(compact('entity', 'relatedEntities'));
    }
}
