<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Behavior;

use BEdita\Core\Model\Action\AddRelatedObjectsAction;
use BEdita\Core\Model\Action\RemoveRelatedObjectsAction;
use BEdita\Core\Model\Action\SetRelatedObjectsAction;
use BEdita\Core\Model\Entity\ObjectEntity;
use Cake\ORM\Behavior;

/**
 * Object Model behavior.
 *
 * @since 4.1.0
 */
class ObjectModelBehavior extends Behavior
{
    /**
     * Add behaviors common to all tables implementing an object type model
     *
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $table = $this->getTable();
        $table->addBehavior('BEdita/Core.History');
        $table->addBehavior('Timestamp');
        $table->addBehavior('BEdita/Core.DataCleanup');
        $table->addBehavior('BEdita/Core.UserModified');
        $table->addBehavior('BEdita/Core.CustomProperties');
        $table->addBehavior('BEdita/Core.UniqueName');
        $table->addBehavior('BEdita/Core.Relations');
        $table->addBehavior('BEdita/Core.Searchable', [
            'fields' => [
                'title' => 10,
                'description' => 7,
                'body' => 5,
            ],
        ]);
    }

    /**
     * Add related objects
     *
     * @param \BEdita\Core\Model\Entity\ObjectEntity $entity Object entity
     * @param string $relation Relation name
     * @param array $relatedEntities The related entities
     * @return void
     */
    public function addRelated(ObjectEntity $entity, string $relation, array $relatedEntities): void
    {
        $action = new AddRelatedObjectsAction([
            'association' => $entity->getTable()->associations()->getByProperty($relation)
        ]);
        $action(compact('entity', 'relatedEntities'));
    }

    /**
     * Replace related objects
     *
     * @param \BEdita\Core\Model\Entity\ObjectEntity $entity Object entity
     * @param string $relation Relation name
     * @param array $relatedEntities The related entities
     * @return void
     */
    public function replaceRelated(ObjectEntity $entity, string $relation, array $relatedEntities): void
    {
        $action = new SetRelatedObjectsAction([
            'association' => $entity->getTable()->associations()->getByProperty($relation)
        ]);
        $action(compact('entity', 'relatedEntities'));
    }

    /**
     * Remove related objects
     *
     * @param \BEdita\Core\Model\Entity\ObjectEntity $entity Object entity
     * @param string $relation Relation name
     * @param array $relatedEntities The related entities
     * @return void
     */
    public function removeRelated(ObjectEntity $entity, string $relation, array $relatedEntities): void
    {
        $action = new RemoveRelatedObjectsAction([
            'association' => $entity->getTable()->associations()->getByProperty($relation)
        ]);
        $action(compact('entity', 'relatedEntities'));
    }
}
