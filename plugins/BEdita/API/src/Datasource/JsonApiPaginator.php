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

use BEdita\Core\Model\Table\DateRangesTable;
use Cake\Datasource\Paginator;
use Cake\Datasource\QueryInterface;
use Cake\Datasource\RepositoryInterface;
use Cake\Datasource\ResultSetInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Query;

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
        'sortWhitelist' => DateRangesTable::SPECIAL_SORT_FIELDS,
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
            if (substr($options['sort'], 0, 1) == '-') {
                $options['sort'] = substr($options['sort'], 1);
                $options['direction'] = 'desc';
            }
            unset($options['order']);
        }

        $options = parent::validateSort($object, $options);

        if ($sortedRequest && empty($options['order'])) {
            throw new BadRequestException(__('Unsupported sorting field'));
        }

        return $options;
    }
}
