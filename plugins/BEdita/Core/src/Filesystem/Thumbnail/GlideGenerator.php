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

namespace BEdita\Core\Filesystem\Thumbnail;

use BEdita\Core\Filesystem\Exception\InvalidStreamException;
use BEdita\Core\Filesystem\FilesystemRegistry;
use BEdita\Core\Filesystem\ThumbnailGenerator;
use BEdita\Core\Model\Entity\Stream;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\ImageManager;
use League\Glide\Api\Api as GlideApi;
use League\Glide\Manipulators\Blur as BlurManipulator;
use League\Glide\Manipulators\Crop as CropManipulator;
use League\Glide\Manipulators\Encode as EncodeManipulator;
use League\Glide\Manipulators\Orientation as OrientationManipulator;
use League\Glide\Manipulators\Size as SizeManipulator;

/**
 * Thumbnail generator that uses Intervention library.
 *
 * @since 4.0.0
 */
class GlideGenerator extends ThumbnailGenerator
{

    /**
     * Enforced maximum image size.
     *
     * @var int
     */
    const MAX_IMAGE_SIZE = 1 << 22; // 2048 * 2048 === 2^11 * 2^11 === 2^22

    /**
     * {@inheritDoc}
     */
    protected $_defaultConfig = [
        'cache' => 'thumbnails',
        'driver' => 'gd',
    ];

    /**
     * Get destination path for a thumbnail.
     *
     * @param \BEdita\Core\Model\Entity\Stream $stream Stream entity instance.
     * @param array $options Thumbnail options.
     * @return string
     */
    protected function getFilename(Stream $stream, array $options = [])
    {
        $ext = pathinfo($stream->file_name, PATHINFO_EXTENSION);
        if ($ext) {
            $ext = '.' . $ext;
        }
        $filesystem = $this->getConfig('cache', 'thumbnails');
        $base = $stream->filesystemPath($filesystem);
        $options = sha1(serialize($options));

        return sprintf(
            '%s/%s%s',
            $base,
            $options,
            (string)$ext
        );
    }

    /**
     * Get Glide API runner.
     *
     * @return \League\Glide\Api\Api
     */
    protected function getGlideApi()
    {
        $driver = $this->getConfig('driver', 'gd');
        $api = new GlideApi(
            new ImageManager(compact('driver')),
            [
                new OrientationManipulator(),
                new CropManipulator(),
                new SizeManipulator(static::MAX_IMAGE_SIZE),
                new BlurManipulator(),
                new EncodeManipulator(),
            ]
        );

        return $api;
    }

    /**
     * Generate a thumbnail and return the thumbnail's binary content as a string.
     *
     * @param \BEdita\Core\Model\Entity\Stream $stream Stream entity instance.
     * @param array $options Thumbnail options.
     * @return string
     */
    protected function makeThumbnail(Stream $stream, array $options = [])
    {
        $source = (string)$stream->contents;

        $thumbnail = $this->getGlideApi()
            ->run($source, $options);

        return $thumbnail;
    }

    /**
     * {@inheritDoc}
     */
    public function getUrl(Stream $stream, array $options = [])
    {
        $path = $this->getFilename($stream, $options);

        return FilesystemRegistry::getPublicUrl($path);
    }

    /**
     * {@inheritDoc}
     */
    public function generate(Stream $stream, array $options = [])
    {
        $path = $this->getFilename($stream, $options);

        try {
            $thumbnail = $this->makeThumbnail($stream, $options);

            FilesystemRegistry::getMountManager()->put($path, $thumbnail);
        } catch (NotReadableException $e) {
            throw new InvalidStreamException(__('Unable to generate thumbnail for stream {0}', $stream->uuid), null, $e);
        }

        return true;
    }

    /**
     * {@inheritDoc}
     */
    public function exists(Stream $stream, array $options = [])
    {
        $path = $this->getFilename($stream, $options);

        return FilesystemRegistry::getMountManager()->has($path);
    }

    /**
     * {@inheritDoc}
     */
    public function delete(Stream $stream)
    {
        $filesystem = $this->getConfig('cache', 'thumbnails');
        $base = $stream->filesystemPath($filesystem);

        FilesystemRegistry::getMountManager()->deleteDir($base);
    }
}
