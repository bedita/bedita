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

namespace BEdita\API\Datasource;

use Cake\Datasource\Paginator;
use Cake\Datasource\QueryInterface;
use Cake\Datasource\RepositoryInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Query;
use Cake\Utility\Hash;
use Cake\Utility\Inflector;

/**
 * Handle model pagination using JSON API conventions.
 *
 * @since 4.0.0
 */
class JsonApiPaginator extends Paginator
{

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'page' => 1,
        'limit' => 20,
        'maxLimit' => 100,
        'whitelist' => ['page', 'page_size', 'sort'],
    ];

    /**
     * Associated sort fields whitelist
     *
     * @var array
     */
    protected $sortWhitelist = [
        'DateRanges.start_date',
        'DateRanges.end_date',
    ];

    /**
     * Max limit per pagination items.
     *
     * @var int
     */
    const MAX_LIMIT = 500;

    /**
     * Remove any other `order` clause if an explicit 'sort' is requested
     *
     * {@inheritDoc}
     */
    public function paginate($object, array $params = [], array $settings = []): ResultSetInterface
    {
        if ($object instanceof QueryInterface && !empty($params['sort'])) {
            $object->order([], Query::OVERWRITE);
        }
        $this->setConfig('filter', (array)Hash::get($params, 'filter'));

        return parent::paginate($object, $params, $settings);
    }

    /**
     * {@inheritDoc}
     */
    public function checkLimit(array $options)
    {
        $options['maxLimit'] = min((int)$options['maxLimit'], static::MAX_LIMIT);

        if (!empty($options['page_size'])) {
            $options['limit'] = $options['page_size'];
        }
        unset($options['page_size']);

        return parent::checkLimit($options);
    }

    /**
     * {@inheritDoc}
     */
    public function validateSort(RepositoryInterface $object, array $options)
    {
        $sortedRequest = false;
        if (!empty($options['sort'])) {
            $sortedRequest = true;
            $this->updateSortOptions($options);
        }

        $options = parent::validateSort($object, $options);

        if ($sortedRequest && empty($options['order'])) {
            throw new BadRequestException(__('Unsupported sorting field'));
        }

        return $options;
    }

    /**
     * Update `sort` related options array
     *
     * @param array $options Options array
     * @return void
     */
    protected function updateSortOptions(array &$options): void
    {
        unset($options['order']);
        if (substr($options['sort'], 0, 1) == '-') {
            $options['sort'] = substr($options['sort'], 1);
            $options['direction'] = 'desc';
        }
        if (strpos($options['sort'], '.') !== false) {
            $parts = explode('.', $options['sort'], 2);
            // sort on associated fields available only
            // if a matching filter query string is set
            if (empty($this->getConfig('filter.' . $parts[0]))) {
                return;
            };
            $parts[0] = Inflector::camelize($parts[0]);
            $options['sort'] = implode('.', $parts);
            $options['sortWhitelist'] = $this->sortWhitelist;
        }
    }
}
