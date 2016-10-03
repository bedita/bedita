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

namespace BEdita\Core\Utility;

use Cake\Cache\Cache;
use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\Log\Log;

/**
 * Retrieve system information on service availability and general status
 *
 * Provides static methods to get information in array forma
 */
class System
{

    /**
     * Get status information
     *
     * @return array Information on environment and datasource/cache connections
     */
    public static function status()
    {
        $res = ['env' => 'ok'];

        return $res;
    }
}
