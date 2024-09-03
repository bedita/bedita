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

namespace BEdita\Core\Filesystem;

use Cake\Core\InstanceConfigTrait;
use League\Flysystem\FilesystemAdapter as LeagueFilesystemAdapter;

/**
 * Filesystem adapter.
 *
 * This abstract class is a wrapper around Flysystem adapters, to ensure they can be instantiated
 * in a consistent way by delegating adapter's specific initialization logic to subclasses.
 *
 * Also, subclasses of this class are responsible for building public URLs from which files can
 * be accessed, although a generic implementation is present in this abstract class.
 *
 * @since 4.0.0
 */
abstract class FilesystemAdapter
{
    use InstanceConfigTrait;

    /**
     * Default configuration for adapter.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'baseUrl' => null,
        'visibility' => 'public',
    ];

    /**
     * Inner adapter instance.
     *
     * @var \League\Flysystem\FilesystemAdapter
     */
    protected $adapter;

    /**
     * Initialize filesystem adapter class.
     *
     * @param array $config Configuration.
     * @return bool
     */
    public function initialize(array $config): bool
    {
        $this->setConfig($config);

        return true;
    }

    /**
     * Get the inner adapter class.
     *
     * @return \League\Flysystem\FilesystemAdapter
     */
    public function getInnerAdapter()
    {
        if (!empty($this->adapter)) {
            return $this->adapter;
        }

        $adapter = $this->buildAdapter($this->getConfig());
        if (!($adapter instanceof LeagueFilesystemAdapter)) {
            throw new \RuntimeException(
                sprintf('Filesystem adapters must use %s as a base class.', AdapterInterface::class)
            );
        }

        return $this->adapter = $adapter;
    }

    /**
     * Build the inner adapter from configuration.
     *
     * @param array $config Adapter configuration.
     * @return \League\Flysystem\FilesystemAdapter
     */
    abstract protected function buildAdapter(array $config);

    /**
     * Get public URL for an item.
     *
     * @param string $path Resource path.
     * @return string
     */
    public function getPublicUrl($path): string
    {
        return sprintf(
            '%s/%s',
            rtrim($this->getConfig('baseUrl'), '/'),
            ltrim($path, '/')
        );
    }

    /**
     * Get default visibility for this adapter.
     *
     * @return string|null
     */
    public function getVisibility(): ?string
    {
        return $this->getConfig('visibility');
    }
}
