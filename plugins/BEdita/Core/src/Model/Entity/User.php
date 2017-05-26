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

/**
 * User Entity.
 *
 * @property int $id
 * @property string $username
 * @property string $password_hash
 * @property bool $blocked
 * @property \Cake\I18n\Time $last_login
 * @property \Cake\I18n\Time $last_login_err
 * @property int $num_login_err
 * @property \BEdita\Core\Model\Entity\ExternalAuth[] $external_auth
 * @property bool $verified
 *
 * @since 4.0.0
 */
class User extends Profile
{

    /**
     * {@inheritDoc}
     *
     * @todo Inherit accessible fields from parent entity.
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
        'object_type_id' => false,
        'object_type' => false,
        'type' => false,
        'deleted' => false,
        'locked' => false,
        'created' => false,
        'modified' => false,
        'published' => false,
        'created_by' => false,
        'modified_by' => false,
        'blocked' => false,
        'last_login' => false,
        'last_login_err' => false,
        'num_login_err' => false,
        'verified' => false,
    ];

    /**
     * {@inheritDoc}
     */
    protected $_hidden = [
        'object_type_id',
        'object_type',
        'password_hash',
        'external_auth',
        'deleted',
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
