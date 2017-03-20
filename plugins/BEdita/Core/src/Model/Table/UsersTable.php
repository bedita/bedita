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

use BEdita\Core\Model\Validation\UsersValidator;
use BEdita\Core\ORM\Inheritance\Table;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\ORM\RulesChecker;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\HasMany $ExternalAuth
 * @property \Cake\ORM\Association\BelongsToMany $Roles
 *
 * @method \BEdita\Core\Model\Entity\User get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 *
 * @since 4.0.0
 */
class UsersTable extends Table
{

    /**
     * {@inheritDoc}
     */
    protected $_validatorClass = UsersValidator::class;

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
        ]);

        $this->belongsToMany('Roles', [
            'through' => 'RolesUsers',
        ]);

        $this->extensionOf('Profiles');

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
