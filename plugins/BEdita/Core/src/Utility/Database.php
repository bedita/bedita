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

        $collection = $connection->schemaCollection();
        $tables = $collection->listTables();
        foreach ($tables as $tableName) {
            $schema[$tableName] = [];
            $table = $collection->describe($tableName);
            $columns = $table->columns();
            foreach ($columns as $col) {
                $schema[$tableName]['columns'][$col] = $table->column($col);
            }
            $constraints = $table->constraints();
            foreach ($constraints as $cons) {
                $schema[$tableName]['constraints'][$cons] = $table->constraint($cons);
            }
            $indexes = $table->indexes();
            foreach ($indexes as $idx) {
                $schema[$tableName]['indexes'][$idx] = $table->index($idx);
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
     * @param string $dbConfig input database configuration ('default' as default)
     *
     * @return array containing requested configuration
     *          + 'vendor' key (mysql, sqlite, postgres,...)
     */
    public static function basicInfo($dbConfig = 'default')
    {
        $config = ConnectionManager::get($dbConfig)->config();
        $config['vendor'] = strtolower(substr($config['driver'], strrpos($config['driver'], '\\') + 1));

        return $config;
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

        return $queries;
    }

    /**
     * Executes SQL query using transactions.
     * Returns an array providing information on SQL query results
     *
     * @param string $sql      SQL query to execute.
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
        $connection = ConnectionManager::get($dbConfig);
        try {
            $res = $connection->transactional(function (Connection $conn) use ($sql) {
                $queries = static::splitSqlQueries($sql);

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
