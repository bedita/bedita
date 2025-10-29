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
namespace BEdita\Core\Command;

use ArrayObject;
use BEdita\Core\Model\Validation\SqlConventionsValidator;
use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Plugin;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Locator\LocatorAwareTrait;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;
use Migrations\Migrations;
use Throwable;

/**
 * CheckSchema command.
 *
 * Current schema is compared with versioned schema dump to check if it is up to date.
 * Also, migrations status and SQL naming conventions are checked.
 */
class CheckSchemaCommand extends Command
{
    use LocatorAwareTrait;

    /**
     * Console arguments
     *
     * @var \Cake\Console\Arguments
     */
    protected Arguments $args;

    /**
     * Console IO
     *
     * @var \Cake\Console\ConsoleIo
     */
    protected ConsoleIo $io;

    /**
     * Registry of all issues found.
     *
     * @var array
     */
    protected array $messages = [];

    /**
     * List of SQL reserved words.
     *
     * @var array
     */
    protected array $reservedWords = [];

    /**
     * {@inheritDoc}
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->setName('cake check_schema');
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser
            ->setDescription([
                'Current schema is compared with versioned schema dump to check if it is up to date.',
                'Also, migrations status and SQL naming conventions are checked.',
            ])
            ->addOption('connection', [
                'help' => 'Connection name to use.',
                'short' => 'c',
                'required' => false,
                'default' => 'default',
                'choices' => ConnectionManager::configured(),
            ])
            ->addOption('ignore-migration-status', [
                'help' => 'Skip checks on migration status.',
                'boolean' => true,
            ]);
    }

    /**
     * @inheritDoc
     */
    public static function getDescription(): string
    {
        return 'Check Schema';
    }

    /**
     * @inheritDoc
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        $this->args = $args;
        $this->io = $io;

        if (!Plugin::isLoaded('Migrations')) {
            $this->io->abort('Plugin "Migrations" must be loaded in order to perform schema checks');
        }

        try {
            $connection = ConnectionManager::get($this->args->getOption('connection'));
        } catch (Throwable $e) {
            $this->io->error($e->getMessage());
            $this->io->abort('Unknown connection type');
        }

        if (!$this->args->getOption('ignore-migration-status')) {
            $this->checkMigrationsStatus($connection);
        }

        // check real vendor for DB like MariaDB or Aurora using MySQL driver where migration based check diff fails
        $realVendor = Hash::get((array)$connection->config(), 'realVendor');
        if (($connection->getDriver() instanceof Mysql) && empty($realVendor)) {
            $this->checkConventions($connection);
            // Schema check removed for now - will be restored in a future release
            // $this->checkDiff($connection);
        } else {
            $this->io->out('=====> <warning>SQL conventions and schema differences can only be checked on MySQL</warning>');
        }

        return $this->formatMessages() ? static::CODE_SUCCESS : static::CODE_ERROR;
    }

    /**
     * Check if all migrations have already been migrated.
     *
     * @param \Cake\Database\Connection $connection Connection instance.
     * @return void
     */
    protected function checkMigrationsStatus(Connection $connection): void
    {
        $migrations = new Migrations([
            'connection' => $connection->configName(),
            'plugin' => 'BEdita/Core',
        ]);
        $status = $migrations->status();

        $this->io->verbose('=====> Checking migrations status:');
        foreach ($status as $item) {
            $info = sprintf('=====>  - Migration <comment>%s</comment> (%s) is ', $item['name'], $item['id']);
            if ($item['status'] === 'up') {
                $this->io->verbose($info . '<info>UP</info>');
                continue;
            }

            $this->io->verbose($info . '<error>DOWN</error>');
            $this->messages['phinxlog'] = true;
        }

        $this->io->verbose('=====> ');
    }

    /**
     * Filter Phinxlog tables out of a list of table names.
     *
     * @param array $tables Table names.
     * @return array
     * @internal
     */
    protected function filterPhinxlogTables(array $tables): array
    {
        return array_filter($tables, function ($table) {
            return $table !== 'phinxlog' && substr($table, -strlen('_phinxlog')) !== '_phinxlog';
        });
    }

