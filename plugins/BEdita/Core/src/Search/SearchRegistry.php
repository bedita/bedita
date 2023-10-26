<?php
declare(strict_types=1);

/**
 * BEdita, API-first content management framework
 * Copyright 2023 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */
namespace BEdita\Core\Search;

use BadMethodCallException;
use BEdita\Core\Search\Adapter\SimpleAdapter;
use Cake\Core\App;
use Cake\Core\ObjectRegistry;
use Cake\Core\StaticConfigTrait;
use RuntimeException;

/**
 * Registry for search adapters.
 *
 * @since 5.14.0
 */
class SearchRegistry extends ObjectRegistry
{
    use StaticConfigTrait;

    /**
     * An array mapping url schemes to fully qualified search adapter class names
     *
     * @var array
     */
    protected static $_dsnClassMap = [
        'simple' => SimpleAdapter::class,
    ];

    /**
     * @inheritDoc
     */
    protected function _resolveClassName(string $class): ?string
    {
        return App::className($class, 'Search/Adapter', 'Adapter');
    }

    /**
     * @inheritDoc
     */
    protected function _throwMissingClassError(string $class, ?string $plugin): void
    {
        throw new BadMethodCallException(sprintf('Search adapter %s is not available.', $class));
    }

    /**
     * @inheritDoc
     */
    protected function _create($class, string $alias, array $config)
    {
        if (is_object($class)) {
            $instance = $class;
        } else {
            unset($config['className']);
            $instance = new $class();
        }

        if (!($instance instanceof BaseAdapter)) {
            throw new RuntimeException(sprintf('Search adapters must use %s as a base class.', BaseAdapter::class));
        }

        return $instance->setAlias($alias)->initialize($config);
    }
}
