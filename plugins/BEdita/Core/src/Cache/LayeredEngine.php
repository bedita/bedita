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
namespace BEdita\Core;

use Cake\Cache\Cache;
use Cake\Cache\CacheEngine;
use Exception;

/**
 * This engine uses two layers of cache, one persistent and one in-memory for faster lookup times.
 */
class LayeredEngine extends CacheEngine
{
    /**
     * Persistent cache instance.
     *
     * @var CacheEngine
     */
    protected $_persistent = null;

    /**
     * In-memory cache instance.
     *
     * @var CacheEngine
     */
    protected $_memory = null;

    /**
     * The default config used unless overridden by runtime configuration
     *
     * - `persistent` A cache configuration or an alias, to use as persistent cache
     *
     * @var array
     */
    protected $_defaultConfig = [
        'persistent' => ['className' => 'File'],
    ];


    /**
     * {@inheritDoc}
     * @throws Exception If the configuration is wrong
     */
    public function init(array $config = []): bool
    {
        parent::init($config);

        $this->_persistent = $this->getEngineInstance($this->getConfig('persistent'));
        $this->_memory = $this->getEngineInstance(['className' => 'Array']);

        return true;
    }

    /**
     * Get the engine instance from a configuration or an alias.
     *
     * @param array|string $config Engine configuration or an alias
     * @return CacheEngine The engine instance
     * @throws Exception If the configuration is wrong
     */
    protected function getEngineInstance($config): CacheEngine
    {
        $registry = Cache::getRegistry();

        if (is_string($config)) {
            if (!$registry->has($config)) {
                throw new Exception("Cache engine alias {$config} is not defined");
            }

            $instance = $registry->get($config);

            if (!$instance instanceof CacheEngine) {
                throw new Exception("Cache engine alias {$config} is not an implementation of CacheEngine");
            }

            return $instance;
        }

        if (is_array($config)) {
            $name = ($config['prefix'] ?? '') . $config['className'];

            return $registry->load($name, $config);
        }

        throw new Exception("Unknown cache configuration");
    }

    /**
     * {@inheritDoc}
     */
    public function write($key, $value): bool
    {
        $this->_memory->write($key, $value);

        return $this->_persistent->write($key, $value);
    }

    /**
     * {@inheritDoc}
     */
    public function read($key)
    {
        $value = $this->_memory->read($key);

        if ($value !== false) {
            return $value;
        }

        $value = $this->_persistent->read($key);
        $this->_memory->write($key, $value);

        return $value;
    }

    /**
     * {@inheritDoc}
     */
    public function increment($key, $offset = 1)
    {
        if ($this->_memory->read($key) === false) {
            $value = $this->_persistent->read($key);
            $this->_memory->write($key, $value);
        }

        $this->_memory->increment($key, $offset);

        return $this->_persistent->increment($key, $offset);
    }

    /**
     * {@inheritDoc}
     */
    public function decrement($key, $offset = 1)
    {
        if ($this->_memory->read($key) === false) {
            $value = $this->_persistent->read($key);
            $this->_memory->write($key, $value);
        }

        $this->_memory->increment($key, $offset);

        return $this->_persistent->increment($key, $offset);
    }

    /**
     * {@inheritDoc}
     */
    public function delete($key): bool
    {
        $this->_memory->delete($key);

        return $this->_persistent->delete($key);
    }

    /**
     * {@inheritDoc}
     */
    public function clear($check): bool
    {
        $this->_memory->clear($check);

        return $this->_persistent->clear($check);
    }
}