    /**
     * Check if a symbol is valid.
     *
     * @param string $symbol Symbol to check.
     * @param array $context Index or constraint options.
     * @return array
     * @internal
     */
    protected function checkSymbol(string $symbol, array $context = []): array
    {
        $validator = new SqlConventionsValidator();
        foreach ($context as $key => $value) {
            if (is_array($value)) {
                $value = new ArrayObject($value);
            }
            $validator->setProvider($key, $value);
        }

        $errors = $validator->validate(compact('symbol'));

        return Hash::get($errors, 'symbol', []);
    }

    /**
     * Check if SQL conventions are followed.
     *
     * @param \Cake\Database\Connection $connection Connection instance.
     * @return void
     */
    protected function checkConventions(Connection $connection): void
    {
        $this->io->verbose('=====> Checking SQL conventions:');
        $allColumns = [];
        $tables = $this->filterPhinxlogTables($connection->getSchemaCollection()->listTables());
        foreach ($tables as $table) {
            $this->io->verbose(sprintf('=====>  - Checking table <comment>%s</comment>... ', $table), 0);

            $schema = $connection->getSchemaCollection()->describe($table);
            $errors = [];

            $errors['table']['naming'] = $this->checkSymbol($table);

            foreach ($schema->columns() as $column) {
                $errors['column'][$column]['naming'] = $this->checkSymbol(
                    $column,
                    compact('table', 'allColumns'),
                );
                $allColumns[$column] = $table;
            }

            foreach ($schema->indexes() as $index) {
                $errors['index'][$index]['naming'] = $this->checkSymbol(
                    $index,
                    $schema->getIndex($index) + compact('table'),
                );
            }

            foreach ($schema->constraints() as $constraint) {
                $errors['constraint'][$constraint]['naming'] = $this->checkSymbol(
                    $constraint,
                    $schema->getConstraint($constraint) + compact('table'),
                );
            }

            $this->messages[$table] = $errors;

            $this->io->verbose('<info>DONE</info>');
        }

        $this->io->verbose('=====> ');
    }

    /**
     * Send all messages to output.
     *
     * @return bool
     */
    protected function formatMessages(): bool
    {
        if (!empty($this->messages['phinxlog'])) {
            $this->io->quiet('=====> <warning>Migration history is not in sync with migration files.</warning>');
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
                        $lines[] = $this->errorMessage($type, $symbol, $errorType, $details);
                    }
                }
            }

            if (!empty($lines)) {
                $this->io->quiet(sprintf('=====> Table <comment>%s</comment>:', $table));
                $this->io->quiet(array_map(
                    function ($line) {
                        return sprintf('=====>  - <warning>%s</warning>', $line);
                    },
                    $lines,
                ));
                $check = false;
            } else {
                $this->io->verbose(sprintf('=====> Table <comment>%s</comment>: <info>OK</info>', $table));
            }
        }

        if ($check) {
            $this->io->verbose('=====> ');
            $this->io->out('=====> <success>Everything seems just fine. Have a nice day!</success>');
        }

        return $check;
    }

    /**
     * Error message.
     *
     * @param string $type Type of symbol.
     * @param string $symbol Symbol name.
     * @param string $errorType Error type.
     * @param array $details Error details.
     * @return string
     */
    protected function errorMessage(string $type, string $symbol, string $errorType, array $details): string
    {
        if ($errorType === 'naming') {
            return sprintf('%s name "%s" is not valid (%s)', $type, $symbol, implode(', ', $details));
        }
        if ($errorType === 'add') {
            return sprintf('%s "%s" has been added', $type, $symbol);
        }
        if ($errorType === 'remove') {
            return sprintf('%s "%s" has been removed', $type, $symbol);
        }
        if ($errorType === 'changed') {
            return sprintf('%s "%s" has been changed', $type, $symbol);
        }

        return '';
    }
}
