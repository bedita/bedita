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

use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Utility\Hash;
use Cake\Validation\Validation as CakeValidation;
use DateTimeInterface;
use Swaggest\JsonSchema\Schema;

/**
 * Class to provide reusable validation rules.
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

    /**
     * Validate using JSON Schema.
     *
     * @param mixed $value Value being validated.
     * @param mixed $schema Schema to validate against.
     * @return true|string
     */
    public static function jsonSchema($value, $schema)
    {
        if (empty($schema)) {
            return true;
        }

        $schema = Schema::import(json_decode(json_encode($schema)));
        try {
            $schema->in(json_decode(json_encode($value)));

            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Validate language tag using `I18n` configuration.
     *
     * @param string $tag Language tag
     * @return true|string
     */
    public static function languageTag($tag)
    {
        $languages = Hash::normalize((array)Configure::read('I18n.languages'));
        if (!empty($languages)) {
            if (!array_key_exists($tag, $languages)) {
                return __d('bedita', 'Invalid language tag "{0}"', $tag);
            }
        }

        return true;
    }

    /**
     * Validate input date and datetime.
     * Accepetd input formats:
     *  - string date format
     *  - integer timestamps
     *  - DateTime objects
     *
     * Accepted date time string formats are
     *  - 2017-01-01                    YYYY-MM-DD
     *  - 2017-01-01 11:22              YYYY-MM-DD hh:mm
     *  - 2017-01-01T11:22:33           YYYY-MM-DDThh:mm:ss
     *  - 2017-01-01T11:22:33Z          YYYY-MM-DDThh:mm:ssZ
     *  - 2017-01-01T19:20+01:00        YYYY-MM-DDThh:mmTZD
     *  - 2017-01-01T11:22:33+01:00     YYYY-MM-DDThh:mm:ssTZD
     *  - 2017-01-01T19:20:30.45+01:00  YYYY-MM-DDThh:mm:ss.sTZD
     *
     * See ISO 8601 subset as defined here https://www.w3.org/TR/NOTE-datetime:
     *
     * Also timestamp as integer are accepted.
     *
     * @param mixed $value Date or datetime value
     * @return true|string
     */
    public static function dateTime($value)
    {
        if ($value instanceof DateTimeInterface) {
            return true;
        }

        if (is_string($value) && preg_match('/^\d{4}(-\d\d(-\d\d([T ]\d\d:\d\d(:\d\d)?(\.\d+)?(([+-]\d\d:\d\d)|Z)?)?)?)?$/i', $value)) {
            return true;
        }

        if (filter_var($value, FILTER_VALIDATE_INT)) {
            return true;
        }

        return __d('bedita', 'Invalid date or datetime "{0}"', print_r($value, true));
    }
}
