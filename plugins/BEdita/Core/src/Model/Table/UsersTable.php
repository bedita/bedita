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

use BEdita\Core\ORM\Inheritance\Table;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\HasMany $ExternalAuth
 * @property \Cake\ORM\Association\BelongsToMany $Roles
 *
 * @since 4.0.0
 */
class UsersTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setPrimaryKey('id');
        $this->setDisplayField('username');

        $this->addBehavior('Timestamp');

        $this->addBehavior('BEdita/Core.DataCleanup');

        $this->addBehavior('BEdita/Core.UserModified');

        $this->hasMany('ExternalAuth', [
            'foreignKey' => 'user_id',
            'className' => 'BEdita/Core.ExternalAuth',
        ]);

        $this->belongsToMany('Roles', [
            'className' => 'BEdita/Core.Roles',
        ]);

        $this->extensionOf('Profiles', [
            'className' => 'BEdita/Core.Profiles'
        ]);

        $this->addBehavior('BEdita/Core.UniqueName', [
            'sourceField' => 'username',
            'prefix' => 'user-'
        ]);

        $this->addBehavior('BEdita/Core.Relations');

        EventManager::instance()->on('Auth.afterIdentify', [$this, 'login']);
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
            ->allowEmpty('id', 'create')

            ->add('username', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])
            ->requirePresence('username', 'create')
            ->notEmpty('username')

            ->allowEmpty('password_hash')

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
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['username']));

        return $rules;
    }

    /**
     * Update last login.
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @return void
     */
    public function login(Event $event)
    {
        $data = $event->getData();

        if (empty($data[0]['id'])) {
            return;
        }

        $this->updateAll(['last_login' => time()], ['id' => $data[0]['id']]);
    }
}
