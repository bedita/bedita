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

namespace BEdita\Core\Filesystem\Adapter;

use BEdita\Core\Filesystem\FilesystemAdapter;
use Cake\Routing\Router;
use League\Flysystem\Adapter\Local;

/**
 * Adapter to store files on local filesystem.
 *
 * @since 4.0.0
 */
class LocalAdapter extends FilesystemAdapter
{

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'baseUrl' => null,
        'path' => WWW_ROOT . DS . 'files',
        'writeFlags' => LOCK_EX,
        'linkHandling' => Local::DISALLOW_LINKS,
        'permissions' => [],
        'visibility' => 'public',
    ];

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        $success = parent::initialize($config);

        if (empty($this->_config['baseUrl']) && strpos($this->getConfig('path'), WWW_ROOT . DS) === 0) {
            // Files are stored within the document root, so base URL can be automatically detected if not already set.
            $path = str_replace(DS, '/', substr($this->getConfig('path'), strlen(WWW_ROOT)));
            $this->setConfig('baseUrl', Router::url($path, true));
        }

        return $success;
    }

    /**
     * {@inheritDoc}
     *
     * @return \League\Flysystem\Adapter\Local
     */
    protected function buildAdapter(array $config)
    {
        return new Local(
            $this->getConfig('path'),
            $this->getConfig('writeFlags'),
            $this->getConfig('linkHandling'),
            (array)$this->getConfig('permissions')
        );
    }
}
