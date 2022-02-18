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
use Cake\Utility\Hash;

/**
 * Behavior to manage priorities.
 *
 * @since 4.0.0
 */
class PriorityBehavior extends Behavior
{
    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'fields' => [],
    ];

    /**
     * {@inheritDoc}
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
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \Cake\Datasource\EntityInterface $entity Entity instance.
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity)
    {
        $fields = $this->getConfig('fields');

        foreach ($fields as $field => $config) {
            if (!empty($entity->get($field)) || empty($config['scope'])) {
                continue;
            }

            $maxValue = $this->maxValue($entity, $field, (array)$config['scope']);
            $entity->set($field, $maxValue + 1);
        }
    }

    /**
     * Get current max priority on an object relation
     *
     * @param EntityInterface $entity Entity being saved
     * @param string $field Priority field name.
     * @param array $scope Priority scope.
     * @return int
     */
    protected function maxValue(EntityInterface $entity, string $field, array $scope): int
    {
        $conditions = [];
        foreach ($scope as $item) {
            $conditions[$this->getTable()->aliasField($item)] = $entity->get($item);
        }
        $query = $this->getTable()->find()->where($conditions);
        $query->select([
            'max_value' => $query->func()->max($this->getTable()->aliasField($field)),
        ]);

        return (int)Hash::get($query->toArray(), '0.max_value');
    }

    /**
     * Compact other priorities before the entity is deleted.
     *
     * @todo Implement this method.
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \Cake\Datasource\EntityInterface $entity Entity instance.
     * @return void
     * @codeCoverageIgnore
     */
    public function beforeDelete(Event $event, EntityInterface $entity)
    {
        return;
    }
}
