<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2016 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\API\Controller\Component;

use Cake\Controller\Component\PaginatorComponent as CakePaginatorComponent;
use Cake\Datasource\RepositoryInterface;
use Cake\Network\Exception\BadRequestException;

/**
 * Handles pagination.
 *
 * @since 4.0.0
 */
class PaginatorComponent extends CakePaginatorComponent
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

    public $absoluteLimit = 500;

    /**
     * {@inheritDoc}
     */
    public function mergeOptions($alias, $settings)
    {
        $options = parent::mergeOptions($alias, $settings);
        $options['maxLimit'] = min($options['maxLimit'], $this->absoluteLimit);

        if (!empty($options['page_size'])) {
            $options['limit'] = $options['page_size'];
        }
        unset($options['page_size']);

        return $options;
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
        }

        $options = parent::validateSort($object, $options);

        if ($sortedRequest && empty($options['order'])) {
            throw new BadRequestException(__('Unsupported sorting field'));
        }

        return $options;
    }
}
