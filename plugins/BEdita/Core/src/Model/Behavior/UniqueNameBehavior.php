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

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\Utility\Text;

/**
 * UniqueName behavior
 *
 * Creates or updates a unique name of objects (see `objects.uname` field).
 *
 * Unique name is created tyipically from object title or from other object properties in case of missing title.
 * An object type may impose custom rule.
 * Name must be unique inside current project.
 *
 * @since 4.0.0
 */
class UniqueNameBehavior extends Behavior
{
    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'sourceField' => 'title',
        'prefix' => '',
        'replacement' => '-',
    ];

    /**
     * Setup unique name of a BEdita object $entity if a new entity is created
     * Unique name is built using a friendly url `slug` version of a `sourceField` (default 'title')
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity to save
     * @return void
     */
    public function uniqueName(EntityInterface $entity)
    {
        $config = $this->config();
        $uname = $entity->get('uname');
        if (empty($uname) && empty($entity->get('id'))) {
            $uname = $config['prefix'] . Text::slug($entity->get($config['sourceField']), $config['replacement']);
        }
        $entity->set('uname', strtolower($uname));
    }

    /**
     * Setup unique name for a BEdita object represented by $entity
     * through `uniqueName()` method
     *
     * @param \Cake\Event\Event $event The event dispatched
     * @param \Cake\Datasource\EntityInterface $entity The entity to save
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity)
    {
        $this->uniqueName($entity);
    }
}
