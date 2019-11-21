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

use BEdita\Core\History\HistoryTableRegistry;
use BEdita\Core\State\CurrentApplication;
use BEdita\Core\Utility\LoggedUser;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\ORM\Behavior;

/**
 * History behavior
 * Add history events on object modifications
 *
 * @since 4.1.0
 */
class HistoryBehavior extends Behavior
{
    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'table' => 'ObjectHistory',
        'exclude' => ['type', 'id'],
    ];

    /**
     *  History table
     *
     * @var \Cake\ORM\Table
     */
    public $Table = null;

    /**
     * The changed properties.
     * Array of visible properties changed by user
     *
     * @var array
     */
    public $changed = [];

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        $tableName = $this->getConfig('table');
        if (!empty($tableName)) {
            $this->Table = HistoryTableRegistry::get($tableName);
        }
    }

    /**
     * Collect user changed properties.
     *
     * @param \Cake\Event\Event $event The event dispatched
     * @param \ArrayObject $data The input data being saved
     * @return void
     */
    public function beforeMarshal(Event $event, \ArrayObject $data)
    {
        $this->changed = $data->getArrayCopy();
        $exclude = (array)$this->getConfig('exclude');
        $this->changed = array_diff_key($this->changed, array_flip($exclude));
    }

    /**
     * Save user changed properties in history table or other datasource.
     *
     * @param \Cake\Event\Event $event Fired event.
     * @param \Cake\Datasource\EntityInterface $entity Entity data.
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity): void
    {
        if (!$entity->isDirty('deleted') && (empty($this->changed) || empty($this->Table))) {
            return;
        }
        $history = $this->historyEntity($entity);
        $this->Table->saveOrFail($history);
    }

    /**
     * Retrieve history event data.
     *
     * @param EntityInterface $entity Object entity.
     * @return EntityInterface
     */
    protected function historyEntity(EntityInterface $entity): EntityInterface
    {
        $history = $this->Table->newEntity();
        $history->object_id = $entity->get('id');
        $history->application_id = CurrentApplication::getApplicationId();
        $history->user_id = LoggedUser::id();
        $history->changed = $this->changed;
        $history->created = Time::now();
        $history->user_action = $this->entityUserAction($entity);

        return $history;
    }

    /**
     * See entity user action, can be 'create', 'update', 'trash' or 'restore'
     * 'remove' action defined in in `afterDelete`
     *
     * @param EntityInterface $entity Object entity.
     * @return string
     */
    protected function entityUserAction(EntityInterface $entity): string
    {
        if ($entity->isNew()) {
            return 'create';
        }

        if ($entity->isDirty('deleted')) {
            if ($entity->get('deleted')) {
                return 'trash';
            } else {
                return 'restore';
            }
        }

        return 'update';
    }

    /**
     * Process delete.
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param EntityInterface $entity Object entity.

     * @return void
     */
    public function afterDelete(Event $event, EntityInterface $entity)
    {
        if (empty($this->Table)) {
            return;
        }

        $history = $this->historyEntity($entity);
        $history->user_action = 'remove';
        $this->Table->saveOrFail($history);
    }
}
