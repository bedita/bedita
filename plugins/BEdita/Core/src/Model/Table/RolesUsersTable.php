<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Table;

use BEdita\Core\Exception\ImmutableResourceException;
use BEdita\Core\Utility\LoggedUser;
use Cake\Datasource\EntityInterface;
use Cake\Event\EventInterface;
use Cake\Http\Exception\ForbiddenException;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Utility\Hash;
use Cake\Validation\Validator;

/**
 * RolesUsers Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Roles
 * @property \Cake\ORM\Association\BelongsTo $Users
 * @method \BEdita\Core\Model\Entity\RolesUser get($primaryKey, $options = [])
 * @method \BEdita\Core\Model\Entity\RolesUser newEntity($data = null, array $options = [])
 * @method \BEdita\Core\Model\Entity\RolesUser[] newEntities(array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\RolesUser|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BEdita\Core\Model\Entity\RolesUser patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\RolesUser[] patchEntities($entities, array $data, array $options = [])
 * @method \BEdita\Core\Model\Entity\RolesUser findOrCreate($search, callable $callback = null, $options = [])
 */
class RolesUsersTable extends Table
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('roles_users');
        $this->setPrimaryKey('id');
        $this->setDisplayField('id');

        $this->belongsTo('Roles', [
            'foreignKey' => 'role_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
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
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        return $validator;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['role_id', 'user_id']));
        $rules->add($rules->existsIn(['role_id'], 'Roles'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }

    /**
     * Before delete checks: if record is not deletable, raise a ForbiddenException or ImmutableResourceException
     *
     * @param \Cake\Event\EventInterface $event The beforeSave event that was fired
     * @param \Cake\Datasource\EntityInterface $entity the entity that is going to be saved
     * @return void
     * @throws \Cake\Http\Exception\ForbiddenException; if logged user cannot modify user role
     * @throws \BEdita\Core\Exception\ImmutableResourceException; if entity is not deletable
     */
    public function beforeDelete(EventInterface $event, EntityInterface $entity)
    {
        if (!$this->canModify($entity->role_id)) {
            throw new ForbiddenException(__d('bedita', 'Could not update role. Insufficient priority'));
        }
        if ($entity->role_id === RolesTable::ADMIN_ROLE && $entity->user_id === UsersTable::ADMIN_USER) {
            throw new ImmutableResourceException(__d('bedita', 'Could not update relationship for users/roles for ADMIN_USER and ADMIN_ROLE'));
        }
    }

    /**
     * Before save checks: if record is not changeable, raise a ForbiddenException
     *
     * @param \Cake\Event\EventInterface $event The beforeSave event that was fired
     * @param \Cake\Datasource\EntityInterface $entity the entity that is going to be saved
     * @return void
     * @throws \Cake\Http\Exception\ForbiddenException; if logged user cannot modify user role
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity)
    {
        if (!$this->canModify($entity->role_id)) {
            throw new ForbiddenException(__d('bedita', 'Could not update role. Insufficient priority'));
        }
    }

    /**
     * Check that logged user can modify role.
     * Logged user roles min priority should be less or equal to the role priority.
     *
     * @param int $roleId The role ID to check againt logged user roles priorities
     * @return bool
     */
    protected function canModify(int $roleId): bool
    {
        // default priority
        $priorityUser = 100;
        $user = LoggedUser::getUser();
        $roles = (array)Hash::get($user, 'roles');
        $ids = (array)Hash::extract($roles, '{n}.id');
        if (!empty($ids)) {
            $query = $this->Roles->find()->where(['id IN' => $ids]);
            $query->select([
                'min_value' => $query->func()->min($this->Roles->aliasField('priority')),
            ]);
            $priorityUser = $query->find('list', ['valueField' => 'min_value'])->first();
        }
        $priorityRole = $this->Roles
            ->find('list', ['valueField' => 'priority'])
            ->where(['id' => $roleId])
            ->firstOrFail();

        return $priorityUser <= $priorityRole;
    }
}
