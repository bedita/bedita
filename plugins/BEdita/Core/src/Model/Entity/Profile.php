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
 *
 * @since 4.0.0
 */
class Profile extends ObjectEntity
{

    /**
     * {@inheritDoc}
     */
    protected $_hidden = [
        'created_by_user',
        'modified_by_user',
        'object_type_id',
        'object_type',
        'deleted',
    ];
}
