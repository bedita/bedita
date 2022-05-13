<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
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
use Cake\ORM\Entity;
use Cake\Utility\Hash;

/**
 * Behavior to manage priorities.
 *
 * @since 4.0.0
 */
class PriorityBehavior extends Behavior
{
    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'fields' => [],
    ];

    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $defaultConfig = (array)$this->getConfig('fields._all');
        $defaultConfig += [
            'scope' => false,
        ];

        $fields = Hash::normalize($this->getConfig('fields'));
        unset($fields['_all']);
        foreach ($fields as $field => &$config) {
            $config = (array)$config + $defaultConfig;
        }
        unset($config);

        $this->setConfig('fields', $fields, false);
    }

    /**
     * Set up priorities before an entity is saved.
     * Use current max value + 1 if not set.
     * New values will start at 1.
     * Other priorities are shifted on priority change.
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \Cake\Datasource\EntityInterface $entity Entity instance.
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity)
    {
        $fields = $this->getConfig('fields');
        foreach ($fields as $field => $config) {
            if (empty($config['scope'])) {
                continue;
            }

            $conditions = $this->_getConditions($entity, $config['scope']);
            if (!empty($entity->get($field))) {
                if ($entity instanceof Entity) {
                    $actualValue = $entity->get($field);
                    $previousValue = $entity->getOriginal($field);

                    if ($previousValue < $actualValue) {
                        $this->compact($field, $previousValue, $actualValue, $conditions);
                    } elseif ($previousValue > $actualValue) {
                        $this->expand($field, $actualValue, $previousValue, $conditions);
                    }
                }
                continue;
            }

            $maxValue = $this->maxValue($field, $conditions);
            $entity->set($field, $maxValue + 1);
        }
    }

    /**
     * Compact other priorities before the entity is deleted.
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \Cake\Datasource\EntityInterface $entity Entity instance.
     * @return void
     */
    public function beforeDelete(Event $event, EntityInterface $entity)
    {
        $fields = $this->getConfig('fields');
        foreach ($fields as $field => $config) {
            if (empty($entity->get($field)) || empty($config['scope'])) {
                continue;
            }

            $conditions = $this->_getConditions($entity, $config['scope']);
            $this->compact($field, $entity->get($field), null, $conditions);
        }
    }

    /**
     * Get scope conditions from entity.
     *
     * @param \Cake\Datasource\EntityInterface $entity Entity instance.
     * @param string[] $scope A list of scope fields.
     * @return array A list of conditions.
     */
    protected function _getConditions(EntityInterface $entity, array $scope): array
    {
        $conditions = [];
        foreach ($scope as $item) {
            $keyField = sprintf('%s IS', $this->getTable()->aliasField($item));
            $conditions[$keyField] = $entity->get($item);
        }

        return $conditions;
    }

    /**
     * Create a gap in the priority list where an item can be inserted or moved.
     *
     * @param string $field Field name.
     * @param int $from The initial priority value to update.
     * @param int|null $to The final priority value to update.
     * @param array $conditions A list of scope conditions.
     * @return void
     */
    protected function expand(string $field, int $from, ?int $to = null, array $conditions = []): void
    {
        $conditions = $conditions + ["{$field} >=" => $from];
        if ($to !== null) {
            $conditions["{$field} <"] = $to;
        }

        $this->getTable()->updateAll([sprintf('%s = %1$s + 1', $field)], $conditions);
    }

    /**
     * Compact priority values.
     *
     * @param string $field Field name.
     * @param int $from The initial priority value to update.
     * @param int|null $to The final priority value to update.
     * @param array $conditions A list of scope conditions.
     * @return void
     */
    protected function compact(string $field, int $from, ?int $to = null, array $conditions = []): void
    {
        $conditions = $conditions + ["{$field} >" => $from];
        if ($to !== null) {
            $conditions["{$field} <="] = $to;
        }

        $this->getTable()->updateAll([sprintf('%s = %1$s - 1', $field)], $conditions);
    }

    /**
     * Get current max priority on an object relation
     *
     * @param string $field Priority field name.
     * @param array $conditions Scope conditions.
     * @return int
     */
    protected function maxValue(string $field, array $conditions): int
    {
        $query = $this->getTable()->find()->where($conditions);
        $query->select([
            'max_value' => $query->func()->max($this->getTable()->aliasField($field)),
        ]);

        return (int)Hash::get($query->toArray(), '0.max_value');
    }
}
