<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2025 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\ORM;

use Cake\ORM\Query\SelectQuery;

/**
 * Interface for finder filters.
 *
 * @since 6.0.0
 */
interface FinderFilterInterface
{
    /**
     * Check if a filter is available in a table or in its behaviors.
     *
     * @param string $name The name of the filter.
     * @return bool
     */
    public function hasFilter(string $name): bool;

    /**
     * Call a filter.
     *
     * @param string $name The name of the filter.
     * @param \Cake\ORM\Query\SelectQuery $query The query to filter.
     * @param mixed $value The value to pass to the filter.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function callFilter(string $name, SelectQuery $query, mixed $value): SelectQuery;
}
