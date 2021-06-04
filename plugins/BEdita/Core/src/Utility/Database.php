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

use Cake\Database\Connection;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Hash;

/**
 * Database utilities class
 *
 * Provides static methods to common db related operations
 */
class Database
{

    /**
     * Returns an array with current database schema information (tables, columns,
     * indexes, constraints)
     * Using $dbConfig database connection ('default' as default)
     *
     * @param string $dbConfig Input database configuration ('default' as default)
     *
     * @return array containing complete schema information, table names as keys
     *     and details on columns, indexes and constraints for every table
     */
    public static function currentSchema($dbConfig = 'default')
    {
        $schema = [];
        $connection = ConnectionManager::get($dbConfig);
        if (!($connection instanceof Connection)) {
            return $schema;
        }

        $connection->cacheMetadata(false);

        $collection = $connection->getSchemaCollection();
        $tables = $collection->listTables();
        foreach ($tables as $tableName) {
            $schema[$tableName] = [];
            $table = $collection->describe($tableName);
            $columns = $table->columns();
            foreach ($columns as $col) {
                $schema[$tableName]['columns'][$col] = $table->getColumn($col);
                unset($schema[$tableName]['columns'][$col]['collate']);
            }
            $constraints = $table->constraints();
            foreach ($constraints as $cons) {
                $schema[$tableName]['constraints'][$cons] = $table->getConstraint($cons);
            }
            $indexes = $table->indexes();
            foreach ($indexes as $idx) {
                $schema[$tableName]['indexes'][$idx] = $table->getIndex($idx);
            }
        }

        return $schema;
    }

    /**
     * Compare schema arrays between $expected and $current schema metadata
     * Returns an array with difference details
     *
     * @param array $expected Expected db schema
     * @param array $current Current db schema from DbUtils::currentSchema()
     *
     * @return array containing information on differences found
     */
    public static function schemaCompare(array $expected, array $current)
    {
        $diff = [];
        foreach ($expected as $table => $tableMeta) {
            if (empty($current[$table])) {
                $diff['missing']['tables'][] = $table;
                continue;
            }
            $itemNames = ['columns', 'constraints', 'indexes'];
            foreach ($itemNames as $itemName) {
                if (empty($tableMeta[$itemName])) {
                    continue;
                }
                if (!isset($current[$table][$itemName])) {
                    $current[$table][$itemName] = [];
                }
                static::compareSchemaItems(
                    $table,
                    $itemName,
                    $tableMeta[$itemName],
                    $current[$table][$itemName],
                    $diff
                );
            }
        }

        return $diff;
    }

    /**
     * Compare schema related arrays relative to some $itemType ('columns', 'constraints', 'indexes')
     * Populate $diff array with differences on 3 keys:
     *  - 'missing' items expected but not found
     *  - 'changed' items with different metadata
     *  - 'exceeding' items not present in expected data
     *
     * @param string $table Current table
     * @param string $itemType Item type ('columns', 'constraints', 'indexes'()
     * @param array $expItems Expected items data
     * @param array $currItems Current items data
     * @param array $diff Difference array
     *
     * @return void
     */
    protected static function compareSchemaItems($table, $itemType, array $expItems, array $currItems, array &$diff)
    {
        foreach ($expItems as $key => $data) {
            if (empty($currItems[$key])) {
                $diff['missing'][$itemType][] = $table . '.' . $key;
                continue;
            }

            if ($currItems[$key] != $data) {
                $diff['changed'][$itemType][] = $table . '.' . $key;
            }
        }
        $exceeding = array_diff_key($currItems, $expItems);
        foreach ($exceeding as $key => $data) {
            $diff['exceeding'][$itemType][] = $table . '.' . $key;
        }
    }

