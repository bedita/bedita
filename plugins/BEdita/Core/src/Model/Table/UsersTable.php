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

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\HasMany $ExternalAuth
 *
 * @since 4.0.0
 */
class UsersTable extends Table
{

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('users');
        $this->primaryKey('id');
        $this->displayField('username');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always',
                ],
                'Users.login' => [
                    'last_login' => 'always',
                ],
                'Users.loginError' => [
                    'last_login_err' => 'always',
                ],
            ],
        ]);

        $this->hasMany('ExternalAuth', [
            'foreignKey' => 'user_id',
            'className' => 'BEdita/Core.ExternalAuth',
        ]);

        $this->belongsToMany('Roles', [
            'className' => 'BEdita/Core.Roles',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->naturalNumber('id')
            ->allowEmpty('id', 'create')

            ->add('username', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])
            ->requirePresence('username')
            ->notEmpty('username')

            ->allowEmpty('password')

            ->boolean('blocked')
            ->allowEmpty('blocked')

            ->dateTime('last_login')
            ->allowEmpty('last_login')

            ->dateTime('last_login_err')
            ->allowEmpty('last_login_err')

            ->naturalNumber('num_login_err')
            ->allowEmpty('num_login_err');

        return $validator;
    }

    /**
     * {@inheritDoc}
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['username']));

        return $rules;
    }
}
