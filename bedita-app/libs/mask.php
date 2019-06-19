<?php
/*-----8<--------------------------------------------------------------------
 *
 * BEdita - a semantic content management framework
 *
 * Copyright 2019 ChannelWeb Srl, Chialab Srl
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

/**
 * Utility to mask sensitive data.
 */
class Mask {

    /**
     * Completely mask a value.
     *
     * @param string $value Value to mask.
     * @param int $maxUnmaskedChars Keep at most N characters unmasked.
     * @return string
     */
    public static function hide($value, $maxUnmaskedChars = 0) {
        if (!is_string($value)) {
            return $value;
        }

        $len = mb_strlen($value);
        $maxUnmaskedChars = min($maxUnmaskedChars, $len - 1);

        return mb_substr($value, 0, $maxUnmaskedChars) . str_repeat('*', $len - $maxUnmaskedChars);
    }

    /**
     * Mask email.
     *
     * @param string $email Value to mask.
     * @param int $userMaxUnmaskedChars Keep at most N characters unmasked for the username.
     * @param bool $maskDomain Set to `true` to mask domain part.
     * @return string
     */
    public static function email($email, $userMaxUnmaskedChars = 3, $maskDomain = false)
    {
        if (!is_string($email) || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return $email;
        }

        list($user, $domain) = explode('@', $email, 2);

        $user = static::hide($user, $userMaxUnmaskedChars);
        if ($maskDomain) {
            $domain = static::hide($domain);
        }

        return implode('@', array($user, $domain));
    }
}
