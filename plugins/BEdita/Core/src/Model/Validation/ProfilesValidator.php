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
     * Regular expression to validate names and surnames.
     * Do not allow special chars like <, >, :, /, = to avoid
     * malicious links or markup insertion.
     *
     * @var string
     */
    public const NAME_REGEX = '/^[^<>:\/=]*$/';

    /**
     * Regular expression to avoid presence of a valid domain name.
     *
     * @var string
     */
    public const NO_DOMAIN_REGEX = '/^(?!.*\.[a-z]{2,}).*$/';

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
            ->add('name', 'validName', ['rule' => [ProfilesValidator::class, 'validName']])

            ->allowEmptyString('surname')
            ->add('surname', 'validName', ['rule' => [ProfilesValidator::class, 'validName']])

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

    /**
     * Checks that a value does not contain malicious elements like:
     *  * URL, code or markup related characters
     *  * domain names
     *
     * @param string $value The string to check
     * @return bool
     */
    public static function validName(string $value)
    {
        // check for invalid characters
        if (!preg_match(static::NAME_REGEX, $value, $matches)) {
            return false;
        }

        // check for domain name
        if (!preg_match(static::NO_DOMAIN_REGEX, $value, $matches)) {
            return false;
        }

        return true;
    }
}
