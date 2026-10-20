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
use Cake\Database\Query;
use Cake\Database\StatementInterface;
use Cake\ORM\Query as OrmQuery;

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
     * @inheritDoc
     */
    public function run(Query $query): StatementInterface
    {
        $statement = parent::run($query);
        $this->executed[] = [
            'type' => $query->type(),
            'table' => $query instanceof OrmQuery ? $query->getRepository()->getAlias() : null,
            'requested' => $query->getConnectionRole(),
            'actual' => $this->getDriver($query->getConnectionRole())->getRole(),
        ];

        return $statement;
    }
}
