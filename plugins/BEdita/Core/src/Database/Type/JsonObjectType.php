<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
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
use Cake\Database\Type\JsonType;

/**
 * Custom JSON type that marshals JSONs into objects.
 *
 * @since 4.0.0
 */
class JsonObjectType extends JsonType
{

    /**
     * {@inheritDoc}
     */
    public function toPHP($value, Driver $driver)
    {
        return json_decode($value, false);
    }
}
