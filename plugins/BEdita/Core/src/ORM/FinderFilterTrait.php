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

use BadMethodCallException;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\Table;
use Closure;
use LogicException;
use ReflectionFunction;
use ReflectionMethod;

/**
 * Trait to handle filters in tables and behaviors.
 *
 * Filters are table `finders` that satisfy the following conditions:
 * - public finder for the table
 * - implemented finder for a loaded behavior eventually filtered by the `filterFinders` configuration
 *
 * @since 6.0.0
 */
trait FinderFilterTrait
{
    /**
     * Check if a filter is available in the table or in its behaviors.
     *
     * @param string $name The name of the filter.
     * @param \Cake\ORM\Table|null $table The table to look for the filter in. `null` to use `$this` as table.
     * @return bool
     */
    public function hasFilter(string $name, ?Table $table = null): bool
    {
        return $this->getFilter($name, $table) !== null;
    }

    /**
     * Look for a finder to use as filter in the table or in its behaviors.
     *
     * A finder is considered a valid filter if it satisfies one of the following conditions:
     * - public finder for the table
     * - implemented finder for a loaded behavior eventually filtered by the `filterFinders` configuration
     *
     * @param string $name The name of the finder without the `find` prefix.
     * @param \Cake\ORM\Table|null $table The table to look for the filter in. `null` to use `$this` as table.
     * @return \Closure|null
     * @throws \LogicException If the table is not an instance of `Cake\ORM\Table`.
     */
    protected function getFilter(string $name, ?Table $table = null): ?Closure
    {
        $table = $table ?? $this;
        if (!$table instanceof Table) {
            throw new LogicException(sprintf(
                'Filters are only available for `%s` instances. Got `%s` instead.',
                Table::class,
                $this::class
            ));
        }

        $name = strtolower($name);
        $finderName = 'find' . $name;
        if (method_exists($table, $finderName) && (new ReflectionMethod($table, $finderName))->isPublic()) {
            return $table->{$finderName}(...);
        }

        foreach ($table->behaviors()->loaded() as $behavior) {
            /** @var \Cake\ORM\Behavior $behaviorInstance */
            $behaviorInstance = $table->behaviors()->get($behavior);
            $implementedFinders = $behaviorInstance->implementedFinders();
            $filterFinders = array_intersect_key(
                $implementedFinders,
                array_flip((array)$behaviorInstance->getConfig('implementedFilters', array_keys($implementedFinders)))
            );
            if (array_key_exists($name, $filterFinders) && method_exists($behaviorInstance, $filterFinders[$name])) {
                return $behaviorInstance->{$finderName}(...);
            }
        }

        return null;
    }

    /**
     * Call a filter method on the table or on its behaviors.
     *
     * @param string $name The name of the filter.
     * @param \Cake\ORM\Query\SelectQuery $query The query instance.
     * @param mixed $value The value to pass to the filter.
     * @param \Cake\ORM\Table|null $table The table to look for the filter in. `null` to use the main object.
     * @return \Cake\ORM\Query\SelectQuery
     * @throws \BadMethodCallException If the filter method is not found.
     */
    public function callFilter(string $name, SelectQuery $query, mixed $value = null, ?Table $table = null): SelectQuery
    {
        $filter = $this->getFilter($name, $table);
        if ($filter === null) {
            throw new BadMethodCallException(sprintf('Unknown filter method `%s`', $name));
        }

        return $this->invokeFilter($filter, $query, $value);
    }

    /**
     * Apply a filter to the query.
     *
     * @param \Closure $callable The callable filter to apply.
     * @param \Cake\ORM\Query\SelectQuery $query The query instance.
     * @param mixed $value The value to pass to the filter.
     * @return \Cake\ORM\Query\SelectQuery
     * @throws \BadMethodCallException
     */
    protected function invokeFilter(Closure $callable, SelectQuery $query, mixed $value): SelectQuery
    {
        $reflected = new ReflectionFunction($callable);
        if ($reflected->getNumberOfParameters() === 0) {
            throw new BadMethodCallException(
                sprintf('filter `%s` must accept at least one parameter', $reflected->getName())
            );
        }

        if ($reflected->getNumberOfParameters() === 1) {
            return $callable($query);
        }

        if (!array_is_list((array)$value)) {
            return $callable($query, ...$value);
        }

        $secondParam = $reflected->getParameters()[1];
        $key = !$secondParam->isVariadic() ? $secondParam->getName() : 'value';

        return $callable($query, ...[$key => $value]);
    }
}
