<?php
/*-----8<--------------------------------------------------------------------
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
namespace BEdita\Lib\Utility;

use Cake\Utility\String as CakeString;

/**
 * String handling methods
 * add functionality to CakePHP String class
 */
class String extends CakeString {

    /**
     * Modify a string to get friendly url version.
     * With a regexp you can choose which characters to preserve.
     *
     * @param string $value
     * @param string $keep, regexp fragment with characters to keep, e.g. '\.' will preserve points,
     *                      '\.\:' points and semicolons
     * @return string
     */
    public static function friendlyUrl($value, $keep = '') {
        if (is_null($value)) {
            $value = '';
        }
        if (is_numeric($value)) {
            $value = 'n' . $value;
        }

        $value = htmlentities(strtolower($value), ENT_NOQUOTES, 'UTF-8');

        // replace accent, uml, tilde,... with letter after & in html entities
        $value = preg_replace('/&(.)(uml);/', '$1e', $value);
        $value = preg_replace('/&(.)(acute|grave|cedil|circ|ring|tilde|uml);/', '$1', $value);
        // replace special chars and space with dash (first decode html entities)
        // exclude chars in $keep regexp fragment
        $regExp = '/[^a-z0-9\-_' . $keep . ']/i';
        $value = preg_replace($regExp, '-', html_entity_decode($value, ENT_NOQUOTES,'UTF-8'));
        // replace two or more consecutive dashes with one dash
        $value = preg_replace('/[\-]{2,}/', '-', $value);
        // trim dashes in the beginning and in the end of nickname
        return trim($value,'-');
    }

}
