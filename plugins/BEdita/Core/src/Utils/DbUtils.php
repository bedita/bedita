<?php
/**
 ${BEDITA_LICENSE_HEADER}
 */
namespace BEdita\Core\Utils;

use Cake\Datasource\ConnectionManager;

/**
 * Database utilities class
 *
 * Provides static methods to common db related operations
 */
class DbUtils
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
     * Compare schema arrays betweend $expected and $current schema metadata
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
                $diff['missingTables'][] = $table;
                continue;
            }
            foreach ($tableMeta['columns'] as $column => $colData) {
                if (empty($current[$table]['columns'][$column])) {
                    $diff['missingColumns'][] = $table . '.' . $column;
                }
                // TODO: column details diff
            }
            if (!empty($tableMeta['constraints'])) {
                foreach ($tableMeta['constraints'] as $constraint => $constData) {
                    if (empty($current[$table]['constraints'][$constraint])) {
                        $diff['missingConstraints'][] = $table . '.' . $constraint;
                    }
                    // TODO: constraint details diff
                }
            }
            if (!empty($tableMeta['indexes'])) {
                foreach ($tableMeta['indexes'] as $index => $idxData) {
                    if (empty($current[$table]['indexes'][$index])) {
                        $diff['missingIndexes'][] = $table . '.' . $index;
                    }
                    // TODO: index details diff
                }
            }
        }
        return $diff;
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
        $connection = ConnectionManager::get($dbConfig);
        $config = $connection->config();
        $config['vendor'] = strtolower(substr($config['driver'], strrpos($config['driver'], '\\') + 1));
        return $config;
    }

    /**
     * Executes SQL query using transactions.
     * Returns an array providing information on SQL query results
     *
     * @param string $sql      SQL query to execute.
     * @param string $dbConfig Database config to use ('default' as default)
     *
     * @return array containing keys: 'success' (boolean), 'error' (string with error message),
     *      'rowCount' (number of affected rows)
     */
    public static function executeTransaction($sql, $dbConfig = 'default')
    {
        $res = ['success' => false, 'error' => '', 'rowCount' => 0];
        try {
            $connection = ConnectionManager::get($dbConfig);
            $connection->begin();
            $statement = $connection->query($sql);
            $connection->commit();
            $res['success'] = true;
            $res['rowCount'] = $statement->rowCount();
        } catch (Exception $e) {
            $connection->rollback();
            $res['error'] = $e->getMessage();
        }
        return $res;
    }
}
