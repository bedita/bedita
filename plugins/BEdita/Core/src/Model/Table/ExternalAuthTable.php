<?php
declare(strict_types=1);

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
use BEdita\Core\Model\Entity\AuthProvider;
use BEdita\Core\Utility\LoggedUser;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * ExternalAuth Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @property \Cake\ORM\Association\BelongsTo $AuthProviders
 * @method \BEdita\Core\Model\Entity\ExternalAuth get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\ExternalAuth newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ExternalAuth[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ExternalAuth|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\ExternalAuth patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ExternalAuth[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\ExternalAuth findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\ExternalAuth newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\ExternalAuth saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\ExternalAuth[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\ExternalAuth>|false saveMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\ExternalAuth[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\ExternalAuth> saveManyOrFail(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\ExternalAuth[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\ExternalAuth>|false deleteMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\ExternalAuth[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\ExternalAuth> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @since 4.0.0
 */
class ExternalAuthTable extends Table
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('external_auth');
        $this->setPrimaryKey('id');
        $this->setDisplayField('provider_username');
        $this->getSchema()->setColumnType('params', 'json');

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
    public function validationDefault(Validator $validator): Validator
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
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['auth_provider_id'], 'AuthProviders'));

        $rules->add($rules->isUnique(['user_id', 'auth_provider_id']));
        $rules->add($rules->isUnique(['auth_provider_id', 'provider_username']));

        return $rules;
    }

    /**
     * Create user before saving if none was set.
     *
     * @param \Cake\Event\EventInterface $event beforeSave event instance.
     * @param \Cake\Datasource\EntityInterface $entity Entity.
     * @return void
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity): void
    {
        if (!$entity->has('user_id')) {
            /** @var \BEdita\Core\Model\Entity\AuthProvider $authProvider*/
            $authProvider = $this->AuthProviders->get($entity->get($this->AuthProviders->getForeignKey()));
            $username = sprintf('%s-%s', $authProvider->get('slug'), $entity->get('provider_username'));

            $user = $this->Users->newEntity(compact('username'));
            $user->set('roles', $authProvider->getRoles());
            // set user status using auth provider params
            $user->set('status', Hash::get((array)$authProvider->params, 'status', 'draft'));
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
                    ['atomic' => false],
                );
            }

            $entity->set($this->Users->getForeignKey(), $user->id);
        }

        $event->setResult(true);
    }

    /**
     * Find external auth by their auth provider.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param \BEdita\Core\Model\Entity\AuthProvider|array|string|int $authProvider Auth provider data.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function findAuthProvider(SelectQuery $query, AuthProvider|array|string|int $authProvider): SelectQuery
    {
        if (empty($authProvider)) {
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => '"authProvider" can not be empty',
            ]);
        }

        if (is_string($authProvider)) {
            return $query
                ->innerJoinWith('AuthProviders', function (SelectQuery $query) use ($authProvider) {
                    return $query->where([
                        $this->AuthProviders->aliasField('name') => $authProvider,
                    ]);
                });
        }

        if (!is_int($authProvider) && !empty($authProvider['id'])) {
            $authProvider = $authProvider['id'];
        }

        return $query->where([
            $this->aliasField($this->AuthProviders->getForeignKey()) => $authProvider,
        ]);
    }

    /**
     * Find enabled external auth by user.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param array|string|int $user The user data.
     * @return \Cake\ORM\Query\SelectQuery
     * @throws \BEdita\Core\Exception\BadFilterException If missing `$options` data
     */
    protected function findUser(SelectQuery $query, array|string|int $user): SelectQuery
    {
        if (empty($user)) {
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => '"user" can not be empty',
            ]);
        }

        if (!empty($user['id'])) {
            $user = $user['id'];
        }

        return $query
            ->contain('AuthProviders')
            ->innerJoinWith('AuthProviders', function (SelectQuery $q) {
                return $q->where(['AuthProviders.enabled' => true]);
            })
            ->where(['ExternalAuth.user_id' => $user]);
    }
}
