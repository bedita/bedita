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
 * Roles Model
 *
 * @property \Cake\ORM\Association\HasMany $Users
 *
 * @since 4.0.0
 */
class RolesTable extends Table
{

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('roles');
        $this->primaryKey('id');
        $this->displayField('name');

        $this->addBehavior('Timestamp', [
            'events' => [
                'Model.beforeSave' => [
                    'created' => 'new',
                    'modified' => 'always',
                ]
            ],
        ]);

        $this->belongsToMany('Users', [
            'className' => 'BEdita/Core.Users',
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

            ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])
            ->requirePresence('name')
            ->notEmpty('name')

            ->allowEmpty('description')

            ->boolean('immutable')
            ->allowEmpty('immutable')

            ->boolean('backend_auth')
            ->allowEmpty('backend_auth');

        return $validator;
    }

    /**
     * {@inheritDoc}
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['name']));

        return $rules;
    }
}
