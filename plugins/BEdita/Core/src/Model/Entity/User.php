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

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * User Entity.
 *
 * @property int $id
 * @property string $username
 * @property string $password
 * @property bool $blocked
 * @property \Cake\I18n\Time $last_login
 * @property \Cake\I18n\Time $last_login_err
 * @property int $num_login_err
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 * @property \BEdita\Core\Model\Entity\ExternalAuth[] $external_auth
 *
 * @since 4.0.0
 */
class User extends Entity
{

    /**
     * {@inheritDoc}
     */
    protected $_accessible = [
        '*' => false,
        'username' => true,
        'password_hash' => true,
        'external_auth' => true,
    ];

    /**
     * {@inheritDoc}
     */
    protected $_hidden = [
        'password_hash',
    ];

    /**
     * Password setter.
     *
     * @param string $password Password to be hashed.
     * @return string
     */
    protected function _setPasswordHash($password)
    {
        return (new DefaultPasswordHasher())->hash($password);
    }
}
