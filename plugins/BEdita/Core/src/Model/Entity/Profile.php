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

namespace BEdita\Core\Model\Entity;

use Cake\Validation\Validation;

/**
 * Profile Entity.
 *
 * @property int $id
 * @property string $name
 * @property string $surname
 * @property string $email
 * @property string $person_title
 * @property string $gender
 * @property \Cake\I18n\Time $birthdate
 * @property \Cake\I18n\Time $deathdate
 * @property bool $company
 * @property string $company_name
 * @property string $company_kind
 * @property string $street_address
 * @property string $city
 * @property string $zipcode
 * @property string $country
 * @property string $state_name
 * @property string $phone
 * @property string $website
 * @property string $national_id_number
 * @property string $vat_number
 * @property string $pseudonym
 * @since 4.0.0
 */
class Profile extends ObjectEntity
{
    /**
     * @inheritDoc
     */
    public function __construct(array $properties = [], array $options = [])
    {
        parent::__construct($properties, $options);

        $this->addNotTranslatable([
            'name',
            'surname',
            'email',
            'company_name',
            'street_address',
            'zipcode',
            'phone',
            'website',
            'national_id_number',
            'vat_number',
            'pseudonym',
        ]);
    }

    /**
     * Ensure URL is standardized by prefixing `http://` to it if necessary.
     *
     * @param mixed $website Website URL.
     * @return mixed
     */
    protected function _setWebsite($website)
    {
        if (is_string($website) && Validation::url($website, false) && !Validation::url($website, true)) {
            return sprintf('http://%s', $website);
        }

        // By returning the original value instead of `null` when an invalid URL is encountered, we preserve validation errors.
        return $website;
    }
}
