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

namespace BEdita\Core\Model\Behavior;

use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;

/**
 * CustomPropertis behavior
 *
 * @since 4.0.0
 */
class CustomPropertisBehavior extends Behavior
{
    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'field' => 'custom_props',
    ];

    /**
     * The custom properties available.
     * It is an array with properties name as key and Property entity as value
     *
     * @var array
     */
    protected $available = null;

    /**
     * Get available properties for object type
     *
     * @return array
     */
    public function getAvailable()
    {
        if ($this->available !== null) {
            return $this->available;
        }

        try {
            $objectType = TableRegistry::get('ObjectTypes')->get($this->getTable()->getAlias());
            // @todo add cache for properties
            $props = TableRegistry::get('Properties')->find()
                ->where([
                    'object_type_id' => $objectType->id,
                    'enabled' => 1,
                ])
                ->all();
        } catch (\Exception $ex) {
            $props = false;
        }

        if (!$props) {
            return $this->available = [];
        }

        $this->available = $props->combine('name', function ($entity) {
            return $entity;
        })->toArray();

        return $this->available;
    }

    /**
     * Return the default values of available properties
     *
     * @return array
     */
    public function getDefaultValues()
    {
        return array_fill_keys(array_keys($this->getAvailable()), null);
    }

    /**
     * Set custom properties keys as main properties
     *
     * @param \Cake\Event\Event $event The beforeFind event that was fired.
     * @param \Cake\ORM\Query $query Query
     * @return void
     */
    public function beforeFind(Event $event, Query $query)
    {
        $query->formatResults(function ($results) {
            return $results->map(function ($row) {
                return $this->promoteProperties($row);
            });
        });
    }

    /**
     * Promote the properties in configuration `field` to first citizen property.
     * Missing properties in `$entity` but available will be filled with default values.
     *
     * @param \Cake\Datasource\EntityInterface|array $entity The entity or the array to work on
     * @return \Cake\Datasource\EntityInterface|array
     */
    protected function promoteProperties($entity)
    {
        if ((!is_array($entity) && !($entity instanceof EntityInterface)) || !$this->isFieldSet($entity)) {
            return $entity;
        }

        $field = $this->getConfig('field');
        if (empty($entity[$field]) || !is_array($entity[$field])) {
            $entity[$field] = [];
        }
        $entity[$field] = $entity[$field] + $this->getDefaultValues();

        if (empty($entity[$field])) {
            return $entity;
        }

        $customProps = $entity[$field];
        unset($entity[$field]);

        if (is_array($entity)) {
            return array_merge($entity, $customProps);
        }

        $entity->set($customProps, ['guard' => false])->clean();

        return $entity;
    }

    /**
     * Check if configured field containing custom properties is set in `$entity`.
     * For "set" is intended that it is present in `$entity` with any value.
     *
     * @param \Cake\Datasource\EntityInterface|array $entity The entity or the array to check
     * @return bool
     */
    protected function isFieldSet($entity)
    {
        $field = $this->getConfig('field');

        $allProperties = $entity;
        if ($entity instanceof EntityInterface) {
            $allProperties = clone($entity);
            $allProperties->setHidden([]);
            $allProperties = $allProperties->toArray();
        }

        return array_key_exists($field, $allProperties);
    }
}
