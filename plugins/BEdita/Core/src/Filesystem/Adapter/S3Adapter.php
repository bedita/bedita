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

use Aws\S3\S3Client;
use BEdita\Core\Filesystem\FilesystemAdapter;
use League\Flysystem\AwsS3v3\AwsS3Adapter;

/**
 * AWS S3 adapter.
 *
 * @since 4.0.0
 */
class S3Adapter extends FilesystemAdapter
{

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'region' => null,
        'version' => 'latest',
    ];

    /**
     * AWS S3 client.
     *
     * @var \Aws\S3\S3Client
     */
    protected $client;

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        if (isset($config['username']) && isset($config['password'])) {
            $config['credentials'] = [
                'key' => $config['username'],
                'secret' => $config['password'],
            ];
        }

        return parent::initialize($config);
    }

    /**
     * {@inheritDoc}
     */
    protected function buildAdapter(array $config)
    {
        $this->client = new S3Client($this->getConfig());

        return new AwsS3Adapter(
            $this->client,
            $this->getConfig('host'),
            $this->getConfig('path'),
            (array)$this->getConfig('options')
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getPublicUrl($path)
    {
        if (!empty($this->_config['baseUrl'])) {
            return parent::getPublicUrl($path);
        }

        return $this->client->getObjectUrl(
            $this->getConfig('host'),
            $this->getConfig('path') . $path
        );
    }
}
