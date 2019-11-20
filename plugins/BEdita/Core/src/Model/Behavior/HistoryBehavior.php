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

use BEdita\Core\History\HistoryInterface;
use BEdita\Core\State\CurrentApplication;
use BEdita\Core\Utility\LoggedUser;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\ORM\Behavior;
use LogicException;

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
        'model' => 'BEdita\Core\History\DefaultObjectHistory',
    ];

    /**
     *  History model
     *
     * @var \BEdida\Core\History\HistoryInterface
     */
    public $historyModel;

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
        $model = $this->getConfig('model');
        $this->historyModel = new $model();
        if (!$this->historyModel instanceof HistoryInterface) {
            throw new \LogicException(__d('bedita', 'History model must implement HistoryInterface'));
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
    }

    /**
     * Save user changed properties in history table or other datasource.
     *
     * @param \Cake\Event\Event $event Fired event.
     * @param \Cake\Datasource\EntityInterface $entity Entity.
     * @return void
     */
    public function afterSave(Event $event, EntityInterface $entity): void
    {
        if (empty($this->changed)) {
            return;
        }
        $data = $this->historyData($entity);
        $this->historyModel->addEvent($data);
    }

    /**
     * Retrieve history event data.
     *
     * @param EntityInterface $entity Object entity.
     * @return array
     */
    protected function historyData(EntityInterface $entity): array
    {
        return [
            'object_id' => $entity->get('id'),
            'application_id' => CurrentApplication::getApplicationId(),
            'user_id' => LoggedUser::id(),
            'changed' => $this->changed,
            'created' => Time::now(),
            'user_action' => $this->entityUserAction($entity),
        ];
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
        $data = $this->historyData($entity);
        $data['user_action'] = 'remove';
        $this->historyModel->addEvent($data);
    }
}
