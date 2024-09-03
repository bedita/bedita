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
use BEdita\Core\ORM\QueryFilterTrait;
use Cake\ORM\Query;
use Cake\Utility\Inflector;

/**
 * Command to list entities.
 *
 * @since 4.0.0
 */
class ListEntitiesAction extends BaseAction
{
    use QueryFilterTrait;

    /**
     * Table.
     *
     * @var \Cake\ORM\Table
     */
    protected $Table;

    /**
     * @inheritDoc
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
     * @param \Cake\ORM\Query $query Query object instance.
     * @param array $filter Filter data.
     * @return \Cake\ORM\Query
     * @throws \BEdita\Core\Exception\BadFilterException
     */
    protected function buildFilter(Query $query, array $filter)
    {
        $customPropsOptions = [];
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
                $target = $this->Table->getAssociation($camelizedKey)->getTarget();
                $targetPrimaryKey = $target->aliasField($target->getPrimaryKey());
                if (is_array($value)) {
                    $targetPrimaryKey .= ' IN';
                }
                $conditions = [$targetPrimaryKey => $value];

                $query = $query
                    ->distinct(array_map(
                        // Avoid duplicate results when INNER JOIN-ing hasMany associations and similar.
                        [$this->Table, 'aliasField'],
                        (array)$this->Table->getPrimaryKey()
                    ))
                    ->innerJoinWith($camelizedKey, function (Query $query) use ($conditions) {
                        return $query->where($conditions);
                    });

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
            $query = $query->find('customProp', $customPropsOptions);
        }

        return $query;
    }

    /**
     * {@inheritDoc}
     *
     * @return \Cake\ORM\Query
     */
    public function execute(array $data = [])
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
