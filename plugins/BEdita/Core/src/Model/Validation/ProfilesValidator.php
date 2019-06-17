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

/**
 * Validator for profiles.
 *
 * @since 4.0.0
 */
class ProfilesValidator extends ObjectsValidator
{
    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        parent::__construct();

        $this
            ->allowEmptyString('name')

            ->allowEmptyString('surname')

            ->email('email')
            ->allowEmptyString('email')

            ->allowEmptyString('person_title')

            ->allowEmptyString('gender')

            ->add('birthdate', 'date', ['rule' => [Validation::class, 'dateTime']])
            ->allowEmptyDateTime('birthdate')

            ->add('deathdate', 'date', ['rule' => [Validation::class, 'dateTime']])
            ->allowEmptyDateTime('deathdate')

            ->boolean('company')
            ->allowEmptyString('company')

            ->allowEmptyString('company_name')

            ->allowEmptyString('company_kind')

            ->allowEmptyString('street_address')

            ->allowEmptyString('city')

            ->allowEmptyString('zipcode')

            ->allowEmptyString('country')

            ->allowEmptyString('state_name')

            ->allowEmptyString('phone')

            // Use `add` instead of `urlWithProtocol` to preserve rule name.
            ->add('website', 'url', [
                'rule' => ['url', true],
            ])
            ->allowEmptyString('website')

            ->allowEmptyString('national_id_number')

            ->allowEmptyString('vat_number');
    }
}
