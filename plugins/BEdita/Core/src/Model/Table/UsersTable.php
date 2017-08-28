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
use BEdita\Core\Utility\LoggedUser;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

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
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
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

        $this->addBehavior('BEdita/Core.CustomProperties');

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
     * Signup validation
     *
     * @param \Cake\Validation\Validator $validator The validator
     * @return \Cake\Validation\Validator
     * @codeCoverageIgnore
     */
    public function validationSignup(Validator $validator)
    {
        $validator = $this->validationDefault($validator);

        $validator
            ->email('email')
            ->requirePresence('email')
            ->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])

            ->requirePresence('password_hash')
            ->notEmpty('password_hash');

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
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function implementedEvents()
    {
        $implementedEvents = parent::implementedEvents();
        $implementedEvents += [
            'Auth.externalAuth' => 'externalAuthLogin',
            'Auth.afterIdentify' => 'login',
        ];

        return $implementedEvents;
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

        $id = $data[0]['id'];
        $this->updateAll(
            [
                'last_login' => $this->timestamp(),
            ],
            compact('id')
        );
    }

    /**
     * Create external auth record for this user.
     *
     * @param \Cake\Event\Event $event Dispatched event.
     * @param \Cake\Datasource\EntityInterface $authProvider Auth provider entity.
     * @param string $username Provider username.
     * @return \Cake\Datasource\EntityInterface|bool
     */
    public function externalAuthLogin(Event $event, EntityInterface $authProvider, $username)
    {
        $params = $event->getData('params');
        $externalAuth = $this->ExternalAuth->newEntity([
            'auth_provider_id' => $authProvider->id,
            'provider_username' => $username,
            'params' => $params,
        ]);

        return $this->ExternalAuth->save($externalAuth);
    }

    /**
     * Find users by their external auth providers.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Additional options.
     * @return \Cake\ORM\Query
     */
    protected function findExternalAuth(Query $query, array $options = [])
    {
        return $query->innerJoinWith('ExternalAuth', function (Query $query) use ($options) {
            $query = $query->find('authProvider', $options);
            if (!empty($options['username'])) {
                $query = $query->where([
                    $this->ExternalAuth->aliasField('provider_username') => $options['username'],
                ]);
            }

            return $query;
        });
    }

    /**
     * Finder for my users. This only returns the currently logged in user.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @return \Cake\ORM\Query
     */
    protected function findMine(Query $query)
    {
        return $query->where(function (QueryExpression $exp) {
            return $exp->eq($this->aliasField((string)$this->getPrimaryKey()), LoggedUser::id());
        });
    }
}
