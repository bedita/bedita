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

use Cake\Database\Connection as CakeConnection;
use Cake\Database\Driver;
use Cake\Database\Query;
use Cake\Database\Query\SelectQuery;
use Cake\Database\StatementInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Exception\NotFoundException;

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
     * Whether any connection of this process has a separate `read` driver.
     *
     * @var bool
     */
    protected static bool $replica = false;

    /**
     * Set the `replica` flag.
     *
     * @param bool $replica Replica flag.
     * @return void
     */
    public static function setReplica(bool $replica): void
    {
        static::$replica = $replica;
    }

    /**
     * Whether any connection of this process has a separate `read` driver.
     *
     * @return bool
     */
    public static function hasReplica(): bool
    {
        return static::$replica;
    }

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
     * Without a replica the lookup runs once.
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

        // The `replica` flag is checked after the first run, which may be what creates the connection.
        try {
            $result = $callback();
            if (!static::$replica || $isMiss === null || !$isMiss($result)) {
                return $result;
            }
        } catch (RecordNotFoundException | NotFoundException $e) {
            if (!static::$replica) {
                throw $e;
            }
        }

        static::$sticky = true;

        return $callback();
    }

    /**
     * @inheritDoc
     */
    protected function createDrivers(array $config): array
    {
        $drivers = parent::createDrivers($config);
        if ($drivers[self::ROLE_READ] !== $drivers[self::ROLE_WRITE]) {
            static::$replica = true;
        }

        return $drivers;
    }

    /**
     * {@inheritDoc}
     *
     * The `read` role uses the write driver when the process is sticky, or while a transaction is open.
     */
    public function getDriver(string $role = self::ROLE_WRITE): Driver
    {
        if ($role === self::ROLE_READ && (static::$sticky || $this->inTransaction())) {
            $role = self::ROLE_WRITE;
        }

        return parent::getDriver($role);
    }

    /**
     * {@inheritDoc}
     *
     * Any query other than a select makes the process sticky.
     */
    public function run(Query $query): StatementInterface
    {
        if (!$query instanceof SelectQuery) {
            static::$sticky = true;
        } elseif ($this->selectsFromReadQueries($query)) {
            // `SelectQuery::_performCount()` wraps complex queries in a new query with the write role.
            $query->useReadRole();
        }

        return parent::run($query);
    }

    /**
     * {@inheritDoc}
     *
     * Raw SQL always makes the process sticky: a read on the primary could feed later queries on the replica,
     * and some `SELECT` statements lock or write.
     */
    public function execute(string $sql, array $params = [], array $types = []): StatementInterface
    {
        static::$sticky = true;

        return parent::execute($sql, $params, $types);
    }

    /**
     * Whether the query selects only from read-role subqueries, without joins.
     *
     * @param \Cake\Database\Query\SelectQuery $query The query.
     * @return bool
     */
    protected function selectsFromReadQueries(SelectQuery $query): bool
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
}
