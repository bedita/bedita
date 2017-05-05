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

use ArrayObject;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\TableRegistry;
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
        'separator' => '_',
        'hashlength' => 6
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
        $uname = $entity->get('uname');
        if (empty($uname)) {
            $uname = $this->generateUniqueName($entity);
        }
        while ($this->uniqueNameExists($uname, $entity->get('id'))) {
            $uname = $this->generateUniqueName($entity, [], true);
        }

        $entity->set('uname', $uname);
    }

    /**
     * Generate unique name string from $config parameters.
     * If $regenerate parameter is true, random hash is added to uname string.
     *
     * @param \Cake\Datasource\EntityInterface $entity The entity to save
     * @param array $cfg parameters to create unique name
     * @param bool $regenerate if true it adds hash string to uname
     * @return string uname
     */
    public function generateUniqueName(EntityInterface $entity, array $cfg = [], $regenerate = false)
    {
        $field = !empty($cfg['sourceField']) ? $cfg['sourceField'] : $this->getConfig('sourceField');
        $fieldValue = $entity->get($field);
        if (empty($fieldValue)) {
            $fieldValue = (string)$entity->get('type');
            $regenerate = true;
        }

        return $this->uniqueNameFromValue($fieldValue, $cfg, $regenerate);
    }

    /**
     * Generate unique name string from $config parameters.
     * If $regenerate parameter is true, random hash is added to uname string.
     *
     * @param string $value String to use in unique name creation
     * @param array $cfg parameters to create unique name
     * @param bool $regenerate if true it adds hash string to uname
     * @return string uname
     */
    public function uniqueNameFromValue($value, array $cfg = [], $regenerate = false)
    {
        $config = array_merge($this->getConfig(), $cfg);
        $uname = $config['prefix'] . Text::slug($value, $config['replacement']);
        if ($regenerate) {
            $hash = Text::uuid();
            $hash = str_replace('-', '', $hash);
            if (!empty($config['hashlength'])) {
                $hash = substr($hash, 0, $config['hashlength']);
            }
            $uname .= $config['separator'] . $hash;
        }

        return strtolower($uname);
    }

    /**
     * Verify $uname is unique
     *
     * @param string $uname to check
     * @param int $id object id to exclude from check
     * @return bool
     */
    public function uniqueNameExists($uname, $id = null)
    {
        $options = ['uname' => $uname];
        if (!empty($id)) {
            $options['id <>'] = $id;
        }

        return TableRegistry::get('Objects')->exists($options);
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
