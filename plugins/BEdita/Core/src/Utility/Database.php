<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2024 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Utility;

use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Exception;

/**
 * Database utilities class
 *
 * Provides static methods to common db related operations
 */
class Database
{
    /**
     * Get basic database connection info
     *
     * @param string $dbConfig Input database configuration ('default' as default)
     * @param bool $version Retrieve or not version info
     * @return array containing requested configuration
     *          + 'vendor' key (mysql, sqlite, postgres,...)
     */
    public static function basicInfo(string $dbConfig = 'default', bool $version = true): array
    {
        $connection = ConnectionManager::get($dbConfig);
        if (!$connection instanceof Connection) {
            return [];
        }
        $config = $connection->config();
        $config['vendor'] = strtolower(substr($config['driver'], strrpos($config['driver'], '\\') + 1));
        $config['version'] = 'unknown';
        if ($version && $config['vendor'] !== 'sqlite') {
            $version = $connection->execute('SELECT VERSION()')->fetch();
            $config['version'] = implode('', $version);
        }

        return $config;
    }

    /**
     * See if Database connection is available and working correctly
     *
     * @param string $dbConfig input database configuration ('default' as default)
     * @return array containing keys: 'success' (boolean), 'error' (string with error message)
     */
    public static function connectionTest(string $dbConfig = 'default'): array
    {
        $res = ['success' => false, 'error' => ''];
        try {
            $connection = ConnectionManager::get($dbConfig);
            $connection->getDriver()->connect();
            $res['success'] = true;
        } catch (Exception $e) {
            $res['error'] = $e->getMessage();
        }

        return $res;
    }
}
