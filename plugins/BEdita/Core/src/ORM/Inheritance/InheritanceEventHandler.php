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

namespace BEdita\Core\ORM\Inheritance;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\Table as CakeTable;

/**
 * Event handler for Class Table Inheritance.
 *
 * @since 4.0.0
 */
class InheritanceEventHandler implements EventListenerInterface
{

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return [
            'Model.beforeSave' => [
                'callable' => 'beforeSave',
                'priority' => 1,
            ],
            'Model.afterDelete' => [
                'callable' => 'afterDelete',
                'priority' => 1,
            ],
        ];
    }

    /**
     * Save entities in inherited tables before saving entity on this table.
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \Cake\Datasource\EntityInterface $entity Entity.
     * @param \ArrayObject $options Save options.
     * @return bool
     */
    public function beforeSave(Event $event, EntityInterface $entity, \ArrayObject $options)
    {
        /* @var \BEdita\Core\ORM\Inheritance\Table $table */
        $table = $event->getSubject();
        $inheritedTable = $table->inheritedTable();
        if ($inheritedTable === null) {
            return true;
        }

        // Prepare parent entity.
        $parentEntity = $inheritedTable->newEntity();
        $parentEntity->isNew($entity->isNew());
        $parentEntity = $this->toParent($entity, $parentEntity, $table, $inheritedTable);
        if (!$parentEntity->isDirty()) {
            $parentEntity->setDirty($inheritedTable->getDisplayField(), true);
        }

        // Save parent entity.
        $options = ['atomic' => false] + $options->getArrayCopy();
        $options['associated'] = array_diff_key(
            $options['associated'],
            array_flip(array_diff($table->associations()->keys(), $inheritedTable->associations()->keys()))
        );
        $parentEntity = $inheritedTable->save($parentEntity, $options);
        if ($parentEntity === false) {
            return false;
        }

        // Copy results from parent entity.
        $this->toDescendant($entity, $parentEntity, $table, $inheritedTable);

        return true;
    }

    /**
     * Delete entities on inherited tables after the entity was deleted.
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \Cake\Datasource\EntityInterface $entity Entity.
     * @param \ArrayObject $options Delete options.
     * @return void
     * @throws \Cake\ORM\Exception\PersistenceFailedException Throws an exception if delete operation on the
     *      parent table fails.
     */
    public function afterDelete(Event $event, EntityInterface $entity, \ArrayObject $options)
    {
        /* @var \BEdita\Core\ORM\Inheritance\Table $table */
        $table = $event->getSubject();
        $inheritedTable = $table->inheritedTable();
        if ($inheritedTable === null) {
            return;
        }

        // Prepare parent entity.
        $parentEntity = $inheritedTable->newEntity();
        $parentEntity->isNew(false);
        $parentEntity = $this->toParent($entity, $parentEntity, $table, $inheritedTable);

        // Delete parent entity.
        // Here we MUST use `saveOrFail`, since simply stopping the event wouldn't abort the delete operation. :(
        $inheritedTable->deleteOrFail($parentEntity, ['atomic' => false] + $options->getArrayCopy());
    }

    /**
     * Copy properties from entity in current table to entity in parent table.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity in current table.
     * @param \Cake\Datasource\EntityInterface $parent Entity in inherited table.
     * @param \Cake\ORM\Table $table Table.
     * @param \Cake\ORM\Table $inheritedTable Inherited table.
     * @return \Cake\Datasource\EntityInterface Entity in inherited table.
     */
    protected function toParent(EntityInterface $entity, EntityInterface $parent, CakeTable $table, CakeTable $inheritedTable)
    {
        $properties = array_diff(
            array_merge(array_keys($entity->toArray()), $entity->getHidden()), // All properties.
            $table->getSchema()->columns() // Remove columns of current table.
        );
        $parent->set($entity->extract($properties), ['guard' => false]); // Copy properties.
        foreach ($properties as $property) {
            $parent->setDirty($property, $entity->isDirty($property));
        }
        if (!$entity->isNew()) {
            $parent->set(
                array_combine(
                    (array)$inheritedTable->getPrimaryKey(),
                    $entity->extract((array)$table->getPrimaryKey())
                ),
                ['guard' => false]
            );
        }

        return $parent;
    }

    /**
     * Copy properties from entity in parent table to entity in current table.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity in current table.
     * @param \Cake\Datasource\EntityInterface $parent Entity in inherited table.
     * @param \Cake\ORM\Table $table Table.
     * @param \Cake\ORM\Table $inheritedTable Inherited table.
     * @return \Cake\Datasource\EntityInterface Entity in current table.
     */
    protected function toDescendant(EntityInterface $entity, EntityInterface $parent, CakeTable $table, CakeTable $inheritedTable)
    {
        $properties = array_merge(array_keys($parent->toArray()), $parent->getHidden()); // All properties.
        $entity->set(array_filter($parent->extract($properties)), ['guard' => false]); // Copy properties.
        if ($entity->isNew()) {
            $entity->set(
                array_combine(
                    (array)$table->getPrimaryKey(),
                    $parent->extract((array)$inheritedTable->getPrimaryKey())
                ),
                ['guard' => false]
            );
        }

        return $entity;
    }
}
