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

namespace BEdita\Core\Filesystem\Adapter;

use BEdita\Core\Filesystem\FilesystemAdapter;
use Cake\Routing\Router;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\UnixVisibility\PortableVisibilityConverter;

/**
 * Adapter to store files on local filesystem.
 *
 * @since 4.0.0
 */
class LocalAdapter extends FilesystemAdapter
{
    /**
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'baseUrl' => null,
        'path' => WWW_ROOT . '_files',
        'writeFlags' => LOCK_EX,
        'linkHandling' => LocalFilesystemAdapter::DISALLOW_LINKS,
        'permissions' => [],
        'visibility' => 'public',
    ];

    /**
     * @inheritDoc
     */
    public function initialize(array $config): bool
    {
        $success = parent::initialize($config);

        if (empty($this->_config['baseUrl']) && strpos($this->getConfig('path'), WWW_ROOT) === 0) {
            // Files are stored within the document root, so base URL can be automatically detected if not already set.
            $path = str_replace(DS, '/', substr($this->getConfig('path'), strlen(WWW_ROOT)));
            $this->setConfig('baseUrl', Router::url($path, true));
        }

        return $success;
    }

    /**
     * {@inheritDoc}
     *
     * @return \League\Flysystem\Local\LocalFilesystemAdapter
     */
    protected function buildAdapter(array $config)
    {
        return new LocalFilesystemAdapter(
            $this->getConfig('path'),
            PortableVisibilityConverter::fromArray((array)$this->getConfig('permissions')),
            $this->getConfig('writeFlags'),
            $this->getConfig('linkHandling'),
        );
    }
}
