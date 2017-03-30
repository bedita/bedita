<?php
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
use Cake\Database\Expression\QueryExpression;
use Cake\ORM\Query;
use Cake\Utility\Inflector;

/**
 * Command to list entities.
 *
 * @since 4.0.0
 */
class ListEntitiesAction extends BaseAction
{

    /**
     * Table.
     *
     * @var \Cake\ORM\Table
     */
    protected $Table;

    /**
     * {@inheritDoc}
     */
    protected function initialize(array $data)
    {
        $this->Table = $this->getConfig('table');
    }

    /**
     * Parse a filter string.
     *
     * @param string $filter Filter string.
     * @return array
     */
    public static function parseFilter($filter)
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
            list($key, $value) = explode('=', $condition, 2) + [null, true];

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
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $filter Filter data.
     * @return \Cake\ORM\Query
     * @throws \BEdita\Core\Exception\BadFilterException
     */
    protected function buildFilter(Query $query, array $filter)
    {
        foreach ($filter as $key => $value) {
            $variableKey = Inflector::variable($key);
            if ($this->Table->hasFinder($variableKey)) {
                // Finder.
                if ($value === true) {
                    $value = [];
                }

                $query = $query->find($variableKey, (array)$value);

                continue;
            }

            $camelizedKey = Inflector::camelize($key);
            if ($this->Table->associations()->has($camelizedKey)) {
                // Associated match (primary key only).
                $target = $this->Table->association($camelizedKey)->getTarget();
                $targetPrimaryKey = array_map(
                    [$target, 'aliasField'],
                    (array)$target->getPrimaryKey()
                );
                $conditions = array_combine($targetPrimaryKey, (array)$value);

                $query = $query
                    ->distinct(array_map(
                        [$this->Table, 'aliasField'],
                        (array)$this->Table->getPrimaryKey()
                    ))
                    ->innerJoinWith($camelizedKey, function (Query $query) use ($conditions) {
                        return $query->where($conditions);
                    });

                // Avoid duplicate results when INNER JOIN-ing hasMany associations and similar.

                continue;
            }

            if ($this->Table->hasField($key, true)) {
                // Filter on single field.
                $key = $this->Table->aliasField($key);
                if ($value === null) {
                    $query = $query->andWhere(function (QueryExpression $exp) use ($key) {
                        return $exp->isNull($key);
                    });

                    continue;
                }

                $query = $query->andWhere(function (QueryExpression $exp) use ($key, $value) {
                    return $exp->in($key, (array)$value);
                });

                continue;
            }

            // No suitable filter was found
            //$this->log('Filter not found ' . $key, 'error');
            throw new BadFilterException([
                'title' => __d('bedita', 'Invalid data'),
                'detail' => 'filter "' . $key . '" was not found',
            ]);
        }

        return $query;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(array $data = [])
    {
        $query = $this->Table->find();

        if (!empty($data['filter'])) {
            $query = $this->buildFilter($query, static::parseFilter($data['filter']));
        }

        return $query;
    }
}
