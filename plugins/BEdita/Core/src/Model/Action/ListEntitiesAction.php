<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2017 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Model\Action;

use BEdita\Core\Exception\BadFilterException;
use BEdita\Core\ORM\FinderFilterInterface;
use BEdita\Core\ORM\FinderFilterTrait;
use BEdita\Core\ORM\QueryFilterTrait;
use Cake\Database\Expression\TupleComparison;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Association\HasMany;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\Table;
use Cake\Utility\Inflector;
use Error;

/**
 * Command to list entities.
 *
 * @since 4.0.0
 */
class ListEntitiesAction extends BaseAction implements FinderFilterInterface
{
    use FinderFilterTrait;
    use QueryFilterTrait;

    /**
     * Table.
     *
     * @var \Cake\ORM\Table
     */
    protected Table $Table;

    /**
     * @inheritDoc
     */
    protected function initialize(array $data): void
    {
        $this->Table = $this->getConfig('table');
    }

    /**
     * Parse a filter string.
     *
     * @param mixed $filter Filter string.
     * @return array
     */
    public static function parseFilter(mixed $filter): array
    {
        if (is_array($filter)) {
            return $filter;
        }
        if (!is_string($filter)) {
            return [];
        }

        $filter = array_filter(explode(',', $filter));

        $result = [];
        foreach ($filter as $condition) {
            [$key, $value] = explode('=', $condition, 2) + [null, true];

            $key = trim($key);
            if ($key === '') {
                continue;
            }
            if ($value === 'null') {
                $value = null;
            }

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * Build a filter and return modified query object.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param array $filter Filter data.
     * @return \Cake\ORM\Query\SelectQuery
     * @throws \BEdita\Core\Exception\BadFilterException
     */
    protected function buildFilter(SelectQuery $query, array $filter): SelectQuery
    {
        $customPropsOptions = [];
        foreach ($filter as $key => $value) {
            $variableKey = Inflector::variable($key);
            $hasFinder = $this->Table instanceof FinderFilterInterface
                ? $this->Table->hasFilter($variableKey)
                : $this->hasFilter($variableKey, $this->Table);

            if ($hasFinder) {
                try {
                    $query = $this->Table instanceof FinderFilterInterface
                        ? $this->Table->callFilter($variableKey, $query, $value)
                        : $this->callFilter($variableKey, $query, $value, $this->Table);

                    continue;
                } catch (Error $e) {
                    if (strpos($e->getMessage(), 'Unknown named parameter') !== false) {
                        throw new BadFilterException([
                            'title' => __d('bedita', 'Invalid data'),
                            'detail' => sprintf('Filter %s error: %s', $key, $e->getMessage()),
                        ]);
                    }
                }
            }

            $camelizedKey = Inflector::camelize($key);
            if ($this->Table->associations()->has($camelizedKey)) {
                // Associated match (primary key only).
                $target = $this->Table->getAssociation($camelizedKey)->getTarget();
                $targetPrimaryKey = $target->aliasField($target->getPrimaryKey());
                if (is_array($value)) {
                    $targetPrimaryKey .= ' IN';
                }
                $conditions = [$targetPrimaryKey => $value];

                $query = $this->associationFilter($query, $camelizedKey, $conditions);

                continue;
            }

            if ($this->Table->hasField($key)) {
                // Filter on single field.
                $key = $this->Table->aliasField($key);
                $query = $this->fieldsFilter($query, [$key => $value]);

                continue;
            }

            if ($this->Table->behaviors()->has('CustomProperties')) {
                /** @var \BEdita\Core\Model\Behavior\CustomPropertiesBehavior $behavior */
                $behavior = $this->Table->behaviors()->get('CustomProperties');
                if (in_array($key, array_keys($behavior->getAvailable()))) {
                    $customPropsOptions[$key] = $value;

                    continue;
                }
            }

            // No suitable filter was found
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => 'filter "' . $key . '" was not found',
            ]);
        }

        if (!empty($customPropsOptions)) {
            $query = $query->find('customProp', ...$customPropsOptions);
        }

        return $query;
    }

    /**
     * Filter on an associated entity.
     *
     * For "to-many" associations a subquery on the association foreign key is used instead of
     * an INNER JOIN + `DISTINCT ON` to avoid duplicate results: on Postgres `DISTINCT ON`
     * is incompatible with an `ORDER BY` on other fields.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query object instance.
     * @param string $assoc Association name.
     * @param array $conditions Conditions on associated entity.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function associationFilter(SelectQuery $query, string $assoc, array $conditions): SelectQuery
    {
        $association = $this->Table->getAssociation($assoc);
        $joinConditions = fn(SelectQuery $query): SelectQuery => $query->where($conditions);
        if (!$association instanceof HasMany && !$association instanceof BelongsToMany) {
            // "to-one" association: no duplicate results.
            return $query->innerJoinWith($assoc, $joinConditions);
        }

        $fkTable = $association instanceof BelongsToMany ? $association->junction() : $association->getTarget();
        $foreignKey = array_map([$fkTable, 'aliasField'], (array)$association->getForeignKey());
        $subQuery = $this->Table->subquery()
            ->select($foreignKey)
            ->innerJoinWith($assoc, $joinConditions);
        $bindingKey = array_map([$this->Table, 'aliasField'], (array)$association->getBindingKey());
        if (count($bindingKey) === 1) {
            return $query->where([$bindingKey[0] . ' IN' => $subQuery]);
        }

        return $query->where(new TupleComparison($bindingKey, $subQuery, [], 'IN'));
    }

    /**
     * {@inheritDoc}
     *
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function execute(array $data = []): SelectQuery
    {
        $query = $this->Table->find();

        if (!empty($data['filter'])) {
            $query = $this->buildFilter($query, static::parseFilter($data['filter']));
        }
        if (!empty($data['contain'])) {
            $query = $query->contain($data['contain']);
        }

        return $query;
    }
}
