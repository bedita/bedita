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

use ArrayObject;
use BEdita\Core\ORM\Association\RelatedTo;
use Cake\Datasource\EntityInterface;

/**
 * Command to replace all objects related to another object.
 *
 * @since 4.0.0
 */
class SetRelatedObjectsAction extends UpdateRelatedObjectsAction
{
    use AssociatedTrait;

    /**
     * {@inheritDoc}
     *
     * @return array|false
     */
    protected function update(EntityInterface $entity, $relatedEntities)
    {
        if (!($this->Association instanceof RelatedTo)) {
            $action = new SetAssociatedAction($this->getConfig());

            return $action->execute(compact('entity', 'relatedEntities'));
        }

        $relatedEntities = new ArrayObject($relatedEntities);
        $this->dispatchEvent('Associated.beforeSave', compact('entity', 'relatedEntities') + ['action' => 'set', 'association' => $this->Association]);

        $relatedEntities = $this->diff($entity, $relatedEntities->getArrayCopy(), true, $affectedEntities);
        if (!$this->Association->replaceLinks($entity, $relatedEntities)) {
            return false;
        }
        $this->dispatchEvent('Associated.afterSave', compact('entity', 'relatedEntities') + ['action' => 'set', 'association' => $this->Association]);

        return collection($affectedEntities)
            ->extract($this->Association->getBindingKey())
            ->toList();
    }
}
