<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2026 Atlas Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Database;

use Cake\Core\App;
use Cake\Database\Connection as CakeConnection;
use Cake\Database\DriverInterface;
use Cake\Database\Exception\MissingDriverException;
use Cake\Database\Exception\MissingExtensionException;
use Cake\Database\Query;
use Cake\Database\Query\SelectQuery;
use Cake\Database\StatementInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Query as OrmQuery;

/**
 * Connection with read replica support.
 *
 * Queries using the `read` role are sent to the primary (`write` role) once the process
 * has performed a write, or while a transaction is open.
 */
class Connection extends CakeConnection
{
    /**
     * Process-wide, since other connections (e.g. `async_jobs`) may write on the same primary.
     *
     * @var bool
     */
    protected static bool $sticky = false;

    /**
     * Set the `sticky` flag.
     *
     * @param bool $sticky Sticky flag.
     * @return void
     */
    public static function setSticky(bool $sticky): void
    {
        static::$sticky = $sticky;
    }

    /**
     * Whether `read` queries of this process are sent to the primary.
     *
     * @return bool
     */
    public static function isSticky(): bool
    {
        return static::$sticky;
    }

    /**
     * Run a lookup, and run it again on the primary if the record is missing on the replica.
     *
     * A miss is a `RecordNotFoundException`/`NotFoundException`, or a result for which `$isMiss` returns `true`.
     *
     * @param callable $callback Lookup to run: it must build its queries anew on each call.
     * @param callable|null $isMiss Check on the result of the lookup.
     * @return mixed
     */
    public static function withPrimaryFallback(callable $callback, ?callable $isMiss = null): mixed
    {
        if (static::$sticky) {
            return $callback();
        }

        try {
            $result = $callback();
            if ($isMiss === null || !$isMiss($result)) {
                return $result;
            }
        } catch (RecordNotFoundException | NotFoundException) {
        }

        static::$sticky = true;

        return $callback();
    }

    /**
     * {@inheritDoc}
     *
     * Backport from CakePHP 5: in 4.5 `read` and `write` configurations are not merged with the shared one.
     */
    protected function createDrivers(array $config): array
    {
        $driver = $config['driver'] ?? '';
        if (!is_string($driver)) {
            return parent::createDrivers($config);
        }

        /** @var class-string<\Cake\Database\DriverInterface>|null $driverClass */
        $driverClass = App::className($driver, 'Database/Driver');
        if ($driverClass === null) {
            throw new MissingDriverException(['driver' => $driver, 'connection' => $this->configName()]);
        }

        $sharedConfig = array_diff_key($config, array_flip([
            'name',
            'className',
            'driver',
            'log',
            'cacheMetaData',
            'cacheKeyPrefix',
            'read',
            'write',
        ]));

        $writeConfig = ($config['write'] ?? []) + $sharedConfig;
        $readConfig = ($config['read'] ?? []) + $sharedConfig;
        if (array_key_exists('write', $config) || array_key_exists('read', $config)) {
            $readDriver = new $driverClass(['_role' => self::ROLE_READ] + $readConfig);
            $writeDriver = new $driverClass(['_role' => self::ROLE_WRITE] + $writeConfig);
        } else {
            $readDriver = $writeDriver = new $driverClass(['_role' => self::ROLE_WRITE] + $writeConfig);
        }

        if (!$writeDriver->enabled()) {
            throw new MissingExtensionException(['driver' => get_class($writeDriver), 'name' => $this->configName()]);
        }

        return [self::ROLE_READ => $readDriver, self::ROLE_WRITE => $writeDriver];
    }

    /**
     * @inheritDoc
     */
    public function getDriver(string $role = self::ROLE_WRITE): DriverInterface
    {
        if ($role === self::ROLE_READ && (static::$sticky || $this->inTransaction())) {
            $role = self::ROLE_WRITE;
        }

        return parent::getDriver($role);
    }

    /**
     * @inheritDoc
     */
    public function run(Query $query): StatementInterface
    {
        if ($query->type() !== 'select') {
            static::$sticky = true;
        } elseif ($query instanceof OrmQuery && $query->getConnectionRole() === self::ROLE_READ) {
            $this->propagateReadRole($query);
        } elseif ($query instanceof SelectQuery && $this->selectsFromReadQueries($query)) {
            // `Query::_performCount()` wraps complex queries in a new query with the write role, also in CakePHP 5.
            $query->useReadRole();
        }

        return parent::run($query);
    }

    /**
     * Check whether every source of a query is a subquery with the read role.
     *
     * @param \Cake\Database\Query $query Query being run.
     * @return bool
     */
    protected function selectsFromReadQueries(Query $query): bool
    {
        $from = $query->clause('from');
        if (empty($from) || !empty($query->clause('join'))) {
            return false;
        }

        foreach ($from as $source) {
            if (!$source instanceof Query || $source->getConnectionRole() !== self::ROLE_READ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Backport from CakePHP 5: contained associations loaded with separate queries use the parent's role.
     *
     * Nested containments get the role in turn when their own query is run.
     *
     * @param \Cake\ORM\Query $query Query being run.
     * @return void
     */
    protected function propagateReadRole(OrmQuery $query): void
    {
        $aliases = array_keys($query->getContain());
        if (empty($aliases)) {
            return;
        }

        // Builders given to `contain()` for an existing alias are chained after the current one.
        $query->contain(array_fill_keys($aliases, fn (OrmQuery $query): OrmQuery => $query->useReadRole()));
    }
}
