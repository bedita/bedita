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

namespace BEdita\Core\Model\Table;

use Cake\Cache\Cache;
use Cake\Event\Event;
use Cake\ORM\Entity;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * RelationTypes Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Relations
 * @property \Cake\ORM\Association\BelongsTo $ObjectTypes
 *
 * @method \BEdita\Core\Model\Entity\RelationType get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\RelationType findOrCreate($search, callable $callback = null, $options = [])
 */
class RelationTypesTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('relation_types');
        $this->setDisplayField('relation_id');
        $this->setPrimaryKey(['relation_id', 'object_type_id', 'side']);

        $this->belongsTo('Relations', [
            'foreignKey' => 'relation_id',
            'joinType' => 'INNER',
            'className' => 'Relations'
        ]);
        $this->belongsTo('ObjectTypes', [
            'foreignKey' => 'object_type_id',
            'joinType' => 'INNER',
            'className' => 'ObjectTypes'
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->inList('side', ['left', 'right'])
            ->notEmpty('side')
            ->requirePresence('side', 'create');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['relation_id', 'object_type_id', 'side']));
        $rules->add($rules->existsIn(['relation_id'], 'Relations'));
        $rules->add($rules->existsIn(['object_type_id'], 'ObjectTypes'));

        return $rules;
    }

    /**
     * Invalidate object types cache after updating a relation's object type.
     *
     * @param \Cake\Event\Event $event Triggered event.
     * @param \Cake\ORM\Entity $entity Subject entity.
     * @return void
     */
    public function afterSave(Event $event, Entity $entity)
    {
        $property = $this->association('ObjectTypes')->getForeignKey();

        $ids = array_unique([$entity->get($property), $entity->getOriginal($property)]);
        foreach ($ids as $id) {
            Cache::delete(ObjectTypesTable::getCacheKey($id), ObjectTypesTable::CACHE_CONFIG);
        }
    }

    /**
     * Invalidate object types cache after deleting a relation's object type.
     *
     * @param \Cake\Event\Event $event Triggered event.
     * @param \Cake\ORM\Entity $entity Subject entity.
     * @return void
     */
    public function afterDelete(Event $event, Entity $entity)
    {
        $property = $this->association('ObjectTypes')->getForeignKey();
        Cache::delete(ObjectTypesTable::getCacheKey($entity->get($property)), ObjectTypesTable::CACHE_CONFIG);
    }
}
