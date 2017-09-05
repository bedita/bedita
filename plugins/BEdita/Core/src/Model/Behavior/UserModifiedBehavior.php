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

namespace BEdita\Core\Model\Behavior;

use BEdita\Core\Utility\LoggedUser;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;

/**
 * UserModified behavior
 *
 * Set current logged user id in `created_by` field (on new entity creation)
 * and in `modified_by` (on entity update, always) field.
 *
 * @since 4.0.0
 */
class UserModifiedBehavior extends Behavior
{

    /**
     * Default config
     *
     * These are merged with user-provided config when the behavior is used.
     *
     * events - an event-name keyed array of which fields to update, and when, for a given event
     * possible values for when a field will be updated are "always", "new" or "existing", to set
     * the field value always, only when a new record or only when an existing record.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'implementedFinders' => [],
        'implementedMethods' => [
            'userId' => 'userId',
            'touchUser' => 'touchUser',
        ],
        'events' => [
            'Model.beforeSave' => [
                'created_by' => 'new',
                'modified_by' => 'always',
            ],
        ],
    ];

    /**
     * Current user ID.
     *
     * @var int|null
     */
    protected $userId = null;

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        if (isset($config['events'])) {
            $this->setConfig('events', $config['events'], false);
        }
    }

    /**
     * There is only one event handler, it can be configured to be called for any event
     *
     * @param \Cake\Event\Event $event Event instance.
     * @param \Cake\Datasource\EntityInterface $entity Entity instance.
     * @throws \UnexpectedValueException if a field's when value is misdefined
     * @return bool Returns true irrespective of the behavior logic, the save will not be prevented.
     * @throws \UnexpectedValueException When the value for an event is not 'always', 'new' or 'existing'
     */
    public function handleEvent(Event $event, EntityInterface $entity)
    {
        $eventName = $event->getName();
        $events = $this->_config['events'];

        $new = $entity->isNew() !== false;

        foreach ($events[$eventName] as $field => $when) {
            if (!in_array($when, ['always', 'new', 'existing'])) {
                throw new \UnexpectedValueException(
                    sprintf('When should be one of "always", "new" or "existing". The passed value "%s" is invalid', $when)
                );
            }
            if ($when === 'always' ||
                ($when === 'new' && $new) ||
                ($when === 'existing' && !$new)
            ) {
                $this->updateField($entity, $field);
            }
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return array_fill_keys(array_keys($this->_config['events']), 'handleEvent');
    }

    /**
     * Get or set the user ID to be used.
     *
     * Set the user ID to the given ID, or if not passed the current logged user ID.
     *
     * @param int|null $userId Timestamp
     * @return int
     */
    public function userId($userId = null)
    {
        if ($userId) {
            $this->userId = $userId;
        } elseif ($this->userId === null) {
            $this->userId = LoggedUser::id();
        }

        return $this->userId;
    }

    /**
     * Touch an entity
     *
     * Bumps timestamp fields for an entity. For any fields configured to be updated
     * "always" or "existing", update the user ID value. This method will overwrite
     * any pre-existing value.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity instance.
     * @param string $eventName Event name.
     * @return bool true if a field is updated, false if no action performed
     */
    public function touchUser(EntityInterface $entity, $eventName = 'Model.beforeSave')
    {
        $events = $this->_config['events'];
        if (empty($events[$eventName])) {
            return false;
        }

        $return = false;

        foreach ($events[$eventName] as $field => $when) {
            if (in_array($when, ['always', 'existing'])) {
                $return = true;

                $entity->setDirty($field, false);
                $this->updateField($entity, $field);
            }
        }

        return $return;
    }

    /**
     * Update a field, if it hasn't been updated already
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity instance.
     * @param string $field Field name
     * @return void
     */
    protected function updateField($entity, $field)
    {
        if ($entity->isDirty($field)) {
            return;
        }

        $userId = $this->userId();
        if ($userId !== null) {
            $entity->set($field, $this->userId());
        }
    }
}
