<?php
declare(strict_types=1);

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

use BEdita\Core\Exception\InvalidDataException;
use BEdita\Core\Filesystem\Exception\InvalidStreamException;
use BEdita\Core\Filesystem\FilesystemRegistry;
use BEdita\Core\Filesystem\ThumbnailGenerator;
use BEdita\Core\Model\Entity\Stream;
use Cake\Log\Log;
use Cake\Utility\Hash;
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
     * @inheritDoc
     */
    protected $_defaultConfig = [
        'cache' => 'thumbnails',
        'driver' => 'gd',
        'maxThumbSize' => 1 << 22, // 2048 * 2048 === 2^11 * 2^11 === 2^22
        'maxImageSize' => 7680 * 4320, // 8K
    ];

    /**
     * Get destination path for a thumbnail.
     *
     * @param \BEdita\Core\Model\Entity\Stream $stream Stream entity instance.
     * @param array $options Thumbnail options.
     * @return string
     * @throws \BEdita\Core\Exception\InvalidDataException
     */
    protected function getFilename(Stream $stream, array $options = []): string
    {
        $ext = Hash::get($options, 'fm', 'jpg'); // jpg default
        if (!in_array($ext, ['jpg', 'pjpg', 'png', 'gif', 'webp', 'avif'])) {
            throw new InvalidDataException(__d('bedita', 'Invalid thumbnail format: {0}', $ext));
        }
        $filesystem = $this->getConfig('cache', 'thumbnails');
        $base = $stream->filesystemPath($filesystem);
        $options = sha1(serialize($options));

        return sprintf(
            '%s/%s.%s',
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
    protected function getGlideApi(): GlideApi
    {
        $driver = $this->getConfig('driver', 'gd');

        return new GlideApi(
            new ImageManager(compact('driver')),
            [
                new OrientationManipulator(),
                new CropManipulator(),
                new SizeManipulator($this->getConfig('maxThumbSize', 1 << 22)), // 2048 * 2048 === 2^11 * 2^11 === 2^22
                new BlurManipulator(),
                new EncodeManipulator(),
            ]
        );
    }

    /**
     * Generate a thumbnail and return the thumbnail's binary content as a string.
     *
     * @param \BEdita\Core\Model\Entity\Stream $stream Stream entity instance.
     * @param array $options Thumbnail options.
     * @return string
     */
    protected function makeThumbnail(Stream $stream, array $options = []): string
    {
        $source = (string)$stream->contents;

        return $this->getGlideApi()
            ->run($source, $options);
    }

    /**
     * Check that image resolution is within configured boundaries.
     *
     * @param \BEdita\Core\Model\Entity\Stream $stream Stream entity instance.
     * @return void
     * @throws \BEdita\Core\Filesystem\Exception\InvalidStreamException
     */
    protected function checkImageResolution(Stream $stream): void
    {
        if (!preg_match('/image\/(?!svg)/', (string)$stream->mime_type)) {
            return;
        }

        $maxImageSize = $this->getConfig('maxImageSize', 7680 * 4320); // 8K
        if (empty($stream->width) || empty($stream->height)) {
            Log::notice(sprintf('Unable to obtain resolution for stream %s', $stream->uuid));

            return;
        }

        if ($stream->width * $stream->height <= $maxImageSize) {
            return;
        }

        throw new InvalidStreamException(__d('bedita', 'Image exceeds the maximum resolution of {0} Megapixels for thumbnail generation', round($maxImageSize / 10 ** 6, 1)));
    }

    /**
     * @inheritDoc
     */
    public function getUrl(Stream $stream, array $options = []): string
    {
        if ($this->isSvg($stream)) {
            return $stream->get('url');
        }

        $path = $this->getFilename($stream, $options);

        return FilesystemRegistry::getPublicUrl($path);
    }

    /**
     * @inheritDoc
     */
    public function generate(Stream $stream, array $options = []): bool
    {
        if ($this->isSvg($stream)) {
            return true;
        }

        $path = $this->getFilename($stream, $options);
        $this->checkImageResolution($stream);

        try {
            $thumbnail = $this->makeThumbnail($stream, $options);

            FilesystemRegistry::getMountManager()->write($path, $thumbnail);
        } catch (NotReadableException $e) {
            throw new InvalidStreamException(__d('bedita', 'Unable to generate thumbnail for stream {0}', $stream->uuid), null, $e);
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    public function exists(Stream $stream, array $options = []): bool
    {
        if ($this->isSvg($stream)) {
            return true;
        }

        $path = $this->getFilename($stream, $options);

        return FilesystemRegistry::getMountManager()->fileExists($path);
    }

    /**
     * @inheritDoc
     */
    public function delete(Stream $stream): void
    {
        $filesystem = $this->getConfig('cache', 'thumbnails');
        $base = $stream->filesystemPath($filesystem);

        FilesystemRegistry::getMountManager()->deleteDirectory($base);
    }

    /**
     * Check if the stream is an SVG image.
     *
     * @param \BEdita\Core\Model\Entity\Stream $stream Stream entity instance.
     * @return bool
     */
    protected function isSvg(Stream $stream): bool
    {
        return $stream->mime_type === 'image/svg+xml';
    }
}
