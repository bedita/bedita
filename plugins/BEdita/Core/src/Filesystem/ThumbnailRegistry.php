<?php
/**
 * BEdita, API-first content management framework
 * Copyright 2018 ChannelWeb Srl, Chialab Srl
 *
 * This file is part of BEdita: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published
 * by the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * See LICENSE.LGPL or <http://gnu.org/licenses/lgpl-3.0.html> for more details.
 */

namespace BEdita\Core\Filesystem;

use Cake\Core\App;
use Cake\Core\ObjectRegistry;

/**
 * Registry for thumbnail generators.
 *
 * @method \BEdita\Core\Filesystem\ThumbnailGenerator get($name)
 * @method \BEdita\Core\Filesystem\ThumbnailGenerator load($objectName, $config = [])
 *
 * @since 4.0.0
 */
class ThumbnailRegistry extends ObjectRegistry
{

    /**
     * {@inheritDoc}
     */
    protected function _resolveClassName($class)
    {
        if (is_object($class)) {
            return $class;
        }

        return App::className($class, 'Filesystem/Thumbnail', 'Generator');
    }

    /**
     * {@inheritDoc}
     */
    protected function _throwMissingClassError($class, $plugin)
    {
        throw new \BadMethodCallException(sprintf('Thumbnail generator %s is not available.', $class));
    }

    /**
     * {@inheritDoc}
     */
    protected function _create($class, $alias, $config)
    {
        if (is_object($class)) {
            $instance = $class;
        }

        unset($config['className']);
        if (!isset($instance)) {
            $instance = new $class($config);
        }

        if (!($instance instanceof ThumbnailGenerator)) {
            throw new \RuntimeException(
                sprintf('Thumbnail generators must use %s as a base class.', ThumbnailGenerator::class)
            );
        }

        if (!$instance->initialize($config)) {
            throw new \RuntimeException(
                sprintf('Thumbnail generator %s is not properly configured.', get_class($instance))
            );
        }

        return $instance;
    }
}
