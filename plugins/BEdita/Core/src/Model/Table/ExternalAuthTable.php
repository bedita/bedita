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

namespace BEdita\Core\Model\Table;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\Utility\LoggedUser;
use Cake\Database\Schema\TableSchema;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ExternalAuth Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $AuthProviders
 *
 * @method \BEdita\Core\Model\Entity\ExternalAuth get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\ExternalAuth newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\ExternalAuth[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ExternalAuth|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\ExternalAuth patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ExternalAuth[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ExternalAuth findOrCreate($search, callable $callback = null, $options = [])
 *
 * @since 4.0.0
 */
class ExternalAuthTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('external_auth');
        $this->setPrimaryKey('id');
        $this->setDisplayField('provider_username');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('AuthProviders', [
            'foreignKey' => 'auth_provider_id',
            'joinType' => 'INNER',
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
            ->naturalNumber('id')
            ->allowEmptyString('id', null, 'create')

            ->requirePresence('provider_username')
            ->notEmptyString('provider_username')

            ->allowEmptyArray('params');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['auth_provider_id'], 'AuthProviders'));

        $rules->add($rules->isUnique(['user_id', 'auth_provider_id']));
        $rules->add($rules->isUnique(['auth_provider_id', 'provider_username']));

        return $rules;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    protected function _initializeSchema(TableSchema $schema)
    {
        $schema->setColumnType('params', 'json');

        return $schema;
    }

    /**
     * Create user before saving if none was set.
     *
     * @param \Cake\Event\Event $event beforeSave event instance.
     * @param \Cake\Datasource\EntityInterface $entity Entity.
     * @return bool
     */
    public function beforeSave(Event $event, EntityInterface $entity)
    {
        if (!$entity->has('user_id')) {
            $authProvider = $this->AuthProviders->get($entity->get($this->AuthProviders->getForeignKey()));
            $username = sprintf('%s-%s', $authProvider->get('slug'), $entity->get('provider_username'));

            $user = $this->Users->newEntity(compact('username'));
            $user->set('roles', $authProvider->getRoles());
            $selfCreated = (LoggedUser::id() === null);
            if ($selfCreated) {
                $user = $user
                    ->set('created_by', 1)
                    ->set('modified_by', 1);
            }
            $user = $this->Users->saveOrFail($user, ['atomic' => false]);
            if ($selfCreated) {
                $user = $this->Users->saveOrFail(
                    $user
                        ->set('created_by', $user->id)
                        ->set('modified_by', $user->id),
                    ['atomic' => false]
                );
            }

            $entity->set($this->Users->getForeignKey(), $user->id);
        }

        return true;
    }

    /**
     * Find external auth by their auth provider.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Additional options.
     * @return \Cake\ORM\Query
     */
    protected function findAuthProvider(Query $query, array $options = [])
    {
        if (empty($options['auth_provider'])) {
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => '"auth_provider" parameter missing',
            ]);
        }

        $authProvider = $options['auth_provider'];
        if (is_string($authProvider)) {
            return $query
                ->innerJoinWith('AuthProviders', function (Query $query) use ($authProvider) {
                    return $query->where([
                        $this->AuthProviders->aliasField('name') => $authProvider,
                    ]);
                });
        }

        if (!empty($authProvider['id'])) {
            $authProvider = $authProvider['id'];
        }

        return $query->where([
            $this->aliasField($this->AuthProviders->getForeignKey()) => $authProvider,
        ]);
    }

    /**
     * Find enabled external auth by user.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Additional options.
     * @return \Cake\ORM\Query
     * @throws \BEdita\Core\Exception\BadFilterException If missing `$options` data
     */
    protected function findUser(Query $query, array $options = []): Query
    {
        if (empty($options['user'])) {
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => '"user" parameter missing',
            ]);
        }

        $user = $options['user'];
        if (!empty($user['id'])) {
            $user = $user['id'];
        }

        return $query
            ->contain('AuthProviders')
            ->innerJoinWith('AuthProviders', function (Query $q) {
                return $q->where(['AuthProviders.enabled' => true]);
            })
            ->where(['ExternalAuth.user_id' => $user]);
    }
}
