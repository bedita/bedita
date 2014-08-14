<?php
/**-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2008-2014 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * BEdita is distributed WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Lesser General Public License for more details.
 * You should have received a copy of the GNU Lesser General Public License
 * version 3 along with BEdita (see LICENSE.LGPL).
 * If not, see <http://gnu.org/licenses/lgpl-3.0.html>.
 *
 *------------------------------------------------------------------->8-----
 */
namespace BEdita\Auth;

use Cake\Auth\AbstractPasswordHasher;

/**
 * Md5 hasher class
 *
 * Used to backward compatibility with BEdita 3.x password algorithm
 */
class Md5PasswordHasher extends AbstractPasswordHasher {

    /**
     * Return hash of password
     *
     * @param string $password
     * @return string
     */
    public function hash($password) {
        return md5($password);
    }

    /**
     * Check md5 password against hashed string
     *
     * @param string $password
     * @param string $hashed
     * @return bool
     */
    public function check($password, $hashed) {
        return md5($password) === $hashed;
    }
}
