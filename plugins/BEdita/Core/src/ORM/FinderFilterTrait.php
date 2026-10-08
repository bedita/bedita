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
use BEdita\Core\Exception\BadFilterException;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\Table;
use Closure;
use LogicException;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

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
                $this::class,
            ));
        }

        $finderName = 'find' . ucfirst($name);
        if (method_exists($table, $finderName) && (new ReflectionMethod($table, $finderName))->isPublic()) {
            return $table->{$finderName}(...);
        }

        foreach ($table->behaviors()->loaded() as $behavior) {
            /** @var \Cake\ORM\Behavior $behaviorInstance */
            $behaviorInstance = $table->behaviors()->get($behavior);
            $implementedFinders = $behaviorInstance->implementedFinders();
            $filterFinders = array_intersect_key(
                $implementedFinders,
                array_flip((array)$behaviorInstance->getConfig('implementedFilters', array_keys($implementedFinders))),
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
                sprintf('filter `%s` must accept at least one parameter', $reflected->getName()),
            );
        }

        if ($reflected->getNumberOfParameters() === 1) {
            return $callable($query);
        }

        $params = array_slice($reflected->getParameters(), 1);
        if (!array_is_list((array)$value)) {
            return $callable($query, ...$this->castFilterArguments($params, $value));
        }

        $secondParam = $params[0];
        $key = !$secondParam->isVariadic() ? $secondParam->getName() : 'value';

        return $callable($query, ...$this->castFilterArguments($params, [$key => $value]));
    }

    /**
     * Cast filter arguments to the scalar types declared by the filter parameters.
     *
     * Filter values usually come from query strings, so they are strings (i.e. `'1'`, `'true'`, `'10'`).
     * Since filters are invoked in strict mode, they are cast here to `bool`, `int`, `float` or `string`
     * when the target parameter declares one of these types and the value can be safely converted.
     * A `BadFilterException` is thrown if a value can't be converted.
     *
     * @param array<\ReflectionParameter> $params Filter parameters, excluding the query.
     * @param array $args Filter arguments, keyed by parameter name.
     * @return array
     * @throws \BEdita\Core\Exception\BadFilterException If an argument can't be cast to the declared type.
     */
    protected function castFilterArguments(array $params, array $args): array
    {
        $byName = [];
        foreach ($params as $param) {
            $byName[$param->getName()] = $param;
        }
        foreach ($args as $name => $arg) {
            $param = is_string($name) ? $byName[$name] ?? null : $params[$name] ?? null;
            if ($param !== null && !$param->isVariadic()) {
                $args[$name] = $this->castFilterArgument($param, $arg);
            }
        }

        return $args;
    }

    /**
     * Cast a single filter argument to the scalar type declared by the parameter.
     *
     * @param \ReflectionParameter $param The filter parameter.
     * @param mixed $arg The argument value.
     * @return mixed
     * @throws \BEdita\Core\Exception\BadFilterException If the argument can't be cast to the declared type.
     */
    protected function castFilterArgument(ReflectionParameter $param, mixed $arg): mixed
    {
        $type = $param->getType();
        if (
            $arg === null
            || !$type instanceof ReflectionNamedType
            || !in_array($type->getName(), ['bool', 'int', 'float', 'string'], true)
        ) {
            return $arg;
        }

        $cast = !is_scalar($arg) ? null : match ($type->getName()) {
            'bool' => filter_var($arg, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE),
            'int' => filter_var($arg, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE),
            'float' => filter_var($arg, FILTER_VALIDATE_FLOAT, FILTER_NULL_ON_FAILURE),
            'string' => (string)$arg,
        };
        if ($cast === null) {
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => sprintf('filter parameter `%s` must be of type %s', $param->getName(), $type->getName()),
            ]);
        }

        return $cast;
    }
}
