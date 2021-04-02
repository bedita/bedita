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

namespace BEdita\Core\Model\Table;

use Cake\Cache\Cache;
use Cake\ORM\Table;

/**
 * Abstract base class for table classes using query `cache` in some methods.
 * Cache invalidation is performed in `afterSave` and `afterDelete` methods.
 *
 * @since 4.4.0
 */
abstract class QueryCacheTable extends Table
{
    /**
     * Cache config name.
     *
     * @var string
     */
    const CACHE_CONFIG = '_bedita_core_';

    /**
     * Invalidate database config cache after saving a config entity.
     *
     * @return void
     */
    public function afterSave(): void
    {
        Cache::clear(false, self::CACHE_CONFIG);
    }

    /**
     * Invalidate database config cache after deleting a config entity.
     *
     * @return void
     */
    public function afterDelete(): void
    {
        Cache::clear(false, self::CACHE_CONFIG);
    }
}
