<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2020 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Test\Utility;

use Cake\Datasource\ConnectionManager;

/**
 * Utility with methods to check connection drivers
 */
class CheckDriver
{
    /**
     * Check if a specific driver is used (like 'Sqlite' or 'Mysql')
     *
     * @param string $className The full name of class, i.e. \Cake\Database\Driver\Mysql::class
     * @return bool
     */
    public static function is(string $className): bool
    {
        $driver = ConnectionManager::get('default')->getDriver();

        return $driver instanceof $className;
    }
}
