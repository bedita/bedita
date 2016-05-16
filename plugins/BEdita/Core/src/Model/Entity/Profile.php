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

use BEdita\Core\Model\Entity\Object;
use Cake\ORM\Entity;

/**
 * Profile Entity.
 *
 * @property int $id
 * @property int $user_id
 * @property \BEdita\Core\Model\Entity\User $user
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
 *
 * @since 4.0.0
 */
class Profile extends Object
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
