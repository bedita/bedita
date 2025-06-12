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
namespace BEdita\API\Datasource;

use BEdita\Core\Model\Enum\DateRangesSortField;
use Cake\Database\Expression\FunctionExpression;
use Cake\Database\Expression\OrderClauseExpression;
use Cake\Datasource\Paging\NumericPaginator;
use Cake\Datasource\Paging\PaginatedInterface;
use Cake\Datasource\QueryInterface;
use Cake\Datasource\RepositoryInterface;
use Cake\Http\Exception\BadRequestException;
use Cake\ORM\Query\SelectQuery;

/**
 * Handle model pagination using JSON API conventions.
 *
 * @since 4.0.0
 */
class JsonApiPaginator extends NumericPaginator
{
    /**
     * @inheritDoc
     */
    protected array $_defaultConfig = [
        'page' => 1,
        'limit' => 20,
        'maxLimit' => 100,
        'allowedParameters' => ['page', 'page_size', 'sort'],
        'sortableFields' => null,
        'finder' => 'all',
        'scope' => null,
    ];

    /**
     * Max limit per pagination items.
     *
     * @var int
     */
    public const MAX_LIMIT = 500;

    /**
     * {@inheritDoc}
     *
     * Remove any other `order` clause if an explicit 'sort' is requested
     */
    public function paginate(mixed $object, array $params = [], array $settings = []): PaginatedInterface
    {
        if ($object instanceof QueryInterface && !empty($params['sort'])) {
            $object->orderBy([], SelectQuery::OVERWRITE);
        }

        return parent::paginate($object, $params, $settings);
    }

    /**
     * @inheritDoc
     */
    public function checkLimit(array $options): array
    {
        $options['maxLimit'] = min((int)$options['maxLimit'], static::MAX_LIMIT);

        if (!empty($options['page_size'])) {
            $options['limit'] = $options['page_size'];
        }
        unset($options['page_size']);

        return parent::checkLimit($options);
    }

    /**
     * @inheritDoc
     */
    public function validateSort(RepositoryInterface $object, array $options): array
    {
        $sortedRequest = false;
        if (!empty($options['sort'])) {
            $sortedRequest = true;
            if (substr($options['sort'], 0, 1) == '-') {
                $options['sort'] = substr($options['sort'], 1);
                $options['direction'] = 'desc';
            }
            unset($options['order']);
            if (in_array($options['sort'], DateRangesSortField::values())) {
                $options['sortableFields'] = [$options['sort']];
            }

            $sortableFields = $options['sortableFields'] ?? null;
            $canSortField = fn (string $field): bool => $sortableFields === null || in_array($field, $sortableFields, true);
            if ($options['sort'] === 'published' && $canSortField('publish_start')) {
                $options['order'] = new OrderClauseExpression(
                    new FunctionExpression(
                        'COALESCE',
                        [
                            $object->getAlias() . '.publish_start' => 'identifier',
                            $object->getAlias() . '.created' => 'identifier',
                        ],
                        ['timestamp', 'timestamp'],
                        'timestamp',
                    ),
                    $options['direction'] ?? 'asc',
                );
                unset($options['sort'], $options['direction']);
            }
        }

        $options = parent::validateSort($object, $options);

        if ($sortedRequest && empty($options['order'])) {
            throw new BadRequestException(__('Unsupported sorting field'));
        }

        return $options;
    }
}
