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

    /**
     * {@inheritDoc}
     */
    public function mergeOptions($alias, $settings)
    {
        $options = parent::mergeOptions($alias, $settings);

        if (!empty($options['page_size'])) {
            $options['limit'] = $options['page_size'];
        }
        unset($options['page_size']);

        return $options;
    }
}
