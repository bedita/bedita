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

namespace BEdita\CORE\Model\Behavior;

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
     * @var array
     */
    protected $_defaultConfig = [
        'new' => 'created_by',
        'always' => 'modified_by'
    ];

    /**
     * Setup `created_by` on new BEdita object entity and `modified_by` always
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity to save
     * @return void
     */
    public function setupUserFields(EntityInterface $entity)
    {
        $new = $entity->isNew() !== false;
        if ($new) {
            $field = $this->_config['new'];
            $entity->{$field} = LoggedUser::id();
        }
        $field = $this->_config['always'];
        $entity->{$field} = LoggedUser::id();
    }

    /**
     * Setup `created_by` and `modified_by` fields
     *
     * @param \Cake\Event\Event $event The event dispatched
     * @param \Cake\Datasource\EntityInterface $entity The entity to save
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity)
    {
        $this->setupUserFields($entity);
    }
}
