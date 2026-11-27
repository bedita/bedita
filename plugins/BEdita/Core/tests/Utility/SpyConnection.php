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
namespace BEdita\Core\Test\Utility;

use BEdita\Core\Database\Connection;
use Cake\Database\Driver;
use Cake\Database\Query;
use Cake\Database\StatementInterface;

/**
 * Connection that records the role each query is requested with and the role it actually runs on.
 */
class SpyConnection extends Connection
{
    /**
     * Executed queries, as `type`, `table` (ORM queries only), `requested` and `actual` role.
     *
     * @var array<array<string, string|null>>
     */
    public array $executed = [];

    /**
     * Role of the driver picked by each `run()` in progress, innermost last.
     *
     * @var array<string|null>
     */
    protected array $running = [];

    /**
     * @inheritDoc
     */
    public function getDriver(string $role = self::ROLE_WRITE): Driver
    {
        $driver = parent::getDriver($role);
        // Cake's `run()` picks the driver before compiling: later calls come from compilation, which may set sticky.
        $last = array_key_last($this->running);
        if ($last !== null && $this->running[$last] === null) {
            $this->running[$last] = $driver->getRole();
        }

        return $driver;
    }

    /**
     * @inheritDoc
     */
    public function run(Query $query): StatementInterface
    {
        $this->running[] = null;
        try {
            $statement = parent::run($query);
        } finally {
            $actual = array_pop($this->running);
        }
        $this->executed[] = [
            'type' => $query->type(),
            'table' => method_exists($query, 'getRepository') ? $query->getRepository()->getAlias() : null,
            'requested' => $query->getConnectionRole(),
            'actual' => $actual,
        ];

        return $statement;
    }
}
