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
use Cake\Validation\Validation as CakeValidation;

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

        $this->setProvider('usersTable', TableRegistry::getTableLocator()->get('Users'));

        $this
            ->add('username', 'unique', ['rule' => 'validateUnique', 'provider' => 'usersTable'])
            ->requirePresence('username', 'create')
            ->notEmptyString('username')
            ->add('username', 'validUsername', ['rule' => [UsersValidator::class, 'validUsername']])

            ->add('email', 'unique', ['rule' => 'validateUnique', 'provider' => 'usersTable'])

            ->allowEmptyString('password_hash')

            ->boolean('blocked')
            ->allowEmptyString('blocked');
    }

    /**
     * Checks that a value is a correct username.
     * It is ok with a valid email format, otherwise it must not contain
     * malicious elements like:
     *  * URL, code or markup related characters
     *  * domain names
     *
     * @param mixed $value The value to check
     * @return bool
     */
    public static function validUsername($value): bool
    {
        if (CakeValidation::email($value)) {
            return true;
        }

        if (!is_string($value)) {
            return false;
        }

        return parent::validName($value);
    }
}
