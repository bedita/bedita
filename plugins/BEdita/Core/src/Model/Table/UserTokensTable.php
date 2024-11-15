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

use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\I18n\DateTime;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * User Tokens Model
 *
 * @property \BEdita\Core\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \BEdita\Core\Model\Table\ApplicationsTable|\Cake\ORM\Association\BelongsTo $Applications
 * @method \BEdita\Core\Model\Entity\UserToken get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 * @method \BEdita\Core\Model\Entity\UserToken newEntity(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\UserToken[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\UserToken|false save(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\UserToken patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\UserToken[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\UserToken findOrCreate(\Cake\ORM\Query\SelectQuery|callable|array $search, ?callable $callback = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\UserToken newEmptyEntity()
 * @method \BEdita\Core\Model\Entity\UserToken saveOrFail(\Cake\Datasource\EntityInterface $entity, array $options = [])
 * @method \BEdita\Core\Model\Entity\UserToken[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\UserToken>|false saveMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\UserToken[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\UserToken> saveManyOrFail(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\UserToken[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\UserToken>|false deleteMany(iterable $entities, array $options = [])
 * @method \BEdita\Core\Model\Entity\UserToken[]|\Cake\Datasource\ResultSetInterface<\BEdita\Core\Model\Entity\UserToken> deleteManyOrFail(iterable $entities, array $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UserTokensTable extends Table
{
    /**
     * List of default allowed token types
     *
     * @var array
     */
    public const DEFAULT_TOKEN_TYPES = ['otp', 'refresh', 'recovery', '2fa', 'access'];

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('user_tokens');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Users',
        ]);
        $this->belongsTo('Applications', [
            'foreignKey' => 'application_id',
            'className' => 'BEdita/Core.Applications',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('client_token')
            ->maxLength('client_token', 255)
            ->requirePresence('client_token', 'create')
            ->notEmptyString('client_token');

        $validator
            ->scalar('secret_token')
            ->maxLength('secret_token', 255)
            ->allowEmptyString('secret_token');

        $validator
            ->scalar('token_type')
            ->inList('token_type', $this->getTokenTypes())
            ->requirePresence('token_type', 'create')
            ->notEmptyString('token_type');

        $validator
            ->dateTime('expires')
            ->allowEmptyDateTime('expires');

        $validator
            ->dateTime('used')
            ->allowEmptyDateTime('used');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['application_id'], 'Applications'));

        return $rules;
    }

    /**
     * Return the list of allowed token types merging default with configured.
     *
     * @return array
     */
    public function getTokenTypes(): array
    {
        $confTypes = (array)Configure::read('UserTokens.types');

        return array_unique(array_merge(static::DEFAULT_TOKEN_TYPES, $confTypes));
    }

    /**
     * Finder for valid tokens: tokens not expired and not used
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function findValid(SelectQuery $query): SelectQuery
    {
        $now = DateTime::now();

        return $query
            ->where(function (QueryExpression $exp) use ($now) {
                return $exp->and([
                    $exp->isNull($this->aliasField('used')),
                    $exp->or(function (QueryExpression $exp) use ($now) {
                        $field = $this->aliasField('expires');

                        return $exp
                            ->isNull($field)
                            ->gte($field, $now);
                    }),
                ]);
            });
    }
}
