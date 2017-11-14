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

namespace BEdita\Core\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type\BoolType as CakeBoolType;
use InvalidArgumentException;

/**
 * Custom BoolType class accepting also `true` and `false` as strings in input
 */
class BoolType extends CakeBoolType
{
    /**
     * Convert bool data into the database format.
     * `true` and `false` as strings are accepted, other strings will cause an `InvalidArgumentException`
     *
     * @param mixed $value The value to convert.
     * @param \Cake\Database\Driver $driver The driver instance to convert with.
     * @return bool|null
     */
    public function toDatabase($value, Driver $driver)
    {
        try {
            return parent::toDatabase($value, $driver);
        } catch (InvalidArgumentException $e) {
            if (is_string($value)) {
                $value = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                if ($value !== null) {
                    return $value;
                }
            }
            throw $e;
        }
    }
}
