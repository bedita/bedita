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

namespace BEdita\Auth\Auth;

use Cake\Auth\AbstractPasswordHasher;

/**
 * MD5 Password hasher
 *
 * @since 4.0.0
 * @deprecated 4.0.0 Added for backwards-compatibility with BEdita 3 users.
 * @see \Cake\Auth\DefaultPasswordHasher
 */
class LegacyMd5PasswordHasher extends AbstractPasswordHasher
{

    /**
     * Generates password hash.
     *
     * @param string|array $password Plain text password to hash or array of data
     *   required to generate password hash.
     * @return string Password hash
     */
    public function hash($password)
    {
        return md5($password);
    }

    /**
     * Check hash. Generate hash from user provided password string or data array
     * and check against existing hash.
     *
     * @param string|array $password Plain text password to hash or data array.
     * @param string $hashedPassword Existing hashed password.
     * @return bool True if hashes match else false.
     */
    public function check($password, $hashedPassword)
    {
        return md5($password) === $hashedPassword;
    }
}
