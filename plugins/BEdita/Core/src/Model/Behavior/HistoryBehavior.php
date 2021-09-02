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

use BEdita\Core\State\CurrentApplication;
use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\Database\Driver\Postgres;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Utility\Hash;

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
        'table' => 'History',
        'exclude' => [
            'id',
            'type',
            'confirm-password',
        ],
        'obfuscate' => [
            'password' => '*****',
        ],
        'resource_type' => 'objects',
        'implementedFinders' => [
            'historyEditor' => 'findHistoryEditor',
        ],
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
    protected $changed = [];

    /**
     * Retrieve changed properties array
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getChanged()
    {
        return $this->changed;
    }

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        // Behavior config may be set via `Configure` but
        // $config array takes precedence
        $config = array_merge((array)Configure::read('History'), $config);
        $this->setConfig($config, null, false);
        $table = $this->getConfig('table');
        if (!empty($table)) {
            $this->Table = TableRegistry::getTableLocator()->get($table);
            if (!$this->Table->hasBehavior('Timestamp')) {
                $this->Table->addBehavior('Timestamp');
            }
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
        $obfuscate = array_intersect_key(
            (array)$this->getConfig('obfuscate'),
            $this->changed
        );
        $this->changed = array_merge($this->changed, $obfuscate);
    }

    /**
     * Remove from `changed` array properties that are not dirty.
     *
     * @param \Cake\Event\Event $event Fired event.
     * @param \Cake\Datasource\EntityInterface $entity Entity data.
     * @return void
     */
    public function beforeSave(Event $event, EntityInterface $entity): void
    {
        foreach (array_keys($this->changed) as $prop) {
            if (!$entity->isDirty($prop)) {
                unset($this->changed[$prop]);
            }
        }
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
        if (empty($this->Table) || (empty($this->changed) && !$entity->isDirty('deleted'))) {
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
        /** @var \BEdita\Core\Model\Entity\History $history */
        $history = $this->Table->newEntity();
        $history->resource_id = $entity->get('id');
        $history->resource_type = $this->getConfig('resource_type');
        $history->application_id = CurrentApplication::getApplicationId();
        $history->user_id = LoggedUser::id();
        $history->changed = $this->changed;
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

        /** @var \BEdita\Core\Model\Entity\History $history */
        $history = $this->historyEntity($entity);
        $history->user_action = 'remove';
        $this->Table->saveOrFail($history);
    }

    /**
     * Finder for editor objects in history: object created or modifed by a user looking at history data.
     * Logged user id is used if no id is present in options array.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Options containing user id
     * @return \Cake\ORM\Query
     */
    public function findHistoryEditor(Query $query, array $options): Query
    {
        $editorId = Hash::get($options, '0');
        if (empty($editorId)) {
            $editorId = LoggedUser::id();
        }
        $subQuery = $this->Table->find()
            ->select(['object_id' => 'resource_id'])
            ->where(function (QueryExpression $exp) use ($editorId) {
                return $exp
                    ->eq('resource_type', $this->getConfig('resource_type'))
                    ->eq('user_id', $editorId);
            })
            ->distinct();

        return $query->innerJoin(
            ['HistoryItems' => $subQuery],
            function (QueryExpression $exp) use ($query) {
                $field = 'HistoryItems.object_id';
                // On Postgres we need an explicit cast to INTEGER to avoid
                // this error "operator does not exist: character varying = integer"
                if ($query->getConnection()->getDriver() instanceof Postgres) {
                    $field = $query->func()->cast('HistoryItems.object_id', 'INTEGER');
                }

                return $exp->equalFields($field, $this->getTable()->aliasField('id'));
            }
        );
    }
}
