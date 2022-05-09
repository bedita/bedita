<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2021 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Model\Behavior;

use Cake\Cache\Cache;
use Cake\ORM\Behavior;
use Cake\ORM\Query;

/**
 * Behavior to handle caching easily in table classes via `queryCache` methods.
 * Cache invalidation is performed in `afterSave` and `afterDelete` methods.
 *
 * @since 4.4.0
 */
class QueryCacheBehavior extends Behavior
{
    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'cacheConfig' => '_bedita_core_',
    ];

    /**
     * Invalidate database config cache after saving a config entity.
     *
     * @return void
     */
    public function afterSave(): void
    {
        Cache::clear($this->getConfig('cacheConfig'));
    }

    /**
     * Invalidate database config cache after deleting a config entity.
     *
     * @return void
     */
    public function afterDelete(): void
    {
        Cache::clear($this->getConfig('cacheConfig'));
    }

    /**
     * Add query cache using configured cache config.
     *
     * @param \Cake\ORM\Query $query Query object
     * @param string $key Cache key
     * @return \Cake\ORM\Query
     */
    public function queryCache(Query $query, string $key): Query
    {
        return $query->cache($key, $this->getConfig('cacheConfig'));
    }
}
