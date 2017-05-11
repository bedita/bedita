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
use Cake\ORM\RulesChecker;
use Cake\Validation\Validator;

/**
 * Profiles Model
 *
 * @since 4.0.0
 */
class ProfilesTable extends Table
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('profiles');
        $this->setPrimaryKey('id');
        $this->setDisplayField('name');

        $this->addBehavior('BEdita/Core.Relations');

        $this->extensionOf('Objects', [
            'className' => 'BEdita/Core.Objects'
        ]);

        $this->addBehavior('BEdita/Core.UniqueName', [
            'sourceField' => 'title',
            'prefix' => 'profile-'
        ]);

        $this->addBehavior('BEdita/Core.Searchable', [
            'fields' => [
                'title' => 10,
                'description' => 7,
                'body' => 5,
                'name' => 10,
                'surname' => 10,
                'email' => 7,
                'company_name' => 10,
                'street_address' => 1,
                'city' => 2,
                'country' => 2,
                'state_name' => 2,
            ],
        ]);
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
            ->allowEmpty('company')

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
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email']));

        return $rules;
    }
}
