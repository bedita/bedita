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
namespace BEdita\Core\Shell\Task;

use Cake\Console\Exception\MissingTaskException;
use Cake\Console\Shell;
use Cake\Core\Plugin;
use Cake\Database\Connection;
use Cake\Database\Schema\Table;
use Cake\Datasource\ConnectionManager;
use Cake\Utility\Inflector;

/**
 * Task to check if current schema is up to date, and if SQL standards are satisfied.
 *
 * @since 4.0.0
 */
class CheckSchemaTask extends Shell
{

    /**
     * Registry of all issues found.
     *
     * @var array
     */
    protected $messages = [];

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $parser
            ->description([
                'Current schema is compared with versioned schema dump to check if it is up to date.',
                'Also, migrations status and SQL naming conventions are checked.',
            ])
            ->addOption('connection', [
                'help' => 'Connection name to use',
                'short' => 'c',
                'required' => false,
                'default' => 'default',
                'choices' => ConnectionManager::configured(),
            ]);

        return $parser;
    }

    /**
     * Run checks on schema.
     *
     * @return bool
     */
    public function main()
    {
        if (!Plugin::loaded('Migrations')) {
            $this->abort('Plugin "Migrations" must be loaded in order to perform schema checks');
        }

        $connection = ConnectionManager::get($this->param('connection'));
        if (!($connection instanceof Connection)) {
            $this->abort('Unknown connection type');
        }

        $this->checkMigrationsStatus($connection);
        $this->checkConventions($connection);
        $this->checkDiff($connection);

        return $this->formatMessages();
    }

    /**
     * Check if all migrations have already been migrated.
     *
     * @param \Cake\Database\Connection $connection Connection instance.
     * @return void
     */
    protected function checkMigrationsStatus(Connection $connection)
    {
        $className = '\Migrations\Migrations'; // Avoid PHP fatal error if Migrations plugin isn't installed.
        $migrations = new $className(['connection' => $connection->configName()]);
        $status = $migrations->status();

        $this->verbose('Checking migrations status:');
        foreach ($status as $item) {
            $info = sprintf(' - Migration <comment>%s</comment> (%s) is ', $item['name'], $item['id']);
            if ($item['status'] === 'up') {
                $this->verbose($info . '<info>UP</info>');
                continue;
            }

            $this->verbose($info . '<error>DOWN</error>');
            $this->messages['phinxlog'] = true;
        }

        $this->verbose('');
    }

    /**
     * Filter Phinxlog tables out of a list of table names.
     *
     * @param array $tables Table names.
     * @return array
     * @internal
     */
    protected function filterPhinxlogTables(array $tables)
    {
        return array_filter($tables, function ($table) {
            return ($table !== 'phinxlog' && substr($table, -strlen('_phinxlog')) !== '_phinxlog');
        });
    }

    /**
     * Check if a symbol is valid.
     *
     * @param string $symbol Symbol to check.
     * @param array|null $options Index or constraint options.
     * @return array
     * @internal
     */
    protected function checkSymbol($symbol, array $options = null)
    {
        static $reservedWords = [];
        if (empty($reservedWords)) {
            $reservedWords = file(Plugin::path('BEdita/Core') . 'config' . DS . 'schema' . DS . 'sql_reserved_words.txt');
            array_walk(
                $reservedWords,
                function (&$word) {
                    $word = strtoupper(trim($word));
                }
            );
            $reservedWords = array_filter(
                $reservedWords,
                function ($word) {
                    return !empty($word) && substr($word, 0, 1) !== '#' && !in_array($word, ['NAME', 'STATUS']);
                }
            );
        }

        $errors = [];
        if (in_array(strtoupper($symbol), $reservedWords)) {
            $errors[] = 'reserved word';
        }
        if ($symbol !== Inflector::underscore($symbol)) {
            $errors[] = 'not underscored';
        }
        if (substr($symbol, 0, 1) === '_') {
            $errors[] = 'starts with "_"';
        }
        if (substr($symbol, -1) === '_') {
            $errors[] = 'ends with "_"';
        }
        if (strpos($symbol, '__') !== false) {
            $errors[] = 'contains "__"';
        }
        if (is_numeric(substr($symbol, 0, 1))) {
            $errors[] = 'starts with a digit';
        }
        if ($options !== null) {
            $prefix = str_replace('_', '', $options['table']) . '_';
            switch ($options['type']) {
                case Table::CONSTRAINT_PRIMARY:
                    if ($symbol === 'primary') {
                        return [];
                    }
                    $suffix = '_pk';
                    break;
                case Table::CONSTRAINT_FOREIGN:
                    $suffix = '_fk';
                    break;
                case Table::CONSTRAINT_UNIQUE:
                    $suffix = '_uq';
                    break;
                case Table::INDEX_FULLTEXT:
                case Table::INDEX_INDEX:
                default:
                    $suffix = '_idx';
            }

            if (substr($symbol, 0, strlen($prefix)) !== $prefix) {
                $errors[] = sprintf('should start with "%s"', $prefix);
            }
            if (substr($symbol, -strlen($suffix)) !== $suffix) {
                $errors[] = sprintf('should end with "%s"', $suffix);
            }
            if ($symbol === $prefix . substr($suffix, 1)) {
                $errors[] = 'should have a unique identifier between prefix and suffix';
            }
        }

        return $errors;
    }

    /**
     * Check if SQL conventions are followed.
     *
     * @param \Cake\Database\Connection $connection Connection instance.
     * @return void
     */
    protected function checkConventions(Connection $connection)
    {
        $this->verbose('Checking SQL conventions:');
        $allColumns = [];
        $tables = $this->filterPhinxlogTables($connection->schemaCollection()->listTables());
        foreach ($tables as $table) {
            $this->verbose(sprintf(' - Checking table <comment>%s</comment>... ', $table), 0);

            $schema = $connection->schemaCollection()->describe($table);
            $errors = [];

            $errors['table']['naming'] = $this->checkSymbol($table);

            foreach ($schema->columns() as $column) {
                $errorMsg = $this->checkSymbol($column);
                if ($column === $table) {
                    $errorMsg[] = 'same name as table';
                }
                if (!in_array($column, ['created', 'description', 'enabled', 'id', 'modified', 'name', 'params']) && substr($column, -3) !== '_id') {
                    if (array_key_exists($column, $allColumns)) {
                        $errorMsg[] = sprintf('already defined in "%s"', $allColumns[$column]);
                    }
                    $allColumns[$column] = $table;
                }
                $errors['column'][$column]['naming'] = $errorMsg;
            }

            foreach ($schema->indexes() as $index) {
                $errors['index'][$index]['naming'] = $this->checkSymbol(
                    $index,
                    $schema->index($index) + compact('table')
                );
            }

            foreach ($schema->constraints() as $constraint) {
                $errors['constraint'][$constraint]['naming'] = $this->checkSymbol(
                    $constraint,
                    $schema->constraint($constraint) + compact('table')
                );
            }

            $this->messages[$table] = $errors;

            $this->verbose('<info>DONE</info>');
        }

        $this->verbose('');
    }

    /**
     * Check if changes in schema occurred.
     *
     * @param \Cake\Database\Connection $connection Connection instance.
     * @return void
     */
    protected function checkDiff(Connection $connection)
    {
        try {
            $diffTask = $this->Tasks->load('Migrations.MigrationDiff');
        } catch (MissingTaskException $e) {
            $this->err('Unable to check schema differences: ' . $e->getMessage());

            return;
        }

        $this->verbose('Checking schema differences:');

        $diffTask->connection = $connection->configName();
        $diffTask->setup();

        $diff = $diffTask->templateData();
        if (empty($diff['data'])) {
            return;
        }
        $diff = $diff['data'];

        $this->verbose(' - Checking tables added or removed... ', 0);
        foreach ($this->filterPhinxlogTables(array_keys($diff['fullTables']['add'])) as $table) {
            $this->messages[$table]['table'][$table]['add'] = true;
        }
        foreach ($this->filterPhinxlogTables(array_keys($diff['fullTables']['remove'])) as $table) {
            $this->messages[$table]['table'][$table]['remove'] = true;
        }
        unset($diff['fullTables']);
        $this->verbose('<info>DONE</info>');

        foreach ($diff as $table => $elements) {
            $this->verbose(sprintf(' - Checking table <comment>%s</comment>... ', $table), 0);

            foreach ($elements as $type => $changes) {
                $type = Inflector::singularize($type);
                foreach ($changes as $action => $list) {
                    foreach (array_keys($list) as $symbol) {
                        $this->messages[$table][$type][$symbol][$action] = true;
                    }
                }
            }

            $this->verbose('<info>DONE</info>');
        }

        $this->verbose('');
    }

    /**
     * Send all messages to output.
     *
     * @return bool
     */
    protected function formatMessages()
    {
        if (!empty($this->messages['phinxlog'])) {
            $this->quiet('<warning>Migration history is not in sync with migration files.</warning>');
        }
        unset($this->messages['phinxlog']);

        ksort($this->messages);

        $check = true;
        foreach ($this->messages as $table => $elements) {
            $lines = [];
            foreach ($elements as $type => $list) {
                $type = Inflector::humanize($type);
                foreach ($list as $symbol => $messages) {
                    $messages = array_filter($messages);
                    foreach ($messages as $errorType => $details) {
                        switch ($errorType) {
                            case 'naming':
                                $lines[] = sprintf('%s name "%s" is not valid (%s)', $type, $symbol, implode(', ', $details));
                                break;
                            case 'add':
                                $lines[] = sprintf('%s "%s" has been added', $type, $symbol);
                                break;
                            case 'remove':
                                $lines[] = sprintf('%s "%s" has been removed', $type, $symbol);
                                break;
                            case 'changed':
                                $lines[] = sprintf('%s "%s" has been changed', $type, $symbol);
                                break;
                        }
                    }
                }
            }

            if (!empty($lines)) {
                $this->quiet(sprintf('Table <comment>%s</comment>:', $table));
                $this->quiet(array_map(
                    function ($line) {
                        return sprintf(' - <warning>%s</warning>', $line);
                    },
                    $lines
                ));
                $check = false;
            } else {
                $this->verbose(sprintf('Table <comment>%s</comment>: <info>OK</info>', $table));
            }
        }

        if ($check) {
            $this->verbose('');
            $this->out('<info>Everything seems just fine. Have a nice day!</info>');
        }

        return $check;
    }
}