    /**
     * Get basic database connection info
     *
     * @param string $dbConfig Input database configuration ('default' as default)
     * @return array containing requested configuration
     *          + 'vendor' key (mysql, sqlite, postgres,...)
     */
    public static function basicInfo(string $dbConfig = 'default'): array
    {
        $connection = ConnectionManager::get($dbConfig);
        $config = $connection->config();
        $config['vendor'] = strtolower(substr($config['driver'], strrpos($config['driver'], '\\') + 1));
        $query = 'SELECT VERSION()';
        if ($config['vendor'] === 'sqlite') {
            $query = 'SELECT SQLITE_VERSION()';
        }
        $version = $connection->execute($query)->fetch();
        $config['version'] = implode('', $version);

        return $config;
    }

    /**
     * See if a DB vendor and min version matches current connection info on 'default'
     *
     * @param array $options Array containing 'vendor' (lower case - 'mysql', 'postgres', 'sqlite') and optionally 'version'
     * @return bool True on match success, false otherwise
     * @deprecated Will be dropped in a future release, not to be used anymore
     */
    public static function supportedVersion($options)
    {
        $info = static::basicInfo();
        if ($options['vendor'] !== $info['vendor']) {
            return false;
        }
        if (!empty($options['version']) && $options['version'] > $info['version']) {
            return false;
        }

        return true;
    }

    /**
     * See if Database connection is available and working correctly
     *
     * @param string $dbConfig input database configuration ('default' as default)
     *
     * @return array containing keys: 'success' (boolean), 'error' (string with error message)
     */
    public static function connectionTest($dbConfig = 'default')
    {
        $res = ['success' => false, 'error' => ''];
        try {
            $connection = ConnectionManager::get($dbConfig);
            $res['success'] = $connection->connect();
        } catch (\Exception $e) {
            $res['error'] = $e->getMessage();
        }

        return $res;
    }

    /**
     * Split a multi-statement SQL query into chunks.
     *
     * @param string $sql SQL to be split.
     *
     * @return array
     */
    protected static function splitSqlQueries($sql)
    {
        $lines = explode(PHP_EOL, $sql);
        $queries = [];
        $query = '';
        foreach ($lines as $line) {
            $line = rtrim($line);
            $query .= (!empty($query) ? PHP_EOL : '') . $line;
            if (substr($line, -1) == ';') {
                $queries[] = $query;
                $query = '';
            }
        }
        if (!empty($query)) {
            $queries[] = $query;
        }

        return $queries;
    }

    /**
     * Executes SQL query using transactions.
     * Returns an array providing information on SQL query results
     *
     * @param string|string[] $sql      SQL query to execute.
     * @param string $dbConfig Database config to use ('default' as default)
     *
     * @return array containing keys: 'success' (boolean), 'error' (string with error message),
     *      'rowCount' (number of affected rows), 'queryCount' (number of queries executed)
     * @throws \Cake\Datasource\Exception\MissingDatasourceConfigException Throws an exception
     *      if the requested `$dbConfig` does not exist.
     */
    public static function executeTransaction($sql, $dbConfig = 'default')
    {
        $res = [];
        try {
            $connection = ConnectionManager::get($dbConfig);
            $res = $connection->transactional(function (Connection $conn) use ($sql) {
                if (!is_array($sql)) {
                    $queries = static::splitSqlQueries($sql);
                } else {
                    $queries = $sql;
                }

                $success = true;
                $rowCount = 0;
                $queryCount = 0;
                foreach ($queries as $query) {
                    if (!trim($query)) {
                        continue;
                    }

                    $statmnt = $conn->prepare($query);
                    $success = $statmnt->execute() && (!$statmnt->errorCode() || $statmnt->errorCode() === '00000');
                    $rowCount += $statmnt->rowCount();
                    $queryCount++;
                    $statmnt->closeCursor();
                    if (!$success) {
                        break;
                    }
                }

                if (!$success) {
                    return [
                        'error' => 'Could not execute statement',
                    ];
                }

                return [
                    'rowCount' => $rowCount,
                    'queryCount' => $queryCount,
                    'success' => true
                ];
            });
        } catch (\Exception $e) {
            $res['error'] = $e->getMessage();
        }

        $res += ['success' => false, 'error' => '', 'rowCount' => 0, 'queryCount' => 0];

        return $res;
    }
}
