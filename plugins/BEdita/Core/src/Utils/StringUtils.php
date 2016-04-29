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

namespace BEdita\Core\Utils;

/**
 * String utilities class
 *
 * Provides static methods to common string related utilities
 */
class StringUtils
{

    /**
     * Get friendly url version of a string
     * With a regexp you can choose which characters to preserve.
     *
     * @param string $value Input string
     * @param string $keep Regexp fragment with characters to keep, e.g. "\." will preserve points,
     *               "\.\:" points and semicolons
     * @return string
     */
    public static function friendlyUrl($value, $keep = '')
    {
        if (empty($value)) {
            $value = '';
        }
        if (is_numeric($value)) {
            $value = 'n' . $value;
        }

        $value = strtolower(htmlentities($value, ENT_NOQUOTES | ENT_IGNORE, 'UTF-8'));

        // replace accent, uml, tilde,... with letter after & in html entities
        $value = preg_replace("/&(.)(uml);/", "$1e", $value);
        $value = preg_replace("/&(.)(acute|grave|cedil|circ|ring|tilde|uml);/", "$1", $value);
        // replace special chars and space with dash (first decode html entities)
        // exclude chars in $keep regexp fragment
        $regExp = "/[^a-z0-9\-_" . $keep . "]/i";
        $value = preg_replace($regExp, '-', html_entity_decode($value, ENT_NOQUOTES, 'UTF-8'));
        // replace two or more consecutive dashes with one dash
        $value = preg_replace("/[\-]{2,}/", '-', $value);

        // trim dashes in the beginning and in the end
        return trim($value, '-');
    }
}
