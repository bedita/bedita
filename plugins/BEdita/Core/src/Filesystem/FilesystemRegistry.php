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

namespace BEdita\Core\Filesystem;

use BEdita\Core\Filesystem\Adapter\LocalAdapter;
use BEdita\Core\SingletonTrait;
use Cake\Core\App;
use Cake\Core\ObjectRegistry;
use Cake\Core\StaticConfigTrait;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemNotFoundException;
use League\Flysystem\MountManager;

/**
 * Registry for filesystem adapters.
 *
 * @since 4.0.0
 */
class FilesystemRegistry extends ObjectRegistry
{

    use SingletonTrait;
    use StaticConfigTrait;

    /**
     * Mount manager.
     *
     * @var \League\Flysystem\MountManager
     */
    protected $mountManager;

    /**
     * An array mapping url schemes to fully qualified Log engine class names
     *
     * @var array
     */
    protected static $_dsnClassMap = [
        'local' => LocalAdapter::class,
    ];

    /**
     * {@inheritDoc}
     */
    protected function _resolveClassName($class)
    {
        if (is_object($class)) {
            return $class;
        }

        return App::className($class, 'Filesystem/Adapter', 'Adapter');
    }

    /**
     * {@inheritDoc}
     */
    protected function _throwMissingClassError($class, $plugin)
    {
        throw new \BadMethodCallException(sprintf('Filesystem adapter %s is not available.', $class));
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

        if (!($instance instanceof FilesystemAdapter)) {
            throw new \RuntimeException(
                sprintf('Filesystem adapters must use %s as a base class.', FilesystemAdapter::class)
            );
        }

        if (!$instance->initialize($config)) {
            throw new \RuntimeException(
                sprintf('Filesystem adapter %s is not properly configured.', get_class($instance))
            );
        }

        return $instance;
    }

    /**
     * {@inheritDoc}
     *
     * @return \BEdita\Core\Filesystem\FilesystemAdapter|null
     */
    public function get($name)
    {
        /* @var \BEdita\Core\Filesystem\FilesystemAdapter|null $adapter */
        $adapter = parent::get($name);
        if ($adapter !== null || !in_array($name, static::configured())) {
            return $adapter;
        }

        return $this->load($name, static::getConfig($name));
    }

    /**
     * Drop all filesystems.
     *
     * @return void
     */
    public static function dropAll()
    {
        foreach (static::configured() as $config) {
            static::drop($config);
        }

        $instance = static::getInstance();
        $instance->reset();
        $instance->mountManager = null;
    }

    /**
     * Get mount manager to transparently handle files basing on the prefix.
     *
     * @return \League\Flysystem\MountManager
     */
    public static function getMountManager()
    {
        $instance = static::getInstance();
        if (!empty($instance->mountManager)) {
            return $instance->mountManager;
        }

        $filesystems = [];
        foreach (static::configured() as $prefix) {
            $adapter = $instance->get($prefix);
            $filesystems[$prefix] = new Filesystem($adapter->getInnerAdapter(), [
                'visibility' => $adapter->getVisibility(),
            ]);
        }

        return $instance->mountManager = new MountManager($filesystems);
    }

    /**
     * Get public URL for a file.
     *
     * @param string $path Original path.
     * @return string
     * @throws \League\Flysystem\FilesystemNotFoundException Throws an exception if a filesystem with such prefix
     *      could not be found.
     */
    public static function getPublicUrl($path)
    {
        list($prefix, $path) = static::getPrefixAndPath($path);

        $adapter = static::getInstance()->get($prefix);
        if ($adapter === null) {
            throw new FilesystemNotFoundException(sprintf('No filesystem mounted with prefix %s', $prefix));
        }

        return $adapter->getPublicUrl($path);
    }

    /**
     * Split path into prefix and path.
     *
     * @see \League\Flysystem\MountManager::getPrefixAndPath()
     * @param string $path Original path.
     * @return string[]
     * @throws \InvalidArgumentException Throws an exception if path could not be parsed.
     */
    protected static function getPrefixAndPath($path)
    {
        if (!is_string($path) || strpos($path, '://') < 1) {
            throw new \InvalidArgumentException(sprintf('No prefix detected in path: %s', $path));
        }

        return explode('://', $path, 2);
    }
}
