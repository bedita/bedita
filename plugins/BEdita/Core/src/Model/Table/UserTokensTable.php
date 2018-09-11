<?php
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

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * User Tokens Model
 *
 * @property \BEdita\Core\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
 * @property \BEdita\Core\Model\Table\ApplicationsTable|\Cake\ORM\Association\BelongsTo $Applications
 *
 * @method \BEdita\Core\Model\Entity\UserToken get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\UserToken newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\UserToken[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\UserToken|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\UserToken patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\UserToken[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\UserToken findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UserTokensTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('user_tokens');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
            'className' => 'BEdita/Core.Users'
        ]);
        $this->belongsTo('Applications', [
            'foreignKey' => 'application_id',
            'className' => 'BEdita/Core.Applications'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->scalar('client_token')
            ->maxLength('client_token', 255)
            ->requirePresence('client_token', 'create')
            ->notEmpty('client_token');

        $validator
            ->scalar('secret_token')
            ->maxLength('secret_token', 255)
            ->allowEmpty('secret_token');

        $validator
            ->scalar('token_type')
            ->requirePresence('token_type', 'create')
            ->notEmpty('token_type');

        $validator
            ->dateTime('expires')
            ->allowEmpty('expires');

        $validator
            ->dateTime('used')
            ->allowEmpty('used');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['application_id'], 'Applications'));

        return $rules;
    }
}