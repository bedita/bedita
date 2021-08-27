<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
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
use BEdita\Core\Exception\ImmutableResourceException;
use BEdita\Core\Model\Table\ObjectsBaseTable as Table;
use BEdita\Core\Model\Validation\UsersValidator;
use BEdita\Core\Utility\LoggedUser;
use Cake\Core\Configure;
use Cake\Database\Expression\QueryExpression;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
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
     * Administrator user id
     *
     * @var int
     */
    const ADMIN_USER = 1;

    /**
     * Deleted user prefix
     *
     * @var string
     */
    const DELETED_USER_PREFIX = '__deleted-';

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

        $this->extensionOf('Profiles');

        $this->getBehavior('UniqueName')->setConfig([
            'sourceField' => 'username',
            'prefix' => 'user-'
        ]);

        $this->getBehavior('Searchable')->setConfig([
            'fields' => [
                'username' => 10,
                'title' => 10,
                'name' => 10,
                'surname' => 10,
                'email' => 7,
                'description' => 7,
                'body' => 5,
            ],
        ]);

        $this->hasMany('ExternalAuth', [
            'foreignKey' => 'user_id',
        ]);

        $this->belongsToMany('Roles', [
            'through' => 'RolesUsers',
        ]);

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
            ->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

        $validator->requirePresence('password_hash', function () {
            return Configure::read('Signup.requirePassword', true);
        });
        $validator->notEmpty('password_hash');

        $validator->requirePresence('email', function () {
            return Configure::read('Signup.requireEmail', true);
        });

        return $validator;
    }

    /**
     * External auth signup validation
     *
     * @param \Cake\Validation\Validator $validator The validator
     * @return \Cake\Validation\Validator
     * @codeCoverageIgnore
     */
    public function validationSignupExternal(Validator $validator)
    {
        $validator = $this->validationDefault($validator);

        $validator
            ->email('email')
            ->requirePresence('email')
            ->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

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
        $rules->add($rules->isUnique(['email']));

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
     * @param string $providerUsername Provider username.
     * @param int $userId Existing user entity id, if null a new user is created.
     * @return \Cake\Datasource\EntityInterface|bool
     */
    public function externalAuthLogin(Event $event, EntityInterface $authProvider, $providerUsername, $userId = null)
    {
        $params = $event->getData('params');
        $externalAuth = $this->ExternalAuth->newEntity([
            'auth_provider_id' => $authProvider->id,
            'provider_username' => $providerUsername,
            'params' => $params,
        ]);
        if (!empty($userId)) {
            $externalAuth->set('user_id', $userId);
        }

        return $this->ExternalAuth->saveOrFail($externalAuth);
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
        $query = $query->find('loginRoles');

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
     * Find users by role name or id.
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $options Array with role names or ids also as comma separated elements
     * @return \Cake\ORM\Query
     */
    protected function findRoles(Query $query, array $options)
    {
        if (empty($options)) {
            throw new BadFilterException(__d('bedita', 'Missing required parameter "{0}"', 'roles'));
        }

        return $query->innerJoinWith('Roles', function (Query $query) use ($options) {
            $items = $this->rolesNamesIds($options);

            return $query->where(function (QueryExpression $exp) use ($items) {
                return $exp->or_(function (QueryExpression $exp) use ($items) {
                    if (!empty($items['ids'])) {
                        $exp->in($this->Roles->aliasField('id'), $items['ids']);
                    }
                    if (!empty($items['names'])) {
                        $exp->in($this->Roles->aliasField('name'), $items['names']);
                    }

                    return $exp;
                });
            });
        });
    }

    /**
     * Create assoc array separating `names` and `ids`
     *
     * @param array $options Options array
     * @return array
     */
    protected function rolesNamesIds(array $options): array
    {
        $names = $ids = [];
        foreach ($options as $opt) {
            $items = (array)explode(',', $opt);
            foreach ($items as $item) {
                if (is_numeric($item)) {
                    $ids[] = $item;
                } else {
                    $names[] = $item;
                }
            }
        }

        return compact('names', 'ids');
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

    /**
     * Finder for valid login users
     * Valid attributes for `blocked`, `deleted` and `status` are checked
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @return \Cake\ORM\Query
     * @throws \Cake\Http\Exception\BadRequestException if `username` is missing
     */
    protected function findLogin(Query $query)
    {
        return $query
            ->where(function (QueryExpression $exp) {
                return $exp
                    ->eq($this->aliasField('deleted'), false)
                    ->eq($this->aliasField('blocked'), false)
                    ->in($this->aliasField('status'), ['on', 'draft']);
            });
    }

    /**
     * Finder for valid login users + associated roles via `contain`
     *
     * @param \Cake\ORM\Query $query Query object instance.
     * @return \Cake\ORM\Query
     */
    protected function findLoginRoles(Query $query)
    {
        $query = $query->find('login');

        return $query->contain(['Roles']);
    }

    /**
     * Before delete checks: if record is not deletable, raise a ImmutableResourceException
     *
     * @param \Cake\Event\Event $event The beforeSave event that was fired
     * @param \Cake\Datasource\EntityInterface $entity the entity that is going to be saved
     * @return void
     * @throws \BEdita\Core\Exception\ImmutableResourceException if entity is not deletable
     */
    public function beforeDelete(Event $event, EntityInterface $entity)
    {
        if (static::ADMIN_USER === $entity->id) {
            throw new ImmutableResourceException(__d('bedita', 'Could not delete "User" {0}', $entity->id));
        }
    }

    /**
     * Modify user entity to become anonymous and hidden
     *
     * @param \Cake\Datasource\EntityInterface $entity the entity to anonimize
     * @return \Cake\Datasource\EntityInterface|bool
     */
    protected function anonymizeUser(EntityInterface $entity)
    {
        $notNull = $this->notNullableColumns($this);
        foreach ($this->inheritedTables() as $table) {
            $notNull = array_merge($notNull, $this->notNullableColumns($table));
        }
        $properties = array_diff((array)$entity->getVisible(), $notNull, ['type', '_optout']);
        foreach ($properties as $name) {
            $entity->set($name, null);
        }
        $deletedValue = self::DELETED_USER_PREFIX . $entity->get('id');
        $entity->set('username', $deletedValue);
        $entity->set('uname', $deletedValue);
        $entity->set('password', null);
        $entity->set('deleted', true);
        $entity->set('locked', true);

        $this->loadInto($entity, ['ExternalAuth']);
        array_walk(
            $entity->external_auth,
            function ($item) {
                $this->ExternalAuth->deleteOrFail($item);
            }
        );

        return $this->save($entity);
    }

    /**
     * Retrieve not nullable columns for a table
     *
     * @param \Cake\ORM\Table $table Table class
     * @return array
     */
    protected function notNullableColumns($table)
    {
        $res = [];
        $schema = $table->getSchema();
        $columns = $schema->columns();
        foreach ($columns as $col) {
            if (!$schema->isNullable($col)) {
                $res[] = $col;
            }
        }

        return $res;
    }

    /**
     * {@inheritDoc}
     *
     * Override Table delete: in case of constraints avoid delete and anonymize user data
     */
    public function delete(EntityInterface $entity, $options = [])
    {
        $exists = TableRegistry::getTableLocator()->get('Objects')->exists([
            'OR' => ['created_by' => $entity->get('id'), 'modified_by' => $entity->get('id')],
        ]);
        if (!$exists) {
            return parent::delete($entity, $options);
        }

        $this->beforeDelete(new Event('Model.beforeDelete'), $entity);

        return (bool)$this->anonymizeUser($entity);
    }

    /**
     * Before save checks: if record is not deletable and deletion is the update type, raise a ImmutableResourceException
     * Use cases:
     *  - trying to soft delete ADMIN_USER
     *  - logged user removing their account, but performing optout via `_optout` special property is allowed
     *  - `username` or `uname` cannot start with reserved `__deleted-` string
     *
     * @param \Cake\Event\Event $event The beforeSave event that was fired
     * @param \Cake\Datasource\EntityInterface $entity the entity that is going to be saved
     * @return void
     * @throws \BEdita\Core\Exception\ImmutableResourceException if entity is not deletable and deletion is the update type
     */
    public function beforeSave(Event $event, EntityInterface $entity)
    {
        if ($entity->deleted === true && static::ADMIN_USER === $entity->id) {
            throw new ImmutableResourceException(__d('bedita', 'Could not delete "User" {0}', $entity->id));
        }
        if ($entity->deleted === true && LoggedUser::id() === $entity->id && empty($entity->get('_optout'))) {
            throw new BadRequestException(__d('bedita', 'Logged users cannot delete their own account'));
        }
        foreach (['username', 'uname'] as $prop) {
            if (
                !($entity->get('deleted') && $entity->get('locked')) &&
                strpos((string)$entity->get($prop), self::DELETED_USER_PREFIX) === 0
            ) {
                throw new BadRequestException(
                    __d('bedita', '"{0}" cannot start with reserved word "{1}"', $prop, self::DELETED_USER_PREFIX)
                );
            }
        }
    }

    /**
     * If password is changed or created: check regexp rule if present
     *
     * @param \Cake\Event\Event $event The event dispatched
     * @param \ArrayObject $data The input data to save
     * @return void
     * @throws \Cake\Http\Exception\BadRequestException if password is not valid
     */
    public function beforeMarshal(Event $event, \ArrayObject $data)
    {
        if (isset($data['password'])) {
            $passwdRule = Configure::read('Auth.passwordPolicy.rule');
            if (!empty($passwdRule) && !preg_match($passwdRule, $data['password'])) {
                throw new BadRequestException(__d('bedita', Configure::read('Auth.passwordPolicy.message')));
            }
        }
    }
}
