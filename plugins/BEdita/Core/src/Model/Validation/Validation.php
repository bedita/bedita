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

use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Validation\Validation as CakeValidation;

/**
 * Reusable class to check for reserved names.
 * Used for object types and properties.
 *
 * @since 4.0.0
 */
class Validation
{
    /**
     * The list of reserved names
     *
     * @var string[]|null
     */
    protected static $reserved = null;

    /**
     * Clear reserved names list
     *
     * @return void
     */
    public static function clear()
    {
        static::$reserved = null;
    }

    /**
     * Load list of reserved names in `$reserved`
     *
     * @return string[]
     */
    protected static function reservedWords()
    {
        if (static::$reserved === null) {
            static::$reserved = (new PhpConfig())->read('BEdita/Core.reserved');
        }

        return static::$reserved;
    }

    /**
     * Check if a value is not reserved
     *
     * @param mixed $value Value to check
     * @return bool
     */
    public static function notReserved($value)
    {
        if ($value && in_array($value, static::reservedWords())) {
            return false;
        }

        return true;
    }

    /**
     * Checks that a value is a valid URL or custom url as myapp://
     *
     * @param string $value The url to check
     * @return bool
     */
    public static function url($value)
    {
        // check for a valid scheme (https://, myapp://,...)
        $regex = '/(?<scheme>^[a-z][a-z0-9+\-.]*:\/\/).*/';
        if (!preg_match($regex, $value, $matches)) {
            return false;
        }

        // if scheme is not an URL protocol then it's a custom url (myapp://) => ok
        if (!preg_match('/^(https?|ftps?|sftp|file|news|gopher:\/\/)/', $matches['scheme'])) {
            return true;
        }

        return CakeValidation::url($value, true);
    }
}
