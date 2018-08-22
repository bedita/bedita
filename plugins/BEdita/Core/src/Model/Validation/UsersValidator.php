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

namespace BEdita\Core\Model\Validation;

use Cake\ORM\TableRegistry;

/**
 * Validator for users.
 *
 * @since 4.0.0
 */
class UsersValidator extends ProfilesValidator
{

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        parent::__construct();

        $this->setProvider('usersTable', TableRegistry::get('Users'));

        $this
            ->add('username', 'unique', ['rule' => 'validateUnique', 'provider' => 'usersTable'])
            ->requirePresence('username', 'create')
            ->notEmpty('username')

            ->notEmpty('password')

            ->boolean('blocked')
            ->allowEmpty('blocked');
    }
}
