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

use BEdita\Core\Filesystem\Exception\InvalidStreamException;
use BEdita\Core\Filesystem\Exception\InvalidThumbnailOptionsException;
use BEdita\Core\Filesystem\Thumbnail\AsyncGenerator;
use BEdita\Core\Filesystem\Thumbnail\GlideGenerator;
use BEdita\Core\Model\Entity\Stream;
use Cake\Core\Configure;
use Cake\Core\StaticConfigTrait;
use Cake\Utility\Hash;

/**
 * High level interface for thumbnails.
 *
 * @since 4.0.0
 */
class Thumbnail
{

    use StaticConfigTrait;

    /**
     * Thumbnail registry.
     *
     * @var \BEdita\Core\Filesystem\ThumbnailRegistry
     */
    protected static $_registry;

    /**
     * An array mapping URL schemes to fully qualified Thumbnail generator class names.
     *
     * @var array
     */
    protected static $_dsnClassMap = [
        'glide' => GlideGenerator::class,
        'async' => AsyncGenerator::class,
    ];

    /**
     * Setter for thumbnails registry.
     *
     * @param \BEdita\Core\Filesystem\ThumbnailRegistry|null $registry Thumbnail generator registry.
     * @return void
     */
    public static function setRegistry(ThumbnailRegistry $registry = null)
    {
        static::$_registry = $registry;
    }

    /**
     * Getter for thumbnails registry.
     *
     * @return \BEdita\Core\Filesystem\ThumbnailRegistry
     */
    public static function getRegistry()
    {
        if (!isset(static::$_registry)) {
            static::$_registry = new ThumbnailRegistry();
        }

        return static::$_registry;
    }

    /**
     * Get a generator by name.
     *
     * @param string $name Name of generator to get.
     * @return \BEdita\Core\Filesystem\GeneratorInterface
     */
    public static function getGenerator($name)
    {
        $registry = static::getRegistry();

        if ($registry->has($name)) {
            return $registry->get($name);
        }

        return $registry->load($name, static::getConfig($name));
    }

    /**
     * Generate a thumbnail for a stream.
     *
     * @param \BEdita\Core\Model\Entity\Stream $stream Stream to generate thumbnail for.
     * @param string|array $options Preset name, or array of thumbnail options.
     * @return array Generated thumbnail URL and ready status.
     */
    public static function get(Stream $stream, $options = 'default')
    {
        $options = self::getOptions($options);

        $generator = Hash::get($options, 'generator', 'default');
        unset($options['generator']);

        $generator = static::getGenerator($generator);
        $url = $generator->getUrl($stream, $options);
        $ready = $generator->exists($stream, $options);
        if (!$ready) {
            try {
                $ready = $generator->generate($stream, $options);
            } catch (InvalidStreamException $e) {
                $acceptable = false;
            }
        }

        return compact('url', 'ready', 'acceptable');
    }

    /**
     * Get options for thumbnail generation.
     *
     * @param string|array $options Preset name, or array of options.
     * @return array
     */
    protected static function getOptions($options)
    {
        if (is_string($options)) {
            $key = sprintf('Thumbnails.presets.%s', $options);
            if (!Configure::check($key)) {
                throw new InvalidThumbnailOptionsException(__('Preset "{0}" not found', $options));
            }
            $options = Configure::read($key);
        } elseif (!Configure::read('Thumbnails.allowAny')) {
            throw new InvalidThumbnailOptionsException(__('Thumbnails can only be generated for one of the configured presets'));
        }

        return $options;
    }

    /**
     * Delete all generated thumbnails for a stream.
     *
     * @param \BEdita\Core\Model\Entity\Stream $stream Stream to delete thumbnails for.
     * @return void
     */
    public static function delete(Stream $stream)
    {
        $generators = static::configured();
        foreach ($generators as $generator) {
            static::getGenerator($generator)->delete($stream);
        }
    }
}
