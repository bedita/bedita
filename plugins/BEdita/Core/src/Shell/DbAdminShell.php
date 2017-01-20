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
namespace BEdita\Core\Shell;

use BEdita\Core\Utility\Database;
use Cake\Cache\Cache;
use Cake\Console\Shell;
use Cake\Core\Configure\Engine\JsonConfig;
use Cake\Core\Plugin;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use Cake\Utility\Inflector;

/**
 * Database related shell commands like:
 *  - initialize a new database instance
 *  - create schema files
 *  - check schema consistency
 *
 * @since 4.0.0
 */
class DbAdminShell extends Shell
{

    /**
     * Default JSON schema file name
     *
     * @var string
     */
    const JSON_SCHEMA_FILE = 'be4-schema.json';

    /**
     * Schema files folder path
     *
     * @var string
     */
    public $schemaDir = null;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();
        $this->schemaDir = Plugin::path('BEdita/Core') . 'config' . DS . 'schema' . DS;
    }

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser->addSubcommand('saveSchema', [
            'help' => 'Save current database schema to JSON file.',
            'parser' => [
                'description' => [
                    'Use this command to generate a JSON file schema.',
                    'File is built using current database connection.',
                ],
                'options' => [
                    'output' => [
                        'help' => 'Specifiy output file path',
                        'short' => 'o',
                        'required' => false,
                        'default' => $this->schemaDir . self::JSON_SCHEMA_FILE,
                    ],
                ],
            ],
        ]);
        $parser->addSubcommand('checkSchema', [
            'help' => 'Check schema differences between current db and schema JSON file.',
            'parser' => [
                'description' => [
                    'A JSON file schema is generated from current DB connection.',
                    'This file is compared with the default one in BEdita/Core/config/schema/be4-schema.json.',
                ],
            ],
        ]);
        $parser->addSubcommand('init', [
            'help' => 'Create a new BE4 schema on current DB connection.',
            'parser' => [
                'description' => [
                    'A new database schema is created using current DB connection.',
                    'BEWARE: all existing BE4 tables will be dropped!!',
                ],
            ],
        ]);

        return $parser;
    }

    /**
     * Save schema to file (JSON format) from current db
     * Generated file is BEdita/Core/config/schema/be4-schema.json)
     *
     * @return void
     */
    public function saveSchema()
    {
        $schemaFile = $this->params['output'];
        if (file_exists($schemaFile)) {
            $res = $this->in('Overwrite schema file "' . $schemaFile . '"?', ['y', 'n'], 'n');
            if ($res != 'y') {
                $this->info('Schema file not updated');

                return;
            }
        }
        $schemaData = Database::currentSchema();
        $this->checkSQLReservedWords($schemaData);
        $this->checkDuplicateColumns($schemaData);
        $this->checkSchemaNaming($schemaData);
        $jsonSchema = json_encode($schemaData, JSON_PRETTY_PRINT);
        $res = file_put_contents($schemaFile, $jsonSchema);
        if (!$res) {
            $this->abort('Error writing schema file ' . $schemaFile);
        }
        $this->info('Schema file updated ' . $schemaFile);
    }

    /**
     * Check schema differences between current db and schema JSON file
     * (in BEdita/Core/config/schema/be4-schema.json)
     *
     * @return bool True on schema match, false on failure
     */
    public function checkSchema()
    {
        $be4Schema = (new JsonConfig())->read('BEdita/Core.schema/be4-schema');
        $currentSchema = Database::currentSchema();
        $this->checkSQLReservedWords($currentSchema);
        $this->checkDuplicateColumns($currentSchema);
        $this->checkSchemaNaming($currentSchema);
        $schemaDiff = Database::schemaCompare($be4Schema, $currentSchema);
        if (!empty($schemaDiff)) {
            $this->warn('Schema differences found!!');
            foreach ($schemaDiff as $key => $data) {
                foreach ($data as $type => $value) {
                    foreach ($value as $v) {
                        $this->warn($key . ' ' . Inflector::singularize($type) . ': ' . $v);
                    }
                }
            }

            return false;
        }

        $this->info('No schema differences found');

        return true;
    }

    /**
     * Load SQL reserved words array from 'reserved_words.txt' file
     * Some words (like 'name' and 'status') are allowed in BEdita4
     *
     * @return array
     */
    protected function loadSQLReservedWords()
    {
        $exceptions = ['NAME', 'STATUS'];
        $reservedWords = [];
        $reservedWordsFile = $this->schemaDir . 'sql_reserved_words.txt';
        $lines = file($reservedWordsFile);
        foreach ($lines as $value) {
            $l = strtoupper(trim($value));
            if (!empty($l) && $l[0] !== '#' && !in_array($l, $exceptions)) {
                $reservedWords[] = $l;
            }
        }

        return $reservedWords;
    }

    /**
     * Check schema array against SQL reserved words
     *
     * @param array $schema Array representation of d schema
     * @return void
     */
    protected function checkSQLReservedWords(array $schema)
    {
        $reservedWords = $this->loadSQLReservedWords();
        foreach ($schema as $table => $data) {
            if (in_array(strtoupper($table), $reservedWords)) {
                $this->warn('Table name "' . $table . '" is a SQL reserved word');
            }
            foreach ($data as $key => $value) {
                foreach ($value as $col => $meta) {
                    if (in_array(strtoupper($col), $reservedWords) &&
                            $key !== 'constraints' && $col !== 'primary') {
                        $this->warn('"' . $table . '.' . $col . '" (' . $key .
                            ') is a SQL reserved word');
                    }
                }
            }
        }
    }

    /**
     * Check for duplicate colum names in schema
     *
     * @param array $schema Array representation of d schema
     * @return void
     */
    protected function checkDuplicateColumns(array $schema)
    {
        $columns = [];
        $allowed = ['created', 'description', 'enabled', 'modified', 'name', 'params', 'label'];
        foreach ($schema as $table => $data) {
            foreach ($data['columns'] as $name => $columnData) {
                if ($name !== 'id' && (substr($name, -3) !== '_id') && !in_array($name, $allowed)) {
                    if (!empty($columns[$name])) {
                        $this->warn('"' . $table . '.' . $name . '" already defined in "' .
                            $columns[$name] . '.' . $name . '"');
                    } else {
                        $columns[$name] = $table;
                    }
                }
            }
        }
    }

    /**
     * Check naming conventions for tables and columns names
     *
     * @param array $schema Array representation of d schema
     * @return void
     */
    protected function checkSchemaNaming(array $schema)
    {
        foreach ($schema as $table => $data) {
            $tableUnderscored = Inflector::underscore($table);
            if ($tableUnderscored !== $table) {
                $this->warn('table name "' . $table . '" should be underscored');
            }
            foreach ($data['columns'] as $name => $columnData) {
                $columnUnderscored = Inflector::underscore($name);
                if ($columnUnderscored !== $name) {
                    $this->warn('column name "' . $table . '.' . $name . '" should be underscored');
                }
                if (substr($name, -1) === '_' || substr($name, 0, 1) === '_') {
                    $this->warn('column name "' . $table . '.' . $name . '" should not start/end with "_"');
                }
                if (strpos($name, '__') !== false) {
                    $this->warn('column name "' . $table . '.' . $name . '" should not contain "__"');
                }
                if (is_numeric(substr($name, 0, 1))) {
                    $this->warn('column name "' . $table . '.' . $name . '" should not start with a number');
                }
            }
        }
    }

    /**
     * Initialize BE4 database schema
     * SQL schema in BEdita/Core/config/schema/be4-schema-<vendor>.sql
     *
     * @return void
     */
    public function init()
    {
        $info = Database::basicInfo();
        $this->warn('You are about to initialize a new database!!');
        $this->warn('ALL CURRENT BEDITA4 TABLES WILL BE DROPPED!!');
        $this->info('Host: ' . $info['host']);
        $this->info('Database: ' . $info['database']);
        $this->info('Vendor: ' . $info['vendor']);
        $res = $this->in('Do you want to proceed?', ['y', 'n'], 'n');
        if ($res != 'y') {
            $this->out('Database unchanged');

            return;
        }

        $dbInitTask = $this->Tasks->load('BEdita/Core.DbInit');
        $dbInitTask->main();
    }
}
