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

use BEdita\Core\Model\Entity\Profile;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Profiles Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Users
 *
 * @since 4.0.0
 */
class ProfilesTable extends Table
{

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('profiles');
        $this->displayField('name');
        $this->primaryKey('id');

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'className' => 'BEdita/Core.Users'
        ]);

        $this->addBehavior('BEdita/Core.ClassTableInheritance', [
            'table' => [
                'tableName' => 'Objects',
                'className' => 'BEdita/Core.Objects'
            ]
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

            ->allowEmpty('name')

            ->allowEmpty('surname')

            ->email('email')
            ->allowEmpty('email')
            ->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'table'])

            ->allowEmpty('person_title')

            ->allowEmpty('gender')

            ->date('birthdate')
            ->allowEmpty('birthdate')

            ->date('deathdate')
            ->allowEmpty('deathdate')

            ->boolean('company')
            ->requirePresence('company', 'create')
            ->notEmpty('company')

            ->allowEmpty('company_name')

            ->allowEmpty('company_kind')

            ->allowEmpty('street_address')

            ->allowEmpty('city')

            ->allowEmpty('zipcode')

            ->allowEmpty('country')

            ->allowEmpty('state_name')

            ->allowEmpty('phone')

            ->allowEmpty('website');

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
        $rules->add($rules->isUnique(['email']));
        return $rules;
    }
}
