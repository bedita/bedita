<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Table;

use BEdita\Core\Search\SimpleSearchTrait;
use BEdita\Core\Utility\LoggedUser;
use Cake\Database\Schema\TableSchemaInterface;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Annotations Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Objects
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @method \BEdita\Core\Model\Entity\Annotation get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\Annotation newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\Annotation[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Annotation|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\Annotation patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Annotation[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\Annotation findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @mixin \BEdita\Core\Model\Behavior\UserModifiedBehavior
 */
class AnnotationsTable extends Table
{
    use SimpleSearchTrait;

    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('annotations');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
        $this->addBehavior('BEdita/Core.UserModified', [
            'events' => [
                'Model.beforeSave' => [
                    'user_id' => 'new',
                ],
            ],
        ]);
        $this->addBehavior('BEdita/Core.Searchable');

        $this->belongsTo('Objects', [
            'foreignKey' => 'object_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Objects',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Users',
        ]);

        $this->setupSimpleSearch(['fields' => ['description']]);
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->integer('object_id')
            ->requirePresence('object_id', 'create')
            ->notEmptyString('object_id');

        $validator
            ->allowEmptyString('description');

        $validator
            ->allowEmptyArray('params');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['object_id'], 'Objects'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getSchema(): TableSchemaInterface
    {
        return parent::getSchema()->setColumnType('params', 'json');
    }

    /**
     * Before save checks:
     *  - `user_id` must match LoggedUser::id() on entity update
     *  - `object_id` cannot be modified
     *
     * @param \Cake\Event\EventInterface $event The beforeSave event that was fired
     * @param \Cake\Datasource\EntityInterface $entity the entity that is going to be saved
     * @return void
     * @throws \BEdita\Core\Exception\ForbiddenException on save check failure
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity)
    {
        if (!$entity->isNew() && $entity->get('user_id') !== LoggedUser::id()) {
            throw new ForbiddenException(
                __d(
                    'bedita',
                    'Could not change annotation "{0}" of user "{1}"',
                    $entity->get('id'),
                    $entity->get('user_id')
                )
            );
        }
        if (!$entity->isNew() && $entity->isDirty('object_id')) {
            throw new ForbiddenException(
                __d(
                    'bedita',
                    'Could not change object id on annotation "{0}"',
                    $entity->get('id')
                )
            );
        }
    }

    /**
     * Before delete checks: `user_id` must match LoggedUser::id()
     *
     * @param \Cake\Event\EventInterface $event The beforeSave event that was fired
     * @param \Cake\Datasource\EntityInterface $entity the entity that is going to be saved
     * @return void
     * @throws \BEdita\Core\Exception\ForbiddenException on delete check failure
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity)
    {
        if ($entity->get('user_id') !== LoggedUser::id()) {
            throw new ForbiddenException(
                __d(
                    'bedita',
                    'Could not delete annotation "{0}" of user "{1}"',
                    $entity->get('id'),
                    $entity->get('user_id')
                )
            );
        }
    }
}
